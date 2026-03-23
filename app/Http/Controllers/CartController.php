<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puzzle;
use App\Models\Panier;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // ==========================
    // PAGE PANIER
    // ==========================
    public function index()
    {
        $user = Auth::user();

        $panier = Panier::firstOrCreate([
            'user_id' => $user->id,
            'status'  => 'open',
        ]);

        $panier->load('puzzles');

        $items = $panier->puzzles->map(function ($p) {
            $image = $this->resolveImage($p);
            $price = $p->pivot->prix ?? $p->prix ?? 0;
            $qty   = $p->pivot->quantite ?? 1;
            return [
                'id'       => $p->id,
                'name'     => $p->nom ?? $p->name ?? 'Produit',
                'image'    => $image,
                'price'    => $price,
                'qty'      => $qty,
                'subtotal' => $price * $qty,
            ];
        })->toArray();

        $total = array_reduce($items, fn($acc, $it) => $acc + ($it['subtotal'] ?? 0), 0);

        // Met à jour le total du panier en base
        $panier->total = $total;
        $panier->save();

        return view('cart.index_db', compact('panier', 'items', 'total'));
    }

    // ==========================
    // AJOUTER AU PANIER
    // ==========================
    public function add(Request $request, Puzzle $puzzle)
    {
        $user = Auth::user();

        $panier = Panier::firstOrCreate([
            'user_id' => $user->id,
            'status'  => 'open',
        ]);

        $qty = max(1, (int) $request->input('qty', 1));
        $prix = $puzzle->prix ?? 0;

        $existing = $panier->puzzles()->where('puzzles.id', $puzzle->id)->first();

        if ($existing) {
            $panier->puzzles()->updateExistingPivot($puzzle->id, [
                'quantite' => $existing->pivot->quantite + $qty,
                'prix'     => $prix,
            ]);
        } else {
            $panier->puzzles()->syncWithoutDetaching([
                $puzzle->id => ['quantite' => $qty, 'prix' => $prix],
            ]);
        }

        $this->updatePanierTotal($panier);

        return redirect()->route('cart.index')->with('message', 'Article ajouté au panier.');
    }

    // ==========================
    // METTRE À JOUR UNE LIGNE
    // ==========================
    public function update(Request $request, $puzzleId)
    {
        $user = Auth::user();

        $panier = Panier::where('user_id', $user->id)
                        ->where('status', 'open')
                        ->first();

        if ($panier) {
            $qty = max(1, (int) $request->input('qty', 1));
            $panier->puzzles()->updateExistingPivot($puzzleId, ['quantite' => $qty]);
            $this->updatePanierTotal($panier);
        }

        return back()->with('message', 'Quantité mise à jour.');
    }

    // ==========================
    // SUPPRIMER UNE LIGNE
    // ==========================
    public function destroy($puzzleId)
    {
        $user = Auth::user();

        $panier = Panier::where('user_id', $user->id)
                        ->where('status', 'open')
                        ->first();

        if ($panier) {
            $panier->puzzles()->detach($puzzleId);
            $this->updatePanierTotal($panier);
        }

        return back()->with('message', 'Article retiré.');
    }

    // ==========================
    // CHECKOUT / PAIEMENT
    // ==========================
    public function checkout()
    {
        $user = Auth::user();

        $panier = Panier::where('user_id', $user->id)
                        ->where('status', 'open')
                        ->with('puzzles')
                        ->first();

        if (!$panier || $panier->puzzles->isEmpty()) {
            return redirect()->route('cart.index')
                             ->with('error', 'Votre panier est vide.');
        }

        $total = $panier->puzzles->reduce(function ($carry, $p) {
            $price = $p->pivot->prix ?? $p->prix ?? 0;
            $qty   = $p->pivot->quantite ?? 1;
            return $carry + ($price * $qty);
        }, 0);

        DB::transaction(function () use ($user, $panier, $total) {
            // 1️⃣ Crée la commande
            $order = Order::create([
                'user_id'      => $user->id,
                'total'        => $total,
                'status'       => 'payée',
                'date_commande'=> now(),
            ]);

            // 2️⃣ Attache les puzzles
            foreach ($panier->puzzles as $p) {
                $order->puzzles()->attach($p->id, [
                    'quantite' => $p->pivot->quantite,
                    'prix'     => $p->pivot->prix,
                ]);
            }

            // 3️⃣ Ferme le panier actuel
            $panier->status = 'closed';
            $panier->save();

            // 4️⃣ Crée un nouveau panier vide
            Panier::create([
                'user_id' => $user->id,
                'status'  => 'open',
                'total'   => 0,
            ]);
        });

        return redirect()->route('cart.index')->with('message', 'Paiement validé ! Votre commande a été créée.');
    }

    // ==========================
    // MISE À JOUR DU TOTAL DU PANIER
    // ==========================
    private function updatePanierTotal(Panier $panier)
    {
        $total = $panier->puzzles->reduce(function ($carry, $p) {
            $prix = $p->pivot->prix ?? $p->prix ?? 0;
            $qty  = $p->pivot->quantite ?? 1;
            return $carry + ($prix * $qty);
        }, 0);

        $panier->total = $total;
        $panier->save();
    }

    // ==========================
    // RÉSOLUTION IMAGE
    // ==========================
    private function resolveImage(Puzzle $p)
    {
        if (!empty($p->image_url)) {
            return $p->image_url;
        } elseif (!empty($p->image_path) && file_exists(public_path($p->image_path))) {
            return asset($p->image_path);
        } elseif (!empty($p->image_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($p->image_path)) {
            return \Illuminate\Support\Facades\Storage::url($p->image_path);
        }
        return asset('images/produit.png');
    }
}