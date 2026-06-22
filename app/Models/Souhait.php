<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Souhait extends Model
{
    protected $table = 'souhaits';

    protected $fillable = [
        'id_participant',
        'id_participant_cible',
        'id_evenement',
        'priorite',
        'type',
        'statut',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'id_participant');
    }

    public function participantCible()
    {
        return $this->belongsTo(Participant::class, 'id_participant_cible');
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'id_evenement');
    }
}