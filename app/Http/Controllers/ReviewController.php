<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Puzzle;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(Request $request, $puzzleId)
{
    $user = $request->user();

    // Vérifie si l’utilisateur a bien acheté ce puzzle
    if (!$user->hasPurchasedPuzzle($puzzleId)) {
        return back()->withErrors(['message' => 'Vous devez avoir commandé ce produit pour laisser un avis.']);
    }

    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ]);

    \App\Models\Review::create([
        'user_id' => $user->id,
        'product_id' => $puzzleId,
        'rating' => $request->rating,
        'comment' => $request->comment,
    ]);

    return back()->with('success', 'Merci pour votre avis !');
}

}
