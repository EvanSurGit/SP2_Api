<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Puzzle;

class PuzzleController extends Controller
{
    // Liste tous les puzzles
    public function index()
    {
        $puzzles = Puzzle::all()->map(function($puzzle) {
            return [
                'id' => $puzzle->id,
                'nom' => $puzzle->nom,
                'description' => $puzzle->description,
                'prix' => $puzzle->prix,
                'stock' => $puzzle->stock,
                'categorie' => $puzzle->categorie_id,
                'path_image' => $puzzle->path_image ? url($puzzle->path_image) : null, // URL complète
            ];
        });

        return response()->json($puzzles);
    }

    // Détail d’un puzzle
    public function show($id)
    {
        $puzzle = Puzzle::findOrFail($id);

        $puzzleData = [
            'id' => $puzzle->id,
            'nom' => $puzzle->nom,
            'description' => $puzzle->description,
            'prix' => $puzzle->prix,
            'stock' => $puzzle->stock,
            'categorie' => $puzzle->categorie_id,
            'path_image' => $puzzle->path_image ? url($puzzle->path_image) : null,
        ];

        return response()->json($puzzleData);
    }
    

    // Création d’un puzzle
    public function store(Request $request)
    {
        // 1. Nouvelle validation avec stock et categorie_id
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'path_image' => 'nullable|string', // <-- ICI : path_image
            'prix' => 'required|numeric', 
            'stock' => 'required|integer|min:0',
            'categorie_id' => 'required|integer',
        ]);

        // 2. Création du puzzle
        $puzzle = Puzzle::create($validatedData);

        // 3. Réponse à ton app Flutter
        return response()->json($puzzle, 201);
    }

    // Mise à jour
    public function update(Request $request, Puzzle $puzzle)
    {
        // C'est ICI qu'il faut corriger les noms pour que ça matche avec Flutter !
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'path_image' => 'nullable|string', // <-- ICI (path_image)
            'prix' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'categorie_id' => 'required|integer|exists:categories,id', // <-- ET ICI (categorie_id)
        ]);

        $puzzle->update($validatedData);

        return response()->json($puzzle, 200);
    }

    // Suppression
    public function destroy(Puzzle $puzzle)
    {
        $puzzle->delete(); // Supprime la ligne de la BDD
        return response()->json(['message' => 'Puzzle supprimé avec succès !'], 200);
    }
    
    public function stockView($id)
    {
        $puzzle = Puzzle::findOrFail($id);
        return response()->json([
            'stock' => $puzzle->stock,
        ]);
    }  
    
    // Retourne uniquement nom, prix, image et stock de tous les puzzles
    public function stockAll()
    {
    $puzzles = Puzzle::all()->map(function($puzzle) {
        return [
            'nom'        => $puzzle->nom,
            'prix'       => $puzzle->prix,
            'path_image' => $puzzle->path_image ? url($puzzle->path_image) : null,
            'stock'      => $puzzle->stock,
        ];
    });

    return response()->json($puzzles);
    }
    
    // Mise à jour du stock uniquement
    public function updateStock(Request $request, $id)
    {
    $puzzle = Puzzle::findOrFail($id);
    
    $request->validate([
        'stock' => 'required|integer|min:0',
    ]);

    $puzzle->stock = $request->stock;
    $puzzle->save();

    return response()->json($puzzle, 200);
    }
    
    // GET /api/puzzles/alertes/stock-bas
    // Retourne tous les puzzles avec un stock inférieur à 5
    public function stockBas()
    {
    $puzzles = Puzzle::where('stock', '<', 5)
                     ->where('stock', '>', 0)
                     ->get(['id', 'nom', 'stock']);

    return response()->json($puzzles);
    }

    // GET /api/puzzles/alertes/ruptures
    // Retourne tous les puzzles en rupture totale de stock
    public function ruptures()
    {
    $puzzles = Puzzle::where('stock', 0)
                     ->get(['id', 'nom', 'stock']);

    return response()->json($puzzles);
}
    
}