<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recu extends Model
{
    protected $table = 'recus';

    protected $fillable = [
        'id_paiement',
        'date',
        'montant',
    ];

    public function paiement()
    {
        return $this->belongsTo(Paiement::class, 'id_paiement');
    }
}