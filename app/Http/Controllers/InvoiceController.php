<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Client;

class InvoiceController extends Controller
{
    // عرض كل الفواتير
    public function index()
    {
        $invoices = Invoice::with('client')->latest()->get();
        return view('invoices.index', compact('invoices'));
    }

    // نموذج إضافة فاتورة جديدة
    public function create()
    {
        $clients = Client::all();
        return view('invoices.create', compact('clients'));
    }

    // حفظ فاتورة جديدة
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'total'     => 'required|numeric',
            'date'      => 'nullable|date',
        ]);

        Invoice::create([
            'client_id' => $request->client_id,
            'total'     => $request->total,
            'date'      => $request->date ?? now(),
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully!');
    }

    // نموذج تعديل فاتورة
    public function edit(Invoice $invoice)
    {
        $clients = Client::all();
        return view('invoices.edit', compact('invoice', 'clients'));
    }

    // تحديث الفاتورة
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'total'     => 'required|numeric',
            'date'      => 'nullable|date',
        ]);

        $invoice->update([
            'client_id' => $request->client_id,
            'total'     => $request->total,
            'date'      => $request->date ?? now(),
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully!');
    }

    // حذف الفاتورة
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully!');
    }
}
