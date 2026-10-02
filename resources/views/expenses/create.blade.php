@extends('layouts.app')
@section('content')

<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <a href="{{ route('expenses.index') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">← Expenses</a>
        <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">Add Expense</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Record a cost for your workspace.</p>
    </div>

    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-7">
        @include('expenses._form', ['action' => route('expenses.store'), 'expense' => null, 'submitLabel' => 'Add Expense'])
    </section>
</div>

@endsection