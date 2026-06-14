<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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