<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    protected $table = 'evenements';

    protected $fillable = [
        'id_type_evenement',
        'nom',
        'annee',
        'date_debut',
        'date_fin',
        'heure_debut',
        'heure_fin',
        'ville',
        'lieu',
        'montant_inscription',
        'type_paiement',
    ];

    public function typeEvenement()
    {
        return $this->belongsTo(TypeEvenement::class, 'id_type_evenement');
    }

    public function participants()
    {
        return $this->hasMany(Participant::class, 'id_evenement');
    }

    public function stands()
    {
        return $this->hasMany(Stand::class, 'id_evenement');
    }

    // ← Relation manquante !
    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'id_evenement');
    }
}