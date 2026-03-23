<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Puzzle;
use App\Models\User;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Liste des puzzles avec stock <= 5
        $stock_faible_list = Puzzle::where('stock', '<=', 5)->get();

        // Total de puzzles différents
        $total_produits = Puzzle::count();

        // 3 dernières commandes avec les puzzles attachés
        $dernieres_commandes = Order::with(['puzzles' => function($q) {
            $q->select('puzzles.id', 'puzzles.nom', 'puzzles.prix');
        }])
        ->orderBy('date_commande', 'desc')
        ->take(3)
        ->get();

        // Somme des ventes des 7 derniers jours
        $ventes_7_jours = Order::where('date_commande', '>=', now()->subDays(7))
            ->sum('total');

        // Nombre total de clients
        $nombre_clients = User::count();

        return response()->json([
            'stock_faible_list' => $stock_faible_list,
            'total_produits' => $total_produits,
            'dernieres_commandes' => $dernieres_commandes,
            'ventes_7_jours' => $ventes_7_jours,
            'nombre_clients' => $nombre_clients,
        ]);
    }
}