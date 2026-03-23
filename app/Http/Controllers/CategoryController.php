<?php

 namespace App\Http\Controllers;


use App\Models\Category;


class CategoryController extends Controller

{

    public function index()

    {

        // récupère toutes les catégories (ou paginate si tu préfères)

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

            // attache l'url d'image au model (attribut temporaire)

            $c->image = $img;

        });


        // passe la collection de modèles à la vue (tu auras toujours accès à tous les champs d'origine)

        return view('categories.index', compact('categories'));

        return response()->json(\App\Models\Categorie::all());

    }

    

    public function show(Category $categorie)

    {

      // Vérifiez que la relation est bien définie et récupérez les puzzles

      $puzzles = $categorie->puzzles; // Récupère les puzzles associés à cette catégorie

     

      // Si la variable $puzzles est null ou vide, vous pouvez la forcer à être une collection vide

      if (is_null($puzzles)) {

          $puzzles = collect(); // Créer une collection vide si aucune puzzle n'est trouvé

      }

     

      // Retourner la vue avec la catégorie et les puzzles associés

      return view('categories.show', compact('categorie', 'puzzles'));

    }

} 