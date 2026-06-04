<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    protected $table = 'entreprises';

 protected $fillable = [
    'id_cdd',
    'nom',
    'nom_responsable',       // ← nouveau
    'prenom_responsable',    // ← nouveau
    'fonction_responsable',  // ← nouveau
    'email_responsable',     // ← nouveau
    'ifu',
    'secteur_activite',
    'description_activites',
    'principaux_produits',
    'sous_secteur',
    'pays',
    'ville',
    'contact',
    'statut_validation',
];

    public function participants()
    {
        return $this->hasMany(Participant::class, 'id_entreprise');
    }

    public function stands()
    {
        return $this->hasMany(Stand::class, 'id_entreprise');
    }
}