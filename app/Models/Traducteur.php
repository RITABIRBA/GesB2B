<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traducteur extends Model
{
    protected $table = 'traducteurs';

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'email',
        'langue',
    ];

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'id_traducteur');
    }
}