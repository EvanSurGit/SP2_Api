<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Paiement</h2>
        <p class="text-sm text-gray-500 mt-1">Étape 3/3 — Panier → Adresse → <span class="font-semibold">Paiement</span></p>
    </x-slot>

    <div class="max-w-4xl mx-auto mt-6 space-y-6">
        @if (session('message'))
            <div class="text-sm text-green-700">{{ session('message') }}</div>
        @endif
        @if ($errors->any())
            <div class="p-3 bg-red-50 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- Récap adresse (optionnel) --}}
        @if(!empty($address))
            <div class="bg-white p-4 rounded-2xl shadow">
                <h3 class="font-semibold mb-2">Adresse de livraison</h3>
                @if(($address['source'] ?? '') === 'existing')
                    <p class="text-sm text-gray-700">
                        Adresse existante — ID : <strong>{{ $address['address_id'] }}</strong>
                    </p>
                @else
                    <p class="text-sm text-gray-700">
                        {{ $address['numero'] ?? '' }} {{ $address['rue'] ?? '' }}<br>
                        {{ $address['cp'] ?? '' }} {{ $address['ville'] ?? '' }} — {{ $address['pays'] ?? '' }}
                    </p>
                @endif
                <a href="{{ route('checkout.address.create') }}" class="text-xs underline mt-2 inline-block">Modifier l’adresse</a>
            </div>
        @endif

        {{-- Choix du moyen de paiement --}}
        <form method="POST" action="{{ route('checkout.payment.process') }}"
       x-data="{ method: @js(old('payment_method','paypal')) }"
       class="bg-white p-6 rounded-2xl shadow space-y-6">

            @csrf

                <fieldset class="space-y-3">
                  {{-- PayPal en premier + coché par défaut --}}
                  <label class="flex items-start gap-3 p-3 border rounded-xl hover:border-indigo-400 transition">
                    <input type="radio" name="payment_method" x-model="method" value="paypal" class="mt-1"
                           @checked(old('payment_method','paypal')==='paypal')>
                    <div>
                      <div class="font-medium">PayPal</div>
                      <div class="text-sm text-gray-600">Redirection vers PayPal pour finaliser le paiement.</div>
                    </div>
                  </label>
                <label class="flex items-start gap-3 p-3 border rounded-xl hover:border-indigo-400 transition">
                    <input type="radio" name="payment_method" value="cheque" class="mt-1"
                           @checked(old('payment_method')==='cheque') @change="method='cheque'">
                    <div>
                        <div class="font-medium">Chèque</div>
                        <div class="text-sm text-gray-600">Envoi du chèque avant expédition (instructions affichées).</div>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-3 border rounded-xl hover:border-indigo-400 transition">
                    <input type="radio" name="payment_method" value="card" class="mt-1"
                           @checked(old('payment_method','card')==='card') @change="method='card'">
                    <div>
                        <div class="font-medium">Carte bancaire</div>
                        <div class="text-sm text-gray-600">Visa, MasterCard… Saisie sécurisée (maquette).</div>
                    </div>
                </label>
            </fieldset>

            {{-- Bloc chèque --}}
            <div x-show="method==='cheque'" x-transition class="p-4 rounded-xl bg-gray-50 text-sm leading-6">
                <p class="font-medium mb-1">Instructions pour le règlement par chèque</p>
                <ul class="list-disc list-inside text-gray-700">
                    <li>À l’ordre de : <strong>WoodyCraft</strong> {{-- adapte si besoin --}}</li>
                    <li>Montant : le total de votre commande</li>
                    <li>Envoyer à : <em>— adresse postale à compléter —</em></li>
                    <li>Référence à indiquer au dos : <em>— numéro de commande —</em></li>
                </ul>
                <p class="mt-2 text-gray-600">Votre commande sera préparée après réception et encaissement du chèque.</p>
            </div>

            {{-- Bloc carte --}}
            <div x-show="method==='card'" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <x-input-label for="card_name" value="Nom sur la carte" />
                    <x-text-input id="card_name" name="card_name" type="text" class="mt-1 block w-full"
                                  value="{{ old('card_name') }}" />
                    <x-input-error :messages="$errors->get('card_name')" class="mt-1" />
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="card_number" value="Numéro de carte" />
                    <x-text-input id="card_number" name="card_number" type="text" inputmode="numeric" autocomplete="cc-number"
                                  class="mt-1 block w-full" placeholder="4242 4242 4242 4242"
                                  value="{{ old('card_number') }}" />
                    <x-input-error :messages="$errors->get('card_number')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="card_exp" value="Expiration (MM/AA)" />
                    <x-text-input id="card_exp" name="card_exp" type="text" inputmode="numeric" autocomplete="cc-exp"
                                  class="mt-1 block w-full" placeholder="09/27"
                                  value="{{ old('card_exp') }}" />
                    <x-input-error :messages="$errors->get('card_exp')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="card_cvc" value="CVC" />
                    <x-text-input id="card_cvc" name="card_cvc" type="text" inputmode="numeric" autocomplete="cc-csc"
                                  class="mt-1 block w-full" placeholder="123"
                                  value="{{ old('card_cvc') }}" />
                    <x-input-error :messages="$errors->get('card_cvc')" class="mt-1" />
                </div>
            </div>

            {{-- Bloc PayPal (juste informatif pour la maquette) --}}
            <div x-show="method==='paypal'" x-transition class="p-4 rounded-xl bg-blue-50 text-sm text-blue-800">
                Vous serez redirigé vers PayPal pour autoriser le paiement, puis renvoyé sur le site.
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('checkout.address.create') }}" class="text-sm underline">← Retour à l’adresse</a>
                <x-primary-button class="px-6">Continuer</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
