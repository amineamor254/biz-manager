@extends('layouts.app')
@section('content')

<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ route('invoices.index') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">← Invoices</a>
            <div class="mt-3 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $invoice->invoice_number ?: 'INV-' . str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</h1>
                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-200">{{ ucfirst($invoice->status) }}</span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('invoices.edit', $invoice) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">Edit invoice</a>
            <a href="{{ route('invoices.create') }}" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">New invoice</a>
        </div>
    </div>

    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-7">
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Bill to</p>
                <p class="mt-2 text-base font-bold text-slate-900 dark:text-white">{{ $invoice->client?->name ?? 'Client unavailable' }}</p>
                @if($invoice->client?->email)<p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $invoice->client->email }}</p>@endif
                @if($invoice->client?->billing_address)<p class="mt-1 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $invoice->client->billing_address }}</p>@endif
            </div>
            <div class="sm:text-right">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Invoice date</p>
                <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $invoice->date?->format('d/m/Y') ?? '—' }}</p>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px] text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/70">
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Product</th>
                        <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Qty</th>
                        <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Unit price</th>
                        <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Line total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($invoice->items as $item)
                        <tr>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $item->product?->name ?? 'Product unavailable' }}</td>
                            <td class="px-5 py-4 text-right text-sm text-slate-600 dark:text-slate-300">{{ $item->quantity }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-slate-600 dark:text-slate-300">{{ number_format((float) $item->unit_price, 2) }} $</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-slate-900 dark:text-white">{{ number_format((float) $item->total, 2) }} $</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500 dark:text-slate-400">This invoice has no line items.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50/70 px-5 py-5 dark:border-slate-700 dark:bg-slate-900/50 sm:px-7">
            <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">Invoice total</span>
            <span class="text-xl font-bold text-slate-900 dark:text-white">{{ number_format((float) $invoice->total, 2) }} $</span>
        </div>
    </section>
</div>

@endsection