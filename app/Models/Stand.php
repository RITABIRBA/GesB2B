<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stand extends Model
{
    protected $table = 'stands';

    protected $fillable = [
        'id_evenement',
        'id_entreprise',
        'numero_stand',
        'superficie',
        'standing',
    ];

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'id_evenement');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise');
    }

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'id_stand');
    }
}