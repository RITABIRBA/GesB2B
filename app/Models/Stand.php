<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stand extends Model
{
    protected $table = 'stands';

    protected $fillable = [
        'id_evenement',
        'id_entreprise',
        'numero_stand',
        'superficie',
        'standing',
        'prix',
        'statut_paiement_stand',
        'statut_reservation',
    ];

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'id_evenement');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise');
    }

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'id_stand');
    }

    /**
     * Calcule le prix du stand selon son standing et le type
     * de paiement de l'événement. Renvoie 0 si l'événement est gratuit.
     */
    public function getPrixCalculeAttribute(): float
    {
        if (!$this->evenement || $this->evenement->type_paiement === 'gratuit') {
            return 0;
        }

        return match ($this->standing) {
            'premium' => (float) $this->evenement->prix_stand_premium,
            'vip'     => (float) $this->evenement->prix_stand_vip,
            default   => (float) $this->evenement->prix_stand_standard,
        };
    }
}