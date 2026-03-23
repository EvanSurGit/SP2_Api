<?php

// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Category;

class DashboardController extends Controller
{
    public function index()
    {
        // Récupérer toutes les catégories depuis la base de données
        $categories = Category::all();

        // Passer les catégories à la vue 'dashboard'
        return view('dashboard', compact('categories'));
    }
}


