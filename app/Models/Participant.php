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
        'ifu',
        'prenom',
        'genre',
        'fonction',
        'participation_rdv',
        'secteur_activite',
        'email',
        'telephone',
        'code_acces',
        'role',
        'statut_historique',
        'statut_adhesion',
    ];

    // ← Helper statique pour trouver le participant connecté
    public static function findForUser($user): ?self
    {
        if (!$user) return null;

        // 1. Par email direct
        $participant = self::where('email', $user->email)->first();
        if ($participant) return $participant;

        // 2. Par email fictif (participant_X@gesb2b.local)
        if (str_contains($user->email, '@gesb2b.local')) {
            $id = str_replace(['participant_', '@gesb2b.local'], '', $user->email);
            $participant = self::find($id);
            if ($participant) return $participant;
        }

        return null;
    }

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

    // ← Relations RDV renommées pour correspondre au code
    public function rendezVous1()
    {
        return $this->hasMany(RendezVous::class, 'id_participant1');
    }

    public function rendezVous2()
    {
        return $this->hasMany(RendezVous::class, 'id_participant2');
    }

    // ← Garde les anciennes pour compatibilité
    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'id_participant1');
    }

    public function rendezVousInvite()
    {
        return $this->hasMany(RendezVous::class, 'id_participant2');
    }
}