<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // On pointe vers la table existante
    protected $table = 'orders';

    protected $fillable = ['user_id','status','total','date_commande','mode_paiement'];

    public function user()            { return $this->belongsTo(User::class, 'user_id'); }
    public function adresseLivraison(){ return $this->belongsTo(\App\Models\Adresse::class, 'adresse_livraison_id'); }
    
   public function puzzles()
    {
      return $this->belongsToMany(Puzzle::class, 'order_puzzle', 'order_id', 'puzzle_id')
                  ->withPivot('quantite','prix')
                  ->withTimestamps();
    }
}