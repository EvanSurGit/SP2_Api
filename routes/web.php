<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PuzzleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutAddressController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =======================
//   ACCUEIL PUBLIC
// =======================
Route::get('/', [CategoryController::class, 'index'])->name('home');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{categorie}', [CategoryController::class, 'show'])->name('categories.show');
Route::resource('puzzles', PuzzleController::class)->only(['index','show']);

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth','verified'])
    ->name('dashboard');
// =======================
//     ZONE PROTÉGÉE
// =======================
Route::middleware('auth')->group(function () {

    // PANIER
    Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
    Route::post('/panier/ajouter/{puzzle}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/panier/{rowId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/panier/{rowId}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/panier/finaliser', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('panier/vider', [CartController::class, 'clear'])->name('cart.clear')->middleware('auth');
    
    // CHECKOUT (adresse + paiement)
    Route::get('/checkout/address', [CheckoutAddressController::class, 'create'])->name('checkout.address.create');
    Route::post('/checkout/address', [CheckoutAddressController::class, 'store'])->name('checkout.address.store');
    Route::get('checkout/address', [CheckoutAddressController::class, 'create'])
    ->name('checkout.address');
    Route::post('checkout/address', [CheckoutAddressController::class, 'store'])
    ->name('checkout.address.store');
    Route::get('/checkout/payment', [PaymentController::class, 'show'])->name('checkout.payment.show');
    Route::post('/checkout/payment', [PaymentController::class, 'process'])->name('checkout.payment.process');
    Route::get('checkout/address/create', [CheckoutAddressController::class, 'create'])
    ->name('checkout.address.create')
    ->middleware('auth');
    Route::get('checkout/address', [CheckoutAddressController::class, 'create'])
    ->name('checkout.address')
    ->middleware('auth');
    Route::post('checkout/address', [CheckoutAddressController::class, 'store'])
    ->name('checkout.address.store')
    ->middleware('auth');
    // Confirmation commande
    Route::get('/checkout/confirmation', [PaymentController::class, 'confirm'])->name('checkout.confirmation');

    // Facture et page succès
    Route::get('/checkout/invoice', [PaymentController::class, 'invoice'])->name('checkout.invoice');
    Route::get('/checkout/success', [PaymentController::class, 'success'])->name('checkout.success');
    Route::resource('reviews', \App\Http\Controllers\ReviewController::class);
    // AVIS
    // PROFIL
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CREATION DE PUZZLES
    Route::resource('puzzles', PuzzleController::class)->only(['create','store']);

    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    
});

require __DIR__.'/auth.php';
