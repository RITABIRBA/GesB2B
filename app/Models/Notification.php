<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'id_participant',
        'contenu',
        'date_envoie',
        'type',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'id_participant');
    }
}