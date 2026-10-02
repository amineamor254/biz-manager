<form action="{{ $editing ? route('orders.update', $order) : route('orders.store') }}" method="POST" class="space-y-6">
    @csrf
    @if($editing) @method('PUT') @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300" role="alert">
            <ul class="list-inside list-disc space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-7">
        <h2 class="border-b border-slate-200 pb-4 text-base font-semibold text-slate-900 dark:border-slate-700 dark:text-white">Order details</h2>
        <div class="mt-5 grid gap-5 sm:grid-cols-2 {{ $editing ? 'lg:grid-cols-3' : '' }}">
            <div>
                <label for="client_id" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-300">Client</label>
                <select id="client_id" name="client_id" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                    <option value="">Choose a client...</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id', $editing ? $order->client_id : '') == $client->id ? 'selected' : '' }}>{{ $client->name }}{{ $client->email ? ' · '.$client->email : '' }}</option>
                    @endforeach
                </select>
                @error('client_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="order_date" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-300">Order date</label>
                <input id="order_date" name="order_date" type="date" value="{{ old('order_date', $editing ? $order->order_date->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                @error('order_date')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>
            @if($editing)
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-300">Status</label>
                    <select id="status" name="status" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        @foreach($statuses as $status)<option value="{{ $status }}" {{ old('status', $order->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>@endforeach
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            @endif
        </div>
        <div class="mt-5">
            <label for="notes" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-300">Notes</label>
            <textarea id="notes" name="notes" rows="3" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-900 dark:text-white">{{ old('notes', $editing ? $order->notes : '') }}</textarea>
            @error('notes')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-7" aria-labelledby="order-items-heading">
        <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-700">
            <div>
                <h2 id="order-items-heading" class="text-base font-semibold text-slate-900 dark:text-white">Products</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Prices are loaded from the product catalog when the order is saved.</p>
            </div>
            <button type="button" id="add-order-item" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-3.5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"><span aria-hidden="true">+</span> Add item</button>
        </div>
        <div id="order-items" class="mt-5 space-y-3"></div>
        @error('items')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        <div class="mt-5 flex items-center justify-between border-t border-slate-200 pt-4 dark:border-slate-700">
            <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">Order total</span>
            <span id="order-total" class="text-xl font-bold text-slate-900 dark:text-white">0.00 TND</span>
        </div>
    </section>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <a href="{{ $editing ? route('orders.show', $order) : route('orders.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">Cancel</a>
        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">{{ $editing ? 'Save changes' : 'Create order' }}</button>
    </div>
</form>

<template id="order-item-template">
    <div class="order-item rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900/50">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[minmax(0,2fr)_minmax(6rem,0.7fr)_minmax(8rem,0.9fr)_minmax(8rem,0.9fr)_auto] lg:items-end">
            <div><label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Product</label><select data-product required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></select></div>
            <div><label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Quantity</label><input data-quantity type="number" min="1" step="1" value="1" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></div>
            <div><label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Unit price</label><input data-unit-price type="text" readonly tabindex="-1" placeholder="0.00" class="w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"></div>
            <div><span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Line total</span><p data-line-total class="rounded-lg bg-white px-3 py-2.5 text-sm font-bold text-slate-900 dark:bg-slate-800 dark:text-white">0.00 TND</p></div>
            <button type="button" data-remove class="inline-flex h-10 items-center justify-center rounded-lg px-3 text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40" aria-label="Remove item">Remove</button>
        </div>
    </div>
</template>

<template id="order-product-options">
    <option value="">Select a product...</option>
    @foreach($products as $product)<option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }}</option>@endforeach
</template>

<script>
    (() => {
        const container = document.getElementById('order-items');
        const rowTemplate = document.getElementById('order-item-template');
        const optionsTemplate = document.getElementById('order-product-options');
        const totalElement = document.getElementById('order-total');
        const initialItems = @js(old('items', $editing ? $order->items->map(fn ($item) => ['product_id' => $item->product_id, 'quantity' => $item->quantity])->values()->all() : [['product_id' => '', 'quantity' => 1]]));

        const cents = (amount) => {
            const [whole, fraction = ''] = String(amount || '0').split('.');
            return (Number(whole) * 100) + Number((fraction + '00').slice(0, 2));
        };
        const money = (value) => `${(value / 100).toFixed(2)} TND`;

        function updateRows() {
            const rows = [...container.querySelectorAll('.order-item')];
            const selectedIds = rows.map((row) => row.querySelector('[data-product]').value).filter(Boolean);
            let totalCents = 0;

            rows.forEach((row, index) => {
                const select = row.querySelector('[data-product]');
                const quantity = row.querySelector('[data-quantity]');
                const selected = select.selectedOptions[0];
                const price = selected?.dataset.price || '';
                const amount = Math.max(0, Number.parseInt(quantity.value || '0', 10) || 0);
                const lineCents = cents(price) * amount;

                select.name = `items[${index}][product_id]`;
                quantity.name = `items[${index}][quantity]`;
                row.querySelector('[data-unit-price]').value = price ? `${Number(price).toFixed(2)} TND` : '';
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
            const row = fragment.querySelector('.order-item');
            const select = row.querySelector('[data-product]');
            select.append(optionsTemplate.content.cloneNode(true));
            row.querySelector('[data-quantity]').value = item.quantity || 1;
            if (item.product_id) select.value = String(item.product_id);
            container.append(fragment);
            updateRows();
        }

        document.getElementById('add-order-item').addEventListener('click', () => addRow());
        container.addEventListener('input', updateRows);
        container.addEventListener('change', updateRows);
        container.addEventListener('click', (event) => {
            if (event.target.closest('[data-remove]') && container.querySelectorAll('.order-item').length > 1) {
                event.target.closest('.order-item').remove();
                updateRows();
            }
        });

        initialItems.forEach((item) => addRow(item));
    })();
</script>