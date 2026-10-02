<header class="sticky top-0 z-40 h-16 border-b border-slate-200 bg-white text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
    <div class="flex h-full min-w-0 items-center justify-between gap-3 px-3 sm:px-5 lg:px-6">
        <div class="flex min-w-0 flex-1 items-center">
            <button type="button" @click="sidebarOpen = !sidebarOpen" :aria-expanded="sidebarOpen.toString()" aria-controls="mobile-sidebar" aria-label="Toggle navigation" class="mr-2 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:text-slate-300 dark:hover:bg-slate-700 sm:mr-3 sm:h-10 sm:w-10 md:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <label for="header-search" class="sr-only">Search</label>
            <div class="relative hidden w-full max-w-lg md:block">
                <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
                <input id="header-search" type="search" placeholder="Search anything..." class="h-10 w-full rounded-lg border border-slate-200 bg-slate-50 pl-10 pr-20 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-blue-400 dark:focus:bg-slate-900">
                <span class="absolute right-2.5 top-1/2 hidden -translate-y-1/2 rounded border border-slate-200 bg-white px-1.5 py-0.5 text-[11px] font-medium text-slate-500 lg:inline-flex dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Ctrl + /</span>
            </div>
        </div>

        <nav class="flex shrink-0 items-center gap-0 sm:gap-2" aria-label="Header actions">
            <button type="button" aria-label="Notifications" title="Notifications" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white sm:h-10 sm:w-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4a2 2 0 0 1-.6-1.4V11a6 6 0 0 0-4-5.7V5a2 2 0 1 0-4 0v.3A6 6 0 0 0 6 11v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9" />
                </svg>
            </button>

            <button id="fullscreen-toggle" type="button" onclick="toggleFullscreen()" aria-label="Enter fullscreen" title="Enter fullscreen" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white sm:h-10 sm:w-10">
                <svg id="fullscreen-enter-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 3H5a2 2 0 0 0-2 2v3m16-5h-3m3 0v3M3 16v3a2 2 0 0 0 2 2h3m11-5v3a2 2 0 0 1-2 2h-3" />
                </svg>
                <svg id="fullscreen-exit-icon" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
                </svg>
            </button>

            <div x-data="{
                open: false,
                currency: 'TND',
                currencies: [
                    { code: 'TND', name: 'Tunisian Dinar', flag: '🇹🇳' },
                    { code: 'USD', name: 'US Dollar', flag: '🇺🇸' },
                    { code: 'EUR', name: 'Euro', flag: '🇪🇺' },
                    { code: 'GBP', name: 'British Pound', flag: '🇬🇧' },
                ],
                init() {
                    const savedCurrency = localStorage.getItem('app-currency');
                    if (this.currencies.some(option => option.code === savedCurrency)) {
                        this.currency = savedCurrency;
                    }
                },
                selectCurrency(code) {
                    this.currency = code;
                    localStorage.setItem('app-currency', code);
                    this.open = false;
                }
            }" class="relative">
                <button type="button" @click="open = !open" @keydown.escape.window="open = false" :aria-expanded="open.toString()" aria-haspopup="true" :aria-label="'Select currency, currently ' + currency" class="inline-flex h-10 items-center gap-1.5 rounded-lg px-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:text-slate-200 dark:hover:bg-slate-700 sm:gap-2 sm:px-2.5">
                    <span class="hidden leading-none sm:inline" aria-hidden="true" x-text="currencies.find(option => option.code === currency).flag"></span>
                    <span class="tabular-nums" x-text="currency"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.09 1.03l-4.25 4.5a.75.75 0 0 1-1.09 0l-4.25-4.5a.75.75 0 0 1 .02-1.05Z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" style="display: none" @click.away="open = false" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute right-0 mt-2 w-60 overflow-hidden rounded-lg border border-slate-200 bg-white p-1 shadow-lg dark:border-slate-700 dark:bg-slate-800">
                    <template x-for="option in currencies" :key="option.code">
                        <button type="button" @click="selectCurrency(option.code)" :aria-pressed="(currency === option.code).toString()" :class="currency === option.code ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700'" class="flex w-full items-center gap-3 rounded-md px-3 py-2.5 text-left text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-500">
                            <span class="text-base" aria-hidden="true" x-text="option.flag"></span>
                            <span class="flex-1 font-medium" x-text="option.code"></span>
                            <span class="text-slate-500 dark:text-slate-400" x-text="option.name"></span>
                            <svg x-show="currency === option.code" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-blue-600 dark:text-blue-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.2 7.25a1 1 0 0 1-1.42.003L3.296 9.2a1 1 0 0 1 1.408-1.42l4.086 4.05 6.5-6.545a1 1 0 0 1 1.414.006Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </template>
                </div>
            </div>

            <div x-data="{ open: false }" class="relative">
                <button type="button" @click="open = !open" @keydown.escape.window="open = false" :aria-expanded="open.toString()" aria-haspopup="true" aria-label="Language: English" class="inline-flex h-10 items-center gap-2 rounded-lg px-2.5 text-slate-700 transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:text-slate-200 dark:hover:bg-slate-700 sm:px-3">
                    <span class="text-base leading-none" aria-hidden="true">🇺🇸</span>
                    <span class="hidden text-sm font-medium sm:inline">English</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="hidden h-4 w-4 text-slate-400 sm:block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.09 1.03l-4.25 4.5a.75.75 0 0 1-1.09 0l-4.25-4.5a.75.75 0 0 1 .02-1.05Z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" style="display: none" @click.away="open = false" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute right-0 mt-2 w-48 overflow-hidden rounded-lg border border-slate-200 bg-white p-1 shadow-lg dark:border-slate-700 dark:bg-slate-800">
                    <button type="button" aria-current="true" class="flex w-full items-center gap-3 rounded-md bg-blue-50 px-3 py-2.5 text-left text-sm font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
                        <span aria-hidden="true">🇺🇸</span><span class="flex-1">English</span><span aria-hidden="true" class="text-blue-600 dark:text-blue-300">✓</span>
                    </button>
                    <button type="button" class="flex w-full items-center gap-3 rounded-md px-3 py-2.5 text-left text-sm text-slate-700 transition hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-500 dark:text-slate-200 dark:hover:bg-slate-700">
                        <span aria-hidden="true">🇫🇷</span><span>Français</span>
                    </button>
                    <button type="button" class="flex w-full items-center gap-3 rounded-md px-3 py-2.5 text-left text-sm text-slate-700 transition hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-500 dark:text-slate-200 dark:hover:bg-slate-700">
                        <span aria-hidden="true">🇹🇳</span><span>العربية</span>
                    </button>
                </div>
            </div>

            <div x-data="{ open: false }" class="relative ml-1 border-l border-slate-200 pl-2 dark:border-slate-700 sm:pl-3">
                <button type="button" @click="open = !open" @keydown.escape.window="open = false" :aria-expanded="open.toString()" aria-haspopup="true" aria-label="Open user menu" class="inline-flex h-10 items-center gap-2 rounded-lg px-1.5 text-left transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:hover:bg-slate-700 sm:px-2">
                    <span role="img" aria-label="Avatar for {{ auth()->user()->name }}" class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-700 ring-1 ring-blue-200 dark:bg-blue-500/20 dark:text-blue-200 dark:ring-blue-400/30">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="hidden min-w-0 text-left lg:block">
                        <span class="block max-w-32 truncate text-sm font-medium leading-4 text-slate-800 dark:text-slate-100">{{ auth()->user()->name }}</span>
                        <span class="mt-0.5 block text-xs leading-4 text-slate-500 dark:text-slate-400">Account</span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="hidden h-4 w-4 text-slate-400 lg:block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.09 1.03l-4.25 4.5a.75.75 0 0 1-1.09 0l-4.25-4.5a.75.75 0 0 1 .02-1.05Z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" style="display: none" @click.away="open = false" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute right-0 mt-2 w-72 max-w-[calc(100vw-1.5rem)] overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-800">
                    <div class="border-b border-slate-200 px-4 py-4 dark:border-slate-700">
                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">{{ auth()->user()->name }}</p>
                        <p class="mt-1 truncate text-sm text-slate-500 dark:text-slate-400">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="p-1.5">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-500 dark:text-slate-200 dark:hover:bg-slate-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m19.4 15 .1.1a1.8 1.8 0 1 1-2.5 2.5l-.1-.1a1.8 1.8 0 0 0-3 .9v.2a1.8 1.8 0 1 1-3.6 0v-.2a1.8 1.8 0 0 0-3-.9l-.1.1a1.8 1.8 0 1 1-2.5-2.5l.1-.1a1.8 1.8 0 0 0-.9-3h-.2a1.8 1.8 0 1 1 0-3.6h.2a1.8 1.8 0 0 0 .9-3l-.1-.1a1.8 1.8 0 1 1 2.5-2.5l.1.1a1.8 1.8 0 0 0 3-.9v-.2a1.8 1.8 0 1 1 3.6 0v.2a1.8 1.8 0 0 0 3 .9l.1-.1a1.8 1.8 0 1 1 2.5 2.5l-.1.1a1.8 1.8 0 0 0 .9 3h.2a1.8 1.8 0 1 1 0 3.6h-.2a1.8 1.8 0 0 0-.9 3Z" />
                            </svg>
                            <span>Profile Settings</span>
                        </a>
                    </div>
                    <div class="border-t border-slate-200 p-1.5 dark:border-slate-700">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 rounded-md px-3 py-2.5 text-left text-sm text-red-600 transition hover:bg-red-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-red-500 dark:text-red-400 dark:hover:bg-red-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 8.25V5.5A2.25 2.25 0 0 0 13.5 3.25h-7A2.25 2.25 0 0 0 4.25 5.5v13A2.25 2.25 0 0 0 6.5 20.75h7a2.25 2.25 0 0 0 2.25-2.25v-2.75M10 12h10m0 0-3.5-3.5M20 12l-3.5 3.5" />
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>

<script>
function updateFullscreenButton() {
    const fullscreenActive = Boolean(document.fullscreenElement);
    const button = document.getElementById('fullscreen-toggle');
    const enterIcon = document.getElementById('fullscreen-enter-icon');
    const exitIcon = document.getElementById('fullscreen-exit-icon');

    if (!button || !enterIcon || !exitIcon) return;

    enterIcon.classList.toggle('hidden', fullscreenActive);
    exitIcon.classList.toggle('hidden', !fullscreenActive);
    button.setAttribute('aria-label', fullscreenActive ? 'Exit fullscreen' : 'Enter fullscreen');
    button.setAttribute('title', fullscreenActive ? 'Exit fullscreen' : 'Enter fullscreen');
}

function toggleFullscreen() {
    if (document.fullscreenElement) {
        document.exitFullscreen().catch(error => console.error('Fullscreen error:', error));
    } else if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen().catch(error => console.error('Fullscreen error:', error));
    }
}

if (!window.headerFullscreenListenerRegistered) {
    document.addEventListener('fullscreenchange', updateFullscreenButton);
    window.headerFullscreenListenerRegistered = true;
}

updateFullscreenButton();
</script>