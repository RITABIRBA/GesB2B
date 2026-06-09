<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\Evenement;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GestionParticipants extends Component
{
    public $search             = '';
    public $participant_id;
    public $id_entreprise      = '';
    public $id_evenement       = '';
    public $nom                = '';
    public $prenom             = '';
    public $genre              = '';
    public $fonction           = '';
    public $ifu                = '';
    public $secteur_activite   = '';
    public $email              = '';
    public $telephone          = '';
    public $role               = 'participant';
    public $showModal          = false;
    public $isEditing          = false;

    // Modal info compte créé
    public $showModalCompte    = false;
    public $compte_email       = '';
    public $compte_password    = '';
    public $compte_code_acces  = '';
    public $compte_has_email   = false;

    public $roles = ['exposant', 'participant'];

    public $secteurs = [
        'Agriculture', 'Industrie', 'Commerce', 'Services',
        'Technologie', 'Transport', 'Construction', 'Tourisme',
        'Santé', 'Education', 'Finance', 'Energie', 'Mines',
        'Artisanat', 'Autre',
    ];

    public function openModal()
    {
        $this->resetFields();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function closeModalCompte()
    {
        $this->showModalCompte = false;
    }

    public function resetFields()
    {
        $this->participant_id  = null;
        $this->id_entreprise   = '';
        $this->id_evenement    = '';
        $this->nom             = '';
        $this->prenom          = '';
        $this->genre           = '';
        $this->fonction        = '';
        $this->ifu             = '';
        $this->secteur_activite = '';
        $this->email           = '';
        $this->telephone       = '';
        $this->role            = 'participant';
        $this->resetErrorBag();
    }

    public function modifier($id)
    {
        $p = Participant::findOrFail($id);
        $this->participant_id   = $p->id;
        $this->id_entreprise    = $p->id_entreprise;
        $this->id_evenement     = $p->id_evenement;
        $this->nom              = $p->nom;
        $this->prenom           = $p->prenom;
        $this->genre            = $p->genre;
        $this->fonction         = $p->fonction;
        $this->ifu              = $p->ifu;
        $this->secteur_activite = $p->secteur_activite;
        $this->email            = $p->email;
        $this->telephone        = $p->telephone;
        $this->role             = $p->role;
        $this->isEditing        = true;
        $this->showModal        = true;
    }

    public function sauvegarder()
    {
        $this->validate([
            'id_evenement' => 'required',
            'nom'          => 'required|string|max:255',
            'prenom'       => 'required|string|max:255',
            'telephone'    => 'required|string|max:20',
            'email'        => $this->isEditing
                ? 'nullable|email|max:255'
                : 'nullable|email|max:255|unique:users,email',
            'ifu'          => 'nullable|string|max:255',
            'role'         => 'required',
            'genre'        => 'nullable|in:homme,femme',
        ]);

        $code_acces = strtoupper(substr($this->nom, 0, 3) . rand(1000, 9999));

        $data = [
            'id_cdd'            => auth()->id(),
            'id_entreprise'     => $this->id_entreprise ?: null,
            'id_evenement'      => $this->id_evenement,
            'nom'               => $this->nom,
            'prenom'            => $this->prenom,
            'genre'             => $this->genre ?: null,
            'fonction'          => $this->fonction ?: null,
            'ifu'               => $this->ifu ?: null,
            'secteur_activite'  => $this->secteur_activite ?: null,
            'email'             => $this->email ?: null,
            'telephone'         => $this->telephone,
            'role'              => $this->role,
            'statut_historique' => 'actif',
        ];

        if ($this->isEditing) {
            Participant::findOrFail($this->participant_id)->update($data);
            session()->flash('success', 'Participant modifié avec succès.');
            $this->closeModal();
        } else {
            $data['code_acces'] = $code_acces;
            Participant::create($data);

            $password_genere = null;

            // ← Crée compte USER seulement si email fourni
            if ($this->email) {
                $password_genere = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
                $user = User::create([
                    'name'     => $this->nom . ' ' . $this->prenom,
                    'email'    => $this->email,
                    'password' => Hash::make($password_genere),
                ]);
                $user->assignRole('participant');
            }

            $this->compte_email      = $this->email;
            $this->compte_password   = $password_genere;
            $this->compte_code_acces = $code_acces;
            $this->compte_has_email  = !empty($this->email);
            $this->showModalCompte   = true;

            $this->closeModal();
        }
    }

    public function render()
    {
        $cddId = auth()->id();

        return view('livewire.cdd.gestion-participants', [
            'participants' => Participant::with(['entreprise', 'evenement'])
                ->where('id_cdd', $cddId)
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('prenom', 'like', '%'.$this->search.'%')
                )
                ->latest()
                ->get(),
            'entreprises' => Entreprise::where('statut_validation', 'valide')->get(),
            'evenements'  => Evenement::orderBy('nom')->get(),
        ])->layout('layouts.cdd', ['title' => 'Mes Participants']);
    }
}