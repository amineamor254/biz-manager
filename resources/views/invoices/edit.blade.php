@extends('layouts.app')
@section('content')

<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-blue-700 dark:text-blue-400">Sales</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Edit invoice</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Update invoice details and items. Existing item prices remain unchanged.</p>
        </div>
        <a href="{{ route('invoices.show', $invoice) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Cancel</a>
    </div>

    <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <div id="deleted-invoice-items"></div>
        @foreach(old('deleted_item_ids', []) as $deletedItemId)
            <input type="hidden" name="deleted_item_ids[]" value="{{ $deletedItemId }}">
        @endforeach

        @if($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300" role="alert">
                <ul class="list-inside list-disc space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-7">
            <h2 class="border-b border-slate-200 pb-4 text-base font-semibold text-slate-900 dark:border-slate-700 dark:text-white">Invoice details</h2>
            <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label for="client_id" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-300">Client</label>
                    <select id="client_id" name="client_id" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $invoice->client_id) == $client->id ? 'selected' : '' }}>{{ $client->name }}{{ $client->email ? ' · '.$client->email : '' }}</option>
                        @endforeach
                    </select>
                    @error('client_id')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="date" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-300">Invoice date</label>
                    <input id="date" name="date" type="date" value="{{ old('date', $invoice->date?->format('Y-m-d')) }}" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                    @error('date')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-300">Status</label>
                    <select id="status" name="status" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ old('status', $invoice->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-7" aria-labelledby="invoice-items-heading">
            <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-700">
                <div>
                    <h2 id="invoice-items-heading" class="text-base font-semibold text-slate-900 dark:text-white">Invoice items</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Original prices are retained for products already on this invoice.</p>
                </div>
                            <button
                    type="button"
                    id="add-invoice-item"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950"
                >
                    <span aria-hidden="true">+</span>
                    Add item
                </button>
            </div>
            <div id="invoice-items" class="mt-5 space-y-3"></div>
            <div class="mt-5 flex items-center justify-between border-t border-slate-200 pt-4 dark:border-slate-700">
                <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">Invoice total</span>
                <span id="invoice-total" class="text-xl font-bold text-slate-900 dark:text-white">0.00 $</span>
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">Cancel</a>
            <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-600/20 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">Save changes</button>
        </div>
    </form>
</div>

<template id="invoice-item-template">
    <div class="invoice-item rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900/50">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[minmax(0,2fr)_minmax(6rem,0.7fr)_minmax(8rem,0.9fr)_minmax(8rem,0.9fr)_auto] lg:items-end">
            <div class="space-y-1.5"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Product</label><select data-product required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></select><p data-stock class="text-xs text-slate-500 dark:text-slate-400">Choose a product</p></div>
            <div class="space-y-1.5"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Quantity</label><input data-quantity type="number" min="1" step="1" value="1" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></div>
            <div class="space-y-1.5"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Unit price</label><input data-unit-price type="text" readonly tabindex="-1" placeholder="0.00" class="w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"></div>
            <div class="space-y-1.5"><span class="block text-sm font-medium text-slate-700 dark:text-slate-300">Line total</span><p data-line-total class="rounded-lg bg-white px-3 py-2.5 text-sm font-bold text-slate-900 dark:bg-slate-800 dark:text-white">0.00 $</p></div>
            <input data-item-id type="hidden">
            <button type="button" data-remove class="inline-flex h-10 items-center justify-center rounded-lg px-3 text-sm font-semibold text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500/30 dark:text-red-400 dark:hover:bg-red-950/40" aria-label="Remove item">Remove</button>
        </div>
    </div>
</template>

<template id="invoice-product-options">
    <option value="">Select a product...</option>
    @foreach($products as $product)
        <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->quantity + $invoice->items->where('product_id', $product->id)->sum('quantity') }}">{{ $product->name }}</option>
    @endforeach
</template>

