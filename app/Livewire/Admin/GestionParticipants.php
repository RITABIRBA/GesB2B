<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\Evenement;

class GestionParticipants extends Component
{
    public $participant_id;
    public $id_entreprise = '';
    public $id_evenement = '';
    public $nom = '';
    public $prenom = '';
    public $genre = '';
    public $fonction = '';
    public $secteur_activite = '';
    public $nouveau_secteur = '';
    public $utiliser_nouveau_secteur = '';
    public $email = '';
    public $telephone = '';
    public $role = 'visiteur';
    public $statut_historique = 'actif';
    public $showModal = false;
    public $isEditing = false;
    public $search = '';

    public $roles = ['exposant', 'visiteur', 'organisateur'];

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

    public function resetFields()
    {
        $this->participant_id           = null;
        $this->id_entreprise            = '';
        $this->id_evenement             = '';
        $this->nom                      = '';
        $this->prenom                   = '';
        $this->genre                    = '';
        $this->fonction                 = '';
        $this->secteur_activite         = '';
        $this->nouveau_secteur          = '';
        $this->utiliser_nouveau_secteur = '';
        $this->email                    = '';
        $this->telephone                = '';
        $this->role                     = 'visiteur';
        $this->statut_historique        = 'actif';
        $this->resetErrorBag();
    }

    public function modifier($id)
    {
        $p = Participant::findOrFail($id);
        $this->participant_id    = $p->id;
        $this->id_entreprise     = $p->id_entreprise;
        $this->id_evenement      = $p->id_evenement;
        $this->nom               = $p->nom;
        $this->prenom            = $p->prenom;
        $this->genre             = $p->genre;
        $this->fonction          = $p->fonction;
        $this->secteur_activite  = $p->secteur_activite;
        $this->email             = $p->email;
        $this->telephone         = $p->telephone;
        $this->role              = $p->role;
        $this->statut_historique = $p->statut_historique;
        $this->isEditing         = true;
        $this->showModal         = true;
    }

    public function sauvegarder()
    {
        $this->validate([
            'id_evenement' => 'required',
            'nom'          => 'required|string|max:255',
            'prenom'       => 'required|string|max:255',
            'telephone'    => 'required|string|max:20',
            'email'        => 'nullable|email|max:255',
            'role'         => 'required',
            'genre'        => 'nullable|in:homme,femme',
            'fonction'     => 'nullable|string|max:255',
        ]);

        $secteur = $this->utiliser_nouveau_secteur === '1'
            ? $this->nouveau_secteur
            : $this->secteur_activite;

        $data = [
            'id_entreprise'     => $this->id_entreprise ?: null,
            'id_evenement'      => $this->id_evenement,
            'nom'               => $this->nom,
            'prenom'            => $this->prenom,
            'genre'             => $this->genre ?: null,
            'fonction'          => $this->fonction ?: null,
            'secteur_activite'  => $secteur,
            'email'             => $this->email ?: null,
            'telephone'         => $this->telephone,
            'role'              => $this->role,
            'statut_historique' => $this->statut_historique,
        ];

        if ($this->isEditing) {
            Participant::findOrFail($this->participant_id)->update($data);
            session()->flash('success', 'Participant modifié avec succès.');
        } else {
            $data['code_acces'] = strtoupper(substr($this->nom, 0, 3) . rand(1000, 9999));
            Participant::create($data);
            session()->flash('success', 'Participant créé avec succès.');
        }

        $this->closeModal();
    }

    public function supprimer($id)
    {
        Participant::findOrFail($id)->delete();
        session()->flash('success', 'Participant supprimé.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-participants', [
            'participants' => Participant::with(['entreprise', 'evenement'])
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('prenom', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%')
                )
                ->latest()->get(),
            'entreprises' => Entreprise::where('statut_validation', 'valide')->get(),
            'evenements'  => Evenement::orderBy('nom')->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Participants']);
    }
}