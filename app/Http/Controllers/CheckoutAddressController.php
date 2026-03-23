<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Adresse;

class CheckoutAddressController extends Controller
{
    public function create()
    {
        $user = auth()->user();

        $addresses = $user->adresses()->orderByDesc('id')->get();
        $preselectedAddressId = optional($addresses->first())->id;

        return view('checkout.address', compact('addresses', 'preselectedAddressId'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // 1) Valider l'id d'adresse si fourni
        $request->validate([
            'address_id' => [
                'nullable',
                'integer',
                Rule::exists('delivery_adresses', 'id')->where(fn($q) => $q->where('user_id', $user->id)),
            ],
        ]);

        if ($request->filled('address_id')) {
            // Utilisation d’une adresse existante
            $addr = $user->adresses()->where('id', $request->address_id)->firstOrFail();
            $addr->touch(); // on la marque comme "dernière utilisée"
            session(['checkout.address_id' => $addr->id]);

            return redirect()->route('checkout.payment.show')
                ->with('message', 'Adresse sélectionnée.');
        }

        // 2) Sinon, on crée une nouvelle adresse → champs requis
        $data = $request->validate([
            'numero' => 'required|string',
            'rue'    => 'required|string',
            'ville'  => 'required|string',
            'cp'     => 'required|string',
            'pays'   => 'nullable|string',
        ]);

        // 3) Sauvegarde explicite (évite tout souci de mass assignment)
        $addr = new Adresse();
        $addr->user_id = $user->id;
        $addr->adresse = $data['numero'] . ' ' . $data['rue']; // combine numéro + rue
        $addr->ville   = $data['ville'];
        $addr->code_postal = $data['cp'];
        $addr->pays   = $data['pays'] ?? null;
        $addr->save();

        session(['checkout.address_id' => $addr->id]);

        return redirect()->route('checkout.payment.show')
            ->with('message', 'Adresse enregistrée avec succès.');
    }
}
