<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Adresse extends Model
{
    use HasFactory;

    protected $table = 'delivery_adresses'; // table au singulier

    protected $fillable = [
        'id',
        'user_id',
        'adresse',
        'ville',
        'code_postal',
        'pays',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
    public $timestamps = false; // <-- important
}
