<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Products</h1>

        <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">Add Product</a>

        <table class="w-full table-auto border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-4 py-2">Name</th>
                    <th class="border px-4 py-2">Price</th>
                    <th class="border px-4 py-2">Quantity</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td class="border px-4 py-2">{{ $product->name }}</td>
                    <td class="border px-4 py-2">{{ $product->price }}</td>
                    <td class="border px-4 py-2">{{ $product->quantity }}</td>
                    <td class="border px-4 py-2 space-x-2">
                        <a href="{{ route('products.edit', $product) }}" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-600 text-white px-2 py-1 rounded" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
