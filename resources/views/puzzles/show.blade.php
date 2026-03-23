<x-app-layout>
    <div class="bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                    {{-- Image large à gauche --}}
                    <div class="lg:col-span-1">
                        <div class="w-full h-80 bg-gray-100 rounded-xl border border-gray-100 flex items-center justify-center overflow-hidden">
                            {{-- Affiche l'image résolue --}}
                            <img src="{{ $image }}" alt="{{ $puzzle->nom ?? $puzzle->name ?? 'Produit' }}"
                                 class="w-full h-full object-cover">
                        </div>
                    </div>

                    {{-- Détails et actions à droite --}}
                    <div class="lg:col-span-2">
                        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">
                            {{ $puzzle->nom ?? $puzzle->name ?? 'Produit' }}
                        </h1>

                        <div class="text-3xl font-bold text-gray-900 mb-4">
                            {{ isset($puzzle->prix) ? number_format($puzzle->prix, 2, ',', ' ') . ' €' : (isset($puzzle->price) ? number_format($puzzle->price, 2, ',', ' ') . ' €' : '—') }}
                        </div>

                        @if(!empty($puzzle->description))
                            <p class="text-sm text-gray-600 mb-6">
                                {!! nl2br(e(\Illuminate\Support\Str::limit($puzzle->description, 600))) !!}
                            </p>
                        @endif

                        {{-- Formulaire d'ajout au panier --}}
                        <form action="{{ route('cart.add', $puzzle) }}" method="POST" class="flex items-center gap-4">
                            @csrf
                            <div class="flex items-center bg-gray-100 rounded-md px-3 py-2">
                                <label for="qty" class="sr-only">Quantité</label>
                                <button type="button" onclick="document.getElementById('qty').stepDown()" class="px-2">−</button>
                                <input id="qty" name="qty" type="number" min="1" value="1" class="w-16 text-center bg-transparent focus:outline-none">
                                <button type="button" onclick="document.getElementById('qty').stepUp()" class="px-2">+</button>
                            </div>

                            <button type="submit"
                                    class="inline-flex items-center px-5 py-2 rounded-full bg-black text-white text-sm font-semibold hover:opacity-95 transition">
                                Ajouter au panier
                            </button>

                            {{-- Optionnel : bouton pour wishlist, partage, etc. --}}
                        </form>

                        {{-- Message flash --}}
                        @if(session('message'))
                            <div class="mt-4 text-sm text-green-700">
                                {{ session('message') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Section avis (existant dans ta maquette) --}}
            <div class="mt-10">
                <h2 class="text-xl font-semibold text-gray-900">Avis des clients</h2>
                <p class="mt-2 text-sm text-gray-500">Aucun avis pour le moment. Soyez le premier à en laisser un !</p>

                <div class="mt-6 bg-white rounded-xl border border-gray-100 p-4">
                    <div class="text-sm text-gray-500">Vous devez avoir commandé ce puzzle pour pouvoir laisser un avis.</div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
