@extends('layouts.app')
@section('content')

    <div class="max-w-3xl mx-auto">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-100 to-purple-50 dark:from-purple-900/40 dark:to-purple-800/30 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Invoice</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Create a new invoice for your client</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-8 lg:p-10">
            <form action="{{ route('invoices.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-6">
                    @if($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300" role="alert">
                            <ul class="list-inside list-disc space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h3 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">
                        Invoice Details
                    </h3>

                    <!-- Client Selection -->
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="client_id">
                            Select Client
                        </label>
                        <select id="client_id"
                                name="client_id"
                                required
                                class="w-full px-4 py-2.5 rounded-lg border text-sm
                                       bg-white dark:bg-gray-900
                                       text-gray-900 dark:text-white
                                       transition-colors
                                       @error('client_id')
                                           border-red-500 focus:ring-red-500
                                       @else
                                           border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500
                                       @enderror
                                       focus:outline-none focus:ring-2 focus:ring-opacity-30">
                            <option value="">Choose a client...</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }} ({{ $client->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="flex items-center gap-1.5 text-sm text-red-600 dark:text-red-400 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Invoice Date -->
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="date">
                            Invoice Date
                        </label>
                        <input type="date"
                               id="date"
                               name="date"
                               value="{{ old('date', date('Y-m-d')) }}"
                               required
                               class="w-full px-4 py-2.5 rounded-lg border text-sm
                                      bg-white dark:bg-gray-900
                                      text-gray-900 dark:text-white
                                      transition-colors
                                      @error('date')
                                          border-red-500 focus:ring-red-500
                                      @else
                                          border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500
                                      @enderror
                                      focus:outline-none focus:ring-2 focus:ring-opacity-30">
                        @error('date')
                            <p class="flex items-center gap-1.5 text-sm text-red-600 dark:text-red-400 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="status">Status</label>
                            <select id="status" name="status" required class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ old('status', 'draft') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <section class="space-y-4 border-t border-gray-200 pt-6 dark:border-gray-700" aria-labelledby="invoice-items-heading">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 id="invoice-items-heading" class="text-base font-semibold text-gray-900 dark:text-white">Invoice items</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose products and quantities. Prices are taken from your product catalog.</p>
                            </div>
                            <button type="button" id="add-invoice-item" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3.5 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-blue-900 dark:bg-blue-950/50 dark:text-blue-300 dark:hover:bg-blue-950">
                                <span aria-hidden="true">+</span> Add item
                            </button>
                        </div>

                        <div id="invoice-items" class="space-y-3"></div>
                        <div class="flex items-center justify-between border-t border-gray-200 pt-4 dark:border-gray-700">
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">Invoice total</span>
                            <span id="invoice-total" class="text-xl font-bold text-gray-900 dark:text-white">0.00 TND</span>
                        </div>
                    </section>

                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex-1 lg:flex-none justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Invoice
                    </button>
                    <a href="{{ route('invoices.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors flex-1 lg:flex-none justify-center">
                        Cancel
                    </a>
                </div>

            </form>
        </div>

    </div>

    <template id="invoice-item-template">
        <div class="invoice-item rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[minmax(0,2fr)_minmax(6rem,0.7fr)_minmax(8rem,0.9fr)_minmax(8rem,0.9fr)_auto] lg:items-end">
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                    <select data-product required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white"></select>
                    <p data-stock class="text-xs text-gray-500 dark:text-gray-400">Choose a product</p>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                    <input data-quantity type="number" min="1" step="1" value="1" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit price</label>
                    <input data-unit-price type="text" readonly tabindex="-1" placeholder="0.00" class="w-full cursor-not-allowed rounded-lg border border-gray-200 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                </div>
                <div class="space-y-1.5">
                    <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Line total</span>
                    <p data-line-total class="rounded-lg bg-white px-3 py-2.5 text-sm font-bold text-gray-900 dark:bg-gray-800 dark:text-white">0.00 $</p>
                </div>
                <button type="button" data-remove class="inline-flex h-10 items-center justify-center rounded-lg px-3 text-sm font-semibold text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500/30 dark:text-red-400 dark:hover:bg-red-950/40" aria-label="Remove item">Remove</button>
            </div>
        </div>
    </template>

    <template id="invoice-product-options">
        <option value="">Select a product...</option>
        @foreach($products as $product)
            <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->quantity }}">{{ $product->name }}</option>
        @endforeach
    </template>

    <script>
        (() => {
            const container = document.getElementById('invoice-items');
            const rowTemplate = document.getElementById('invoice-item-template');
            const optionsTemplate = document.getElementById('invoice-product-options');
            const addButton = document.getElementById('add-invoice-item');
            const totalElement = document.getElementById('invoice-total');
            const initialItems = @js(old('items', [['product_id' => '', 'quantity' => 1]]));

            const cents = (amount) => {
                const [whole, fraction = ''] = String(amount || '0').split('.');
                return (Number(whole) * 100) + Number((fraction + '00').slice(0, 2));
            };
            const money = (value) => `${(value / 100).toFixed(2)} $`;

            function updateRows() {
                const rows = [...container.querySelectorAll('.invoice-item')];
                const selectedIds = rows.map((row) => row.querySelector('[data-product]').value).filter(Boolean);
                let totalCents = 0;

                rows.forEach((row, index) => {
                    const select = row.querySelector('[data-product]');
                    const quantity = row.querySelector('[data-quantity]');
                    const selected = select.selectedOptions[0];
                    const price = selected?.dataset.price || '';
                    const stock = Number(selected?.dataset.stock || 0);
                    const amount = Math.max(0, Number.parseInt(quantity.value || '0', 10) || 0);
                    const lineCents = cents(price) * amount;

                    select.name = `items[${index}][product_id]`;
                    quantity.name = `items[${index}][quantity]`;
                    quantity.max = stock || '';
                    row.querySelector('[data-unit-price]').value = price ? `${Number(price).toFixed(2)} TND` : '';
                    row.querySelector('[data-stock]').textContent = selected?.value ? `Available stock: ${stock}` : 'Choose a product';
                    row.querySelector('[data-line-total]').textContent = money(lineCents);
                    totalCents += lineCents;

                    [...select.options].forEach((option) => {
                        option.disabled = Boolean(option.value && option.value !== select.value && selectedIds.includes(option.value));
                    });
                    row.querySelector('[data-remove]').disabled = rows.length === 1;
                    row.querySelector('[data-remove]').classList.toggle('opacity-40', rows.length === 1);
                });

                totalElement.textContent = money(totalCents);
            }

            function addRow(item = {}) {
                const fragment = rowTemplate.content.cloneNode(true);
                const row = fragment.querySelector('.invoice-item');
                const select = row.querySelector('[data-product]');
                select.append(optionsTemplate.content.cloneNode(true));
                row.querySelector('[data-quantity]').value = item.quantity || 1;
                if (item.product_id) select.value = String(item.product_id);
                container.append(fragment);
                updateRows();
            }

            addButton.addEventListener('click', () => addRow());
            container.addEventListener('input', updateRows);
            container.addEventListener('change', updateRows);
            container.addEventListener('click', (event) => {
                if (event.target.closest('[data-remove]') && container.querySelectorAll('.invoice-item').length > 1) {
                    event.target.closest('.invoice-item').remove();
                    updateRows();
                }
            });

            initialItems.forEach((item) => addRow(item));
        })();
    </script>

@endsection