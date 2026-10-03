@extends('layouts.app')
@section('content')

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Expenses</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track spending for your workspace.</p>
        </div>
        <a href="{{ route('expenses.create') }}" class="inline-flex self-start items-center justify-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700 md:self-auto md:px-4 md:py-2.5 md:text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Expense
        </a>
    </div>

    @if(session('success'))
        <div role="status" class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 md:overflow-x-auto">
        @if($expenses->isNotEmpty())
            <div class="hidden md:block">
                <table class="w-full min-w-[700px] text-left">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Description</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Category</th>
                            <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Amount</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Date</th>
                            <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($expenses as $expense)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-5 py-4">
                                    <a href="{{ route('expenses.show', $expense) }}" class="font-semibold text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-300">{{ $expense->description }}</a>
                                    @if($expense->notes)
                                        <p class="mt-1 max-w-sm truncate text-xs text-gray-500 dark:text-gray-400">{{ $expense->notes }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $expense->category }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">${{ number_format((float) $expense->amount, 2) }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $expense->expense_date->format('M d, Y') }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('expenses.edit', $expense) }}" class="inline-flex items-center gap-1 rounded-lg px-2 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-lg px-2 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700 md:hidden">
                @foreach($expenses as $expense)
                    <div class="min-w-0 p-4">
                        <div class="min-w-0">
                            <a href="{{ route('expenses.show', $expense) }}" class="break-words font-semibold text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-300">
                                {{ $expense->description }}
                            </a>
                            @if($expense->notes)
                                <p class="mt-1 break-words text-sm text-gray-500 dark:text-gray-400">{{ $expense->notes }}</p>
                            @endif
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Category</p>
                                <p class="mt-1 break-words text-gray-700 dark:text-gray-300">{{ $expense->category }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Amount</p>
                                <p class="mt-1 break-words font-semibold text-gray-900 dark:text-white">${{ number_format((float) $expense->amount, 2) }}</p>
                            </div>
                            <div class="col-span-2 min-w-0">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Date</p>
                                <p class="mt-1 text-gray-600 dark:text-gray-300">{{ $expense->expense_date->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center gap-2">
                            <a href="{{ route('expenses.edit', $expense) }}" class="inline-flex items-center gap-1 rounded-lg px-2 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center rounded-lg px-2 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center px-6 py-16 text-center">
                <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6v12m6-6H6m15 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">No expenses yet</h2>
                <p class="mt-2 max-w-md text-sm text-gray-500 dark:text-gray-400">Record your first expense to start tracking workspace spending.</p>
                <a href="{{ route('expenses.create') }}" class="mt-5 inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Add Expense</a>
            </div>
        @endif
    </section>
</div>

@endsection