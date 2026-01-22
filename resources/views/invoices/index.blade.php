<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Invoices</h1>

        <!-- زر إضافة فاتورة -->
        <a href="{{ route('invoices.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">Add Invoice</a>

        <table class="w-full table-auto border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-4 py-2">Client</th>
                    <th class="border px-4 py-2">Total</th>
                    <th class="border px-4 py-2">Date</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                <tr>
                    <td class="border px-4 py-2">{{ $invoice->client->name }}</td>
                    <td class="border px-4 py-2">{{ $invoice->total }}</td>
                    <td class="border px-4 py-2">{{ $invoice->date ?? $invoice->created_at->format('Y-m-d') }}</td>
                    <td class="border px-4 py-2 space-x-2">
                        <!-- زر تعديل -->
                        <a href="{{ route('invoices.edit', $invoice) }}" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                        
                        <!-- زر حذف -->
                        <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline-block" onsubmit="return confirm('هل أنت متأكد من حذف الفاتورة؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
