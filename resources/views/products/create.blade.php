<x-app-layout>
    <div class="p-6 max-w-xl">
        <h1 class="text-2xl font-bold mb-4">Add Product</h1>

        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Name" class="w-full mb-3 border p-2 rounded" required>
            <input type="number" step="0.01" name="price" placeholder="Price" class="w-full mb-3 border p-2 rounded" required>
            <input type="number" name="quantity" placeholder="Quantity" class="w-full mb-3 border p-2 rounded" required>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
        </form>
    </div>
</x-app-layout>
