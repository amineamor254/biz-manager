@extends('layouts.app')
@section('content')

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-blue-700 dark:text-blue-400">Sales</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Orders</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Track customer orders independently from invoices.</p>
        </div>
        <a href="{{ route('orders.create') }}" class="inline-flex min-h-9 self-start items-center justify-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700 md:min-h-10 md:self-auto md:px-4 md:text-sm">Create order</a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 md:overflow-x-auto">
        @if($orders->isNotEmpty())
            <div class="hidden md:block">
                <table class="w-full text-left">
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
                            <td class="whitespace-nowrap px-4 py-4 text-sm font-bold text-slate-900 dark:text-white">{{ number_format((float) $order->total, 2) }} $</td>
                            <td class="px-4 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ ucfirst($order->status) }}</span></td>
                            <td class="whitespace-nowrap px-3 py-4">
                                <div class="flex items-center justify-end gap-0.5">
                                    <a href="{{ route('orders.show', $order) }}" class="rounded-md px-2 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white">View</a>
                                    <a href="{{ route('orders.edit', $order) }}" class="rounded-md px-2 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">
                                                                 <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-3.5 w-3.5"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                        Edit</a>
                                    <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirm('Delete this order?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md px-2 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
                                             <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                              
                                                                                    Delete</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    </table>
                                </div>

                        <div class="divide-y divide-slate-100 dark:divide-slate-700 md:hidden">
                            @foreach($orders as $order)
                                @php
                                    $statusClasses = match($order->status) {
                                        'pending' => 'bg-amber-50 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300',
                                        'processing' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300',
                                        'completed' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
                                        default => 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300',
                                    };
                                @endphp
                    <div class="min-w-0 p-4">
                        <div class="flex min-w-0 items-start justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ route('orders.show', $order) }}" class="break-words text-sm font-semibold text-blue-700 hover:text-blue-800 dark:text-blue-400">
                                    {{ $order->order_number }}
                                </a>
                                <p class="mt-1 break-words text-sm font-medium text-slate-800 dark:text-slate-200">{{ $order->client?->name ?? 'Client unavailable' }}</p>
                            </div>
                            <span class="inline-flex flex-shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ ucfirst($order->status) }}</span>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Date</p>
                                <p class="mt-1 text-slate-600 dark:text-slate-300">{{ $order->order_date->format('M d, Y') }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Total</p>
                                <p class="mt-1 break-words font-bold text-slate-900 dark:text-white">{{ number_format((float) $order->total, 2) }} $</p>
                            </div>
                            <div class="col-span-2 min-w-0">
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Items / qty</p>
                                <p class="mt-1 text-slate-600 dark:text-slate-300">{{ $order->items->count() }} items · {{ $order->items->sum('quantity') }} qty</p>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-1.5">
                            <a href="{{ route('orders.show', $order) }}" class="inline-flex min-h-10 items-center rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white">View</a>
                            <a href="{{ route('orders.edit', $order) }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">
                                 <svg xmlns="http://www.w3.org/2000/svg"
             class="h-3.5 w-3.5"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
                                Edit
                            </a>
                            <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirm('Delete this order?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex min-h-10 items-center rounded-lg px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
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