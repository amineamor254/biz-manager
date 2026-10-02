@extends('layouts.app')
@section('content')

<div class="space-y-6 lg:space-y-8">
    <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overview of your business activity.</p>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ now()->format('l, M d, Y') }}</p>
    </header>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-5" aria-label="Business metrics">
        <article class="min-w-0 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0"><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Sales</p><p class="mt-2 break-words text-2xl font-bold text-gray-900 dark:text-white">${{ number_format((float) $totalRevenue, 2) }}</p><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">All invoices</p></div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v18m4-14.5c-.7-1-2-1.5-4-1.5-2.2 0-4 1.1-4 3s1.8 3 4 3 4 1.1 4 3-1.8 3-4 3c-2 0-3.3-.5-4-1.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>
            </div>
        </article>
        <article class="min-w-0 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0"><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Orders</p><p class="mt-2 break-words text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($ordersCount) }}</p><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Customer orders</p></div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            </div>
        </article>
        <article class="min-w-0 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0"><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Clients</p><p class="mt-2 break-words text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($clientsCount) }}</p><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Workspace clients</p></div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-3A4.5 4.5 0 0 0 4 18.5V20m14-9a3 3 0 1 0-2.5-4.7M20 20v-1.5a4.5 4.5 0 0 0-3-4.25M13.5 7.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>
            </div>
        </article>
        <article class="min-w-0 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0"><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Profit</p><p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">—</p><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Not calculated from available records</p></div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m4 16 5-5 3 3 7-8m0 0h-5m5 0v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            </div>
        </article>
        <article class="min-w-0 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0"><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Expenses</p><p class="mt-2 break-words text-2xl font-bold text-gray-900 dark:text-white">${{ number_format((float) $totalExpenses, 2) }}</p><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Current workspace</p></div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 7h14M7 4h10l1 16H6L7 4Zm3 7h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            </div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="min-w-0 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6 lg:col-span-2">
            <div class="mb-4">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Sales Overview</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Invoice sales by month for {{ now()->year }}.</p>
            </div>
            @if($monthlyIncome->isNotEmpty())
                <div id="revenueChart" class="min-h-[280px] w-full"></div>
            @else
                <div class="flex min-h-[280px] flex-col items-center justify-center rounded-lg border border-dashed border-gray-200 px-5 text-center dark:border-gray-700">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">No sales data for {{ now()->year }} yet</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Invoice totals will appear here when they are recorded.</p>
                </div>
            @endif
        </div>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6" aria-labelledby="quick-actions-heading">
            <h2 id="quick-actions-heading" class="text-base font-bold text-gray-900 dark:text-white">Quick Actions</h2>
            <div class="mt-3 grid grid-cols-1 gap-1 sm:grid-cols-2 lg:grid-cols-1">
                <a href="{{ route('invoices.create') }}" class="flex min-h-11 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700/50">
                    <svg class="h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M9 13h6m-6 4h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    New Invoice
                </a>
                <a href="{{ route('orders.create') }}" class="flex min-h-11 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700/50">
                    <svg class="h-5 w-5 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    New Order
                </a>
                <a href="{{ route('clients.create') }}" class="flex min-h-11 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700/50">
                    <svg class="h-5 w-5 shrink-0 text-sky-600 dark:text-sky-400" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-3A4.5 4.5 0 0 0 4 18.5V20m14-9a3 3 0 1 0-2.5-4.7M13.5 7.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    Add Client
                </a>
                <a href="{{ route('expenses.create') }}" class="flex min-h-11 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700/50">
                    <svg class="h-5 w-5 shrink-0 text-rose-600 dark:text-rose-400" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 7h14M7 4h10l1 16H6L7 4Zm3 7h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Add Expense
                </a>
            </div>
        </section>
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800" aria-labelledby="recent-orders-heading">
        <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
            <div><h2 id="recent-orders-heading" class="text-base font-bold text-gray-900 dark:text-white">Recent Orders</h2><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Latest customer orders.</p></div>
            <a href="{{ route('orders.index') }}" class="shrink-0 rounded-lg px-3 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">View all</a>
        </div>
        @if($recentOrders->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left">
                    <thead class="bg-gray-50 dark:bg-gray-900/50"><tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Order #</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Client</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Date</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($recentOrders as $order)
                            @php
                                $orderStatusClass = match($order->status) {
                                    'processing' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300',
                                    'completed' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
                                    'cancelled' => 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300',
                                    default => 'bg-amber-50 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300',
                                };
                            @endphp
                            <tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-700/30">
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm font-semibold text-blue-700 dark:text-blue-300">{{ $order->order_number }}</td>
                                <td class="px-5 py-3.5 text-sm text-gray-700 dark:text-gray-300">{{ $order->client?->name ?? 'Client unavailable' }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400">{{ $order->order_date?->format('M d, Y') ?? '—' }}</td>
                                <td class="px-5 py-3.5"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $orderStatusClass }}">{{ ucfirst($order->status) }}</span></td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-right text-sm font-semibold text-gray-900 dark:text-white">${{ number_format((float) $order->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No orders have been recorded yet.</p>
        @endif
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800" aria-labelledby="recent-invoices-heading">
            <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                <div><h2 id="recent-invoices-heading" class="text-base font-bold text-gray-900 dark:text-white">Recent Invoices</h2><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Latest invoice activity.</p></div>
                <a href="{{ route('invoices.index') }}" class="shrink-0 rounded-lg px-3 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">View all</a>
            </div>
            @if($recentInvoices->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[520px] text-left">
                        <thead class="bg-gray-50 dark:bg-gray-900/50"><tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Invoice</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Client</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($recentInvoices as $invoice)
                                @php
                                    $invoiceStatusClass = match($invoice->status) {
                                        'paid' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
                                        'overdue', 'cancelled' => 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300',
                                        'sent' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300',
                                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                    };
                                @endphp
                                <tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-700/30">
                                    <td class="whitespace-nowrap px-5 py-3.5 text-sm font-semibold text-gray-900 dark:text-white">{{ $invoice->invoice_number ?: 'INV-' . str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-5 py-3.5 text-sm text-gray-700 dark:text-gray-300">{{ $invoice->client?->name ?? 'Client unavailable' }}</td>
                                    <td class="px-5 py-3.5"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $invoiceStatusClass }}">{{ ucfirst($invoice->status ?: 'Draft') }}</span></td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right text-sm font-semibold text-gray-900 dark:text-white">${{ number_format((float) $invoice->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No invoices have been recorded yet.</p>
            @endif
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800" aria-labelledby="recent-expenses-heading">
            <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                <div><h2 id="recent-expenses-heading" class="text-base font-bold text-gray-900 dark:text-white">Recent Expenses</h2><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Latest workspace expenses.</p></div>
                <a href="{{ route('expenses.index') }}" class="shrink-0 rounded-lg px-3 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">View all</a>
            </div>
            @if($recentExpenses->isNotEmpty())
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($recentExpenses as $expense)
                        <li class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-gray-50/70 dark:hover:bg-gray-700/30">
                            <div class="min-w-0"><p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $expense->description }}</p><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $expense->category }} <span aria-hidden="true">·</span> {{ $expense->expense_date?->format('M d, Y') }}</p></div>
                            <p class="shrink-0 text-sm font-semibold text-gray-900 dark:text-white">${{ number_format((float) $expense->amount, 2) }}</p>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No expenses have been recorded yet.</p>
            @endif
        </div>
    </section>

</div>

@if($monthlyIncome->isNotEmpty())
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const revenueData = @json($monthlyIncome);
    const getIsDark = () => document.documentElement.classList.contains('dark');
    let isDark = getIsDark();

    const labels = revenueData.map(item => new Date(2000, Number(item.month) - 1, 1).toLocaleString('en', { month: 'short' }));
    const values = revenueData.map(item => Number(item.total));

    // ===== Revenue Chart =====
    const revenueOptions = {
        series: [{ name: 'Sales', data: values }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: { show: false },
            zoom: { enabled: false },
            background: 'transparent',
            fontFamily: 'Inter, sans-serif',
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: isDark ? 0.35 : 0.25,
                opacityTo: 0.02,
                stops: [0, 100],
            }
        },
        colors: ['#3b82f6'],
        markers: {
            size: 5,
            colors: ['#3b82f6'],
            strokeColors: isDark ? '#1f2937' : '#ffffff',
            strokeWidth: 2,
            hover: { size: 7 }
        },
        xaxis: {
            categories: labels,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: {
                    colors: isDark ? '#9ca3af' : '#6b7280',
                    fontSize: '11px',
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: isDark ? '#9ca3af' : '#6b7280',
                    fontSize: '11px',
                },
                formatter: val => '$' + val.toLocaleString()
            }
        },
        grid: {
            borderColor: isDark ? 'rgba(156,163,175,0.1)' : 'rgba(0,0,0,0.05)',
            strokeDashArray: 4,
            xaxis: { lines: { show: false } }
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: { formatter: val => '$' + val.toLocaleString() }
        },
        theme: { mode: isDark ? 'dark' : 'light' }
    };

    const revenueChart = new ApexCharts(document.getElementById('revenueChart'), revenueOptions);
    revenueChart.render();

    // ===== Dark Mode Observer =====
    new MutationObserver(() => {
        const newDark = getIsDark();
        if (newDark !== isDark) {
            isDark = newDark;

            revenueChart.updateOptions({
                theme: { mode: isDark ? 'dark' : 'light' },
                markers: {
                    strokeColors: isDark ? '#1f2937' : '#ffffff',
                },
                xaxis: {
                    labels: { style: { colors: isDark ? '#9ca3af' : '#6b7280' } }
                },
                yaxis: {
                    labels: { style: { colors: isDark ? '#9ca3af' : '#6b7280' } }
                },
                grid: {
                    borderColor: isDark ? 'rgba(156,163,175,0.1)' : 'rgba(0,0,0,0.05)',
                },
                tooltip: { theme: isDark ? 'dark' : 'light' },
                fill: {
                    gradient: {
                        opacityFrom: isDark ? 0.35 : 0.25,
                    }
                }
            });

        }
    }).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

});
</script>
@endif

@endsection