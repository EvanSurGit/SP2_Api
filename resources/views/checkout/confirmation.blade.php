<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Confirmation</h2></x-slot>

    <div class="max-w-3xl mx-auto mt-6 bg-white p-6 rounded-2xl shadow space-y-4">
        @if (session('message'))
            <div class="text-sm text-green-700">{{ session('message') }}</div>
        @endif

        @php
            $addr = $address ?? session('checkout.address');
            $pay  = $payment ?? session('checkout.payment_choice');
        @endphp

        <h3 class="font-semibold">Récapitulatif</h3>

        <div class="grid sm:grid-cols-2 gap-6">
            <div>
                <div class="text-sm text-gray-500 mb-1">Adresse de livraison</div>
                @if($addr)
                    @if(($addr['source'] ?? '') === 'existing')
                        <div class="text-sm text-gray-800">Adresse existante — ID : <strong>{{ $addr['address_id'] }}</strong></div>
                    @else
                        <div class="text-sm text-gray-800">
                            {{ $addr['numero'] ?? '' }} {{ $addr['rue'] ?? '' }}<br>
                            {{ $addr['cp'] ?? '' }} {{ $addr['ville'] ?? '' }} — {{ $addr['pays'] ?? '' }}
                        </div>
                    @endif
                @else
                    <div class="text-sm text-red-600">Aucune adresse.</div>
                @endif
            </div>

            <div>
                <div class="text-sm text-gray-500 mb-1">Paiement</div>
                @if($pay)
                    @if($pay['method'] === 'paypal')
                        <div class="text-sm text-gray-800">PayPal</div>
                    @elseif($pay['method'] === 'cheque')
                        <div class="text-sm text-gray-800">Chèque</div>
                    @else
                        <div class="text-sm text-gray-800">
                            Carte bancaire •••• {{ $pay['card']['last4'] ?? 'XXXX' }} ({{ $pay['card']['exp'] ?? 'MM/AA' }})
                        </div>
                    @endif
                @else
                    <div class="text-sm text-red-600">Aucun choix de paiement.</div>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('checkout.payment.show') }}" class="text-sm underline">← Modifier le paiement</a>
            <x-primary-button class="px-6">Valider la commande (maquette)</x-primary-button>
        </div>
    </div>
</x-app-layout>
