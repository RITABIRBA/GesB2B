<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    protected $table = 'evenements';

    protected $fillable = [
        'id_type_evenement',
        'nom',
        'annee',
        'type_evenement',
        'date_debut',
        'date_fin',
        'date_ouverture_inscriptions',
        'date_cloture_inscriptions',
        'date_limite_rdv',
        'heure_debut',
        'heure_fin',
        'ville',
        'lieu',
        'nom_salle',
        'nombre_tables',
        'montant_inscription',
        'type_paiement',
        'nombre_stands',
        'prix_stand_standard',
        'prix_stand_premium',
        'prix_stand_vip',
        'min_souhaits',
        'max_souhaits',
        'duree_rdv',
        'duree_pause',
    ];

    protected $casts = [
        'date_debut'                  => 'date',
        'date_fin'                    => 'date',
        'date_ouverture_inscriptions' => 'date',
        'date_cloture_inscriptions'   => 'date',
        'date_limite_rdv'             => 'date',
    ];

    // ─── Helpers ───────────────────────────────────────────

    /**
     * Vérifie si l'événement est de type B2B
     */
    public function estB2B(): bool
    {
        return ($this->type_evenement ?? 'avec_b2b') === 'avec_b2b';
    }

    /**
     * Vérifie si les inscriptions sont encore ouvertes
     */
    public function inscriptionsOuvertes(): bool
    {
        $today = now()->toDateString();

        if ($this->date_cloture_inscriptions &&
            $today > $this->date_cloture_inscriptions->toDateString()) {
            return false;
        }

        return true;
    }

    /**
     * Vérifie si les souhaits sont encore ouverts
     */
    public function souhaitOuverts(): bool
    {
        if (!$this->date_limite_rdv) return true;
        return now()->toDateString() <= $this->date_limite_rdv->toDateString();
    }

    // ─── Relations ─────────────────────────────────────────

    public function typeEvenement()
    {
        return $this->belongsTo(TypeEvenement::class, 'id_type_evenement');
    }

    public function participants()
    {
        return $this->hasMany(Participant::class, 'id_evenement');
    }

    public function stands()
    {
        return $this->hasMany(Stand::class, 'id_evenement');
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'id_evenement');
    }

    public function typesStands()
    {
        return $this->hasMany(TypeStand::class, 'id_evenement');
    }

    public function remises()
    {
        return $this->hasMany(RemiseEvenement::class, 'id_evenement');
    }
}