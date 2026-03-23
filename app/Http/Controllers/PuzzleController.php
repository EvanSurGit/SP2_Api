<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puzzle;
use App\Models\Category;


class PuzzleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $puzzles = Puzzle::all();
        return view('puzzles.index', compact('puzzles'));

        $categories = Category::all();
        return view('categories', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    $categories = Category::all();  
    
    return view('puzzles.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(Puzzle $puzzle)
    {
        // 1) Si tu stockes une URL complète dans la BDD
        if (!empty($puzzle->image_url)) {
            $image = $puzzle->image_url;
        }
        // 2) Si tu stockes un chemin relatif et que tu veux utiliser public/ (ex: "images/produit.png")
        elseif (!empty($puzzle->image_path) && file_exists(public_path($puzzle->image_path))) {
            $image = asset($puzzle->image_path);
        }
        // 3) Si tu as stocké dans storage/app/public -> Storage::url (nécessite storage:link)
        elseif (!empty($puzzle->image_path) && Storage::disk('public')->exists($puzzle->image_path)) {
            $image = Storage::url($puzzle->image_path); // ex: /storage/products/xxx.jpg
        }
        // 4) Fallback vers public/images/produit.png
        elseif (file_exists(public_path('images/produit.png'))) {
            $image = asset('images/produit.png');
        } else {
            // dernier recours (dev)
            $image = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=='; // 1px transparent
        }
    
        return view('puzzles.show', compact('puzzle', 'image'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Puzzle $puzzle)
    {
        return view('puzzles.edit', compact('puzzle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Puzzle $puzzle)
    {
        $data = $request->validate([
            'nom'         => 'required|max:100',
            'categorie'   => 'required|max:100',
            'description' => 'required|max:500',
            'image'       => 'required|max:500',
            'prix'        => 'required|numeric|between:0,99.99',]);
            
            $puzzle->nom=$request->nom;
            $puzzle->categorie=$request->categorie;
            $puzzle->description=$request->description;
            $puzzle->image=$request->image;
            $puzzle->prix=$request->prix;

            $puzzle->update($data);

            return redirect()
            ->route('puzzles.edit', $puzzle)
            ->with('message', 'Puzzle mis à jour !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Puzzle $puzzle)
    {
        return $this->remove($request, $puzzleId);
    }
    
}
