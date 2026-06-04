<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Hash;

class InscriptionEntreprise extends Component
{
    // Infos responsable
    public $nom_responsable      = ''; // ← nouveau
    public $prenom_responsable   = ''; // ← nouveau
    public $fonction_responsable = ''; // ← nouveau

    // Infos entreprise
    public $nom                  = '';
    public $ifu                  = '';
    public $secteur_activite     = '';
    public $sous_secteur         = '';
    public $description_activites = '';
    public $principaux_produits  = '';
    public $pays                 = '';
    public $ville                = '';
    public $contact              = '';

    // Compte
    public $email                = '';
    public $password             = '';
    public $password_confirmation = '';
    public $id_cdd               = '';

    public $showSuccessModal = false;

    public $secteurs = [
        'Agriculture', 'Industrie', 'Commerce', 'Services',
        'Technologie', 'Transport', 'Construction', 'Tourisme',
        'Santé', 'Education', 'Finance', 'Energie', 'Mines',
        'Artisanat', 'Autre',
    ];

    public $pays_liste = [
        'Burkina Faso', 'Côte d\'Ivoire', 'Mali', 'Sénégal',
        'Ghana', 'Togo', 'Bénin', 'Niger', 'Guinée', 'Cameroun',
        'Nigeria', 'France', 'Allemagne', 'États-Unis', 'Chine', 'Autre',
    ];

    public function sinscrire()
    {
        $this->validate([
            'nom_responsable'       => 'required|string|max:255',
            'prenom_responsable'    => 'required|string|max:255',
            'fonction_responsable'  => 'nullable|string|max:255',
            'nom'                   => 'required|string|max:255',
            'ifu'                   => 'nullable|string|max:255|unique:entreprises,ifu',
            'secteur_activite'      => 'required|string|max:255',
            'description_activites' => 'required|string',
            'pays'                  => 'required|string|max:255',
            'ville'                 => 'required|string|max:255',
            'contact'               => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:8|confirmed',
            'id_cdd'                => 'required',
        ]);

        // Crée le compte USER
        $user = User::create([
            'name'     => $this->nom_responsable . ' ' . $this->prenom_responsable,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
        ]);
        $user->assignRole('entreprise');

        // Crée l'ENTREPRISE
        Entreprise::create([
            'id_cdd'                => $this->id_cdd,
            'nom'                   => $this->nom,
            'nom_responsable'       => $this->nom_responsable,       // ← nouveau
            'prenom_responsable'    => $this->prenom_responsable,    // ← nouveau
            'fonction_responsable'  => $this->fonction_responsable,  // ← nouveau
            'email_responsable'     => $this->email,                 // ← nouveau
            'ifu'                   => $this->ifu ?: null,
            'secteur_activite'      => $this->secteur_activite,
            'description_activites' => $this->description_activites,
            'principaux_produits'   => $this->principaux_produits,
            'sous_secteur'          => $this->sous_secteur,
            'pays'                  => $this->pays,
            'ville'                 => $this->ville,
            'contact'               => $this->contact,
            'statut_validation'     => 'en_attente',
        ]);

        $this->showSuccessModal = true;
    }

    public function render()
    {
        return view('livewire.auth.inscription-entreprise', [
            'secteurs'   => $this->secteurs,
            'pays_liste' => $this->pays_liste,
            'cdds'       => User::role('cdd')->orderBy('name')->get(),
        ])->layout('layouts.guest');
    }
}