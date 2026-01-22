<x-app-layout>
    <div class="p-6 max-w-xl">
        <h1 class="text-2xl font-bold mb-4">Edit Product</h1>

        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="text" name="name" value="{{ $product->name }}" class="w-full mb-3 border p-2 rounded" required>
            <input type="number" step="0.01" name="price" value="{{ $product->price }}" class="w-full mb-3 border p-2 rounded" required>
            <input type="number" name="quantity" value="{{ $product->quantity }}" class="w-full mb-3 border p-2 rounded" required>

            <button class="bg-yellow-900 px-4 py-2 rounded">Update</button>
        </form>
    </div>
</x-app-layout>
