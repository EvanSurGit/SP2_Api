<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// Import des modèles liés
use App\Models\Adresse;
use App\Models\Panier;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Les attributs pouvant être remplis en masse.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Les attributs cachés pour la sérialisation.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs à caster.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /* =======================
     |  Relations Adresses
     |======================= */

    // Toutes les adresses de l'utilisateur
    public function adresses()
    {
        return $this->hasMany(Adresse::class, 'user_id', 'id');
    }

    // Dernière adresse modifiée (utile pour le préremplissage)
    public function derniereAdresse()
    {
        return $this->hasOne(Adresse::class, 'user_id', 'id')
                    ->latestOfMany('updated_at');
    }

    /* =======================
     |  Relations Paniers
     |======================= */

    public function paniers()
    {
        return $this->hasMany(Panier::class, 'user_id', 'id');
    }

    /* =======================
     |  Méthodes métiers
     |======================= */

    // Vérifie si l'utilisateur a déjà acheté un puzzle donné
    public function hasPurchasedPuzzle($puzzleId)
    {
        return $this->paniers()
            ->where('status', '!=', 'open') // uniquement paniers finalisés
            ->whereHas('puzzles', fn($q) => $q->where('puzzles.id', $puzzleId))
            ->exists();
    }
    public $timestamps = false; // <-- désactive created_at / updated_at
}
