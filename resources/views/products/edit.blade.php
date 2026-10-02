@extends('layouts.app')
@section('content')

    <div class="max-w-3xl mx-auto">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-sky-100 to-sky-50 dark:from-sky-900/40 dark:to-sky-800/30 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4m0 0L4 7m16 0l-8 4m0 0l8 4m-8-4v10m0 0l-8-4m0 0l8-4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Product</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update product information and inventory</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-8 lg:p-10">
            <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">
                        Product Details
                    </h3>

                    <!-- Product Name -->
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="name">
                            Product Name *
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name', $product->name) }}"
                               required
                               class="w-full px-4 py-2.5 rounded-lg border text-sm
                                      bg-white dark:bg-gray-900
                                      text-gray-900 dark:text-white
                                      placeholder-gray-400 dark:placeholder-gray-500
                                      transition-colors
                                      @error('name')
                                          border-red-500 focus:ring-red-500
                                      @else
                                          border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500
                                      @enderror
                                      focus:outline-none focus:ring-2 focus:ring-opacity-30">
                        @error('name')
                            <p class="flex items-center gap-1.5 text-sm text-red-600 dark:text-red-400 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Price & Quantity Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                        <!-- Price -->
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="price">
                                Price *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 font-semibold text-sm">$</span>
                                <input type="number"
                                       id="price"
                                       name="price"
                                       step="0.01"
                                       value="{{ old('price', $product->price) }}"
                                       required
                                       class="w-full pl-8 pr-4 py-2.5 rounded-lg border text-sm
                                              bg-white dark:bg-gray-900
                                              text-gray-900 dark:text-white
                                              placeholder-gray-400 dark:placeholder-gray-500
                                              transition-colors
                                              @error('price')
                                                  border-red-500 focus:ring-red-500
                                              @else
                                                  border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500
                                              @enderror
                                              focus:outline-none focus:ring-2 focus:ring-opacity-30">
                            </div>
                            @error('price')
                                <p class="flex items-center gap-1.5 text-sm text-red-600 dark:text-red-400 mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Quantity -->
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="quantity">
                                Available Stock *
                            </label>
                            <input type="number"
                                   id="quantity"
                                   name="quantity"
                                   value="{{ old('quantity', $product->quantity) }}"
                                   required
                                   class="w-full px-4 py-2.5 rounded-lg border text-sm
                                          bg-white dark:bg-gray-900
                                          text-gray-900 dark:text-white
                                          placeholder-gray-400 dark:placeholder-gray-500
                                          transition-colors
                                          @error('quantity')
                                              border-red-500 focus:ring-red-500
                                          @else
                                              border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500
                                          @enderror
                                          focus:outline-none focus:ring-2 focus:ring-opacity-30">
                            @error('quantity')
                                <p class="flex items-center gap-1.5 text-sm text-red-600 dark:text-red-400 mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors flex-1 lg:flex-none justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Product
                    </button>
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors flex-1 lg:flex-none justify-center">
                        Cancel
                    </a>
                </div>

            </form>
        </div>

    </div>

@endsection