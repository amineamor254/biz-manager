<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f8fafc">
    <meta name="description" content="BizManager brings clients, products, invoices, orders, expenses, and reports together in one workspace.">
    <title>BizManager</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="min-h-screen overflow-hidden">
        <header class="border-b border-slate-200/80 bg-white/90 dark:border-slate-800 dark:bg-slate-950/90">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
                <a href="/" class="flex min-w-0 items-center gap-3" aria-label="BizManager home">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-700 text-lg font-bold text-white">B</span>
                    <span class="min-w-0"><span class="block text-base font-bold text-slate-950 dark:text-white">BizManager</span><span class="hidden text-xs text-slate-500 dark:text-slate-400 sm:block">Your business, in better order</span></span>
                </a>
                <nav class="flex shrink-0 items-center gap-2 sm:gap-4" aria-label="Account navigation">
                    @auth
                        <a href="{{ route('invoices.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:text-blue-700 dark:text-slate-300 dark:hover:text-blue-400">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:text-blue-700 dark:text-slate-300 dark:hover:text-blue-400">Login</a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950">Get Started</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            <section class="relative isolate border-b border-slate-200 bg-gradient-to-br from-slate-50 via-sky-50 to-white dark:border-slate-800 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
                <div class="absolute inset-y-0 right-0 -z-10 hidden w-2/5 border-l border-blue-100/70 bg-[linear-gradient(135deg,rgba(219,234,254,0.28)_25%,transparent_25%,transparent_50%,rgba(219,234,254,0.28)_50%,rgba(219,234,254,0.28)_75%,transparent_75%,transparent)] bg-[length:32px_32px] dark:border-slate-800 dark:opacity-10 lg:block"></div>
                <div class="mx-auto grid max-w-7xl items-center gap-12 px-5 py-16 sm:px-8 sm:py-20 lg:grid-cols-[0.9fr_1.1fr] lg:gap-14 lg:py-24">
                    <div class="max-w-xl">
                        <p class="mb-5 inline-flex items-center gap-2 text-sm font-semibold text-blue-800 dark:text-blue-300"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Business, in better order</p>
                        <h1 class="text-4xl font-bold leading-tight text-slate-950 sm:text-5xl lg:text-[3.5rem] dark:text-white">Run your business. <span class="text-blue-700 dark:text-blue-400">Keep the whole picture.</span></h1>
                        <p class="mt-6 max-w-lg text-base leading-7 text-slate-600 sm:text-lg sm:leading-8 dark:text-slate-300">BizManager brings your clients, products, invoices, orders, expenses, and reports together in one simple, organized workspace.</p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            @auth
                                <a href="{{ route('invoices.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-lg bg-blue-700 px-6 py-3 text-sm font-semibold text-white shadow-md shadow-blue-900/15 transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950">Go to Dashboard <span class="ml-2" aria-hidden="true">→</span></a>
                            @else
                                <a href="{{ route('register') }}" class="inline-flex min-h-12 items-center justify-center rounded-lg bg-blue-700 px-6 py-3 text-sm font-semibold text-white shadow-md shadow-blue-900/15 transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950">Get Started <span class="ml-2" aria-hidden="true">→</span></a>
                                <a href="{{ route('login') }}" class="inline-flex min-h-12 items-center justify-center rounded-lg border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">Login</a>
                            @endauth
                        </div>
                        <p class="mt-5 text-sm text-slate-500 dark:text-slate-400">A practical home for the records your business runs on.</p>
                    </div>

                    <div class="mx-auto w-full max-w-2xl lg:ml-auto">
                        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/30">
                            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-slate-800 sm:px-5">
                                <div class="flex gap-1.5" aria-hidden="true"><span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span></div>
                                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">BIZMANAGER WORKSPACE</span>
                                <span class="h-7 w-7 rounded-lg bg-blue-100 text-center text-sm font-bold leading-7 text-blue-800 dark:bg-blue-950 dark:text-blue-300" aria-hidden="true">B</span>
                            </div>
                            <div class="grid min-h-[300px] grid-cols-[3.5rem_1fr] sm:grid-cols-[11rem_1fr]">
                                <aside class="border-r border-slate-200 bg-slate-50 p-2 dark:border-slate-800 dark:bg-slate-950/60 sm:p-3" aria-label="Workspace modules">
                                    <p class="mb-3 hidden px-2 text-[10px] font-bold uppercase text-slate-400 sm:block">Workspace</p>
                                    <div class="space-y-1 text-xs font-medium">
                                        <span class="flex h-9 items-center justify-center rounded-lg bg-blue-100 text-blue-800 sm:justify-start sm:gap-2 sm:px-2 dark:bg-blue-950 dark:text-blue-300"><span aria-hidden="true">▦</span><span class="hidden sm:inline">Overview</span></span>
                                        <span class="flex h-9 items-center justify-center rounded-lg text-slate-600 sm:justify-start sm:gap-2 sm:px-2 dark:text-slate-400"><span aria-hidden="true">♙</span><span class="hidden sm:inline">Clients</span></span>
                                        <span class="flex h-9 items-center justify-center rounded-lg text-slate-600 sm:justify-start sm:gap-2 sm:px-2 dark:text-slate-400"><span aria-hidden="true">◇</span><span class="hidden sm:inline">Products &amp; Stock</span></span>
                                        <span class="flex h-9 items-center justify-center rounded-lg text-slate-600 sm:justify-start sm:gap-2 sm:px-2 dark:text-slate-400"><span aria-hidden="true">▤</span><span class="hidden sm:inline">Invoices</span></span>
                                        <span class="flex h-9 items-center justify-center rounded-lg text-slate-600 sm:justify-start sm:gap-2 sm:px-2 dark:text-slate-400"><span aria-hidden="true">▣</span><span class="hidden sm:inline">Orders</span></span>
                                        <span class="flex h-9 items-center justify-center rounded-lg text-slate-600 sm:justify-start sm:gap-2 sm:px-2 dark:text-slate-400"><span aria-hidden="true">◷</span><span class="hidden sm:inline">Expenses</span></span>
                                        <span class="flex h-9 items-center justify-center rounded-lg text-slate-600 sm:justify-start sm:gap-2 sm:px-2 dark:text-slate-400"><span aria-hidden="true">▥</span><span class="hidden sm:inline">Reports</span></span>
                                    </div>
                                </aside>
                                <div class="min-w-0 p-4 sm:p-6">
                                    <p class="text-xs font-semibold uppercase text-blue-700 dark:text-blue-400">Your workspace</p>
                                    <h2 class="mt-2 text-xl font-bold text-slate-950 sm:text-2xl dark:text-white">Everything has its place.</h2>
                                    <p class="mt-2 max-w-sm text-sm leading-6 text-slate-600 dark:text-slate-400">Move between the everyday parts of your business from one clear starting point.</p>
                                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                        <div class="rounded-lg border border-slate-200 p-4 dark:border-slate-800"><span class="mb-4 block h-8 w-8 rounded-lg bg-blue-100 dark:bg-blue-950"></span><span class="block text-sm font-semibold text-slate-800 dark:text-slate-200">Clients &amp; products</span><span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">Keep core business details organized.</span></div>
                                        <div class="rounded-lg border border-slate-200 p-4 dark:border-slate-800"><span class="mb-4 block h-8 w-8 rounded-lg bg-emerald-100 dark:bg-emerald-950"></span><span class="block text-sm font-semibold text-slate-800 dark:text-slate-200">Orders &amp; invoices</span><span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">Keep sales records together.</span></div>
                                        <div class="rounded-lg border border-slate-200 p-4 dark:border-slate-800 sm:col-span-2"><span class="mb-4 block h-8 w-8 rounded-lg bg-amber-100 dark:bg-amber-950"></span><span class="block text-sm font-semibold text-slate-800 dark:text-slate-200">Expenses &amp; reports</span><span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">Review business costs and useful summaries.</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 text-center text-xs text-slate-500 dark:text-slate-400">A workspace preview. Your records stay your own.</p>
                    </div>
                </div>
            </section>

            <section id="features" class="bg-white py-16 sm:py-20 dark:bg-slate-950">
                <div class="mx-auto max-w-7xl px-5 sm:px-8">
                    <div class="max-w-2xl">
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-700 dark:text-blue-400">The essentials</p>
                        <h2 class="mt-3 text-3xl font-bold text-slate-950 sm:text-4xl dark:text-white">Your business, organized around the work.</h2>
                        <p class="mt-4 text-base leading-7 text-slate-600 dark:text-slate-400">The tools you need for everyday business records, brought into one place.</p>
                    </div>
                    <div class="mt-9 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <article class="rounded-lg border border-slate-200 p-5 dark:border-slate-800"><span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-3A4.5 4.5 0 0 0 4 18.5V20m6-9a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm10 9v-1.5a4.5 4.5 0 0 0-3.5-4.38M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /></svg></span><h3 class="mt-4 font-bold text-slate-900 dark:text-white">Clients</h3><p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Keep client contacts and business details easy to find.</p></article>
                        <article class="rounded-lg border border-slate-200 p-5 dark:border-slate-800"><span class="flex h-10 w-10 items-center justify-center rounded-lg bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m4.5 7.75 7.5 4.5 7.5-4.5M12 12.25V21" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></span><h3 class="mt-4 font-bold text-slate-900 dark:text-white">Products &amp; Stock</h3><p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Maintain a product catalog and keep stock details together.</p></article>
                        <article class="rounded-lg border border-slate-200 p-5 dark:border-slate-800"><span class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 3v5h5M9 13h6m-6 4h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><h3 class="mt-4 font-bold text-slate-900 dark:text-white">Invoices</h3><p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Create and manage invoices using your client and product details.</p></article>
                        <article class="rounded-lg border border-slate-200 p-5 dark:border-slate-800"><span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16v13H4zM8 7V4h8v3m-8 5h8m-8 4h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h3 class="mt-4 font-bold text-slate-900 dark:text-white">Orders</h3><p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Keep customer orders and their associated details organized.</p></article>
                        <article class="rounded-lg border border-slate-200 p-5 dark:border-slate-800"><span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v18m5-14H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><h3 class="mt-4 font-bold text-slate-900 dark:text-white">Expenses</h3><p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Record business expenses and keep costs in view.</p></article>
                        <article class="rounded-lg border border-slate-200 p-5 dark:border-slate-800"><span class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19V5m0 14h17M8 15l3-4 3 2 5-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h3 class="mt-4 font-bold text-slate-900 dark:text-white">Reports</h3><p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Review useful summaries of the information you manage.</p></article>
                    </div>
                </div>
            </section>

            <section class="border-y border-slate-200 bg-slate-50 py-16 sm:py-20 dark:border-slate-800 dark:bg-slate-900/50">
                <div class="mx-auto grid max-w-7xl gap-10 px-5 sm:px-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-center">
                    <div class="max-w-lg">
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-700 dark:text-blue-400">A clearer workspace</p>
                        <h2 class="mt-3 text-3xl font-bold text-slate-950 sm:text-4xl dark:text-white">Stay close to the details that keep work moving.</h2>
                        <p class="mt-4 text-base leading-7 text-slate-600 dark:text-slate-400">BizManager gives everyday business records a shared home, with the tools arranged around the way you manage them.</p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950"><h3 class="font-semibold text-slate-900 dark:text-white">One place for business data</h3><p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Manage your key records in one organized workspace.</p></div>
                        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950"><h3 class="font-semibold text-slate-900 dark:text-white">Clients and products, in order</h3><p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Keep contacts, catalogs, and stock details accessible.</p></div>
                        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950"><h3 class="font-semibold text-slate-900 dark:text-white">Records you can follow</h3><p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Track invoices, orders, and expenses alongside reports.</p></div>
                        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950"><h3 class="font-semibold text-slate-900 dark:text-white">Workspace-based separation</h3><p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Business records are organized within their workspace.</p></div>
                        <div class="rounded-lg border border-slate-200 bg-white p-5 sm:col-span-2 dark:border-slate-800 dark:bg-slate-950"><h3 class="font-semibold text-slate-900 dark:text-white">Responsive by design</h3><p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">A responsive interface adapts to the screen you are using.</p></div>
                    </div>
                </div>
            </section>

            <section class="bg-white px-5 py-16 sm:px-8 sm:py-20 dark:bg-slate-950">
                <div class="mx-auto flex max-w-6xl flex-col items-start justify-between gap-7 rounded-xl bg-blue-800 px-6 py-10 sm:px-10 lg:flex-row lg:items-center lg:px-12">
                    <div class="max-w-2xl"><p class="text-sm font-semibold text-blue-200">Bring your everyday work together</p><h2 class="mt-2 text-2xl font-bold text-white sm:text-3xl">Ready to manage your business?</h2><p class="mt-3 text-sm leading-6 text-blue-100">Start organizing your business records with BizManager.</p></div>
                    @auth
                        <a href="{{ route('invoices.index') }}" class="inline-flex min-h-12 shrink-0 items-center justify-center rounded-lg bg-white px-6 py-3 text-sm font-bold text-blue-800 transition hover:bg-blue-50">Go to Dashboard <span class="ml-2" aria-hidden="true">→</span></a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex min-h-12 shrink-0 items-center justify-center rounded-lg bg-white px-6 py-3 text-sm font-bold text-blue-800 transition hover:bg-blue-50">Get Started <span class="ml-2" aria-hidden="true">→</span></a>
                    @endauth
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-5 py-8 sm:px-8 md:flex-row md:items-center md:justify-between">
                <div class="max-w-md"><a href="/" class="text-sm font-bold text-slate-900 dark:text-white">BizManager</a><p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">A straightforward workspace for managing the everyday essentials of your business.</p></div>
                <nav class="flex flex-wrap gap-x-5 gap-y-2 text-sm font-medium" aria-label="Footer navigation">
                    @auth
                        <a href="{{ route('invoices.index') }}" class="text-slate-600 hover:text-blue-700 dark:text-slate-300 dark:hover:text-blue-400">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-slate-600 hover:text-blue-700 dark:text-slate-300 dark:hover:text-blue-400">Login</a>
                        <a href="{{ route('register') }}" class="text-slate-600 hover:text-blue-700 dark:text-slate-300 dark:hover:text-blue-400">Register</a>
                    @endauth
                    <a href="#" class="text-slate-600 hover:text-blue-700 dark:text-slate-300 dark:hover:text-blue-400">Terms of Service</a>
                    <a href="#" class="text-slate-600 hover:text-blue-700 dark:text-slate-300 dark:hover:text-blue-400">Privacy Policy</a>
                </nav>
                <p class="text-xs text-slate-500 dark:text-slate-400">© {{ date('Y') }} BizManager</p>
            </div>
        </footer>
    </div>
</body>
</html>
