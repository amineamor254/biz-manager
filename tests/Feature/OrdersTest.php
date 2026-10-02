<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_orders_index(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('orders-index@example.com');
        $client = $this->createClient($workspace->id, 'Index client');
        $this->createOrder($workspace->id, $client->id, 'ORD-000001');

        $this->actingAs($user)->get(route('orders.index'))
            ->assertOk()
            ->assertSee('ORD-000001')
            ->assertSee('Index client')
            ->assertSee('Pending');
        $this->get(route('orders.create'))->assertOk()->assertSee('Create order');
    }

    public function test_order_creation_calculates_database_prices_and_totals_without_changing_stock(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('orders-create@example.com');
        $client = $this->createClient($workspace->id, 'Order client');
        $keyboard = $this->createProduct($workspace->id, 'Keyboard', '100.00', 10);
        $mouse = $this->createProduct($workspace->id, 'Mouse', '50.25', 8);

        $this->actingAs($user)->post(route('orders.store'), [
            'client_id' => $client->id,
            'order_date' => '2026-09-23',
            'total' => '0.01',
            'items' => [
                ['product_id' => $keyboard->id, 'quantity' => 2],
                ['product_id' => $mouse->id, 'quantity' => 3],
            ],
        ])->assertRedirect();

        $order = Order::with('items')->firstOrFail();
        $this->assertSame('ORD-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT), $order->order_number);
        $this->assertSame($workspace->id, $order->workspace_id);
        $this->assertSame('350.75', $order->total);
        $this->assertSame(2, $order->items->count());
        $this->assertSame('100.00', $order->items->firstWhere('product_id', $keyboard->id)->unit_price);
        $this->assertSame('200.00', $order->items->firstWhere('product_id', $keyboard->id)->total);
        $this->assertSame('50.25', $order->items->firstWhere('product_id', $mouse->id)->unit_price);
        $this->assertSame(10, $keyboard->fresh()->quantity);
        $this->assertSame(8, $mouse->fresh()->quantity);
    }

    public function test_submitted_item_prices_and_totals_are_rejected(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('orders-price@example.com');
        $client = $this->createClient($workspace->id, 'Client');
        $product = $this->createProduct($workspace->id, 'Product', '25.00', 4);

        $this->actingAs($user)->post(route('orders.store'), [
            'client_id' => $client->id,
            'order_date' => '2026-09-23',
            'workspace_id' => 999999,
            'items' => [['product_id' => $product->id, 'quantity' => 2, 'unit_price' => '0.01', 'total' => '0.02']],
        ])->assertSessionHasErrors(['workspace_id', 'items.0.unit_price', 'items.0.total']);

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(4, $product->fresh()->quantity);
    }

    public function test_order_edit_status_transitions_and_delete_do_not_change_stock(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('orders-edit@example.com');
        $client = $this->createClient($workspace->id, 'Client');
        $product = $this->createProduct($workspace->id, 'Product', '20.00', 6);
        $order = $this->createOrder($workspace->id, $client->id, 'ORD-000021');
        $order->items()->create(['product_id' => $product->id, 'quantity' => 1, 'unit_price' => '20.00', 'total' => '20.00']);
        $this->actingAs($user);

        $this->get(route('orders.edit', $order))->assertOk()->assertSee('Edit order');
        $this->put(route('orders.update', $order), [
            'client_id' => $client->id,
            'order_date' => '2026-09-24',
            'status' => 'processing',
            'total' => '0.01',
            'notes' => 'Updated order',
            'items' => [['product_id' => $product->id, 'quantity' => 3]],
        ])->assertRedirect(route('orders.show', $order));

        $order->refresh()->load('items');
        $this->assertSame('processing', $order->status);
        $this->assertSame('60.00', $order->total);
        $this->assertSame('Updated order', $order->notes);
        $this->assertSame(6, $product->fresh()->quantity);

        $this->put(route('orders.update', $order), [
            'client_id' => $client->id,
            'order_date' => '2026-09-24',
            'status' => 'completed',
            'items' => [['product_id' => $product->id, 'quantity' => 3]],
        ])->assertRedirect(route('orders.show', $order));
        $this->assertSame('completed', $order->fresh()->status);

        $this->put(route('orders.update', $order), [
            'client_id' => $client->id,
            'order_date' => '2026-09-24',
            'status' => 'processing',
            'items' => [['product_id' => $product->id, 'quantity' => 3]],
        ])->assertSessionHasErrors('status');

        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('ORD-000021')
            ->assertSee('Completed')
            ->assertSee('60.00 TND');
        $this->delete(route('orders.destroy', $order))->assertRedirect(route('orders.index'));
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertSame(6, $product->fresh()->quantity);
    }

    public function test_pending_and_processing_orders_can_be_cancelled(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('orders-cancel@example.com');
        $client = $this->createClient($workspace->id, 'Client');
        $product = $this->createProduct($workspace->id, 'Product', '20.00', 6);
        $pendingOrder = $this->createOrder($workspace->id, $client->id, 'ORD-PENDING');
        $processingOrder = $this->createOrder($workspace->id, $client->id, 'ORD-PROCESSING');
        $processingOrder->update(['status' => 'processing']);
        $this->actingAs($user);

        foreach ([$pendingOrder, $processingOrder] as $order) {
            $this->put(route('orders.update', $order), [
                'client_id' => $client->id,
                'order_date' => '2026-09-23',
                'status' => 'cancelled',
                'items' => [['product_id' => $product->id, 'quantity' => 1]],
            ])->assertRedirect(route('orders.show', $order));
            $this->assertSame('cancelled', $order->fresh()->status);
        }

        $this->put(route('orders.update', $pendingOrder), [
            'client_id' => $client->id,
            'order_date' => '2026-09-23',
            'status' => 'processing',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertSessionHasErrors('status');
        $this->assertSame(6, $product->fresh()->quantity);
    }

    public function test_orders_reject_cross_workspace_clients_products_and_order_access(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('orders-tenant-a@example.com');
        [, $otherWorkspace] = $this->createWorkspaceUser('orders-tenant-b@example.com');
        $ownClient = $this->createClient($workspace->id, 'Own client');
        $otherClient = $this->createClient($otherWorkspace->id, 'Other client');
        $ownProduct = $this->createProduct($workspace->id, 'Own product', '10.00', 5);
        $otherProduct = $this->createProduct($otherWorkspace->id, 'Other product', '15.00', 5);
        $otherOrder = $this->createOrder($otherWorkspace->id, $otherClient->id, 'ORD-OTHER');
        $this->actingAs($user);

        $this->post(route('orders.store'), $this->payload($otherClient->id, $ownProduct->id))
            ->assertSessionHasErrors('client_id');
        $this->post(route('orders.store'), $this->payload($ownClient->id, $otherProduct->id))
            ->assertSessionHasErrors('items.0.product_id');
        $this->get(route('orders.show', $otherOrder))->assertNotFound();
        $this->get(route('orders.edit', $otherOrder))->assertNotFound();
        $this->put(route('orders.update', $otherOrder), $this->payload($ownClient->id, $ownProduct->id, 'processing'))
            ->assertNotFound();
        $this->delete(route('orders.destroy', $otherOrder))->assertNotFound();
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_unauthenticated_users_cannot_access_orders(): void
    {
        $this->get(route('orders.index'))->assertRedirect(route('login'));
        $this->get(route('orders.create'))->assertRedirect(route('login'));
    }

    private function payload(int $clientId, int $productId, ?string $status = null): array
    {
        return [
            'client_id' => $clientId,
            'order_date' => '2026-09-23',
            ...($status ? ['status' => $status] : []),
            'items' => [['product_id' => $productId, 'quantity' => 1]],
        ];
    }

    private function createClient(int $workspaceId, string $name): Client
    {
        return Client::withoutGlobalScopes()->create(['workspace_id' => $workspaceId, 'name' => $name]);
    }

    private function createProduct(int $workspaceId, string $name, string $price, int $quantity): Product
    {
        return Product::withoutGlobalScopes()->create([
            'workspace_id' => $workspaceId,
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
        ]);
    }

    private function createOrder(int $workspaceId, int $clientId, string $number): Order
    {
        return Order::withoutGlobalScopes()->create([
            'workspace_id' => $workspaceId,
            'client_id' => $clientId,
            'order_number' => $number,
            'order_date' => '2026-09-23',
            'status' => 'pending',
            'total' => '0.00',
        ]);
    }

    private function createWorkspaceUser(string $email): array
    {
        $user = User::factory()->create([
            'email' => $email,
            'email_verified_at' => now(),
        ]);
        $workspace = Workspace::create([
            'user_id' => $user->id,
            'name' => $email . ' Business',
            'slug' => str_replace(['@', '.'], '-', $email) . '-' . $user->id,
        ]);
        WorkspaceUser::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'accepted_at' => now(),
        ]);
        $user->update(['current_workspace_id' => $workspace->id]);

        return [$user->fresh(), $workspace];
    }
}