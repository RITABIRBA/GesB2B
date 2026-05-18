<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $table = 'badges';

    protected $fillable = [
        'id_participant',
        'id_type_badge',
        'qr_code',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'id_participant');
    }

    public function typeBadge()
    {
        return $this->belongsTo(TypeBadge::class, 'id_type_badge');
    }
}