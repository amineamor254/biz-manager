<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    private const MAX_MONEY_CENTS = 9999999999;

    private const STATUSES = ['draft', 'sent', 'paid', 'overdue', 'cancelled'];

   
public function index()
{
        $invoices = Invoice::with('client')->latest()->get();


    return view('invoices.index', compact('invoices'));
}


    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $products = Product::where('quantity', '>', 0)->orderBy('name')->get(['id', 'name', 'price', 'quantity']);
        $statuses = self::STATUSES;

        return view('invoices.create', compact('clients', 'products', 'statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        DB::transaction(function () use ($validated): void {
            $client = Client::query()
                ->whereKey($validated['client_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $products = $this->lockProducts(collect($validated['items'])->pluck('product_id')->all());
            $invoice = Invoice::create([
                'client_id' => $client->id,
                'date' => $validated['date'],
                'status' => $validated['status'],
                'total' => '0.00',
            ]);

            $totalCents = 0;
            foreach ($validated['items'] as $index => $item) {
                $product = $products->get((int) $item['product_id']);
                $quantity = (int) $item['quantity'];
                $this->assertStockAvailable($product, $quantity, $index);

                $unitPriceCents = $this->moneyToCents((string) $product->getRawOriginal('price'));
                $lineTotalCents = $this->lineTotalCents($unitPriceCents, $quantity, $index);
                $totalCents = $this->addMoneyCents($totalCents, $lineTotalCents, $index);

                $invoice->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $this->moneyFromCents($unitPriceCents),
                    'total' => $this->moneyFromCents($lineTotalCents),
                ]);

                $product->quantity -= $quantity;
                $product->save();
            }

            $invoice->update(['total' => $this->moneyFromCents($totalCents)]);
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'items.product']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items.product');
        $clients = Client::orderBy('name')->get();
        $products = Product::orderBy('name')->get(['id', 'name', 'price', 'quantity']);
        $statuses = self::STATUSES;

        return view('invoices.edit', compact('invoice', 'clients', 'products', 'statuses'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate($this->rules($invoice));
        $items = $validated['items'] ?? [];

        DB::transaction(function () use ($invoice, $validated, $items): void {
            $lockedInvoice = Invoice::query()
                ->whereKey($invoice->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $client = Client::query()
                ->whereKey($validated['client_id'])
                ->lockForUpdate()
                ->firstOrFail();
            $oldItems = $lockedInvoice->items()->lockForUpdate()->get();
            $deletedItemIds = collect($validated['deleted_item_ids'] ?? [])->map(fn ($id) => (int) $id)->all();
            $submittedItemIds = collect($items)->pluck('id')->filter()->map(fn ($id) => (int) $id)->all();
            $oldItemIds = $oldItems->pluck('id')->map(fn ($id) => (int) $id)->all();
            $unaccountedItemIds = array_diff($oldItemIds, $submittedItemIds, $deletedItemIds);

            if ($unaccountedItemIds) {
                throw ValidationException::withMessages([
                    'items' => 'Keep each existing invoice item or remove it before saving.',
                ]);
            }

            if (array_intersect($submittedItemIds, $deletedItemIds)) {
                throw ValidationException::withMessages([
                    'items' => 'An invoice item cannot be both kept and removed.',
                ]);
            }

            $productIds = array_merge(
                $oldItems->pluck('product_id')->all(),
                collect($items)->pluck('product_id')->all()
            );
            $products = $this->lockProducts($productIds);
            $oldItemsById = $oldItems->keyBy('id');

            foreach ($oldItems as $oldItem) {
                $product = $products->get((int) $oldItem->product_id);
                $product->quantity += (int) $oldItem->quantity;
                $product->save();
            }

            if ($deletedItemIds) {
                $lockedInvoice->items()->whereIn('id', $deletedItemIds)->delete();
            }

            $totalCents = 0;
            foreach ($items as $index => $item) {
                $productId = (int) $item['product_id'];
                $product = $products->get($productId);
                $quantity = (int) $item['quantity'];
                $this->assertStockAvailable($product, $quantity, $index);

                $existingItem = isset($item['id']) ? $oldItemsById->get((int) $item['id']) : null;
                $unitPriceCents = $existingItem && (int) $existingItem->product_id === $productId
                    ? $this->moneyToCents((string) $existingItem->unit_price)
                    : $this->moneyToCents((string) $product->getRawOriginal('price'));
                $lineTotalCents = $this->lineTotalCents($unitPriceCents, $quantity, $index);
                $totalCents = $this->addMoneyCents($totalCents, $lineTotalCents, $index);

                $itemAttributes = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $this->moneyFromCents($unitPriceCents),
                    'total' => $this->moneyFromCents($lineTotalCents),
                ];

                if ($existingItem) {
                    $existingItem->update($itemAttributes);
                } else {
                    $lockedInvoice->items()->create($itemAttributes);
                }

                $product->quantity -= $quantity;
                $product->save();
            }

            $lockedInvoice->update([
                'client_id' => $client->id,
                'date' => $validated['date'],
                'status' => $validated['status'],
                'total' => $this->moneyFromCents($totalCents),
            ]);
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        DB::transaction(function () use ($invoice): void {
            $lockedInvoice = Invoice::query()
                ->whereKey($invoice->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $items = $lockedInvoice->items()->lockForUpdate()->get();
            $products = $this->lockProducts($items->pluck('product_id')->all());

            foreach ($items as $item) {
                $product = $products->get((int) $item->product_id);
                $product->quantity += (int) $item->quantity;
                $product->save();
            }

            $lockedInvoice->delete();
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted and stock restored.');
    }

    private function rules(?Invoice $invoice = null): array
    {
        $workspaceId = current_workspace_id();
        abort_unless($workspaceId, 403);

        $rules = [
            'client_id' => [
                'required',
                'integer',
                Rule::exists('clients', 'id')->where('workspace_id', $workspaceId),
            ],
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'items' => $invoice ? ['sometimes', 'array'] : ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('products', 'id')->where('workspace_id', $workspaceId),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.id' => $invoice
                ? ['sometimes', 'integer', 'distinct', Rule::exists('invoice_items', 'id')->where('invoice_id', $invoice->id)]
                : ['prohibited'],
            'deleted_item_ids' => $invoice ? ['sometimes', 'array'] : ['prohibited'],
            'deleted_item_ids.*' => $invoice
                ? ['required', 'integer', 'distinct', Rule::exists('invoice_items', 'id')->where('invoice_id', $invoice->id)]
                : ['prohibited'],
        ];

        return $rules;
    }

    private function lockProducts(array $productIds)
    {
        $ids = collect($productIds)->map(fn ($id) => (int) $id)->unique()->sort()->values();
        if ($ids->isEmpty()) {
            return collect();
        }

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

    private function assertStockAvailable(Product $product, int $quantity, int $index): void
    {
        if ($quantity > $product->quantity) {
            throw ValidationException::withMessages([
                "items.{$index}.quantity" => "Insufficient stock for {$product->name}. Available: {$product->quantity}.",
            ]);
        }
    }

    private function moneyToCents(string $amount): int
    {
        if (!preg_match('/^(\d{1,8})(?:\.(\d{1,2}))?$/', $amount, $matches)) {
            throw ValidationException::withMessages([
                'items' => 'A selected product has an invalid unit price.',
            ]);
        }

        return ((int) $matches[1] * 100) + (int) str_pad($matches[2] ?? '', 2, '0');
    }

    private function lineTotalCents(int $unitPriceCents, int $quantity, int $index): int
    {
        if ($unitPriceCents > 0 && $quantity > intdiv(self::MAX_MONEY_CENTS, $unitPriceCents)) {
            throw ValidationException::withMessages([
                "items.{$index}.quantity" => 'The line total exceeds the maximum supported invoice amount.',
            ]);
        }

        return $unitPriceCents * $quantity;
    }

    private function addMoneyCents(int $totalCents, int $lineTotalCents, int $index): int
    {
        if ($lineTotalCents > self::MAX_MONEY_CENTS - $totalCents) {
            throw ValidationException::withMessages([
                "items.{$index}.quantity" => 'The invoice total exceeds the maximum supported amount.',
            ]);
        }

        return $totalCents + $lineTotalCents;
    }

    private function moneyFromCents(int $amount): string
    {
        return intdiv($amount, 100) . '.' . str_pad((string) ($amount % 100), 2, '0', STR_PAD_LEFT);
    }
}