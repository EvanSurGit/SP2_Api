<x-app-layout>
    <div class="bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-6">Mon panier</h1>

            @if(session('message'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-green-700">
                    {{ session('message') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Liste des items (col 1-2) --}}
                <div class="lg:col-span-2">
                    @if(empty($items) || count($items) === 0)
                        <div class="bg-white rounded-xl p-6 shadow-sm">
                            <p class="text-gray-600">Votre panier est vide.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($items as $item)
                                <div class="bg-white rounded-xl shadow-sm p-4 flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        {{-- Image produit --}}
                                        <div class="w-24 h-24 bg-gray-100 rounded-md overflow-hidden flex items-center justify-center">
                                            <img src="{{ $item['image'] }}"
                                                 alt="{{ $item['name'] }}"
                                                 onerror="this.onerror=null;this.src='{{ asset('images/produit.png') }}';"
                                                 class="w-full h-full object-cover">
                                        </div>

                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $item['name'] }}</div>
                                            <div class="text-xs text-gray-500">{{ number_format($item['price'], 2, ',', ' ') }} €</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-6">
                                        {{-- Quantité (formulaire) --}}
                                        <form action="{{ route('cart.update', $item['id']) }}" method="POST" class="flex items-center">
                                            @csrf
                                            @method('PUT')
                                            <div class="inline-flex items-center bg-gray-100 rounded-md">
                                                <button type="button" onclick="changeQty(this, -1)" data-input-id="qty-{{ $item['id'] }}" class="px-3">−</button>
                                                <input id="qty-{{ $item['id'] }}" name="qty" type="number" min="1" value="{{ $item['qty'] }}" class="w-12 text-center bg-transparent border-none">
                                                <button type="button" onclick="changeQty(this, 1)" data-input-id="qty-{{ $item['id'] }}" class="px-3">+</button>
                                            </div>
                                            <button type="submit" class="ml-2 text-sm text-blue-600">OK</button>
                                        </form>

                                        {{-- Sous-total --}}
                                        <div class="text-sm text-gray-700 text-right">
                                            <div class="text-xs text-gray-500">Sous-total</div>
                                            <div class="font-semibold">{{ number_format($item['subtotal'], 2, ',', ' ') }} €</div>
                                        </div>

                                        {{-- Supprimer --}}
                                        <form action="{{ route('cart.destroy', $item['id']) }}" method="POST" class="ml-4">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-gray-700" title="Retirer">
                                                🗑
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('categories.index') }}" class="text-gray-600 hover:underline">← Continuer mes achats</a>
                            <a href="{{ route('cart.clear') ?? '#' }}" class="ml-6 text-gray-600 hover:underline">Vider le panier</a> {{-- adapte la route si besoin --}}
                        </div>
                    @endif
                </div>

                {{-- Résumé total (col 3) --}}
                <aside class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Total</h2>

                    <div class="text-sm text-gray-500 mb-4">
                        <div class="flex justify-between">
                            <span>Sous-total</span>
                            <span>{{ number_format($total, 2, ',', ' ') }} €</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span>Livraison</span>
                            <span class="font-medium">Offerte dès 100 €</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span>TVA</span>
                            <span>Incluse</span>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-4">
                            <div class="text-sm text-gray-700 font-bold">Total</div>
                            <div class="text-lg font-extrabold">{{ number_format($total, 2, ',', ' ') }} €</div>
                        </div>

                       <a href="{{ route('checkout.address') }}" 
                         class="block text-center px-6 py-3 rounded-full bg-black text-white font-semibold">
                          Aller au paiement
                       </a>

                        <p class="mt-3 text-xs text-gray-500 text-center">Paiement sécurisé • PayPal • Carte • Chèque</p>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <script>
        function changeQty(btn, delta) {
            const inputId = btn.getAttribute('data-input-id');
            const input = document.getElementById(inputId);
            if (!input) return;
            let value = parseInt(input.value) || 1;
            value = Math.max(1, value + delta);
            input.value = value;
        }
    </script>
</x-app-layout>
