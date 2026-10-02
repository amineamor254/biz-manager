@extends('layouts.app')
@section('content')

<div class="space-y-6 lg:space-y-8">
    <header class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reports</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overview of your business activity and performance.</p>
        </div>
        <form action="{{ route('reports.index') }}" method="GET" class="flex flex-col gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:flex-row sm:items-end">
            <div class="min-w-44">
                <label for="period" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Date range</label>
                <select id="period" name="period" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                    <option value="today" @selected($period === 'today')>Today</option>
                    <option value="week" @selected($period === 'week')>This Week</option>
                    <option value="month" @selected($period === 'month')>This Month</option>
                    <option value="year" @selected($period === 'year')>This Year</option>
                    <option value="custom" @selected($period === 'custom')>Custom Range</option>
                </select>
            </div>
            <div id="custom-range" class="grid grid-cols-1 gap-3 sm:grid-cols-2 {{ $period === 'custom' ? '' : 'hidden' }}">
                <div>
                    <label for="start_date" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Start Date</label>
                    <input id="start_date" type="date" name="start_date" value="{{ old('start_date', $period === 'custom' ? $startDate->toDateString() : '') }}" {{ $period === 'custom' ? 'required' : '' }} class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                </div>
                <div>
                    <label for="end_date" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">End Date</label>
                    <input id="end_date" type="date" name="end_date" value="{{ old('end_date', $period === 'custom' ? $endDate->toDateString() : '') }}" {{ $period === 'custom' ? 'required' : '' }} class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                </div>
            </div>
            <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Apply</button>
        </form>
    </header>

    @if($errors->any())
        <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
            <ul class="list-inside list-disc space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-2">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $periodLabel }}</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $startDate->format('M d, Y') }} – {{ $endDate->format('M d, Y') }}</p>
    </div>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Report metrics">
        <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sales</p>
            <p class="mt-2 break-words text-2xl font-bold text-gray-900 dark:text-white">{{ $currency }} {{ number_format($salesTotal, 2) }}</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Invoice totals in this period</p>
        </article>
        <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Invoices</p>
            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($invoiceCount) }}</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Issued in this period</p>
        </article>
        <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Orders</p>
            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($ordersCount) }}</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Customer orders in this period</p>
        </article>
        <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Expenses</p>
            <p class="mt-2 break-words text-2xl font-bold text-gray-900 dark:text-white">{{ $currency }} {{ number_format((float) $expensesTotal, 2) }}</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recorded in this period</p>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="min-w-0 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6 xl:col-span-2">
            <div class="mb-4">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Sales Overview</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Invoice sales grouped by {{ $period === 'today' ? 'hour' : ($period === 'year' ? 'month' : 'date') }}.</p>
            </div>
            @if($salesChart->isNotEmpty())
                <div id="reports-sales-chart" class="min-h-[280px] w-full"></div>
            @else
                <div class="flex min-h-[280px] items-center justify-center rounded-lg border border-dashed border-gray-200 px-5 text-center dark:border-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No sales data available for this period.</p>
                </div>
            @endif
        </div>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6" aria-labelledby="orders-overview-heading">
            <h2 id="orders-overview-heading" class="text-base font-bold text-gray-900 dark:text-white">Orders Overview</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Order status for this period.</p>
            <dl class="mt-4 space-y-2">
                @foreach(['pending' => 'Pending', 'processing' => 'Processing', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $status => $label)
                    @php
                        $statusClass = match($status) {
                            'processing' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300',
                            'completed' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
                            'cancelled' => 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300',
                            default => 'bg-amber-50 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300',
                        };
                    @endphp
                    <div class="flex items-center justify-between gap-3 rounded-lg bg-gray-50 px-3.5 py-3 dark:bg-gray-900/60">
                        <dt><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $label }}</span></dt>
                        <dd class="text-sm font-bold tabular-nums text-gray-900 dark:text-white">{{ number_format($ordersByStatus[$status]) }}</dd>
                    </div>
                @endforeach
            </dl>
            @if($ordersCount === 0)<p class="mt-3 text-xs text-gray-500 dark:text-gray-400">No orders recorded for this period.</p>@endif
        </section>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Expenses by Category</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Recorded expense categories for this period.</p>
            </div>
            @if($expensesByCategory->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[420px] text-left">
                        <thead class="bg-gray-50 dark:bg-gray-900/50"><tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Category</th>
                            <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Records</th>
                            <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($expensesByCategory as $category)
                                <tr>
                                    <td class="px-5 py-3.5 text-sm font-medium text-gray-800 dark:text-gray-200">{{ $category->category ?: 'Uncategorized' }}</td>
                                    <td class="px-5 py-3.5 text-right text-sm text-gray-600 dark:text-gray-400">{{ number_format($category->expense_count) }}</td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right text-sm font-semibold text-gray-900 dark:text-white">{{ $currency }} {{ number_format((float) $category->expense_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No expenses recorded for this period.</p>
            @endif
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Top Products</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ranked by invoice quantity sold.</p>
            </div>
            @if($topProducts->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[420px] text-left">
                        <thead class="bg-gray-50 dark:bg-gray-900/50"><tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Product</th>
                            <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Qty Sold</th>
                            <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Sales</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($topProducts as $product)
                                <tr>
                                    <td class="px-5 py-3.5 text-sm font-medium text-gray-800 dark:text-gray-200">{{ $product->product_name }}</td>
                                    <td class="px-5 py-3.5 text-right text-sm tabular-nums text-gray-600 dark:text-gray-400">{{ number_format($product->quantity_sold) }}</td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right text-sm font-semibold text-gray-900 dark:text-white">{{ $currency }} {{ number_format((float) $product->sales_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No product sales found for this period.</p>
            @endif
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Recent Invoices</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Latest invoices in this period.</p>
            </div>
            @if($recentInvoices->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px] text-left">
                        <thead class="bg-gray-50 dark:bg-gray-900/50"><tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Invoice</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Client</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Date</th>
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
                                    <td class="whitespace-nowrap px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400">{{ $invoice->date?->format('M d, Y') ?? '—' }}</td>
                                    <td class="px-5 py-3.5"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $invoiceStatusClass }}">{{ ucfirst($invoice->status ?: 'Draft') }}</span></td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right text-sm font-semibold text-gray-900 dark:text-white">{{ $currency }} {{ number_format((float) $invoice->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No invoices found for this period.</p>
            @endif
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Recent Expenses</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Latest workspace expenses in this period.</p>
            </div>
            @if($recentExpenses->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[440px] text-left">
                        <thead class="bg-gray-50 dark:bg-gray-900/50"><tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Description</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Category</th>
                            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Date</th>
                            <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Amount</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($recentExpenses as $expense)
                                <tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-700/30">
                                    <td class="px-5 py-3.5 text-sm font-medium text-gray-800 dark:text-gray-200">{{ $expense->description }}</td>
                                    <td class="px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400">{{ $expense->category ?: 'Uncategorized' }}</td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400">{{ $expense->expense_date?->format('M d, Y') }}</td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right text-sm font-semibold text-gray-900 dark:text-white">{{ $currency }} {{ number_format((float) $expense->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No expenses recorded for this period.</p>
            @endif
        </div>
    </section>
</div>

<script>
    (() => {
        const periodSelect = document.getElementById('period');
        const rangeFields = document.getElementById('custom-range');
        const dateInputs = rangeFields.querySelectorAll('input');

        function syncCustomRange() {
            const custom = periodSelect.value === 'custom';
            rangeFields.classList.toggle('hidden', !custom);
            dateInputs.forEach((input) => { input.required = custom; });
        }

        periodSelect.addEventListener('change', () => {
            syncCustomRange();
            if (periodSelect.value !== 'custom') {
                periodSelect.form.requestSubmit();
            }
        });
        syncCustomRange();
    })();
</script>

@if($salesChart->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const chartElement = document.getElementById('reports-sales-chart');
        const darkMode = () => document.documentElement.classList.contains('dark');
        let dark = darkMode();
        const labels = @json($salesChart->pluck('label')->values());
        const values = @json($salesChart->pluck('total')->values());
        const chart = new ApexCharts(chartElement, {
            series: [{ name: 'Sales', data: values }],
            chart: { type: 'area', height: 300, toolbar: { show: false }, zoom: { enabled: false }, background: 'transparent', fontFamily: 'Inter, sans-serif' },
            colors: ['#2563eb'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2.5 },
            fill: { type: 'gradient', gradient: { opacityFrom: dark ? 0.3 : 0.18, opacityTo: 0.02, stops: [0, 100] } },
            markers: { size: 3, strokeWidth: 2, strokeColors: dark ? '#1f2937' : '#ffffff', hover: { size: 5 } },
            xaxis: { categories: labels, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { colors: dark ? '#9ca3af' : '#6b7280', fontSize: '11px' } } },
            yaxis: { labels: { style: { colors: dark ? '#9ca3af' : '#6b7280', fontSize: '11px' }, formatter: value => `${@json($currency)} ${Number(value).toLocaleString()}` } },
            grid: { borderColor: dark ? 'rgba(156,163,175,0.14)' : 'rgba(0,0,0,0.07)', strokeDashArray: 4, xaxis: { lines: { show: false } } },
            tooltip: { theme: dark ? 'dark' : 'light', y: { formatter: value => `${@json($currency)} ${Number(value).toLocaleString()}` } },
            theme: { mode: dark ? 'dark' : 'light' },
        });
        chart.render();

        new MutationObserver(() => {
            const nextDark = darkMode();
            if (nextDark === dark) return;
            dark = nextDark;
            chart.updateOptions({
                theme: { mode: dark ? 'dark' : 'light' },
                xaxis: { labels: { style: { colors: dark ? '#9ca3af' : '#6b7280' } } },
                yaxis: { labels: { style: { colors: dark ? '#9ca3af' : '#6b7280' } } },
                grid: { borderColor: dark ? 'rgba(156,163,175,0.14)' : 'rgba(0,0,0,0.07)' },
                tooltip: { theme: dark ? 'dark' : 'light' },
                markers: { strokeColors: dark ? '#1f2937' : '#ffffff' },
                fill: { gradient: { opacityFrom: dark ? 0.3 : 0.18 } },
            });
        }).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    });
</script>
@endif

@endsection