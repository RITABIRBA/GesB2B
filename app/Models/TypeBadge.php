<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeBadge extends Model
{
    protected $table = 'type_badges';

    protected $fillable = [
        'libelle',
        'description',
    ];

    public function badges()
    {
        return $this->hasMany(Badge::class, 'id_type_badge');
    }
}