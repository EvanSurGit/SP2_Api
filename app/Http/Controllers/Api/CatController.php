<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller; // N'oublie pas cet import pour utiliser Storage::disk()

class CatController extends Controller
{
    /**
     * Renvoie la liste de toutes les catégories pour l'API
     */
    public function index()
    {
        // Récupère toutes les catégories
        $categories = Category::all();

        // Pour chaque catégorie, on attache une propriété 'image' contenant l'URL finale
        $categories->each(function ($c) {
            if (!empty($c->image_url)) {
                $img = $c->image_url;
            } elseif (!empty($c->image_path) && file_exists(public_path($c->image_path))) {
                $img = asset($c->image_path);
            } elseif (!empty($c->image_path) && Storage::disk('public')->exists($c->image_path)) {
                $img = Storage::url($c->image_path);
            } else {
                $img = asset('images/produit.png');
            }
            // Attache l'url d'image au model (attribut temporaire)
            $c->image = $img;
        });

        // Renvoie directement la collection en format JSON
        return response()->json($categories, 200);
    }

    /**
     * Renvoie une seule catégorie spécifique avec ses puzzles associés
     */
    public function show(Category $category)
    {
        // Charge automatiquement les puzzles associés à cette catégorie
        $category->load('puzzles');

        // Si jamais la relation est vide ou nulle, on s'assure qu'elle renvoie un tableau vide
        if (is_null($category->puzzles)) {
            $category->setRelation('puzzles', collect());
        }

        // Renvoie la catégorie (avec ses puzzles inclus) en format JSON
        return response()->json($category, 200);
    }
}