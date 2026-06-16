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
        'date_ouverture_inscriptions',
        'date_cloture_inscriptions',
        'heure_debut',
        'heure_fin',
        'ville',
        'lieu',
        'nom_salle',
        'nombre_tables',
        'montant_inscription',
        'type_paiement',
        'nombre_stands',
        'prix_stand_standard',
        'prix_stand_premium',
        'prix_stand_vip',
        'min_souhaits',
        'max_souhaits',
        'duree_rdv',
        'duree_pause',
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

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'id_evenement');
    }
}