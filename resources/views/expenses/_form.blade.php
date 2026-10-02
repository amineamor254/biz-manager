<form action="{{ $action }}" method="POST" class="space-y-6">
    @csrf
    @if($expense)
        @method('PUT')
    @endif

    <div>
        <label for="description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
        <input id="description" name="description" type="text" maxlength="255" required value="{{ old('description', $expense?->description) }}" class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
        @error('description')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="amount" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
            <input id="amount" name="amount" type="number" min="0.01" max="9999999999.99" step="0.01" required value="{{ old('amount', $expense?->amount) }}" class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
            @error('amount')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="category" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
            <input id="category" name="category" type="text" list="expense-categories" maxlength="100" required value="{{ old('category', $expense?->category) }}" class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
            <datalist id="expense-categories">
                @foreach($categories as $category)
                    <option value="{{ $category }}">
                @endforeach
            </datalist>
            @error('category')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label for="expense_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
        <input id="expense_date" name="expense_date" type="date" required value="{{ old('expense_date', $expense?->expense_date?->format('Y-m-d') ?? now()->toDateString()) }}" class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
        @error('expense_date')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="notes" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Notes <span class="font-normal text-gray-500 dark:text-gray-400">(optional)</span></label>
        <textarea id="notes" name="notes" rows="4" maxlength="5000" class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">{{ old('notes', $expense?->notes) }}</textarea>
        @error('notes')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row dark:border-gray-700">
        <a href="{{ route('expenses.index') }}" class="inline-flex items-center justify-center rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Cancel</a>
        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">{{ $submitLabel }}</button>
    </div>
</form>