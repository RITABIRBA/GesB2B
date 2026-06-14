<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $table = 'participants';

    protected $fillable = [
        'id_entreprise',
        'id_evenement',
        'id_chef_delegation',
        'nom',
        'prenom',
        'genre',
        'fonction',
        'ifu',
        'email',
        'telephone',
        'pays',
        'ville',
        'code_acces',
        'role',
        'statut_historique',
        'statut_adhesion',
        'participation_rdv',
        'secteur_activite',
        'sous_secteur',
        'description_activites',
        'principaux_produits',
        'annee_creation',
        'nombre_salaries',
        'chiffre_affaires',
        'type_partenaire',
        'zone_geographique',
        'disponibilites',
        // Nouveaux champs
        'types_partenariat',
        'type_partenariat_autre',
        'profils_partenaire',
        'secteurs_recherche',
        'secteur_recherche_autre',
        'objectif_participation',
    ];

    protected $casts = [
        'disponibilites'    => 'array',
        'types_partenariat' => 'array',
        'profils_partenaire' => 'array',
        'secteurs_recherche' => 'array',
        'participation_rdv'  => 'boolean',
    ];

    /**
     * Helper statique pour trouver le participant connecté.
     */
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

    public function chefDelegation()
    {
        return $this->belongsTo(Participant::class, 'id_chef_delegation');
    }

    public function participantsSousDelegation()
    {
        return $this->hasMany(Participant::class, 'id_chef_delegation');
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

    public function rendezVous1()
    {
        return $this->hasMany(RendezVous::class, 'id_participant1');
    }

    public function rendezVous2()
    {
        return $this->hasMany(RendezVous::class, 'id_participant2');
    }

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'id_participant1');
    }

    public function rendezVousInvite()
    {
        return $this->hasMany(RendezVous::class, 'id_participant2');
    }
}