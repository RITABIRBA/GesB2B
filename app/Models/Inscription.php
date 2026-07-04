<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Inscription extends Model
{
    protected $table = 'inscriptions';

    protected $fillable = [
        'id_participant',
        'id_evenement',
        'date_inscription',
        'montant_paye',
        'statut_paiement',
        'statut_presence',
        'secteur_recherche',
        'type_partenaire',
        'zone_geographique',
    ];

    // ============================================================
    // BOOT — Génération automatique du badge
    // ============================================================

    protected static function boot()
    {
        parent::boot();

        // ✅ Se déclenche à chaque update ou create d'une inscription
        static::saved(function (Inscription $inscription) {
            // Si le statut de paiement est "paye", on génère le badge
            if ($inscription->statut_paiement === 'paye') {
                static::genererBadgeAutomatique($inscription);
            }
        });
    }

    /**
     * ✅ Génère automatiquement un badge pour le participant
     * dès que son inscription passe au statut "paye".
     * Cette méthode est centralisée ici pour que TOUS les
     * composants (Admin, Superviseur, CDD, Entreprise Dashboard...)
     * déclenchent la génération du badge de la même façon,
     * sans avoir à dupliquer le code partout.
     */
    private static function genererBadgeAutomatique(Inscription $inscription): void
    {
        $participant = $inscription->participant ?? Participant::find($inscription->id_participant);
        if (!$participant) return;

        // Ne pas créer de doublon
        if (Badge::where('id_participant', $participant->id)->exists()) return;

        $libelle = match($participant->role) {
            'vip'          => 'VIP',
            'organisateur' => 'Organisateur',
            'exposant'     => 'Exposant',
            'representant' => 'Représentant',
            default        => 'Visiteur',
        };

        $typeBadge = TypeBadge::firstOrCreate(
            ['libelle' => $libelle],
            ['description' => 'Badge ' . $libelle]
        );

        $qrCode = strtoupper(
            substr($participant->nom ?: 'XX', 0, 2) .
            substr($participant->prenom ?: 'XX', 0, 2) .
            '-' . $inscription->id_evenement .
            '-' . Str::random(6)
        );

        Badge::create([
            'id_participant' => $participant->id,
            'id_type_badge'  => $typeBadge->id,
            'qr_code'        => $qrCode,
        ]);
    }

    // ============================================================
    // RELATIONS
    // ============================================================

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'id_participant');
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'id_evenement');
    }

    /**
     * Retourne le DERNIER paiement de l'inscription.
     * ← Important : on utilise latest() pour avoir
     *   le paiement le plus récent avec son reçu.
     */
    public function paiement()
    {
        return $this->hasOne(Paiement::class, 'id_inscription')
            ->latest();
    }

    /**
     * Retourne TOUS les paiements de l'inscription.
     */
    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'id_inscription');
    }
}