<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sponsor extends Model
{
    protected $table = 'sponsors';

    protected $fillable = [
        'id_evenement',
        'type_entite',
        'nom',
        'nom_contact',
        'email',
        'telephone',
        'site_web',
        'logo',
        'description',
        'niveau',
        'nb_stands_gratuits',
        'nb_badges_vip',
        'remise_inscription',
        'autres_avantages',
        'id_entreprise',
    ];

    // ─── Relations ────────────────────────────────────────────

    public function evenement(): BelongsTo
    {
        return $this->belongsTo(Evenement::class, 'id_evenement');
    }

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise');
    }

    // ─── Helpers ──────────────────────────────────────────────

    public function getNiveauLabelAttribute(): string
    {
        return match($this->niveau) {
            'principal'  => 'Principal',
            'associe'    => 'Associé',
            'partenaire' => 'Partenaire',
            'supporter'  => 'Supporter',
            default      => ucfirst($this->niveau),
        };
    }

    public function getNiveauCouleurAttribute(): string
    {
        return match($this->niveau) {
            'principal'  => '#FFD700', // Or
            'associe'    => '#C0C0C0', // Argent
            'partenaire' => '#CD7F32', // Bronze
            'supporter'  => '#6b7280', // Gris
            default      => '#6b7280',
        };
    }

    public function getTotalAvantagesAttribute(): array
    {
        $avantages = [];
        if ($this->nb_stands_gratuits > 0)
            $avantages[] = $this->nb_stands_gratuits . ' stand(s) gratuit(s)';
        if ($this->nb_badges_vip > 0)
            $avantages[] = $this->nb_badges_vip . ' badge(s) VIP';
        if ($this->remise_inscription > 0)
            $avantages[] = $this->remise_inscription . '% de remise sur inscription';
        if ($this->autres_avantages)
            $avantages[] = $this->autres_avantages;
        return $avantages;
    }
}