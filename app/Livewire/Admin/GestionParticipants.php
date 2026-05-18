<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\Evenement;

class GestionParticipants extends Component
{
    public $participants;
    public $entreprises;
    public $evenements;

    public $participant_id;
    public $id_entreprise;
    public $id_evenement;
    public $nom;
    public $prenom;
    public $secteur_activite;
    public $email;
    public $telephone;
    public $code_acces;
    public $role = 'visiteur';
    public $statut_historique = 'actif';

    public $showModal = false;
    public $isEditing = false;

    public $roles = ['exposant', 'visiteur', 'organisateur'];

    public function mount()
    {
        $this->participants = Participant::with(['entreprise', 'evenement'])->latest()->get();
        $this->entreprises  = Entreprise::where('statut_validation', 'valide')->get();
        $this->evenements   = Evenement::all();
    }

    public function openModal()
    {
        $this->reset(['participant_id', 'id_entreprise', 'id_evenement', 'nom', 'prenom', 'secteur_activite', 'email', 'telephone', 'code_acces', 'isEditing']);
        $this->role              = 'visiteur';
        $this->statut_historique = 'actif';
        $this->showModal         = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['participant_id', 'id_entreprise', 'id_evenement', 'nom', 'prenom', 'secteur_activite', 'email', 'telephone', 'code_acces', 'isEditing']);
    }

    public function sauvegarder()
    {
        $this->validate([
            'id_evenement'     => 'required',
            'nom'              => 'required|string|max:255',
            'prenom'           => 'required|string|max:255',
            'secteur_activite' => 'required|string|max:255',
            'telephone'        => 'required|string|max:20',
            'email'            => 'nullable|email|max:255',
            'role'             => 'required',
        ]);

        $code = strtoupper(substr($this->nom, 0, 3) . rand(1000, 9999));

        if ($this->isEditing) {
            Participant::find($this->participant_id)->update([
                'id_entreprise'    => $this->id_entreprise ?: null,
                'id_evenement'     => $this->id_evenement,
                'nom'              => $this->nom,
                'prenom'           => $this->prenom,
                'secteur_activite' => $this->secteur_activite,
                'email'            => $this->email ?: null,
                'telephone'        => $this->telephone,
                'role'             => $this->role,
                'statut_historique' => $this->statut_historique,
            ]);
        } else {
            Participant::create([
                'id_entreprise'    => $this->id_entreprise ?: null,
                'id_evenement'     => $this->id_evenement,
                'nom'              => $this->nom,
                'prenom'           => $this->prenom,
                'secteur_activite' => $this->secteur_activite,
                'email'            => $this->email ?: null,
                'telephone'        => $this->telephone,
                'code_acces'       => $code,
                'role'             => $this->role,
                'statut_historique' => 'actif',
            ]);
        }

        $this->participants = Participant::with(['entreprise', 'evenement'])->latest()->get();
        $this->closeModal();
    }

    public function modifier($id)
    {
        $participant = Participant::find($id);
        $this->participant_id    = $participant->id;
        $this->id_entreprise     = $participant->id_entreprise;
        $this->id_evenement      = $participant->id_evenement;
        $this->nom               = $participant->nom;
        $this->prenom            = $participant->prenom;
        $this->secteur_activite  = $participant->secteur_activite;
        $this->email             = $participant->email;
        $this->telephone         = $participant->telephone;
        $this->role              = $participant->role;
        $this->statut_historique = $participant->statut_historique;
        $this->isEditing         = true;
        $this->showModal         = true;
    }

    public function supprimer($id)
    {
        Participant::find($id)->delete();
        $this->participants = Participant::with(['entreprise', 'evenement'])->latest()->get();
    }

    public function render()
    {
        return view('livewire.admin.gestion-participants')
            ->layout('layouts.app');
    }
}