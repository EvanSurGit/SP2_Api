<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class CommandeController extends Controller
{
    // Liste toutes les commandes en attente
    public function enAttente()
    {
    $commandes = Order::whereIn('status', ['payée', 'en_attente'])
        ->with('user', 'puzzles')
        ->get();

    return response()->json($commandes);
    }

    // Valider une commande
    public function valider($id)
    {
        $commande = Order::findOrFail($id);
        $commande->status = 'validée';
        $commande->save();

        return response()->json(['message' => 'Commande validée', 'commande' => $commande]);
    }

    // Marquer comme expédiée
    public function expedier($id)
    {
        $commande = Order::findOrFail($id);
        $commande->status = 'expédiée';
        $commande->save();

        return response()->json(['message' => 'Commande expédiée', 'commande' => $commande]);
    }

    // Supprimer une commande
    public function supprimer($id)
    {
        $commande = Order::findOrFail($id);
        $commande->delete();

        return response()->json(['message' => 'Commande supprimée']);
    }
    
    // Voir une commande
    public function show($id)
{
    $commande = Order::findOrFail($id);
    return response()->json($commande);
}
}