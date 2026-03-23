<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['nom']; // <-- PAS 'name'

    public function puzzles()
    {
        return $this->hasMany(Puzzle::class, 'categorie_id');
    }
}
