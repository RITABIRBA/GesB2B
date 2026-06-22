<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stand extends Model
{
    protected $table = 'stands';

    protected $fillable = [
        'id_evenement',
        'id_entreprise',
        'id_type_stand',
        'id_participant',
        'numero_stand',
        'superficie',
        'standing',
        'composants',
        'prix',
        'est_gratuit',
        'motif_gratuite',
        'statut_paiement_stand',
        'statut_reservation',
    ];

    protected $casts = [
        'composants'   => 'array',
        'est_gratuit'  => 'boolean',
    ];

    // ─── Relations ─────────────────────────────────────────

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'id_evenement');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise');
    }

    public function typeStand()
    {
        return $this->belongsTo(TypeStand::class, 'id_type_stand');
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'id_participant');
    }

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'id_stand');
    }

    // ─── Helpers ───────────────────────────────────────────

    /**
     * Calcule le prix du stand selon son standing
     * et le type de paiement de l'événement.
     */
    public function getPrixCalculeAttribute(): float
    {
        if ($this->est_gratuit) return 0;

        if (!$this->evenement ||
            $this->evenement->type_paiement === 'gratuit') {
            return 0;
        }

        if ($this->typeStand) {
            return (float) $this->typeStand->montant;
        }

        return match ($this->standing) {
            'premium' => (float) $this->evenement->prix_stand_premium,
            'vip'     => (float) $this->evenement->prix_stand_vip,
            default   => (float) $this->evenement->prix_stand_standard,
        };
    }
}