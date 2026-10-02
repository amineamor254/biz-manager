@extends('layouts.app')
@section('content')

<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('expenses.index') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">← Expenses</a>
            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">{{ $expense->description }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('expenses.edit', $expense) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800">Edit</a>
            <form action="{{ route('expenses.destroy', $expense) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div role="status" class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">{{ session('success') }}</div>
    @endif

    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-7">
        <dl class="grid gap-6 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Category</dt>
                <dd class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $expense->category }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Amount</dt>
                <dd class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">${{ number_format((float) $expense->amount, 2) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Date</dt>
                <dd class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $expense->expense_date->format('M d, Y') }}</dd>
            </div>
            @if($expense->notes)
                <div class="sm:col-span-2">
                    <dt class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Notes</dt>
                    <dd class="mt-2 whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">{{ $expense->notes }}</dd>
                </div>
            @endif
        </dl>
    </section>
</div>

@endsection