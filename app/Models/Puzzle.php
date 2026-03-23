<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Puzzle extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'description', 'path_image', 'prix', 'stock', 'categorie_id'];
    
    public $timestamps = false;

    /**
     * Relation : un puzzle peut avoir plusieurs avis
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'puzzles_id');
    }

    public function paniers()
    {
        return $this->belongsToMany(Panier::class, 'appartient', 'id_Puzzle', 'id_Panier')
        ->withPivot('quantite', 'prix');
    }
    
    public function categorie()
    {
      return $this->belongsTo(Category::class, 'categorie_id');
    }
    
   public function orders()
   {
      return $this->belongsToMany(Order::class, 'orders_puzzle', 'puzzle_id', 'order_id')
                  ->withPivot('quantite','prix')
                  ->withTimestamps();
  }
}
