@extends('layouts.app')
@section('content')

@php
    $statusClasses = match($order->status) {
        'pending' => 'bg-amber-50 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300',
        'processing' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300',
        'completed' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
        default => 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300',
    };
@endphp

<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800 dark:text-blue-400">← Orders</a>
            <div class="mt-3 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $order->order_number }}</h1>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ ucfirst($order->status) }}</span>
            </div>
        </div>
        <a href="{{ route('orders.edit', $order) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Edit order</a>
    </div>

    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">{{ session('success') }}</div>@endif

    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-7">
        <div class="grid gap-6 sm:grid-cols-3">
            <div><p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Client</p><p class="mt-2 text-base font-bold text-slate-900 dark:text-white">{{ $order->client?->name ?? 'Client unavailable' }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Order date</p><p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $order->order_date->format('d/m/Y') }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Created</p><p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $order->created_at->format('d/m/Y H:i') }}</p></div>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px] text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/70"><tr class="border-b border-slate-200 dark:border-slate-700">
                    <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Product</th>
                    <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Qty</th>
                    <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Unit price</th>
                    <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Line total</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $item->product?->name ?? 'Product unavailable' }}</td>
                            <td class="px-5 py-4 text-right text-sm text-slate-600 dark:text-slate-300">{{ $item->quantity }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-slate-600 dark:text-slate-300">{{ number_format((float) $item->unit_price, 2) }} TND</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-slate-900 dark:text-white">{{ number_format((float) $item->total, 2) }} TND</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50/70 px-5 py-5 dark:border-slate-700 dark:bg-slate-900/50 sm:px-7">
            <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">Grand total</span>
            <span class="text-xl font-bold text-slate-900 dark:text-white">{{ number_format((float) $order->total, 2) }} TND</span>
        </div>
    </section>

    @if($order->notes)
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-7">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white">Notes</h2>
            <p class="mt-2 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $order->notes }}</p>
        </section>
    @endif
</div>

@endsection