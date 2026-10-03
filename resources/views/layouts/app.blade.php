<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'BizManager') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
   
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Theme initialization script (runs before rendering) -->
    <script>
        // Initialize theme immediately to prevent flash of wrong theme
    (function() {
        const THEME_KEY = 'app-theme';
        const DARK_CLASS = 'dark';
        const theme = localStorage.getItem(THEME_KEY);

        if (theme === 'dark') {
            document.documentElement.classList.add(DARK_CLASS);
            window.currentTheme = 'dark';
        } else {
            document.documentElement.classList.remove(DARK_CLASS);
            window.currentTheme = 'light';
        }
    })();
    /* Fullscreen responsive fixes */
:-webkit-full-screen body,
:-moz-full-screen body,
:fullscreen body {
    overflow: auto !important;
}

:-webkit-full-screen .flex.h-screen,
:-moz-full-screen .flex.h-screen,
:fullscreen .flex.h-screen {
    height: 100vh !important;
    width: 100vw !important;
}

:-webkit-full-screen aside,
:-moz-full-screen aside,
:fullscreen aside {
    height: 100vh !important;
    position: sticky !important;
    top: 0 !important;
}

:-webkit-full-screen header,
:-moz-full-screen header,
:fullscreen header {
    position: sticky !important;
    top: 0 !important;
    z-index: 40 !important;
    width: 100% !important;
}

:-webkit-full-screen main:not(.dashboard-page),
:-moz-full-screen main:not(.dashboard-page),
:fullscreen main:not(.dashboard-page) {
    flex: 1 !important;
    overflow-y: auto !important;
    height: calc(100vh - 4rem) !important;
}
    </script>
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">

<div class="{{ request()->routeIs('dashboard') ? 'flex min-h-screen' : 'flex h-screen overflow-hidden' }}">

    @include('layouts.sidebar')

    <div class="flex-1 min-w-0 flex flex-col {{ request()->routeIs('dashboard') ? '' : 'overflow-hidden' }}">
        @include('layouts.header')

        <main class="min-w-0 flex-1 bg-gray-50 p-4 dark:bg-gray-900 sm:p-6 {{ request()->routeIs('dashboard') ? 'dashboard-page overflow-x-clip pb-20 sm:pb-24' : 'overflow-x-hidden overflow-y-auto' }}">
            @if(isset($header))
                <div class="mb-6">{{ $header }}</div>
            @endif

            @if(isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </main>
    </div>

</div>

@if(request()->routeIs('dashboard'))
    <button
        type="button"
        x-data="{ visible: false }"
        x-init="visible = window.scrollY > 300; window.addEventListener('scroll', () => visible = window.scrollY > 300, { passive: true })"
        x-show="visible"
        style="display: none"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-5 right-5 z-[60] inline-flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-700 shadow-lg transition hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700 sm:bottom-6 sm:right-6"
        aria-label="Back to top"
    >
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 19V5m-7 7 7-7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </button>
@endif

</body>
</html>
