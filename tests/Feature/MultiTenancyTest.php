<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MultiTenancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_only_access_records_in_the_current_workspace(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('owner@example.com');
        [, $otherWorkspace] = $this->createWorkspaceUser('other@example.com');
        $client = Client::withoutGlobalScopes()->create([
            'workspace_id' => $workspace->id,
            'name' => 'Own Client',
        ]);
        $otherClient = Client::withoutGlobalScopes()->create([
            'workspace_id' => $otherWorkspace->id,
            'name' => 'Other Client',
        ]);
        $product = Product::withoutGlobalScopes()->create([
            'workspace_id' => $workspace->id,
            'name' => 'Own Product',
            'price' => 10,
            'quantity' => 1,
        ]);
        $otherProduct = Product::withoutGlobalScopes()->create([
            'workspace_id' => $otherWorkspace->id,
            'name' => 'Other Product',
            'price' => 20,
            'quantity' => 1,
        ]);
        $invoice = Invoice::withoutGlobalScopes()->create([
            'workspace_id' => $workspace->id,
            'client_id' => $client->id,
            'total' => 10,
            'date' => Carbon::today(),
        ]);
        $otherInvoice = Invoice::withoutGlobalScopes()->create([
            'workspace_id' => $otherWorkspace->id,
            'client_id' => $otherClient->id,
            'total' => 20,
            'date' => Carbon::today(),
        ]);

        $this->actingAs($user);

        $this->get(route('clients.edit', $client))->assertOk();
        $this->get(route('products.edit', $product))->assertOk();
        $this->get(route('invoices.edit', $invoice))->assertOk();
        $this->get(route('clients.edit', $otherClient))->assertNotFound();
        $this->get(route('products.edit', $otherProduct))->assertNotFound();
        $this->get(route('invoices.edit', $otherInvoice))->assertNotFound();
    }

    public function test_tenant_payload_is_ignored_when_creating_records(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('owner@example.com');
        [, $otherWorkspace] = $this->createWorkspaceUser('other@example.com');

        $response = $this->actingAs($user)->post(route('clients.store'), [
            'name' => 'Tenant Client',
            'workspace_id' => $otherWorkspace->id,
        ]);

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseHas('clients', [
            'name' => 'Tenant Client',
            'workspace_id' => $workspace->id,
        ]);
        $this->assertDatabaseMissing('clients', [
            'name' => 'Tenant Client',
            'workspace_id' => $otherWorkspace->id,
        ]);
    }

    public function test_invoice_client_must_belong_to_the_current_workspace(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('owner@example.com');
        [, $otherWorkspace] = $this->createWorkspaceUser('other@example.com');
        $otherClient = Client::withoutGlobalScopes()->create([
            'workspace_id' => $otherWorkspace->id,
            'name' => 'Other Client',
        ]);

        $response = $this->actingAs($user)->post(route('invoices.store'), [
            'client_id' => $otherClient->id,
            'total' => 100,
            'date' => Carbon::today()->toDateString(),
        ]);

        $response->assertSessionHasErrors('client_id');
        $this->assertDatabaseMissing('invoices', [
            'workspace_id' => $workspace->id,
            'client_id' => $otherClient->id,
        ]);
    }

    public function test_dashboard_statistics_are_limited_to_the_current_workspace(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('owner@example.com');
        [, $otherWorkspace] = $this->createWorkspaceUser('other@example.com');
        $client = Client::withoutGlobalScopes()->create([
            'workspace_id' => $workspace->id,
            'name' => 'Own Client',
        ]);
        $otherClient = Client::withoutGlobalScopes()->create([
            'workspace_id' => $otherWorkspace->id,
            'name' => 'Other Client',
        ]);
        Invoice::withoutGlobalScopes()->create([
            'workspace_id' => $workspace->id,
            'client_id' => $client->id,
            'total' => 10,
            'date' => Carbon::today(),
        ]);
        Invoice::withoutGlobalScopes()->create([
            'workspace_id' => $otherWorkspace->id,
            'client_id' => $otherClient->id,
            'total' => 90,
            'date' => Carbon::today(),
        ]);
        Order::withoutGlobalScopes()->create([
            'workspace_id' => $workspace->id,
            'client_id' => $client->id,
            'order_number' => 'ORD-OWN',
            'order_date' => Carbon::today(),
            'status' => 'pending',
            'total' => 15,
        ]);
        Order::withoutGlobalScopes()->create([
            'workspace_id' => $otherWorkspace->id,
            'client_id' => $otherClient->id,
            'order_number' => 'ORD-OTHER',
            'order_date' => Carbon::today(),
            'status' => 'pending',
            'total' => 95,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('clientsCount', 1);
        $response->assertViewHas('invoicesCount', 1);
        $response->assertViewHas('totalRevenue', 10);
        $response->assertViewHas('ordersCount', 1);
        $response->assertViewHas('recentOrders', fn ($orders) => $orders->count() === 1 && $orders->first()->order_number === 'ORD-OWN');
        $response->assertSee('ORD-OWN')->assertDontSee('ORD-OTHER');
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
