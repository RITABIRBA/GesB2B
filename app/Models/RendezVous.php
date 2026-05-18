<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    protected $table = 'rendez_vous';

    protected $fillable = [
        'id_participant1',
        'id_participant2',
        'id_traducteur',
        'id_stand',
        'date',
        'heure_debut',
        'heure_fin',
        'statut',
    ];

    public function participant1()
    {
        return $this->belongsTo(Participant::class, 'id_participant1');
    }

    public function participant2()
    {
        return $this->belongsTo(Participant::class, 'id_participant2');
    }

    public function traducteur()
    {
        return $this->belongsTo(Traducteur::class, 'id_traducteur');
    }

    public function stand()
    {
        return $this->belongsTo(Stand::class, 'id_stand');
    }
}