<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Entreprise;

class GestionEntreprises extends Component
{
    public $entreprise_id;
    public $nom = '';
    public $ifu = ''; // ← nouveau
    public $secteur_activite = '';
    public $sous_secteur = '';
    public $pays = '';
    public $ville = '';
    public $telephone = '';
    public $email = '';
    public $statut_validation = 'en_attente';
    public $showModal = false;
    public $isEditing = false;
    public $search = '';

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
        $this->entreprise_id     = null;
        $this->nom               = '';
        $this->ifu               = ''; // ← nouveau
        $this->secteur_activite  = '';
        $this->sous_secteur      = '';
        $this->pays              = '';
        $this->ville             = '';
        $this->telephone         = '';
        $this->email             = '';
        $this->statut_validation = 'en_attente';
        $this->resetErrorBag();
    }

    public function modifier($id)
    {
        $e = Entreprise::findOrFail($id);
        $this->entreprise_id     = $e->id;
        $this->nom               = $e->nom;
        $this->ifu               = $e->ifu ?? ''; // ← nouveau
        $this->secteur_activite  = $e->secteur_activite;
        $this->sous_secteur      = $e->sous_secteur;
        $this->pays              = $e->pays;
        $this->ville             = $e->ville;
        $this->telephone         = $e->contact;
        $this->statut_validation = $e->statut_validation;
        $this->isEditing         = true;
        $this->showModal         = true;
    }

    public function sauvegarder()
    {
        $this->validate([
            'nom'              => 'required|string|max:255',
            'ifu'              => [
                'nullable',
                'string',
                'max:255',
                // Unique sauf pour l'entreprise en cours de modification
                $this->isEditing
                    ? \Illuminate\Validation\Rule::unique('entreprises', 'ifu')->ignore($this->entreprise_id)
                    : \Illuminate\Validation\Rule::unique('entreprises', 'ifu'),
            ],
            'secteur_activite' => 'required|string|max:255',
            'sous_secteur'     => 'nullable|string|max:255',
            'pays'             => 'required|string|max:255',
            'ville'            => 'required|string|max:255',
            'telephone'        => 'required|string|max:20',
            'email'            => 'nullable|email|max:255',
        ]);

        $data = [
            'nom'               => $this->nom,
            'ifu'               => $this->ifu ?: null, // ← nouveau
            'secteur_activite'  => $this->secteur_activite,
            'sous_secteur'      => $this->sous_secteur,
            'pays'              => $this->pays,
            'ville'             => $this->ville,
            'contact'           => $this->telephone . ($this->email ? ' / ' . $this->email : ''),
            'statut_validation' => $this->statut_validation,
        ];

        if ($this->isEditing) {
            Entreprise::findOrFail($this->entreprise_id)->update($data);
            session()->flash('success', 'Entreprise modifiée avec succès.');
        } else {
            $data['statut_validation'] = 'en_attente';
            Entreprise::create($data);
            session()->flash('success', 'Entreprise créée avec succès.');
        }

        $this->closeModal();
    }

    public function valider($id)
    {
        Entreprise::findOrFail($id)->update(['statut_validation' => 'valide']);
        session()->flash('success', 'Entreprise validée.');
    }

    public function rejeter($id)
    {
        Entreprise::findOrFail($id)->update(['statut_validation' => 'rejete']);
        session()->flash('success', 'Entreprise rejetée.');
    }

    public function supprimer($id)
    {
        Entreprise::findOrFail($id)->delete();
        session()->flash('success', 'Entreprise supprimée.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-entreprises', [
            'entreprises' => Entreprise::when($this->search, fn($q) =>
                $q->where('nom', 'like', '%'.$this->search.'%')
                  ->orWhere('pays', 'like', '%'.$this->search.'%')
                  ->orWhere('ville', 'like', '%'.$this->search.'%')
                  ->orWhere('ifu', 'like', '%'.$this->search.'%')
            )->latest()->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Entreprises']);
    }
}