<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Hash;

class InscriptionEntreprise extends Component
{
    public $nom = '';
    public $secteur_activite = '';
    public $sous_secteur = '';
    public $pays = '';
    public $ville = '';
    public $contact = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $id_cdd = '';

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
            'nom'              => 'required|string|max:255',
            'secteur_activite' => 'required|string|max:255',
            'pays'             => 'required|string|max:255',
            'ville'            => 'required|string|max:255',
            'contact'          => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|min:8|confirmed',
            'id_cdd'           => 'required',
        ]);

        // Crée le compte USER
        $user = User::create([
            'name'     => $this->nom,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
        ]);
        $user->assignRole('entreprise');

        // Crée l'ENTREPRISE
        Entreprise::create([
            'id_cdd'           => $this->id_cdd,
            'nom'              => $this->nom,
            'secteur_activite' => $this->secteur_activite,
            'sous_secteur'     => $this->sous_secteur,
            'pays'             => $this->pays,
            'ville'            => $this->ville,
            'contact'          => $this->email,
            'statut_validation' => 'en_attente',
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