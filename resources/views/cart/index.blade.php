<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Panier') }}</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 py-6">

        {{-- Message de confirmation --}}
        @if (session('message'))
            <div class="mb-4 text-sm text-green-600">{{ session('message') }}</div>
        @endif

        {{-- Si aucun produit dans le panier --}}
        @if ($panier->puzzles->isEmpty())
            <div class="bg-white border rounded-xl p-6 text-sm text-gray-600">
                Votre panier est vide.
            </div>
        @else
            {{-- Liste des produits --}}
            <div class="space-y-4">
                @foreach ($panier->puzzles as $puzzle)
                    <div class="bg-white border rounded-xl p-4 flex items-center gap-4">
                        {{-- Image du puzzle --}}
                        <div class="w-20 h-20 rounded-md overflow-hidden bg-gray-100">
                            @if(!empty($puzzle->image_url))
                                <img src="{{ $puzzle->image_url }}" class="w-full h-full object-cover" alt="">
                            @elseif(!empty($puzzle->image_path))
                                <img src="{{ Storage::url($puzzle->image_path) }}" class="w-full h-full object-cover" alt="">
                            @endif
                        </div>

                        {{-- Infos puzzle --}}
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">{{ $puzzle->nom }}</div>
                            <div class="text-sm text-gray-500">{{ number_format($puzzle->pivot->prix, 2) }} €</div>

                            {{-- Formulaire MAJ quantité --}}
                            <form method="POST" action="{{ route('cart.update', $puzzle->id) }}" class="mt-2 flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input name="qty" type="number" min="1" value="{{ $puzzle->pivot->quantite }}"
                                       class="w-20 rounded-md border-gray-300 text-center">
                                <button class="px-3 py-1 rounded-md bg-gray-900 text-white text-sm">Mettre à jour</button>
                            </form>
                        </div>

                        {{-- Prix total ligne + suppression --}}
                        <div class="text-right">
                            <div class="font-semibold text-gray-900">
                                {{ number_format($puzzle->pivot->quantite * $puzzle->pivot->prix, 2) }} €
                            </div>
                            <form method="POST" action="{{ route('cart.destroy', $puzzle->id) }}" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button class="text-sm text-red-600 hover:underline">Retirer</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Total général --}}
            <div class="mt-6 flex items-center justify-between bg-white border rounded-xl p-4">
                <div class="text-gray-600">Total</div>
                <div class="text-xl font-extrabold text-gray-900">
                    {{ number_format($total, 2) }} €
                </div>
            </div>

            {{-- Bouton de validation --}}
            <div class="mt-4 text-right">
                <a href="{{ route('checkout.address.create') }}"
                   class="inline-flex items-center justify-center h-10 px-5 rounded-md bg-gray-900 text-white text-sm font-medium">
                    Finaliser ma commande
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
