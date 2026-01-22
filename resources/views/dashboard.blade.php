<x-app-layout>
    <div class="p-6 space-y-8">

        <!-- Header -->
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                Welcome, {{ auth()->user()->name }} 
            </h1>
            <p class="text-gray-600 mt-1">
                Manage your business from one place
            </p>
        </div>
        <!-- Quick Actions -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

    <!-- Add Client -->
    <a href="{{ route('clients.create') }}"
       class="flex items-center justify-center gap-2 bg-blue-600 py-3 rounded-lg shadow hover:bg-blue-700 transition">
        <span class="text-xl">➕</span>
        <span class="font-semibold text-slate-900">Add Client</span>
    </a>

    <!-- Add Product -->
    <a href="{{ route('products.create') }}"
       class="flex items-center justify-center gap-2 bg-green-600 py-3 rounded-lg shadow hover:bg-green-700 transition">
        <span class="text-xl">➕</span>
        <span class="font-semibold text-slate-900">Add Product</span>
    </a>

    <!-- Add Invoice -->
    <a href="{{ route('invoices.create') }}"
       class="flex items-center justify-center gap-2 bg-yellow-500 py-3 rounded-lg shadow hover:bg-yellow-600 transition">
        <span class="text-xl">➕</span>
        <span class="font-semibold text-slate-900">Create Invoice</span>
    </a>

</div>



        <!-- Stats + Navigation Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Clients -->
            <a href="{{ route('clients.index') }}"
               class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition flex items-center justify-between">
                <div>
                    <h2 class="text-gray-500 font-semibold">Clients</h2>
                    <p class="text-3xl font-bold text-blue-600">{{ $clientsCount }}</p>
                </div>
                <div class="text-blue-500 text-4xl"></div>
            </a>

            <!-- Products -->
            <a href="{{ route('products.index') }}"
               class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition flex items-center justify-between">
                <div>
                    <h2 class="text-gray-500 font-semibold">Products</h2>
                    <p class="text-3xl font-bold text-green-600">{{ $productsCount }}</p>
                </div>
                <div class="text-green-500 text-4xl"></div>
            </a>

            <!-- Invoices -->
            <a href="{{ route('invoices.index') }}"
               class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition flex items-center justify-between">
                <div>
                    <h2 class="text-gray-500 font-semibold">Invoices</h2>
                    <p class="text-3xl font-bold text-yellow-600">{{ $invoicesCount }}</p>
                </div>
                <div class="text-yellow-500 text-4xl"></div>
            </a>

        </div>

        <!-- Monthly Income Chart -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">Monthly Income</h2>
            <canvas id="incomeChart" class="w-full h-64"></canvas>
        </div>

    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const incomeData = @json($monthlyIncome);

        const labels = incomeData.map(item => 'Month ' + item.month);
        const totals = incomeData.map(item => item.total);

        new Chart(document.getElementById('incomeChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Income',
                    data: totals,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</x-app-layout>
