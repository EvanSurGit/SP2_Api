<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panier extends Model
{
    use HasFactory;

    protected $table = 'paniers';

    protected $fillable = [
        'status',
        'total',
        'user_id',
        'adresse_livraison_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function puzzles()
    {
        return $this->belongsToMany(Puzzle::class, 'panier_puzzle', 'panier_id', 'puzzle_id')
        ->withPivot('quantite', 'prix');
    }

    public function adresseLivraison()
    {
        return $this->belongsTo(Adresse::class, 'id', 'id');
    }
    public $timestamps = false; // <-- désactive created_at / updated_at
}