<script>
    (() => {
        const container = document.getElementById('invoice-items');
        const rowTemplate = document.getElementById('invoice-item-template');
        const optionsTemplate = document.getElementById('invoice-product-options');
        const addButton = document.getElementById('add-invoice-item');
        const totalElement = document.getElementById('invoice-total');
        const deletedItems = document.getElementById('deleted-invoice-items');
        const initialItems = @js(old('items', $invoice->items->reject(fn ($item) => in_array($item->id, old('deleted_item_ids', [])))->map(fn ($item) => ['id' => $item->id, 'product_id' => $item->product_id, 'quantity' => $item->quantity, 'unit_price' => $item->unit_price])->values()->all()));

        const cents = (amount) => {
            const [whole, fraction = ''] = String(amount || '0').split('.');
            return (Number(whole) * 100) + Number((fraction + '00').slice(0, 2));
        };
        const money = (value) => `${(value / 100).toFixed(2)} TND`;

        function updateRows() {
            const rows = [...container.querySelectorAll('.invoice-item')];
            const selectedIds = rows.map((row) => row.querySelector('[data-product]').value).filter(Boolean);
            let totalCents = 0;

            rows.forEach((row, index) => {
                const select = row.querySelector('[data-product]');
                const quantity = row.querySelector('[data-quantity]');
                const itemId = row.querySelector('[data-item-id]');
                const selected = select.selectedOptions[0];
                const sameSnapshotProduct = row.dataset.snapshotProduct === select.value;
                const price = sameSnapshotProduct && row.dataset.snapshotPrice ? row.dataset.snapshotPrice : (selected?.dataset.price || '');
                const stock = Number(selected?.dataset.stock || 0);
                const amount = Math.max(0, Number.parseInt(quantity.value || '0', 10) || 0);
                const lineCents = cents(price) * amount;

                select.name = `items[${index}][product_id]`;
                quantity.name = `items[${index}][quantity]`;
                if (itemId.value) itemId.name = `items[${index}][id]`;
                quantity.max = stock || '';
                row.querySelector('[data-unit-price]').value = price ? `${Number(price).toFixed(2)} TND` : '';
                row.querySelector('[data-stock]').textContent = selected?.value ? `Available stock: ${stock}` : 'Choose a product';
                row.querySelector('[data-line-total]').textContent = money(lineCents);
                totalCents += lineCents;

                [...select.options].forEach((option) => {
                    option.disabled = Boolean(option.value && option.value !== select.value && selectedIds.includes(option.value));
                });
            });

            totalElement.textContent = money(totalCents);
        }

        function addRow(item = {}) {
            const fragment = rowTemplate.content.cloneNode(true);
            const row = fragment.querySelector('.invoice-item');
            const select = row.querySelector('[data-product]');
            select.append(optionsTemplate.content.cloneNode(true));
            row.querySelector('[data-item-id]').value = item.id || '';
            row.querySelector('[data-quantity]').value = item.quantity || 1;
            if (item.product_id) select.value = String(item.product_id);
            row.dataset.snapshotProduct = item.unit_price ? String(item.product_id) : '';
            row.dataset.snapshotPrice = item.unit_price || '';
            container.append(fragment);
            updateRows();
        }

        addButton.addEventListener('click', () => addRow());
        container.addEventListener('input', updateRows);
        container.addEventListener('change', updateRows);
        container.addEventListener('click', (event) => {
            const removeButton = event.target.closest('[data-remove]');
            if (!removeButton) return;

            const row = removeButton.closest('.invoice-item');
            const itemId = row.querySelector('[data-item-id]').value;
            if (itemId) {
                const deletedItem = document.createElement('input');
                deletedItem.type = 'hidden';
                deletedItem.name = 'deleted_item_ids[]';
                deletedItem.value = itemId;
                deletedItems.append(deletedItem);
            }

            row.remove();
            updateRows();
        });

        initialItems.forEach((item) => addRow(item));
    })();
</script>

@endsection