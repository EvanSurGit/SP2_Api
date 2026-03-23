{{-- resources/views/puzzles/delete.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Supprimer un puzzle') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto mt-6 p-6 bg-white shadow rounded-xl">
        <p class="text-red-700 font-semibold">
            {{ __("Es-tu sûr de vouloir supprimer définitivement ce puzzle ? Cette action est irréversible.") }}
        </p>

        <div class="mt-4 space-y-1 text-sm text-gray-700">
            <p><span class="font-semibold">{{ __('Nom') }}:</span> {{ $puzzle->nom }}</p>
            <p><span class="font-semibold">{{ __('Catégorie') }}:</span> {{ $puzzle->categorie }}</p>
            <p><span class="font-semibold">{{ __('Prix') }}:</span> {{ number_format($puzzle->prix, 2, ',', ' ') }} €</p>
            <p><span class="font-semibold">{{ __('Créé le') }}:</span> {{ optional($puzzle->created_at)->format('d/m/Y') }}</p>
        </div>

        <form class="mt-6 flex items-center gap-3"
              action="{{ route('puzzles.destroy', $puzzle->id) }}"
              method="POST">
            @csrf
            @method('DELETE')

            <x-danger-button onclick="return confirm('{{ __('Confirmer la suppression ?') }}')">
                {{ __('Supprimer') }}
            </x-danger-button>

            <x-secondary-button type="button" onclick="window.location='{{ route('puzzles.index') }}'">
                {{ __('Annuler') }}
            </x-secondary-button>
        </form>
    </div>
</x-app-layout>
