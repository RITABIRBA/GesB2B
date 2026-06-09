<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Models\Participant;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class InscriptionParticipant extends Component
{
    public $nom = '';
    public $prenom = '';
    public $genre = '';
    public $fonction = '';
    public $ifu = '';
    public $email = '';
    public $telephone = '';
    public $secteur_activite = '';
    public $participation_rdv = true;
    public $entreprise_trouvee = null;
    public $role = 'participant';
    public $id_cdd = '';
    public $password = '';
    public $password_confirmation = '';
    public $showSuccessModal = false;
    public $code_acces_genere = '';

    public $secteurs = [
        'Agriculture', 'Industrie', 'Commerce', 'Services',
        'Technologie', 'Transport', 'Construction', 'Tourisme',
        'Santé', 'Education', 'Finance', 'Energie', 'Mines',
        'Artisanat', 'Autre',
    ];

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
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'genre'     => 'required|string',
            'telephone' => 'required|string|max:20',
            'id_cdd'    => 'required',
            'role'      => 'required',
            'ifu'       => 'nullable|string|max:255',
            // ← Email optionnel
            'email'     => 'nullable|email|unique:users,email',
            // ← Mot de passe requis seulement si email fourni
            'password'  => $this->email
                ? 'required|min:8|confirmed'
                : 'nullable',
        ]);

        $code = strtoupper(substr($this->nom, 0, 3) . rand(1000, 9999));

        $entreprise = null;
        if ($this->ifu) {
            $entreprise = Entreprise::where('ifu', $this->ifu)->first();
        }

        // ← Crée compte USER seulement si email fourni
        if ($this->email) {
            $user = User::create([
                'name'     => $this->nom . ' ' . $this->prenom,
                'email'    => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $user->assignRole('participant');
            Auth::login($user);
        }

        // Crée le PARTICIPANT
        Participant::create([
            'id_cdd'            => $this->id_cdd,
            'id_evenement'      => null,
            'id_entreprise'     => $entreprise?->id,
            'nom'               => $this->nom,
            'prenom'            => $this->prenom,
            'genre'             => $this->genre,
            'fonction'          => $this->fonction,
            'ifu'               => $this->ifu ?: null,
            'email'             => $this->email ?: null,
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

    public function allerAuDashboard()
    {
        return redirect()->to(route('participant.dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.inscription-participant', [
            'cdds' => User::role('cdd')->orderBy('name')->get(),
        ])->layout('layouts.guest');
    }
}