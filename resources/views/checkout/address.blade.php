<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Choix de l’adresse de livraison</h2>
        <p class="text-sm text-gray-500 mt-1">
            Étape 2/3 — Panier → <span class="font-semibold">Adresse</span> → Paiement
        </p>
    </x-slot>

    <div class="max-w-4xl mx-auto mt-6">
        {{-- Messages --}}
        @if (session('message'))
            <div class="mb-4 text-sm text-green-700">{{ session('message') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        @php
            $hasAddresses = isset($addresses) && $addresses->isNotEmpty();
            // Si rien dans old() et pas de préselection, on prend la plus récente si dispo
            $selected = old('address_id', $preselectedAddressId ?? ($hasAddresses ? $addresses->first()->id : null));
        @endphp

        <form
            method="POST"
            action="{{ route('checkout.address.store') }}"
            x-data="{ newAddr: {{ $selected ? 'false' : 'true' }} }"
            class="space-y-8"
        >
            @csrf

            {{-- 1) Liste des adresses existantes --}}
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Mes adresses</h3>

                @if($hasAddresses)
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach ($addresses as $addr)
                            <label class="relative border rounded-xl p-4 flex items-start gap-3 hover:border-indigo-400 transition cursor-pointer">
                                <input
                                    type="radio"
                                    name="address_id"
                                    value="{{ $addr->id }}"
                                    class="mt-1"
                                    @checked((string)$selected === (string)$addr->id)
                                    @change="newAddr = false"
                                >
                                <div class="text-sm leading-5">
                                    <div class="font-medium">
                                        {{ $addr->numero }} {{ $addr->rue }}
                                    </div>
                                    <div class="text-gray-600">
                                        {{ $addr->cp }} {{ $addr->ville }}@if(!empty($addr->pays)) — {{ $addr->pays }} @endif
                                    </div>
                                </div>
                            </label>
                        @endforeach

                        {{-- Carte "Nouvelle adresse" --}}
                        <label class="relative border-dashed border-2 rounded-xl p-4 flex items-start gap-3 hover:border-indigo-400 transition cursor-pointer">
                            <input
                                type="radio"
                                name="address_id"
                                value=""
                                class="mt-1"
                                @checked(empty($selected))
                                @change="newAddr = true"
                            >
                            <div class="text-sm">
                                <div class="font-medium">Utiliser une nouvelle adresse</div>
                                <div class="text-gray-600">Saisis une autre adresse de livraison ci-dessous.</div>
                            </div>
                        </label>
                    </div>
                @else
                    {{-- Aucune adresse → on force la saisie --}}
                    <p class="text-sm text-gray-600">Aucune adresse enregistrée pour l’instant. Ajoute une adresse ci-dessous.</p>
                    <input type="hidden" name="address_id" value="">
                    <script>
                        document.addEventListener('alpine:init', () => {
                            const f = document.querySelector('form');
                            if (f && f.__x) { f.__x.$data.newAddr = true; }
                        })
                    </script>
                @endif
            </div>

            {{-- 2) Nouvelle adresse (affichée seulement si "nouvelle adresse" est cochée) --}}
            <div class="bg-white rounded-2xl shadow p-6" x-show="newAddr" x-transition x-cloak>
                <h3 class="text-lg font-semibold mb-4">Nouvelle adresse</h3>

                <div class="grid grid-cols-1 sm:grid-cols-6 gap-4">
                    <div class="sm:col-span-2">
                        <x-input-label for="numero" value="Numéro" />
                        <x-text-input id="numero" name="numero" type="text" class="mt-1 block w-full"
                                      value="{{ old('numero') }}"
                                      x-bind:disabled="!newAddr"
                                      x-bind:required="newAddr"
                                      autocomplete="address-line1" />
                        <x-input-error :messages="$errors->get('numero')" class="mt-1" />
                    </div>

                    <div class="sm:col-span-4">
                        <x-input-label for="rue" value="Rue" />
                        <x-text-input id="rue" name="rue" type="text" class="mt-1 block w-full"
                                      value="{{ old('rue') }}"
                                      x-bind:disabled="!newAddr"
                                      x-bind:required="newAddr"
                                      autocomplete="address-line2" />
                        <x-input-error :messages="$errors->get('rue')" class="mt-1" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-input-label for="cp" value="Code postal" />
                        <x-text-input id="cp" name="cp" type="text" class="mt-1 block w-full"
                                      value="{{ old('cp') }}"
                                      x-bind:disabled="!newAddr"
                                      x-bind:required="newAddr"
                                      autocomplete="postal-code" />
                        <x-input-error :messages="$errors->get('cp')" class="mt-1" />
                    </div>

                    <div class="sm:col-span-3">
                        <x-input-label for="ville" value="Ville" />
                        <x-text-input id="ville" name="ville" type="text" class="mt-1 block w-full"
                                      value="{{ old('ville') }}"
                                      x-bind:disabled="!newAddr"
                                      x-bind:required="newAddr"
                                      autocomplete="address-level2" />
                        <x-input-error :messages="$errors->get('ville')" class="mt-1" />
                    </div>

                    <div class="sm:col-span-1">
                        <x-input-label for="pays" value="Pays" />
                        <x-text-input id="pays" name="pays" type="text" class="mt-1 block w-full"
                                      value="{{ old('pays','France') }}"
                                      x-bind:disabled="!newAddr"
                                      autocomplete="country-name" />
                        <x-input-error :messages="$errors->get('pays')" class="mt-1" />
                    </div>
                </div>
            </div>

            {{-- 3) Actions --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('cart.index') }}" class="text-sm underline">← Retour au panier</a>
                <x-primary-button class="px-6">Continuer</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
