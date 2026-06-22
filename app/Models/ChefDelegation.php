<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChefDelegation extends Model
{
    protected $table = 'chefs_delegation';

    protected $fillable = [
        'id_user', 'nom', 'email', 'pays_zone', 'zone_personnalisee',
        'id_evenement', 'telephone', 'notes', 'est_admin', 'actif',
    ];

    protected $casts = [
        'est_admin' => 'boolean',
        'actif'     => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'id_evenement');
    }

    public function membres()
    {
        return $this->hasMany(Participant::class, 'chef_delegation_id');
    }

    /**
     * Affiche la zone réelle couverte (pays ou zone libre si "Autre").
     */
    public function getZoneAffichageAttribute(): string
    {
        return $this->pays_zone === 'Autre'
            ? ($this->zone_personnalisee ?: 'Non précisée')
            : $this->pays_zone;
    }

    /**
     * Vérifie si ce CDD couvre un événement donné.
     * Si aucun événement n'est précisé (id_evenement null),
     * il couvre TOUS les événements.
     */
    public function couvreEvenement(int $idEvenement): bool
    {
        if (!$this->id_evenement) {
            return true;
        }
        return $this->id_evenement == $idEvenement;
    }
}