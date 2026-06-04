<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\Evenement;
use Illuminate\Support\Facades\Hash;

class InscriptionParticipant extends Component
{
    // Infos personnelles
    public $nom = '';
    public $prenom = '';
    public $genre = '';
    public $fonction = '';
    public $ifu = ''; // ← nouveau
    public $email = '';
    public $telephone = '';
    public $secteur_activite = '';
    public $participation_rdv = true;

    // Entreprise trouvée via IFU
    public $entreprise_trouvee = null; // ← nouveau

    // Rôle et événement
    public $role = 'participant';
    public $id_evenement = '';
    public $id_cdd = '';

    // Mot de passe
    public $password = '';
    public $password_confirmation = '';

    // Modal succès
    public $showSuccessModal = false;
    public $code_acces_genere = '';

    public $roles = ['exposant', 'participant'];

    public $secteurs = [
        'Agriculture', 'Industrie', 'Commerce', 'Services',
        'Technologie', 'Transport', 'Construction', 'Tourisme',
        'Santé', 'Education', 'Finance', 'Energie', 'Mines',
        'Artisanat', 'Autre',
    ];

    // ← Quand l'IFU change → cherche l'entreprise
    public function updatedIfu($value)
    {
        if ($value && strlen($value) >= 3) {
            $this->entreprise_trouvee = Entreprise::where('ifu', $value)->first();
        } else {
            $this->entreprise_trouvee = null;
        }
    }

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
            'ifu'          => 'nullable|string|max:255',
        ]);

        // Génère le code d'accès
        $code = strtoupper(substr($this->nom, 0, 3) . rand(1000, 9999));

        // Cherche l'entreprise via IFU
        $entreprise = null;
        if ($this->ifu) {
            $entreprise = Entreprise::where('ifu', $this->ifu)->first();
        }

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
            'id_entreprise'     => $entreprise?->id, // ← lié auto si IFU trouvé
            'nom'               => $this->nom,
            'prenom'            => $this->prenom,
            'genre'             => $this->genre,
            'fonction'          => $this->fonction,
            'ifu'               => $this->ifu ?: null,
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
            'evenements' => Evenement::where('date_fin', '>=', now()->toDateString())
                ->orderBy('nom')
                ->get(),
            'cdds' => User::role('cdd')->orderBy('name')->get(),
        ])->layout('layouts.guest');
    }
}