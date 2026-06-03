<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Models\Participant;
use App\Models\Evenement;
use Illuminate\Support\Facades\Hash;

class InscriptionParticipant extends Component
{
    // Infos personnelles
    public $nom = '';
    public $prenom = '';
    public $genre = '';
    public $fonction = '';
    public $email = '';
    public $telephone = '';
    public $secteur_activite = '';
    public $participation_rdv = true;

    // Rôle et événement
    public $role = 'visiteur';
    public $id_evenement = '';
    public $id_cdd = '';

    // Mot de passe
    public $password = '';
    public $password_confirmation = '';

    // Modal succès
    public $showSuccessModal = false;
    public $code_acces_genere = '';

    public $roles = ['exposant', 'visiteur', 'vip', 'organisateur'];

    public $secteurs = [
        'Agriculture', 'Industrie', 'Commerce', 'Services',
        'Technologie', 'Transport', 'Construction', 'Tourisme',
        'Santé', 'Education', 'Finance', 'Energie', 'Mines',
        'Artisanat', 'Autre',
    ];

    public function sinscrire()
    {
        $this->validate([
            'nom'          => 'required|string|max:255',
            'prenom'       => 'required|string|max:255',
            'genre'        => 'required|string',
            'email'        => 'required|email|unique:users,email',
            'telephone'    => 'required|string|max:20',
            'password'     => 'required|min:8|confirmed',
            'id_evenement' => 'required',
            'id_cdd'       => 'required',
            'role'         => 'required',
        ]);

        // Génère le code d'accès
        $code = strtoupper(substr($this->nom, 0, 3) . rand(1000, 9999));

        // Crée le compte USER
        $user = User::create([
            'name'     => $this->nom . ' ' . $this->prenom,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
        ]);
        $user->assignRole('participant');

        // Crée le PARTICIPANT
        Participant::create([
            'id_cdd'            => $this->id_cdd,
            'id_evenement'      => $this->id_evenement,
            'id_entreprise'     => null,
            'nom'               => $this->nom,
            'prenom'            => $this->prenom,
            'genre'             => $this->genre,
            'fonction'          => $this->fonction,
            'email'             => $this->email,
            'telephone'         => $this->telephone,
            'secteur_activite'  => $this->secteur_activite,
            'participation_rdv' => $this->participation_rdv,
            'role'              => $this->role,
            'code_acces'        => $code,
            'statut_historique' => 'actif',
        ]);

        $this->code_acces_genere = $code;
        $this->showSuccessModal  = true;
    }

    public function render()
    {
        return view('livewire.auth.inscription-participant', [
            'evenements' => Evenement::orderBy('nom')->get(),
            'cdds'       => User::role('cdd')->orderBy('name')->get(),
        ])->layout('layouts.guest');
    }
}