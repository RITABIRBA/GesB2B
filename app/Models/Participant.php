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
        'statut_participant',
        'statut_preinscription',
        'participation_rdv',
        'date_naissance',
        'filiere',
        'universite',
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
        'types_partenariat',
        'type_partenariat_autre',
        'profils_partenaire',
        'secteurs_recherche',
        'secteur_recherche_autre',
        'objectif_participation',
    ];

    protected $casts = [
        'disponibilites'     => 'array',
        'types_partenariat'  => 'array',
        'profils_partenaire' => 'array',
        'secteurs_recherche' => 'array',
        'participation_rdv'  => 'boolean',
        'date_naissance'     => 'date',
    ];

    // ─── Helpers ───────────────────────────────────────────

    /**
     * Vérifie si le participant est un étudiant
     */
    public function estEtudiant(): bool
    {
        return strtolower($this->fonction ?? '') === 'étudiant'
            || strtolower($this->fonction ?? '') === 'etudiant';
    }

    /**
     * Vérifie si le participant est un sponsor
     */
    public function estSponsor(): bool
    {
        return $this->statut_participant === 'sponsor';
    }

    /**
     * Vérifie si le participant est un partenaire
     */
    public function estPartenaire(): bool
    {
        return $this->statut_participant === 'partenaire';
    }

    /**
     * Calcule l'âge du participant
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_naissance) return null;
        return $this->date_naissance->age;
    }

    /**
     * Helper statique pour trouver le participant connecté.
     */
    public static function findForUser($user): ?self
    {
        if (!$user) return null;

        $participant = self::where('email', $user->email)->first();
        if ($participant) return $participant;

        if (str_contains($user->email, '@gesb2b.local')) {
            $id = str_replace(['participant_', '@gesb2b.local'], '', $user->email);
            $participant = self::find($id);
            if ($participant) return $participant;
        }

        return null;
    }
    /**
 * Vérifie si le profil B2B (critères de recherche) est complété.
 */
public function profilB2BComplet(): bool
{
    $zone = !empty($this->zone_geographique);

    $secteurs = is_array($this->secteurs_recherche)
        ? $this->secteurs_recherche
        : (json_decode($this->secteurs_recherche ?? '[]', true) ?: []);

    $types = is_array($this->types_partenariat)
        ? $this->types_partenariat
        : (json_decode($this->types_partenariat ?? '[]', true) ?: []);

    return $zone && !empty($secteurs) && !empty($types);
}

    // ─── Relations ─────────────────────────────────────────

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise');
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'id_evenement');
    }

    public function chefDelegationOfficiel()
{
    return $this->belongsTo(ChefDelegation::class, 'chef_delegation_id');
}

/**
 * Calcule le montant net à payer après remise éventuelle.
 */
public function montantApresRemise(float $montantBase): array
{
    $pourcentage = \App\Models\Remise::calculerMeilleureRemise($this);
    $montantRemise = $montantBase * ($pourcentage / 100);
    return [
        'montant_brut'    => $montantBase,
        'pourcentage'     => $pourcentage,
        'montant_remise'  => round($montantRemise, 2),
        'montant_net'     => round($montantBase - $montantRemise, 2),
    ];
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

    public function stand()
    {
        return $this->hasOne(Stand::class, 'id_participant');
    }

    public function absencesJournee()
    {
        return $this->hasMany(AbsenceJournee::class, 'id_participant');
    }
}