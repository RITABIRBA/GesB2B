<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    protected $table = 'inscriptions';

    protected $fillable = [
        'id_participant',
        'id_evenement',
        'date_inscription',
        'montant_paye',
        'statut_paiement',
        'statut_presence',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'id_participant');
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'id_evenement');
    }

    public function paiement()
    {
        return $this->hasOne(Paiement::class, 'id_inscription');
    }
}