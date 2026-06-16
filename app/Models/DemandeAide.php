<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeAide extends Model
{
    protected $table = 'demandes_aide';

    protected $fillable = [
        'id_participant',
        'id_evenement',
        'id_cdd',
        'destinataire_type',
        'sujet',
        'message',
        'statut',
        'traite_le',
        'reponse',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'id_participant');
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'id_evenement');
    }
}