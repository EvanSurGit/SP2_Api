<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PuzzleController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CatController;
use App\Http\Controllers\Api\CommandeController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// --- PUZZLES -----------------------------------------------------------------
// Routes sp�cifiques AVANT apiResource pour �viter les conflits avec {id}
Route::get('/puzzles/stock', [PuzzleController::class, 'stockAll']);
Route::get('/puzzles/{id}/stock', [PuzzleController::class, 'stockView']);
Route::patch('/puzzles/{id}/stock', [PuzzleController::class, 'updateStock']);
Route::get('/puzzles/alertes/stock-bas', [PuzzleController::class, 'stockBas']);
Route::get('/puzzles/alertes/ruptures', [PuzzleController::class, 'ruptures']);

// apiResource g�n�re automatiquement : index, show, store, update, destroy
Route::apiResource('puzzles', PuzzleController::class);

// --- ADMIN -------------------------------------------------------------------
Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);

// --- CATEGORIES --------------------------------------------------------------
Route::get('/cat', [CatController::class, 'index']);

// --- COMMANDES ---------------------------------------------------------------
Route::get('/commandes/en-attente', [CommandeController::class, 'enAttente']);
Route::get('/commandes/{id}', [CommandeController::class, 'show'])->whereNumber('id');
Route::post('/commandes/{id}/valider', [CommandeController::class, 'valider'])->whereNumber('id');
Route::post('/commandes/{id}/expedier', [CommandeController::class, 'expedier'])->whereNumber('id');
Route::delete('/commandes/{id}', [CommandeController::class, 'supprimer'])->whereNumber('id');
// Route pour afficher la page de d�tail Blade
Route::get('/commandes/{id}/detail', [CommandeController::class, 'detail'])->whereNumber('id');