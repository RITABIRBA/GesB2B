<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $table = 'paiements';

    protected $fillable = [
        'id_inscription',
        'montant',
        'date_paiement',
        'mode_paiement',
        'statut',
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class, 'id_inscription');
    }

    public function recu()
    {
        return $this->hasOne(Recu::class, 'id_paiement');
    }
}