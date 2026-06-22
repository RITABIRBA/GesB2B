<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemiseEvenement extends Model
{
    protected $table = 'remises_evenement';

    protected $fillable = [
        'id_evenement',
        'critere',
        'age_max',
        'seuil_participants',
        'pourcentage_remise',
        'stand_gratuit',
        'cumulable',
    ];

    protected $casts = [
        'stand_gratuit'      => 'boolean',
        'cumulable'          => 'boolean',
        'pourcentage_remise' => 'decimal:2',
    ];

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'id_evenement');
    }
}