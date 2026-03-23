<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Éditer un puzzle') }}
        </h2>
    </x-slot>

    <x-puzzles-card>
        @if (session()->has('message'))
            <div class="mt-3 mb-4 text-sm text-green-600">
                {{ session('message') }}
            </div>
        @endif

        {{-- Affiche un résumé des erreurs si la validation échoue --}}
        @if ($errors->any())
            <div class="mb-4 text-sm text-red-600">
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('puzzles.update', $puzzle) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Nom --}}
            <div>
                <x-input-label for="nom" :value="__('Nom')" />
                <x-text-input id="nom" name="nom" type="text" class="block mt-1 w-full"
                              :value="old('nom', $puzzle->nom)" required autofocus />
                <x-input-error :messages="$errors->get('nom')" class="mt-2" />
            </div>

            {{-- Catégorie (input texte, pas textarea) --}}
            <div class="mt-4">
                <x-input-label for="categorie" :value="__('Catégorie')" />
                <x-text-input id="categorie" name="categorie" type="text" class="block mt-1 w-full"
                              :value="old('categorie', $puzzle->categorie)" required />
                <x-input-error :messages="$errors->get('categorie')" class="mt-2" />
            </div>

            {{-- Description (input texte, pas textarea) --}}
            <div class="mt-4">
                <x-input-label for="description" :value="__('Description')" />
                <x-text-input id="description" name="description" type="text" class="block mt-1 w-full"
                              :value="old('description', $puzzle->description)" required />
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            {{-- image --}}
            <div class="mt-4">
                <x-input-label for="image" :value="__('Image')" />
                <x-text-input id="image" name="image" type="text" class="block mt-1 w-full"
                              :value="old('image', $puzzle->image)" required />
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>

            {{-- Prix --}}
            <div class="mt-4">
                <x-input-label for="prix" :value="__('Prix')" />
                <x-text-input id="prix" name="prix" type="number" step="0.01" min="0" max="99.99"
                              class="block mt-1 w-full" :value="old('prix', $puzzle->prix)" required />
                <x-input-error :messages="$errors->get('prix')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-6">
                <x-primary-button>{{ __('Mettre à jour') }}</x-primary-button>
            </div>
        </form>
    </x-puzzles-card>
</x-app-layout>
