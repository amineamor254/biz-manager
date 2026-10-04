@extends('layouts.app')
@section('content')

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Products</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your complete product catalog, pricing, and inventory levels</p>
        </div>
        <a href="{{ route('products.create') }}"
           class="inline-flex self-start sm:self-auto items-center gap-2 px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add New Product</span>
        </a>
    </div>

    <!-- Products Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-x-auto">
        @if($products->count() > 0)
            <div class="hidden md:block">
                <table class="w-full min-w-[720px]">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Product</th>
                        <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Price</th>
                        <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Stock</th>
                        <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Status</th>
                        <th class="text-right px-6 py-4 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($products as $product)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">

                        <!-- Product -->
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-sky-100 to-sky-50 dark:from-sky-900/40 dark:to-sky-800/30 flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4m0 0L4 7m16 0l-8 4m0 0l8 4m-8-4v10m0 0l-8-4m0 0l8-4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Product ID: {{ str_pad($products->count() - $loop->iteration + 1, 4, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Price -->
                        <td class="px-6 py-5">
                            <span class="font-bold text-gray-900 dark:text-white text-sm">${{ number_format($product->price, 2) }}</span>
                        </td>

                        <!-- Quantity -->
                                        <td class="px-6 py-5">
                        <span class="font-bold text-gray-900 dark:text-white text-sm">
                            {{ $product->quantity }}
                        </span>
                    </td>

                        <!-- Status -->
                                    <td class="px-6 py-5">
                        @if($product->quantity <= 0)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                Out of Stock
                            </span>
                        @elseif($product->quantity <= 5)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                Low Stock
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                In Stock
                            </span>
                        @endif
                    </td>

                        <!-- Actions -->
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('products.edit', $product) }}"
                                   class="inline-flex items-center gap-1 rounded-lg px-2 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                                    @csrf
                                    
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center rounded-lg px-2 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
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

            <div class="divide-y divide-gray-100 dark:divide-gray-700 md:hidden">
                @foreach ($products as $product)
                    <div class="min-w-0 p-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-sky-100 to-sky-50 dark:from-sky-900/40 dark:to-sky-800/30 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4m0 0L4 7m16 0l-8 4m0 0l8 4m-8-4v10m0 0l-8-4m0 0l8-4" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="break-words font-bold text-gray-900 dark:text-white">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Product ID: {{ str_pad($products->count() - $loop->iteration + 1, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Price</p>
                                <p class="mt-1 font-bold text-gray-900 dark:text-white">${{ number_format($product->price, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Stock</p>
                                <p class="mt-1 font-bold text-gray-900 dark:text-white">{{ $product->quantity }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</p>
                                <div class="mt-1">
                                    @if($product->quantity <= 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                            Out of Stock
                                        </span>
                                    @elseif($product->quantity <= 5)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                            Low Stock
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                            In Stock
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center gap-2">
                            <a href="{{ route('products.edit', $product) }}"
                               class="inline-flex min-h-10 items-center gap-1 rounded-lg px-3 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex min-h-10 items-center rounded-lg px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16 px-4">
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4 text-gray-400 dark:text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4m0 0L4 7m16 0l-8 4m0 0l8 4m-8-4v10m0 0l-8-4m0 0l8-4" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">No products found</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center max-w-md mb-6">Create your first product to manage your inventory and start selling.</p>
                <a href="{{ route('products.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Your First Product
                </a>
            </div>
        @endif
    </div>

@endsection