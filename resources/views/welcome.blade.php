<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Manager</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="min-h-screen flex flex-col">

        <header class="bg-white/60 backdrop-blur border-b border-slate-200">
            <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center font-bold">BM</div>
                    <div>
                        <h2 class="text-lg font-semibold">Business Manager</h2>
                        <p class="text-xs text-slate-500 -mt-1">Clients · Products · Invoices</p>
                    </div>
                </a>

                <nav class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('invoices.index') }}" class="text-sm text-slate-700 hover:text-slate-900">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">@csrf
                            <button type="submit" class="text-sm text-slate-700 hover:text-slate-900">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 border border-slate-200 rounded-md text-sm hover:bg-slate-50">Register</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="flex-1">
            <section class="bg-gradient-to-r from-white to-slate-50">
                <div class="max-w-6xl mx-auto px-6 py-20 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                    <div>
                        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-4">Manage invoices, clients, and products with ease</h1>
                        <p class="text-slate-600 mb-6">A lightweight invoicing and client management app built for small businesses — fast workflows, clear invoices, and reliable records.</p>

                        <div class="flex space-x-4">
                            @auth
                                <a href="{{ route('invoices.index') }}" class="px-6 py-3 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700">Go to Dashboard</a>
                            @else
                                <a href="{{ route('register') }}" class="px-6 py-3 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700">Get Started</a>
                                <a href="{{ route('login') }}" class="px-6 py-3 bg-white border border-slate-200 rounded-md text-slate-700 hover:bg-slate-50">Sign In</a>
                            @endauth
                        </div>
                    </div>

                    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs text-slate-400">Invoice Preview</p>
                                <p class="font-semibold">INV-0001 · Acme Co.</p>
                            </div>
                            <div class="text-sm text-slate-500">Due in 14 days</div>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div class="flex justify-between text-sm text-slate-600">
                                <span>Web design</span>
                                <span>$1,200.00</span>
                            </div>
                            <div class="flex justify-between text-sm text-slate-600">
                                <span>Hosting (12 mo)</span>
                                <span>$240.00</span>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 mt-6 pt-4 flex items-center justify-between">
                            <div class="text-sm text-slate-500">Total</div>
                            <div class="text-xl font-bold">$1,440.00</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="max-w-6xl mx-auto px-6 py-14">
                <h3 class="text-lg font-semibold mb-6">Built for small business</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-white rounded-lg p-5 border border-slate-100 shadow-sm">
                        <h4 class="font-semibold mb-2">Clients</h4>
                        <p class="text-sm text-slate-500">Keep contacts, billing details and activity in one place.</p>
                    </div>
                    <div class="bg-white rounded-lg p-5 border border-slate-100 shadow-sm">
                        <h4 class="font-semibold mb-2">Products</h4>
                        <p class="text-sm text-slate-500">Manage prices, inventory and quick item inserts for invoices.</p>
                    </div>
                    <div class="bg-white rounded-lg p-5 border border-slate-100 shadow-sm">
                        <h4 class="font-semibold mb-2">Invoices</h4>
                        <p class="text-sm text-slate-500">Create, send and track invoices with clear totals and taxes.</p>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="max-w-6xl mx-auto px-6 py-6 flex items-center justify-between text-sm text-slate-500">
                <div>© {{ date('Y') }} Business Manager</div>
                <div class="space-x-4">
                    <a href="#" class="hover:text-slate-700">Privacy</a>
                    <a href="#" class="hover:text-slate-700">Terms</a>
                </div>
            </div>
        </footer>

    </div>

</body>
</html>
