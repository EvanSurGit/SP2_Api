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

    // Récupère toutes les commandes non expédiées (en_attente ou validee)
    public function nonExpediees()
    {
        $commandes = Order::whereIn('status', ['en_attente', 'validee'])
            ->with(['puzzles', 'user', 'adresseLivraison'])
            ->get();

        return response()->json(
            $commandes->map(function ($commande) {
                return [
                    'id' => $commande->id,
                    'user_id' => $commande->user_id,
                    'total' => $commande->total,
                    'status' => $commande->status,
                    'date_commande' => $commande->date_commande,
                    'created_at' => $commande->created_at,
                    'updated_at' => $commande->updated_at,
                    'mode_paiement' => $commande->mode_paiement,
                ];
            })
        );
    }

    // API : Afficher le d�tail d'une commande en JSON
    public function show(int $id)
    {
        $commande = Order::with(['puzzles', 'user', 'adresseLivraison'])->findOrFail($id);

        return response()->json([
            'id' => $commande->id,
            'user_id' => $commande->user_id,
            'total' => $commande->total,
            'status' => $commande->status,
            'date_commande' => $commande->date_commande,
            'created_at' => $commande->created_at,
            'updated_at' => $commande->updated_at,
            'mode_paiement' => $commande->mode_paiement,
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