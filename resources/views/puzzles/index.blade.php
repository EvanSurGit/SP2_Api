<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Liste des puzzles') }}
        </h2>
    </x-slot>

    <div class="container mx-auto">
        @if (session()->has('message'))
            <div class="mt-3 mb-4 text-sm text-green-600">
                {{ session('message') }}
            </div>
        @endif

        <div class="overflow-x-auto border-b border-gray-200 shadow pt-6 bg-white">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-2 text-xs text-gray-500">#</th>
                        <th class="px-2 py-2 text-xs text-gray-500">Nom</th>
                        <th class="px-2 py-2 text-xs text-gray-500" colspan="3">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse ($puzzles as $puzzle)
                        <tr class="whitespace-nowrap">
                            <td class="px-4 py-4 text-sm text-gray-500">{{ $puzzle->id }}</td>
                            <td class="px-4 py-4">{{ $puzzle->nom }}</td>

                            {{-- Show --}}
                            <td class="px-2 py-2">
                                <a href="{{ route('puzzles.show', $puzzle->id) }}"
                                   class="inline-flex items-center px-2 py-1 bg-gray-800 text-white rounded-md text-xs">
                                   Show
                                </a>
                            </td>

                            {{-- Edit --}}
                            <td class="px-2 py-2">
                                <a href="{{ route('puzzles.edit', $puzzle->id) }}"
                                   class="inline-flex items-center px-2 py-1 bg-gray-800 text-white rounded-md text-xs">
                                   Edit
                                </a>
                            </td>

                            {{-- Delete --}}
                            <td class="px-2 py-2">
                                <form action="{{ route('puzzles.destroy', $puzzle->id) }}" method="POST"
                                      onsubmit="return confirm('Supprimer ce puzzle ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-2 py-1 bg-red-600 text-white rounded-md text-xs">
                                        Delete
                                    </button>
                                </form>
                            </td>
                            {{-- Ajout au Panier --}}
                            <td class="px-2 py-2">
                            <form action="{{ route('cart.add', $puzzle) }}" method="POST" class="inline-flex items-center space-x-2">
                            @csrf
                            <input type="number" name="qty" value="1" min="1" class="w-16 border rounded px-2 py-1">
                            <x-primary-button>Ajouter au panier</x-primary-button>
                            
                             </form>
                        </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-4 text-sm text-gray-500" colspan="5">Aucun puzzle.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>