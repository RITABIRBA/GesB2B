<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeStand extends Model
{
    protected $table = 'types_stands';

   protected $fillable = [
    'id_evenement',
    'standing',
    'quantite',
    'superficie',
    'est_gratuit',
    'montant',
    'composants',
];
    protected $casts = [
        'composants'  => 'array',
        'est_gratuit' => 'boolean',
        'montant'     => 'decimal:2',
    ];

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'id_evenement');
    }

    public function stands()
    {
        return $this->hasMany(Stand::class, 'id_type_stand');
    }
}