<!-- Desktop Sidebar -->
<aside class="hidden shrink-0 w-64 h-screen sticky top-0 z-50
              flex-col md:flex
              bg-gradient-to-b from-[#07152E] via-[#041227] to-[#020B17]
              shadow-2xl border-r border-blue-900/30">

    <!-- Logo -->
    <div class="h-20 shrink-0 px-6 flex items-center border-b border-white/10">
        <a href="{{ route('dashboard') }}" class="flex items-center">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-700 text-lg font-bold text-white">B</span>
            <span class="ml-3 text-2xl font-bold text-white">BizManager</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-navigation min-h-0 flex-1 overflow-x-hidden overflow-y-auto px-4 py-6 space-y-1">

        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300
           {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/30' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('clients.index') }}"
              class="flex items-center justify-between px-4 py-3 rounded-xl transition {{ request()->routeIs('clients.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <div class="flex items-center gap-3">
               <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-3A4.5 4.5 0 0 0 4 18.5V20m14-9a3 3 0 1 0-2.5-4.7M20 20v-1.5a4.5 4.5 0 0 0-3-4.25M13.5 7.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                <span>Clients</span>
            </div>
           
        </a>

        <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('products.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m8 4v10"/>
            </svg>
            <span>Products</span>
        </a>

        <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('orders.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Orders</span>
        </a>

        <a href="{{ route('invoices.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('invoices.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
             <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M9 13h6m-6 4h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Invoices</span>
        </a>

           <a href="{{ route('roles-permissions.index') }}"
           class="flex items-center justify-between px-4 py-3 rounded-xl transition {{ request()->routeIs('roles-permissions.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-4 0-7 2-7 4s3 4 7 4 7-2 7-4-3-4-7-4z"/>
                </svg>
                <span>Roles & Permissions</span>
            </div>
           
        </a>

        {{-- Payments - reserved for V2 --}}

        {{-- <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition">
            <span>Payments</span>
        </a>--}}

        <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('expenses.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 7h14M7 4h10l1 16H6L7 4Zm3 7h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Expenses</span>
        </a>

        {{-- Categories - reserved for V2 --}}

        {{-- <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition">
            <span>Categories</span>
        </a>--}}

        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('reports.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <span>Reports</span>
        </a>

        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('profile.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m19.4 15 .1.1a1.8 1.8 0 1 1-2.5 2.5l-.1-.1a1.8 1.8 0 0 0-3 .9v.2a1.8 1.8 0 1 1-3.6 0v-.2a1.8 1.8 0 0 0-3-.9l-.1.1a1.8 1.8 0 1 1-2.5-2.5l.1-.1a1.8 1.8 0 0 0-.9-3h-.2a1.8 1.8 0 1 1 0-3.6h.2a1.8 1.8 0 0 0 .9-3l-.1-.1a1.8 1.8 0 1 1 2.5-2.5l.1.1a1.8 1.8 0 0 0 3-.9v-.2a1.8 1.8 0 1 1 3.6 0v.2a1.8 1.8 0 0 0 3 .9l.1-.1a1.8 1.8 0 1 1 2.5 2.5l-.1.1a1.8 1.8 0 0 0 .9 3h.2a1.8 1.8 0 1 1 0 3.6h-.2a1.8 1.8 0 0 0-.9 3Z" />
                            </svg>
            <span>Settings</span>
        </a>

    </nav>

    <!-- Footer -->
    <div class="shrink-0 p-4 border-t border-white/10 space-y-4">

        <!-- Dark Mode Toggle -->
        <div x-data="{ darkMode: window.currentTheme === 'dark' }"
             class="bg-white/5 border border-white/10 rounded-xl p-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                    </svg>
                    <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364l-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0L16.95 7.05M7.05 16.95l-1.414 1.414M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                    </svg>
                    <span x-show="!darkMode" class="text-sm text-white">Dark Mode</span>
                    <span x-show="darkMode" class="text-sm text-white">Light Mode</span>
                </div>
                <button
                    @click="darkMode = !darkMode; window.currentTheme = darkMode ? 'dark' : 'light'; localStorage.setItem('app-theme', window.currentTheme); document.documentElement.classList.toggle('dark', darkMode);"
                    class="relative w-11 h-6 rounded-full transition"
                    :class="darkMode ? 'bg-blue-600' : 'bg-gray-500'">
                    <span class="absolute top-1 h-4 w-4 bg-white rounded-full transition-all"
                          :class="darkMode ? 'right-1' : 'left-1'"></span>
                </button>
            </div>
        </div>

        <!-- User Card -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-3 flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                <p class="text-gray-400 text-xs truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>

    </div>

</aside>

<!-- Mobile Sidebar -->
<div id="mobile-sidebar" x-show="sidebarOpen"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     @click.away="sidebarOpen = false"
    class="fixed top-0 left-0 w-64 h-full z-50 flex flex-col md:hidden
            bg-gradient-to-b from-[#07152E] via-[#041227] to-[#020B17]
            shadow-2xl border-r border-blue-900/30">

    <!-- Logo -->
    <div class="h-20 shrink-0 px-6 flex items-center border-b border-white/10">
        <a href="{{ route('dashboard') }}" class="flex items-center">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                </svg>
            </div>
            <span class="ml-3 text-2xl font-bold text-white">BizManager</span>
            <span class="w-3 h-3 bg-blue-500 rounded-full ml-2"></span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-navigation min-h-0 flex-1 overflow-x-hidden overflow-y-auto px-4 py-6 space-y-1">

        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300
           {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/30' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('clients.index') }}" class="flex items-center justify-between px-4 py-3 rounded-xl transition {{ request()->routeIs('clients.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.88 6.196"/>
                </svg>
                <span>Clients</span>
            </div>
        </a>

        <a href="{{ route('roles-permissions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('roles-permissions.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <span>Roles & Permissions</span>
        </a>
        <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('products.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <span>Products</span>
        </a>
        <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('orders.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <span>Orders</span>
        </a>
    
        <a href="{{ route('invoices.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('invoices.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <span>Invoices</span>
        </a>
        {{-- Payments - reserved for V2 --}}
        {{-- <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition">
            <span>Payments</span>
        </a> --}}
        <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('expenses.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <span>Expenses</span>
        </a>
        {{-- Categories - reserved for V2 --}}
         {{--<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition">
            <span>Categories</span>     
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white transition">
            <span>Categories</span>
        </a>--}}
        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('reports.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <span>Reports</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('profile.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <span>Settings</span>
        </a>

    </nav>

    <!-- Footer -->
    <div class="shrink-0 p-4 border-t border-white/10 space-y-4">
        <!-- Dark Mode Toggle -->
        <div x-data="{ darkMode: window.currentTheme === 'dark' }"
             class="bg-white/5 border border-white/10 rounded-xl p-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                    </svg>
                    <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364l-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0L16.95 7.05M7.05 16.95l-1.414 1.414M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                    </svg>
                    <span x-show="!darkMode" class="text-sm text-white">Dark Mode</span>
                    <span x-show="darkMode" class="text-sm text-white">Light Mode</span>
                </div>
                <button
                    type="button"
                    aria-label="Toggle dark mode"
                    @click="darkMode = !darkMode; window.currentTheme = darkMode ? 'dark' : 'light'; localStorage.setItem('app-theme', window.currentTheme); document.documentElement.classList.toggle('dark', darkMode);"
                    class="relative w-11 h-6 rounded-full transition"
                    :class="darkMode ? 'bg-blue-600' : 'bg-gray-500'">
                    <span class="absolute top-1 h-4 w-4 bg-white rounded-full transition-all"
                          :class="darkMode ? 'right-1' : 'left-1'"></span>
                </button>
            </div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-xl p-3 flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                <p class="text-gray-400 text-xs truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>

</div>

<!-- Mobile Overlay -->
<div
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen = false"
    class="fixed inset-0 bg-black/60 z-40 md:hidden">
</div>
