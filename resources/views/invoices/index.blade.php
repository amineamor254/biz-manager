@extends('layouts.app')
@section('content')

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-blue-700 dark:text-blue-400">Sales</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Invoices</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Review client sales and invoice records.</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="inline-flex min-h-9 self-start items-center justify-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-sm shadow-blue-600/20 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 sm:min-h-10 sm:self-auto sm:px-4 sm:text-sm">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14m7-7H5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" /></svg>
            Create invoice
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 md:overflow-x-auto">
        @if($invoices->count() > 0)
            <div class="hidden md:block">
                <table class="w-full min-w-[560px] text-left lg:min-w-[700px]">
                    <thead class="bg-slate-50 dark:bg-slate-900/70">
                        <tr class="border-b border-slate-200 dark:border-slate-700">
                            <th class="px-2 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400 lg:px-5">Invoice</th>
                            <th class="px-2 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400 lg:px-5">Client</th>
                            <th class="px-2 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400 lg:px-5">Invoice date</th>
                            <th class="px-2 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400 lg:px-5">Total</th>
                            <th class="px-2 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400 lg:px-5">Status</th>
                            <th class="sticky right-0 z-20 whitespace-nowrap bg-slate-50 px-2 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-slate-500 dark:bg-slate-900/70 dark:text-slate-400 lg:px-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($invoices as $invoice)
                            @php
                                $statusClasses = match($invoice->status) {
                                    'paid' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
                                    'overdue', 'cancelled' => 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300',
                                    'sent' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300',
                                    default => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
                                };
                            @endphp
                            <tr class="group transition hover:bg-slate-50/80 dark:hover:bg-slate-700/30">
                                <td class="whitespace-nowrap px-2 py-4 lg:px-5"><a href="{{ route('invoices.show', $invoice) }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"> INV-{{ str_pad($invoices->count() - $loop->iteration + 1, 5, '0', STR_PAD_LEFT) }}</a></td>
                                <td class="px-2 py-4 lg:px-5"><span class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $invoice->client?->name ?? 'Client unavailable' }}</span></td>
                                <td class="whitespace-nowrap px-2 py-4 text-sm text-slate-600 dark:text-slate-300 lg:px-5">{{ $invoice->date?->format('M d, Y') ?? '—' }}</td>
                                <td class="whitespace-nowrap px-2 py-4 text-sm font-bold text-slate-900 dark:text-white lg:px-5">{{ number_format((float) $invoice->total, 2) }} $</td>
                                <td class="px-2 py-4 lg:px-5"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ ucfirst($invoice->status) }}</span></td>
                                <td class="sticky right-0 z-10 whitespace-nowrap bg-white px-2 py-4 group-hover:bg-slate-50/80 dark:bg-slate-800 dark:group-hover:bg-slate-700/30 lg:px-3">
                                   
<div class="flex items-center justify-end gap-1.5">
    <a href="{{ route('invoices.show', $invoice) }}"
       class="inline-flex items-center rounded-lg px-2 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white">
        View
    </a>

    <a href="{{ route('invoices.edit', $invoice) }}"
       class="inline-flex items-center gap-1 rounded-lg px-2 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">
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
        <span>Edit</span>
    </a>

    <form action="{{ route('invoices.destroy', $invoice) }}"
          method="POST"
          class="inline">
        @csrf
        @method('DELETE')

        <button type="submit"
                class="inline-flex items-center rounded-lg px-2 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
            Delete
        </button>
    </form>
</div>


                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-700 md:hidden">
                @foreach($invoices as $invoice)
                    @php
                        $statusClasses = match($invoice->status) {
                            'paid' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
                            'overdue', 'cancelled' => 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300',
                            'sent' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300',
                            default => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
                        };
                    @endphp
                    <div class="min-w-0 p-4">
                        <div class="flex min-w-0 items-start justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ route('invoices.show', $invoice) }}" class="break-words text-sm font-semibold text-blue-700 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    INV-{{ str_pad($invoices->count() - $loop->iteration + 1, 5, '0', STR_PAD_LEFT) }}
                                </a>
                                <p class="mt-1 break-words text-sm font-medium text-slate-800 dark:text-slate-200">{{ $invoice->client?->name ?? 'Client unavailable' }}</p>
                            </div>
                            <span class="inline-flex flex-shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ ucfirst($invoice->status) }}</span>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Invoice date</p>
                                <p class="mt-1 text-slate-600 dark:text-slate-300">{{ $invoice->date?->format('M d, Y') ?? '—' }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Total</p>
                                <p class="mt-1 break-words font-bold text-slate-900 dark:text-white">{{ number_format((float) $invoice->total, 2) }} $</p>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-1.5">
                            <a href="{{ route('invoices.show', $invoice) }}"
                               class="inline-flex min-h-10 items-center rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white">
                                View
                            </a>
                            <a href="{{ route('invoices.edit', $invoice) }}"
                               class="inline-flex min-h-10 items-center gap-1 rounded-lg px-3 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Edit</span>
                            </a>
                            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex min-h-10 items-center rounded-lg px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center px-5 py-16 text-center">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300"><svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" /><path d="M14 3v5h5M9 13h6m-6 4h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /></svg></span>
                <h2 class="mt-4 text-base font-bold text-slate-900 dark:text-white">No invoices yet</h2>
                <p class="mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">Create your first invoice from a client and the products you sell.</p>
                <a href="{{ route('invoices.create') }}" class="mt-5 inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Create invoice</a>
            </div>
        @endif
    </div>
</div>

@endsection