<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    protected $table = 'entreprises';

    protected $fillable = [
        'nom',
        'secteur_activite',
        'sous_secteur',
        'pays',
        'ville',
        'contact',
        'statut_validation',
    ];

    public function participants()
    {
        return $this->hasMany(Participant::class, 'id_entreprise');
    }

    public function stands()
    {
        return $this->hasMany(Stand::class, 'id_entreprise');
    }
}