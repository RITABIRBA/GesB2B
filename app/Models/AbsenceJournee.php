<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenceJournee extends Model
{
    protected $table = 'absences_journee';

    protected $fillable = [
        'id_participant',
        'id_evenement',
        'date',
        'motif',
        'rdv_annules',
        'signale_par',
    ];

    protected $casts = [
        'date'        => 'date',
        'rdv_annules' => 'array',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'id_participant');
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'id_evenement');
    }
}