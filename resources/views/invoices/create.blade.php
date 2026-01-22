<x-app-layout>
    <div class="p-6 max-w-xl">
        <h1 class="text-2xl font-bold mb-4">Add Invoice</h1>

        <form action="{{ route('invoices.store') }}" method="POST">
            @csrf

            <!-- اختيار العميل -->
            <label class="block mb-2">Client</label>
            <select name="client_id" class="w-full mb-3 border p-2 rounded" required>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>

            <!-- Total -->
            <label class="block mb-2">Total</label>
            <input type="number" name="total" class="w-full mb-3 border p-2 rounded" required>

            <!-- التاريخ -->
            <label class="block mb-2">Date</label>
            <input type="date" name="date" class="w-full mb-3 border p-2 rounded">

            <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
        </form>
    </div>
</x-app-layout>
