<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceRole;
use App\Models\WorkspaceUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_workspace_owner_can_view_reports_and_unauthenticated_user_is_redirected(): void
    {
        [$user] = $this->createWorkspaceUser('reports-access@example.com');

        $this->get(route('reports.index'))->assertRedirect(route('login'));
        $this->actingAs($user)->get(route('reports.index'))
            ->assertOk()
            ->assertSee('Reports')
            ->assertSee('Overview of your business activity and performance')
            ->assertSee('This Month')
            ->assertSee('No sales data available for this period.')
            ->assertSee('No expenses recorded for this period.');
    }

    public function test_user_without_reports_permission_is_forbidden(): void
    {
        [, $workspace] = $this->createWorkspaceUser('reports-owner@example.com');
        [$staff] = $this->addWorkspaceUser($workspace, 'reports-staff@example.com', 'staff');
        WorkspaceRole::query()->create([
            'workspace_id' => $workspace->id,
            'name' => 'Staff',
            'slug' => 'staff',
            'permissions' => ['dashboard.view'],
            'is_system' => true,
        ]);

        $this->actingAs($staff)->get(route('reports.index'))->assertForbidden();
    }

    public function test_workspace_member_with_reports_permission_can_view_reports(): void
    {
        [, $workspace] = $this->createWorkspaceUser('reports-permission-owner@example.com');
        [$member] = $this->addWorkspaceUser($workspace, 'reports-permission-member@example.com', 'staff');
        WorkspaceRole::query()->create([
            'workspace_id' => $workspace->id,
            'name' => 'Staff',
            'slug' => 'staff',
            'permissions' => ['reports.view'],
            'is_system' => true,
        ]);

        $this->actingAs($member)->get(route('reports.index'))
            ->assertOk()
            ->assertSee('Reports');
    }

    public function test_kpis_statuses_and_recent_lists_are_workspace_scoped(): void
    {
        $this->travelTo(Carbon::parse('2026-09-15 12:00:00'));
        [$user, $workspace] = $this->createWorkspaceUser('reports-tenant-a@example.com');
        [, $otherWorkspace] = $this->createWorkspaceUser('reports-tenant-b@example.com');
        $client = $this->createClient($workspace->id, 'Current Client');
        $otherClient = $this->createClient($otherWorkspace->id, 'Foreign Client');

        $ownProduct = $this->createProduct($workspace->id, 'Current Product', '75.00', 1);
        $otherProduct = $this->createProduct($otherWorkspace->id, 'Foreign Product', '90.00', 9);
        $ownInvoice = $this->createInvoice($workspace->id, $client->id, 'INV-CURRENT', '2026-09-10', '120.00', 'sent');
        $otherInvoice = $this->createInvoice($otherWorkspace->id, $otherClient->id, 'INV-FOREIGN', '2026-09-10', '900.00', 'paid');
        $this->createInvoiceItem($ownInvoice->id, $ownProduct->id, 4, '13.00', '52.00');
        $this->createInvoiceItem($otherInvoice->id, $otherProduct->id, 80, '11.25', '900.00');
        $this->createOrder($workspace->id, $client->id, 'ORD-PENDING', '2026-09-11', 'pending', '18.00');
        $this->createOrder($workspace->id, $client->id, 'ORD-PROCESSING', '2026-09-12', 'processing', '19.00');
        $this->createOrder($workspace->id, $client->id, 'ORD-COMPLETE', '2026-09-12', 'completed', '22.00');
        $this->createOrder($workspace->id, $client->id, 'ORD-CANCELLED', '2026-09-13', 'cancelled', '20.00');
        $this->createOrder($otherWorkspace->id, $otherClient->id, 'ORD-FOREIGN', '2026-09-12', 'cancelled', '99.00');
        $this->createExpense($workspace->id, 'Current rent', 'Rent', '2026-09-13', '30.00');
        $this->createExpense($otherWorkspace->id, 'Foreign expense', 'Other', '2026-09-13', '300.00');

        $response = $this->actingAs($user)->get(route('reports.index', ['period' => 'month']));

        $response->assertOk()
            ->assertViewHas('salesTotal', 120.0)
            ->assertViewHas('invoiceCount', 1)
            ->assertViewHas('ordersCount', 4)
            ->assertViewHas('ordersByStatus', fn (array $statuses) => $statuses['pending'] === 1 && $statuses['processing'] === 1 && $statuses['completed'] === 1 && $statuses['cancelled'] === 1)
            ->assertViewHas('expensesTotal', 30.0)
            ->assertViewHas('expensesByCategory', fn ($categories) => $categories->count() === 1 && $categories->first()->category === 'Rent' && (float) $categories->first()->expense_total === 30.0)
            ->assertSee('INV-CURRENT')
            ->assertSee('Current Client')
            ->assertSee('Current Product')
            ->assertSee('Current rent')
            ->assertDontSee('INV-FOREIGN')
            ->assertDontSee('Foreign Client')
            ->assertDontSee('Foreign Product')
            ->assertDontSee('Foreign expense');

        $this->get(route('reports.index', ['workspace_id' => $otherWorkspace->id]))
            ->assertSessionHasErrors('workspace_id');
    }

    public function test_custom_date_range_filters_all_report_data_and_rejects_invalid_range(): void
    {
        $this->travelTo(Carbon::parse('2026-09-15 12:00:00'));
        [$user, $workspace] = $this->createWorkspaceUser('reports-custom@example.com');
        $client = $this->createClient($workspace->id, 'Date client');
        $this->createInvoice($workspace->id, $client->id, 'INV-IN-RANGE', '2026-09-05', '40.00');
        $this->createInvoice($workspace->id, $client->id, 'INV-OUT-RANGE', '2026-09-25', '80.00');
        $this->createOrder($workspace->id, $client->id, 'ORD-IN-RANGE', '2026-09-08', 'processing', '10.00');
        $this->createOrder($workspace->id, $client->id, 'ORD-OUT-RANGE', '2026-09-26', 'pending', '20.00');
        $this->createExpense($workspace->id, 'Included expense', 'Supplies', '2026-09-09', '5.00');
        $this->createExpense($workspace->id, 'Excluded expense', 'Rent', '2026-09-27', '15.00');
        $this->actingAs($user);

        $this->get(route('reports.index', [
            'period' => 'custom',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-15',
        ]))
            ->assertOk()
            ->assertViewHas('salesTotal', 40.0)
            ->assertViewHas('invoiceCount', 1)
            ->assertViewHas('ordersCount', 1)
            ->assertViewHas('expensesTotal', 5.0)
            ->assertSee('INV-IN-RANGE')
            ->assertDontSee('INV-OUT-RANGE')
            ->assertSee('Included expense')
            ->assertDontSee('Excluded expense');

        $this->get(route('reports.index', [
            'period' => 'custom',
            'start_date' => '2026-09-20',
            'end_date' => '2026-09-10',
        ]))->assertSessionHasErrors('end_date');
    }

    public function test_standard_period_excludes_records_outside_selected_month(): void
    {
        $this->travelTo(Carbon::parse('2026-09-15 12:00:00'));
        [$user, $workspace] = $this->createWorkspaceUser('reports-month@example.com');
        $client = $this->createClient($workspace->id, 'Month client');
        $this->createInvoice($workspace->id, $client->id, 'INV-SEP', '2026-09-02', '25.00');
        $this->createInvoice($workspace->id, $client->id, 'INV-AUG', '2026-08-31', '100.00');

        $this->actingAs($user)->get(route('reports.index', ['period' => 'month']))
            ->assertOk()
            ->assertViewHas('salesTotal', 25.0)
            ->assertViewHas('invoiceCount', 1)
            ->assertSee('INV-SEP')
            ->assertDontSee('INV-AUG');
    }

    public function test_each_period_filters_all_report_data_and_custom_end_date_is_inclusive(): void
    {
        $this->travelTo(Carbon::parse('2026-09-15 12:00:00'));
        [$user, $workspace] = $this->createWorkspaceUser('reports-periods@example.com');
        $client = $this->createClient($workspace->id, 'Period client');
        $product = $this->createProduct($workspace->id, 'Period product', '10.00', 1);
        $records = [
            ['today', '2026-09-15', '10.00', 'pending', 1],
            ['week', '2026-09-14', '20.00', 'processing', 2],
            ['month', '2026-09-01', '30.00', 'completed', 3],
            ['year', '2026-01-01', '40.00', 'cancelled', 4],
            ['outside', '2025-12-31', '50.00', 'pending', 5],
        ];

        foreach ($records as [$key, $date, $total, $status, $quantity]) {
            $invoice = $this->createInvoice($workspace->id, $client->id, 'INV-' . strtoupper($key), $date, $total);
            $this->createInvoiceItem($invoice->id, $product->id, $quantity, '10.00', $total);
            $this->createOrder($workspace->id, $client->id, 'ORD-' . strtoupper($key), $date, $status, $total);
            $this->createExpense($workspace->id, 'Expense ' . $key, 'Supplies', $date, $quantity . '.00');
        }

        $this->actingAs($user);

        $expected = [
            'today' => [10.0, 1, 1, 1.0, 1],
            'week' => [30.0, 2, 3, 3.0, 2],
            'month' => [60.0, 3, 6, 6.0, 3],
            'year' => [100.0, 4, 10, 10.0, 2],
        ];

        foreach ($expected as $period => [$sales, $orders, $expenses, $quantity, $chartCount]) {
            $this->get(route('reports.index', ['period' => $period]))
                ->assertOk()
                ->assertViewHas('salesTotal', $sales)
                ->assertViewHas('invoiceCount', $orders)
                ->assertViewHas('ordersCount', $orders)
                ->assertViewHas('expensesTotal', $expenses)
                ->assertViewHas('topProducts', fn ($products) => (int) $products->first()->quantity_sold === (int) $quantity)
                ->assertViewHas('salesChart', fn ($chart) => $chart->count() === $chartCount)
                ->assertDontSee('INV-OUTSIDE')
                ->assertDontSee('Expense outside');
        }

        $this->get(route('reports.index', [
            'period' => 'custom',
            'start_date' => '2026-09-14',
            'end_date' => '2026-09-15',
        ]))
            ->assertOk()
            ->assertViewHas('salesTotal', 30.0)
            ->assertViewHas('invoiceCount', 2)
            ->assertViewHas('ordersCount', 2)
            ->assertViewHas('expensesTotal', 3.0)
            ->assertViewHas('topProducts', fn ($products) => (int) $products->first()->quantity_sold === 3)
            ->assertViewHas('salesChart', fn ($chart) => $chart->count() === 2)
            ->assertSee('INV-TODAY')
            ->assertSee('Expense today')
            ->assertDontSee('INV-MONTH')
            ->assertDontSee('Expense month');
    }

    public function test_today_sales_are_grouped_by_available_invoice_date(): void
    {
        $today = now();
        $todayDate = $today->toDateString();
        $yesterdayDate = $today->copy()->subDay()->toDateString();
        [$user, $workspace] = $this->createWorkspaceUser('reports-today@example.com');
        $client = $this->createClient($workspace->id, 'Today client');
        $this->createInvoice($workspace->id, $client->id, 'INV-TODAY-A', $todayDate, '12.00');
        $this->createInvoice($workspace->id, $client->id, 'INV-TODAY-B', $todayDate, '18.00');
        $this->createInvoice($workspace->id, $client->id, 'INV-YESTERDAY', $yesterdayDate, '99.00');
        $this->assertDatabaseHas('invoices', ['invoice_number' => 'INV-TODAY-A']);

        $response = $this->actingAs($user)->get(route('reports.index', ['period' => 'today']));
        $response->assertOk()
            ->assertViewHas('startDate', fn ($startDate) => $startDate->toDateString() === $todayDate)
            ->assertViewHas('salesTotal', 30.0)
            ->assertViewHas('invoiceCount', 2)
            ->assertViewHas('salesChart', fn ($chart) => $chart->count() === 1 && $chart->first()['label'] === $today->format('M j') && $chart->first()['total'] === 30.0)
            ->assertSee('INV-TODAY-A')
            ->assertDontSee('INV-YESTERDAY');
    }

    public function test_top_products_use_invoice_item_quantity_and_historical_item_totals(): void
    {
        $this->travelTo(Carbon::parse('2026-09-15 12:00:00'));
        [$user, $workspace] = $this->createWorkspaceUser('reports-products@example.com');
        $client = $this->createClient($workspace->id, 'Product client');
        $product = $this->createProduct($workspace->id, 'Historical price item', '999.00', 1);
        $invoice = $this->createInvoice($workspace->id, $client->id, 'INV-HISTORICAL', '2026-09-10', '50.00');
        $this->createInvoiceItem($invoice->id, $product->id, 5, '10.00', '50.00');

        $this->actingAs($user)->get(route('reports.index', ['period' => 'month']))
            ->assertOk()
            ->assertSee('Historical price item')
            ->assertSee('USD 50.00')
            ->assertViewHas('topProducts', fn ($products) => $products->first()->quantity_sold == 5 && (float) $products->first()->sales_total === 50.0);
    }

    private function createWorkspaceUser(string $email): array
    {
        $user = User::factory()->create(['email' => $email, 'email_verified_at' => now()]);
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

    private function addWorkspaceUser(Workspace $workspace, string $email, string $role): array
    {
        $user = User::factory()->create(['email' => $email, 'email_verified_at' => now()]);
        $membership = WorkspaceUser::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => $role,
            'accepted_at' => now(),
        ]);
        $user->update(['current_workspace_id' => $workspace->id]);

        return [$user->fresh(), $membership];
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

    private function createInvoice(int $workspaceId, int $clientId, string $number, string $date, string $total, string $status = 'sent'): Invoice
    {
        return Invoice::withoutGlobalScopes()->create([
            'workspace_id' => $workspaceId,
            'client_id' => $clientId,
            'invoice_number' => $number,
            'date' => $date,
            'status' => $status,
            'total' => $total,
        ]);
    }

    private function createInvoiceItem(int $invoiceId, int $productId, int $quantity, string $unitPrice, string $total): void
    {
        DB::table('invoice_items')->insert([
            'invoice_id' => $invoiceId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $total,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createOrder(int $workspaceId, int $clientId, string $number, string $date, string $status, string $total): void
    {
        Order::withoutGlobalScopes()->create([
            'workspace_id' => $workspaceId,
            'client_id' => $clientId,
            'order_number' => $number,
            'order_date' => $date,
            'status' => $status,
            'total' => $total,
        ]);
    }

    private function createExpense(int $workspaceId, string $description, string $category, string $date, string $amount): void
    {
        DB::table('expenses')->insert([
            'workspace_id' => $workspaceId,
            'description' => $description,
            'category' => $category,
            'expense_date' => $date,
            'amount' => $amount,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}