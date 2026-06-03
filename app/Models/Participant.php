<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $table = 'participants';

    protected $fillable = [
    'id_cdd',
    'id_entreprise',
    'id_evenement',
    'nom',
    'prenom',
    'genre',           // ← nouveau
    'fonction',        // ← nouveau
    'participation_rdv', // ← nouveau
    'secteur_activite',
    'email',
    'telephone',
    'code_acces',
    'role',
    'statut_historique',
];
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise');
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'id_evenement');
    }

    public function badge()
{
    return $this->hasOne(Badge::class, 'id_participant');
}
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'id_participant');
    }

    public function souhaits()
    {
        return $this->hasMany(Souhait::class, 'id_participant');
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'id_participant');
    }

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'id_participant1')
                    ->orWhere('id_participant2', $this->id);
    }
}