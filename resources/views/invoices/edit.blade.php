<x-app-layout>
    <div class="p-6 max-w-xl">
        <h1 class="text-2xl font-bold mb-4">Edit Invoice</h1>

        <form action="{{ route('invoices.update', $invoice) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- اختيار العميل -->
            <label class="block mb-2">Client</label>
            <select name="client_id" class="w-full mb-3 border p-2 rounded" required>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $invoice->client_id == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>

            <!-- المجموع -->
            <label class="block mb-2">Total</label>
            <input type="number" name="total" value="{{ $invoice->total }}" class="w-full mb-3 border p-2 rounded" required>

            <!-- التاريخ -->
            <label class="block mb-2">Date</label>
            <input type="date" name="date" value="{{ $invoice->date ?? $invoice->created_at->format('Y-m-d') }}" class="w-full mb-3 border p-2 rounded">

            <!-- زر التحديث -->
            <button class="bg-yellow-500 px-4 py-2 rounded">Update</button>
        </form>
    </div>
</x-app-layout>
