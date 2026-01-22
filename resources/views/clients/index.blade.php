<x-app-layout>
    <div class="p-6">

        <div class="flex justify-between mb-4">
            <h1 class="text-2xl font-bold">Clients</h1>
            <a href="{{ route('clients.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded">
                + Add Client
            </a>
        </div>

        <table class="w-full bg-white rounded shadow">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-3">Name</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Phone</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                <tr class="border-t">
                    <td class="p-3">{{ $client->name }}</td>
                    <td class="p-3">{{ $client->email }}</td>
                    <td class="p-3">{{ $client->phone }}</td>
                    <td class="p-3 flex gap-2">

                        <a href="{{ route('clients.edit', $client) }}"
                           class="text-blue-600">Edit</a>

                        <form action="{{ route('clients.destroy', $client) }}"
                              method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600"
                                onclick="return confirm('Delete this client?')">
                                Delete
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</x-app-layout>
