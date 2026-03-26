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
    $commandes = Order::whereIn('status', ['payee', 'en_attente'])
        ->with(['user', 'puzzles'])
        ->get();

    return response()->json($commandes);
    }

    // Valider une commande
    public function valider($id)
    {
        $commande = Order::findOrFail($id);
        $commande->status = 'validee';
        $commande->save();

        return response()->json(['message' => 'Commande validee', 'commande' => $commande]);
    }

    // Marquer comme exp�di�e
    public function expedier($id)
    {
        $commande = Order::findOrFail($id);
        $commande->status = 'expediee';
        $commande->save();

        return response()->json(['message' => 'Commande expediee', 'commande' => $commande]);
    }

    // Supprimer une commande
    public function supprimer($id)
    {
        $commande = Order::findOrFail($id);
        $commande->delete();

        return response()->json(['message' => 'Commande supprimee']);
    }
    
   // Voir une commande (détail complet)
   public function show($id)
   {
       $commande = Order::with(['user', 'puzzles'])->findOrFail($id);

       // Préparer les données pour le JSON
       $response = [
           'id'          => $commande->id,
           'user'        => $commande->user,
           'date_commande' => $commande->date_commande,
           'total'       => $commande->total,
           'status'      => $commande->status,
           'puzzles'     => $commande->puzzles->map(function($puzzle) use ($commande) {
               $pivot = $puzzle->pivot;
               return [
                   'id'         => $puzzle->id,
                   'nom'        => $puzzle->nom,
                   'prix'       => $pivot->prix,
                   'quantite'   => $pivot->quantite,
                   'path_image' => $puzzle->path_image
               ];
           })
       ];

       return response()->json($response);
   }
}