<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Product;
use App\Models\Invoice;

class DashboardController extends Controller
{
     public function index()
    {
        // الإحصائيات
        $clientsCount  = Client::count();
        $productsCount = Product::count();
        $invoicesCount = Invoice::count();

        // الدخل الشهري
        $monthlyIncome = Invoice::selectRaw('MONTH(date) as month, SUM(total) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard', compact(
            'clientsCount',
            'productsCount',
            'invoicesCount',
            'monthlyIncome'
        ));
    }
}
