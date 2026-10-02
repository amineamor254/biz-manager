<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    private const STATUSES = ['pending', 'processing', 'completed', 'cancelled'];

    private const MAX_TOTAL_CENTS = 999999999999;

    public function index()
    {
        $orders = Order::with(['client', 'items'])->latest('order_date')->latest('id')->get();

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $products = Product::orderBy('name')->get(['id', 'name', 'price']);
        $statuses = ['pending'];

        return view('orders.create', compact('clients', 'products', 'statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $order = DB::transaction(function () use ($validated): Order {
            $client = Client::query()->whereKey($validated['client_id'])->lockForUpdate()->firstOrFail();
            $products = $this->lockProducts(collect($validated['items'])->pluck('product_id')->all());

            $order = Order::create([
                'client_id' => $client->id,
                'order_number' => 'ORD-TEMP-' . Str::uuid(),
                'order_date' => $validated['order_date'],
                'status' => 'pending',
                'total' => '0.00',
                'notes' => $validated['notes'] ?? null,
            ]);

            $totalCents = $this->replaceItems($order, $validated['items'], $products);
            $order->update([
                'order_number' => 'ORD-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
                'total' => $this->moneyFromCents($totalCents),
            ]);

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $order->load(['client', 'items.product']);

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load('items.product');
        $clients = Client::orderBy('name')->get();
        $products = Product::orderBy('name')->get(['id', 'name', 'price']);
        $statuses = $this->allowedStatuses($order->status);

        return view('orders.edit', compact('order', 'clients', 'products', 'statuses'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate($this->rules($order));

        DB::transaction(function () use ($order, $validated): void {
            $lockedOrder = Order::query()->whereKey($order->getKey())->lockForUpdate()->firstOrFail();
            $client = Client::query()->whereKey($validated['client_id'])->lockForUpdate()->firstOrFail();
            $products = $this->lockProducts(collect($validated['items'])->pluck('product_id')->all());
            $totalCents = $this->replaceItems($lockedOrder, $validated['items'], $products);

            $lockedOrder->update([
                'client_id' => $client->id,
                'order_date' => $validated['order_date'],
                'status' => $validated['status'],
                'total' => $this->moneyFromCents($totalCents),
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('orders.show', $order)->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }

    private function rules(?Order $order = null): array
    {
        $workspaceId = current_workspace_id();
        abort_unless($workspaceId, 403);

        return [
            'client_id' => ['required', 'integer', Rule::exists('clients', 'id')->where('workspace_id', $workspaceId)],
            'order_date' => ['required', 'date'],
            'status' => $order
                ? ['required', Rule::in($this->allowedStatuses($order->status))]
                : ['prohibited'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'order_number' => ['prohibited'],
            'workspace_id' => ['prohibited'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('products', 'id')->where('workspace_id', $workspaceId),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'items.*.unit_price' => ['prohibited'],
            'items.*.total' => ['prohibited'],
        ];
    }

    private function allowedStatuses(string $current): array
    {
        return match ($current) {
            'pending' => ['pending', 'processing', 'cancelled'],
            'processing' => ['processing', 'completed', 'cancelled'],
            default => [$current],
        };
    }

    private function lockProducts(array $productIds)
    {
        $ids = collect($productIds)->map(fn ($id) => (int) $id)->unique()->sort()->values();
        $products = Product::query()
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($products->count() !== $ids->count()) {
            throw ValidationException::withMessages([
                'items' => 'One or more selected products are unavailable in this workspace.',
            ]);
        }

        return $products;
    }

    private function replaceItems(Order $order, array $items, $products): int
    {
        $order->items()->delete();
        $totalCents = 0;

        foreach ($items as $index => $item) {
            $product = $products->get((int) $item['product_id']);
            $quantity = (int) $item['quantity'];
            $unitPriceCents = $this->moneyToCents((string) $product->getRawOriginal('price'), $index);

            if ($unitPriceCents > 0 && $quantity > intdiv(self::MAX_TOTAL_CENTS, $unitPriceCents)) {
                throw ValidationException::withMessages(["items.{$index}.quantity" => 'The line total exceeds the maximum supported order amount.']);
            }

            $lineTotalCents = $unitPriceCents * $quantity;
            if ($lineTotalCents > self::MAX_TOTAL_CENTS - $totalCents) {
                throw ValidationException::withMessages(["items.{$index}.quantity" => 'The order total exceeds the maximum supported amount.']);
            }

            $totalCents += $lineTotalCents;
            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $this->moneyFromCents($unitPriceCents),
                'total' => $this->moneyFromCents($lineTotalCents),
            ]);
        }

        return $totalCents;
    }

    private function moneyToCents(string $amount, int $index): int
    {
        if (!preg_match('/^(\d{1,8})(?:\.(\d{1,2}))?$/', $amount, $matches)) {
            throw ValidationException::withMessages(["items.{$index}.product_id" => 'A selected product has an invalid unit price.']);
        }

        return ((int) $matches[1] * 100) + (int) str_pad($matches[2] ?? '', 2, '0');
    }

    private function moneyFromCents(int $amount): string
    {
        return intdiv($amount, 100) . '.' . str_pad((string) ($amount % 100), 2, '0', STR_PAD_LEFT);
    }
}