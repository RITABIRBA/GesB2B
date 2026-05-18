<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeEvenement extends Model
{
    protected $table = 'type_evenements';

    protected $fillable = [
        'nom',
    ];

    public function evenements()
    {
        return $this->hasMany(Evenement::class, 'id_type_evenement');
    }
}