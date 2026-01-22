<x-app-layout>
    <div class="p-6 max-w-xl">

        <h1 class="text-2xl font-bold mb-4">Add Client</h1>

        <form action="{{ route('clients.store') }}" method="POST">
            @csrf

            <input type="text" name="name"
                   placeholder="Name"
                   class="w-full mb-3 border p-2 rounded" required>

            <input type="email" name="email"
                   placeholder="Email"
                   class="w-full mb-3 border p-2 rounded">

            <input type="text" name="phone"
                   placeholder="Phone"
                   class="w-full mb-3 border p-2 rounded">

            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Save
            </button>

        </form>
    </div>
</x-app-layout>
