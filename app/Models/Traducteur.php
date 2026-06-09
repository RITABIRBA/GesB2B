<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traducteur extends Model
{
    protected $table = 'traducteurs';

    protected $fillable = [
        'user_id', // ← nouveau
        'nom',
        'prenom',
        'telephone',
        'email',
        'langue',
    ];

    // ← Relation avec User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'id_traducteur');
    }
}