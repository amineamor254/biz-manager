@extends('layouts.app')
@section('content')

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-blue-700 dark:text-blue-400">Sales</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Orders</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Track customer orders independently from invoices.</p>
        </div>
        <a href="{{ route('orders.create') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Create order</a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        @if($orders->isNotEmpty())
            <table class="w-full min-w-[820px] text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/70">
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Order #</th>
                        <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Client</th>
                        <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Date</th>
                        <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Items / qty</th>
                        <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Total</th>
                        <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</th>
                        <th class="px-3 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($orders as $order)
                        @php
                            $statusClasses = match($order->status) {
                                'pending' => 'bg-amber-50 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300',
                                'processing' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300',
                                'completed' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
                                default => 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300',
                            };
                        @endphp
                        <tr class="transition hover:bg-slate-50/80 dark:hover:bg-slate-700/30">
                            <td class="whitespace-nowrap px-4 py-4"><a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800 dark:text-blue-400">{{ $order->order_number }}</a></td>
                            <td class="px-4 py-4 text-sm font-medium text-slate-800 dark:text-slate-200">{{ $order->client?->name ?? 'Client unavailable' }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $order->order_date->format('M d, Y') }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $order->items->count() }} items · {{ $order->items->sum('quantity') }} qty</td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm font-bold text-slate-900 dark:text-white">{{ number_format((float) $order->total, 2) }} TND</td>
                            <td class="px-4 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ ucfirst($order->status) }}</span></td>
                            <td class="whitespace-nowrap px-3 py-4">
                                <div class="flex items-center justify-end gap-0.5">
                                    <a href="{{ route('orders.show', $order) }}" class="rounded-md px-2 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white">View</a>
                                    <a href="{{ route('orders.edit', $order) }}" class="rounded-md px-2 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">Edit</a>
                                    <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirm('Delete this order?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md px-2 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="flex flex-col items-center px-5 py-16 text-center">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">No orders yet</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Create an order to keep track of a customer request.</p>
                <a href="{{ route('orders.create') }}" class="mt-5 inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Create order</a>
            </div>
        @endif
    </div>
</div>

@endsection