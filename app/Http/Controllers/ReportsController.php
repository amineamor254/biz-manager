<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReportsController extends Controller
{
    private const PERIODS = ['today', 'week', 'month', 'year', 'custom'];

    public function index(Request $request)
    {
        $validated = $request->validate([
            'period' => ['sometimes', 'required', Rule::in(self::PERIODS)],
            'start_date' => ['required_if:period,custom', 'nullable', 'date'],
            'end_date' => ['required_if:period,custom', 'nullable', 'date', 'after_or_equal:start_date'],
            'workspace_id' => ['prohibited'],
        ]);

        $period = $validated['period'] ?? 'month';
        [$startDate, $endDate, $periodLabel] = $this->periodBounds($period, $validated);
        $workspaceId = current_workspace_id();
        abort_unless($workspaceId, 403);

        $invoiceTotals = $this->withinDateRange(Invoice::query(), 'date', $startDate, $endDate)
            ->selectRaw('COUNT(*) as invoice_count, COALESCE(SUM(total), 0) as sales_total')
            ->first();
        $salesTotal = (float) $invoiceTotals->sales_total;
        $invoiceCount = (int) $invoiceTotals->invoice_count;

        $ordersCount = $this->withinDateRange(Order::query(), 'order_date', $startDate, $endDate)->count();
        $ordersByStatus = array_fill_keys(['pending', 'processing', 'completed', 'cancelled'], 0);
        $statusCounts = $this->withinDateRange(Order::query(), 'order_date', $startDate, $endDate)
            ->select(['status', DB::raw('COUNT(*) as status_count')])
            ->groupBy('status')
            ->pluck('status_count', 'status');
        foreach ($statusCounts as $status => $count) {
            if (array_key_exists($status, $ordersByStatus)) {
                $ordersByStatus[$status] = (int) $count;
            }
        }

        $expensesTotal = $this->withinDateRange(Expense::query(), 'expense_date', $startDate, $endDate)->sum('amount');
        $expensesByCategory = $this->withinDateRange(Expense::query(), 'expense_date', $startDate, $endDate)
            ->select(['category', DB::raw('COUNT(*) as expense_count, SUM(amount) as expense_total')])
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        $driver = DB::connection()->getDriverName();
        $salesGroupExpression = $this->salesGroupExpression($period, $driver);
        $salesByDate = $this->withinDateRange(Invoice::query(), 'date', $startDate, $endDate)
            ->selectRaw("{$salesGroupExpression} as period_key, SUM(total) as sales_total")
            ->groupBy('period_key')
            ->orderBy('period_key')
            ->get();
        $salesChart = $salesByDate->map(fn ($row) => [
            'label' => $this->chartLabel((string) $row->period_key, $period),
            'total' => (float) $row->sales_total,
        ])->values();

        $topProducts = $this->withinDateRange(Invoice::query(), 'invoices.date', $startDate, $endDate)
            ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'products.id', '=', 'invoice_items.product_id')
            ->where('products.workspace_id', $workspaceId)
            ->select([
                'products.id as product_id',
                'products.name as product_name',
            ])
            ->selectRaw('SUM(invoice_items.quantity) as quantity_sold, SUM(invoice_items.total) as sales_total')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('quantity_sold')
            ->orderBy('products.name')
            ->limit(5)
            ->get();

        $recentInvoices = $this->withinDateRange(Invoice::query(), 'date', $startDate, $endDate)
            ->with('client')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $recentExpenses = $this->withinDateRange(Expense::query(), 'expense_date', $startDate, $endDate)
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $currency = current_workspace()?->currency ?? 'USD';

        return view('reports.index', compact(
            'period', 'periodLabel', 'startDate', 'endDate', 'salesTotal', 'invoiceCount',
            'ordersCount', 'ordersByStatus', 'expensesTotal', 'expensesByCategory',
            'salesChart', 'topProducts', 'recentInvoices', 'recentExpenses', 'currency',
        ));
    }

    private function periodBounds(string $period, array $validated): array
    {
        $today = now();

        return match ($period) {
            'today' => [$today->copy()->startOfDay(), $today->copy()->endOfDay(), 'Today'],
            'week' => [$today->copy()->startOfWeek(), $today->copy()->endOfWeek(), 'This Week'],
            'year' => [$today->copy()->startOfYear(), $today->copy()->endOfYear(), 'This Year'],
            'custom' => [
                Carbon::parse($validated['start_date'])->startOfDay(),
                Carbon::parse($validated['end_date'])->endOfDay(),
                Carbon::parse($validated['start_date'])->format('M d, Y') . ' – ' . Carbon::parse($validated['end_date'])->format('M d, Y'),
            ],
            default => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth(), 'This Month'],
        };
    }

    private function salesGroupExpression(string $period, string $driver): string
    {
        if ($period === 'today') {
            return 'DATE(date)';
        }

        if ($period === 'year') {
            return $driver === 'sqlite' ? "strftime('%m', date)" : "DATE_FORMAT(date, '%m')";
        }

        return 'DATE(date)';
    }

    private function withinDateRange(Builder $query, string $column, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query
            ->whereDate($column, '>=', $startDate->toDateString())
            ->whereDate($column, '<=', $endDate->toDateString());
    }

    private function chartLabel(string $key, string $period): string
    {
        if ($period === 'today') {
            return Carbon::parse($key)->format('M j');
        }

        if ($period === 'year') {
            return Carbon::createFromFormat('!m', $key)->format('M');
        }

        return Carbon::parse($key)->format('M j');
    }
}