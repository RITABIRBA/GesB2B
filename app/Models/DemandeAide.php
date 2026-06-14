<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeAide extends Model
{
    protected $table = 'demandes_aide';

    protected $fillable = [
        'id_participant',
        'id_cdd',
        'sujet',
        'message',
        'statut',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'id_participant');
    }
}