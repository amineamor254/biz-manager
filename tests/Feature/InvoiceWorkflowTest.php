<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InvoiceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_creation_calculates_totals_snapshots_prices_and_decrements_stock(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('sales@example.com');
        $client = $this->createClient($workspace->id, 'Mohamed');
        $keyboard = $this->createProduct($workspace->id, 'Keyboard', '100.00', 10);
        $mouse = $this->createProduct($workspace->id, 'Mouse', '50.00', 8);

        $response = $this->actingAs($user)->post(route('invoices.store'), [
            'client_id' => $client->id,
            'date' => '2026-09-23',
            'status' => 'paid',
            'total' => '0.01',
            'items' => [
                ['product_id' => $keyboard->id, 'quantity' => 2, 'unit_price' => '0.01', 'total' => '0.02'],
                ['product_id' => $mouse->id, 'quantity' => 3, 'unit_price' => '0.01', 'total' => '0.03'],
            ],
        ]);

        $response->assertRedirect(route('invoices.index'));
        $invoice = Invoice::with('items')->firstOrFail();
        $this->assertSame('350.00', $invoice->total);
        $this->assertSame(2, $invoice->items->count());
        $this->assertSame('100.00', $invoice->items->firstWhere('product_id', $keyboard->id)->unit_price);
        $this->assertSame('200.00', $invoice->items->firstWhere('product_id', $keyboard->id)->total);
        $this->assertSame(8, $keyboard->fresh()->quantity);
        $this->assertSame(5, $mouse->fresh()->quantity);

        $dashboard = $this->get(route('dashboard'));
        $dashboard->assertOk();
        $dashboard->assertViewHas('totalRevenue', 350);
        $dashboard->assertViewHas('invoicesCount', 1);
    }

    public function test_invoice_rejects_insufficient_stock_zero_quantities_and_duplicate_products(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('stock@example.com');
        $client = $this->createClient($workspace->id, 'Client');
        $product = $this->createProduct($workspace->id, 'Keyboard', '100.00', 5);
        $this->actingAs($user);

        $this->post(route('invoices.store'), $this->invoicePayload($client->id, [
            ['product_id' => $product->id, 'quantity' => 7],
        ]))->assertSessionHasErrors('items.0.quantity');

        $this->post(route('invoices.store'), $this->invoicePayload($client->id, [
            ['product_id' => $product->id, 'quantity' => 0],
        ]))->assertSessionHasErrors('items.0.quantity');

        $this->post(route('invoices.store'), $this->invoicePayload($client->id, [
            ['product_id' => 999999, 'quantity' => 1],
        ]))->assertSessionHasErrors('items.0.product_id');

        $this->post(route('invoices.store'), $this->invoicePayload($client->id, [
            ['product_id' => $product->id, 'quantity' => 2],
            ['product_id' => $product->id, 'quantity' => 1],
        ]))->assertSessionHasErrors('items.1.product_id');

        $this->assertDatabaseCount('invoices', 0);
        $this->assertSame(5, $product->fresh()->quantity);
    }

    public function test_invoice_rejects_clients_products_and_invoices_from_another_workspace(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('tenant-a@example.com');
        [, $otherWorkspace] = $this->createWorkspaceUser('tenant-b@example.com');
        $client = $this->createClient($workspace->id, 'Own client');
        $otherClient = $this->createClient($otherWorkspace->id, 'Other client');
        $product = $this->createProduct($workspace->id, 'Own product', '20.00', 5);
        $otherProduct = $this->createProduct($otherWorkspace->id, 'Other product', '30.00', 5);
        $this->actingAs($user);

        $this->post(route('invoices.store'), $this->invoicePayload($otherClient->id, [
            ['product_id' => $product->id, 'quantity' => 1],
        ]))->assertSessionHasErrors('client_id');

        $this->post(route('invoices.store'), $this->invoicePayload($client->id, [
            ['product_id' => $otherProduct->id, 'quantity' => 1],
        ]))->assertSessionHasErrors('items.0.product_id');

        $otherInvoiceId = DB::table('invoices')->insertGetId([
            'workspace_id' => $otherWorkspace->id,
            'client_id' => $otherClient->id,
            'total' => '30.00',
            'date' => '2026-09-23',
            'status' => 'draft',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get(route('invoices.show', $otherInvoiceId))->assertNotFound();
        $this->get(route('invoices.edit', $otherInvoiceId))->assertNotFound();
        $this->assertDatabaseCount('invoices', 1);
        $this->assertSame(5, $product->fresh()->quantity);
        $this->assertSame(5, $otherProduct->fresh()->quantity);
    }

    public function test_edit_preserves_price_snapshot_reconciles_stock_and_delete_restores_stock_once(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('edit-sale@example.com');
        $client = $this->createClient($workspace->id, 'Client');
        $product = $this->createProduct($workspace->id, 'Keyboard', '100.00', 10);
        $removedProduct = $this->createProduct($workspace->id, 'Mouse', '50.00', 8);
        $this->actingAs($user);

        $this->post(route('invoices.store'), $this->invoicePayload($client->id, [
            ['product_id' => $product->id, 'quantity' => 2],
            ['product_id' => $removedProduct->id, 'quantity' => 1],
        ]))->assertRedirect(route('invoices.index'));
        $invoice = Invoice::with('items')->firstOrFail();
        $keyboardItem = $invoice->items->firstWhere('product_id', $product->id);
        $mouseItem = $invoice->items->firstWhere('product_id', $removedProduct->id);
        $product->update(['price' => '120.00']);

        $updatePayload = $this->invoicePayload($client->id, [
            ['id' => $keyboardItem->id, 'product_id' => $product->id, 'quantity' => 3],
        ]);
        $updatePayload['deleted_item_ids'] = [$mouseItem->id];

        $this->put(route('invoices.update', $invoice), $updatePayload)
            ->assertRedirect(route('invoices.show', $invoice));

        $invoice->refresh()->load('items');
        $this->assertSame('300.00', $invoice->total);
        $this->assertSame('100.00', $invoice->items->first()->unit_price);
        $this->assertSame($keyboardItem->id, $invoice->items->first()->id);
        $this->assertDatabaseMissing('invoice_items', ['id' => $mouseItem->id]);
        $this->assertSame(7, $product->fresh()->quantity);
        $this->assertSame(8, $removedProduct->fresh()->quantity);
        $this->get(route('invoices.show', $invoice))
            ->assertOk()
            ->assertSee('100.00 TND')
            ->assertSee('300.00 TND');

        $this->delete(route('invoices.destroy', $invoice))->assertRedirect(route('invoices.index'));
        $this->assertSame(10, $product->fresh()->quantity);
        $this->assertSame(8, $removedProduct->fresh()->quantity);
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);

        $this->delete(route('invoices.destroy', $invoice))->assertNotFound();
        $this->assertSame(10, $product->fresh()->quantity);
    }

    public function test_edit_form_delegates_remove_for_existing_and_unsaved_rows(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('remove-ui@example.com');
        $client = $this->createClient($workspace->id, 'Client');
        $product = $this->createProduct($workspace->id, 'Keyboard', '100.00', 5);
        $this->actingAs($user)->post(route('invoices.store'), $this->invoicePayload($client->id, [
            ['product_id' => $product->id, 'quantity' => 1],
        ]));

        $response = $this->actingAs($user)->get(route('invoices.edit', Invoice::firstOrFail()));

        $response->assertOk()
            ->assertSee('type="button" data-remove', false)
            ->assertSee("addButton.addEventListener('click', () => addRow());", false)
            ->assertSee("container.addEventListener('click'", false)
            ->assertSee("const itemId = row.querySelector('[data-item-id]').value;", false)
            ->assertSee('deletedItems.append(deletedItem);', false)
            ->assertSee('row.remove();', false);
    }

    public function test_edit_can_remove_the_final_existing_item_and_restore_its_stock(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('remove-final@example.com');
        $client = $this->createClient($workspace->id, 'Client');
        $product = $this->createProduct($workspace->id, 'Keyboard', '100.00', 10);
        $this->actingAs($user)->post(route('invoices.store'), $this->invoicePayload($client->id, [
            ['product_id' => $product->id, 'quantity' => 2],
        ]));
        $invoice = Invoice::with('items')->firstOrFail();
        $itemId = $invoice->items->first()->id;
        $this->assertSame(8, $product->fresh()->quantity);

        $updatePayload = $this->invoicePayload($client->id, []);
        $updatePayload['deleted_item_ids'] = [$itemId];
        $this->put(route('invoices.update', $invoice), $updatePayload)
            ->assertRedirect(route('invoices.show', $invoice));

        $this->assertSame('0.00', $invoice->fresh()->total);
        $this->assertDatabaseMissing('invoice_items', ['id' => $itemId]);
        $this->assertSame(10, $product->fresh()->quantity);
    }

    public function test_failure_after_an_earlier_line_rolls_back_invoice_items_and_stock(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('rollback@example.com');
        $client = $this->createClient($workspace->id, 'Client');
        $first = $this->createProduct($workspace->id, 'First', '10.00', 5);
        $tooExpensive = $this->createProduct($workspace->id, 'Expensive', '99999999.99', 5);

        $this->actingAs($user)->post(route('invoices.store'), $this->invoicePayload($client->id, [
            ['product_id' => $first->id, 'quantity' => 1],
            ['product_id' => $tooExpensive->id, 'quantity' => 2],
        ]))->assertSessionHasErrors('items.1.quantity');

        $this->assertDatabaseCount('invoices', 0);
        $this->assertDatabaseCount('invoice_items', 0);
        $this->assertSame(5, $first->fresh()->quantity);
        $this->assertSame(5, $tooExpensive->fresh()->quantity);
    }

    private function invoicePayload(int $clientId, array $items): array
    {
        return [
            'client_id' => $clientId,
            'date' => '2026-09-23',
            'status' => 'draft',
            'items' => $items,
        ];
    }

    private function createClient(int $workspaceId, string $name): Client
    {
        return Client::withoutGlobalScopes()->create([
            'workspace_id' => $workspaceId,
            'name' => $name,
        ]);
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
