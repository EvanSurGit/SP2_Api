<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CommandeController extends Controller
{
    // Afficher la liste des commandes en attente (statut 'payee' ou 'en attente')
    public function listeEnAttente()
    {
        $commandes = Order::whereIn('status', ['payee', 'en_attente'])->with('puzzles')->get();
        return view('admin.commandes.en_attente', compact('commandes'));
    }

    // API : Afficher le d�tail d'une commande en JSON
    public function show(int $id)
    {
        $commande = Order::with(['puzzles', 'user', 'adresseLivraison'])->findOrFail($id);

        return response()->json([
            'id'                => $commande->id,
            'numero_commande'   => '#CMD-' . str_pad($commande->id, 3, '0', STR_PAD_LEFT),
            'statut'            => $commande->status,
            'total'             => $commande->total,
            'created_at'        => $commande->created_at,
            'adresse_livraison' => $commande->adresseLivraison->adresse ?? '',
            'client_nom'        => $commande->user->name ?? 'N/A',
            'client_email'      => $commande->user->email ?? '',
            'items'             => $commande->puzzles->map(function ($puzzle) {
                return [
                    'id'            => $puzzle->id,
                    'nom_produit'   => $puzzle->nom,
                    'quantite'      => $puzzle->pivot->quantite,
                    'prix_unitaire' => $puzzle->pivot->prix,
                ];
            }),
        ]);
    }

    // Valider une commande
    public function valider(int $id)
    {
        $commande = Order::findOrFail($id);
        if ($commande->status === 'payee') {
            $commande->status = 'validee';
            $commande->save();
            return redirect()->back()->with('success', "Commande #{$id} validee.");
        }
        return redirect()->back()->with('error', "La commande #{$id} ne peut pas etre validee.");
    }

    // Marquer une commande comme exp�di�e
    public function expedier(int $id)
    {
        $commande = Order::findOrFail($id);
        if ($commande->status === 'validee') {
            $commande->status = 'expediee';
            $commande->save();
            return redirect()->back()->with('success', "Commande #{$id} marquee comme expediee.");
        }
        return redirect()->back()->with('error', "La commande #{$id} ne peut pas etre expediee.");
    }

    // Supprimer une commande
    public function supprimer(int $id)
    {
        $commande = Order::findOrFail($id);
        DB::transaction(function () use ($commande) {
            $commande->puzzles()->detach();
            $commande->delete();
        });
        return redirect()->back()->with('success', "Commande #{$id} supprimee.");
    }
    
    // Afficher la page de d�tails (Vue Blade) pour l'administrateur
public function detail(int $id)
{
    // On charge la commande avec toutes les relations n�cessaires
    $commande = Order::with(['puzzles', 'user', 'adresseLivraison'])->findOrFail($id);

    // Retourne la vue admin avec les donn�es de la commande
    return view('admin.commandes.show', compact('commande'));
}
}