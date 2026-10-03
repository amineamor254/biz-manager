@extends('layouts.app')
@section('content')

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Clients</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage and organize your complete client database with all their information</p>
        </div>
        <a href="{{ route('clients.create') }}"
           class="inline-flex self-start sm:self-auto items-center gap-2 px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add New Client</span>
        </a>
    </div>

    <!-- Clients Table -->
    <div class="w-full min-w-0 max-w-full bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        @if($clients->count() > 0)
            <div class="hidden md:block">
                <table class="w-full min-w-[640px]">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Client Name</th>
                        <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Email Address</th>
                        <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Phone</th>
                        <th class="text-right px-6 py-4 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($clients as $client)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">

                        <!-- Client Name -->
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/40 dark:to-blue-800/30 flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">
                                        {{ substr($client->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $client->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">ID: #{{ str_pad($client->id, 4, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Email -->
                        <td class="px-6 py-5">
                            <a href="mailto:{{ $client->email }}"
                               class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors hover:underline text-sm">
                                {{ $client->email }}
                            </a>
                        </td>

                        <!-- Phone -->
                        <td class="px-6 py-5">
                            <a href="tel:{{ $client->phone }}"
                               class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 font-medium transition-colors hover:underline text-sm">
                                {{ $client->phone }}
                            </a>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('clients.edit', $client) }}"
                                   class="inline-flex items-center gap-1 rounded-lg px-2 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline">
                                   
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
                @foreach($clients as $client)
                    <div class="min-w-0 p-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/40 dark:to-blue-800/30 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-blue-600 dark:text-blue-400">
                                    {{ substr($client->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <p class="break-words font-bold text-gray-900 dark:text-white">{{ $client->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">ID: #{{ str_pad($client->id, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>

                        <div class="mt-3 space-y-2 text-sm">
                            <a href="mailto:{{ $client->email }}"
                               class="block break-all font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $client->email }}
                            </a>
                            @if($client->phone)
                                <a href="tel:{{ $client->phone }}"
                                   class="block break-words font-medium text-gray-700 dark:text-gray-300 hover:underline">
                                    {{ $client->phone }}
                                </a>
                            @endif
                        </div>

                        <div class="mt-3 flex items-center gap-2">
                            <a href="{{ route('clients.edit', $client) }}"
                               class="inline-flex items-center gap-1 rounded-lg px-2 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline">
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
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16 px-4">
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4 text-gray-400 dark:text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM16 11a4 4 0 11-8 0 4 4 0 018 0zM9 20H4v-2a6 6 0 0112 0v2H9z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">No clients found</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center max-w-md mb-6">Start by adding your first client to get started. You can manage all their information and track your business relationships.</p>
                <a href="{{ route('clients.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Your First Client
                </a>
            </div>
        @endif
    </div>

@endsection