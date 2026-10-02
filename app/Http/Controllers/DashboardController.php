<?php
// SaaS MVP - add workspace support
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Basic counts
        $clientsCount  = Client::count();
        $productsCount = Product::count();
        $invoicesCount = Invoice::count();
        $ordersCount = Order::count();

        // Monthly income
        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "CAST(strftime('%m', date) AS INTEGER)"
            : 'MONTH(date)';

        $monthlyIncome = Invoice::selectRaw("{$monthExpression} as month, SUM(total) as total")
            ->whereYear('date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Total revenue (all time)
        $totalRevenue = Invoice::sum('total');

        // Total expenses in the current workspace
        $totalExpenses = Expense::sum('amount');

        // Average invoice value
        $averageInvoice = $invoicesCount > 0 ? $totalRevenue / $invoicesCount : 0;

        // Stock value (price * quantity for all products)
        $stockValue = Product::selectRaw('SUM(price * quantity) as value')
            ->first()
            ->value ?? 0;

        // Month growth (compare current month to previous month)
        $currentMonthRevenue = Invoice::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('total');
        
        $previousMonthRevenue = Invoice::whereMonth('date', now()->month - 1)
            ->whereYear('date', now()->year)
            ->sum('total');

        $monthGrowth = $previousMonthRevenue > 0 
            ? round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100)
            : 0;

        // Recent invoices (last 5)
        $recentInvoices = Invoice::with('client')
            ->latest('created_at')
            ->limit(5)
            ->get();

        $recentOrders = Order::with('client')
            ->latest('order_date')
            ->latest('id')
            ->limit(5)
            ->get();

        $recentExpenses = Expense::latest('expense_date')
            ->latest('id')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'clientsCount',
            'productsCount',
            'invoicesCount',
            'ordersCount',
            'totalExpenses',
            'monthlyIncome',
            'totalRevenue',
            'averageInvoice',
            'stockValue',
            'monthGrowth',
            'recentInvoices',
            'recentOrders',
            'recentExpenses'
        ));
    }
}
