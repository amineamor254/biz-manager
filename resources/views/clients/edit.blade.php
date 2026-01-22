<x-app-layout>
    <div class="p-6 max-w-xl">

        <h1 class="text-2xl font-bold mb-4">Edit Client</h1>

        <form action="{{ route('clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="text" name="name"
                   value="{{ $client->name }}"
                   class="w-full mb-3 border p-2 rounded" required>

            <input type="email" name="email"
                   value="{{ $client->email }}"
                   class="w-full mb-3 border p-2 rounded">

            <input type="text" name="phone"
                   value="{{ $client->phone }}"
                   class="w-full mb-3 border p-2 rounded">

            <button class="bg-yellow-900 px-4 py-2 rounded">
                Update
            </button>

        </form>
    </div>
</x-app-layout>
