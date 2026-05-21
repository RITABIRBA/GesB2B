<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\Entreprise;

/**
 * Composant CDD — Gestion des Entreprises
 *
 * Le CDD peut :
 * - Voir la liste des entreprises de sa délégation
 * - Modifier les informations d'une entreprise
 * - Valider ou rejeter une inscription
 */
class GestionEntreprises extends Component
{
    public $entreprise_id;
    public $nom = '';
    public $secteur_activite = '';
    public $sous_secteur = '';
    public $pays = '';
    public $ville = '';
    public $telephone = '';
    public $email = '';
    public $statut_validation = 'en_attente';
    public $showModal = false;
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

    public function openModal($id)
    {
        $e = Entreprise::findOrFail($id);
        $this->entreprise_id     = $e->id;
        $this->nom               = $e->nom;
        $this->secteur_activite  = $e->secteur_activite;
        $this->sous_secteur      = $e->sous_secteur;
        $this->pays              = $e->pays;
        $this->ville             = $e->ville;
        $this->telephone         = $e->contact;
        $this->statut_validation = $e->statut_validation;
        $this->showModal         = true;
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
        $this->secteur_activite  = '';
        $this->sous_secteur      = '';
        $this->pays              = '';
        $this->ville             = '';
        $this->telephone         = '';
        $this->statut_validation = 'en_attente';
        $this->resetErrorBag();
    }

    public function sauvegarder()
    {
        $this->validate([
            'nom'              => 'required|string|max:255',
            'secteur_activite' => 'required|string|max:255',
            'pays'             => 'required|string|max:255',
            'ville'            => 'required|string|max:255',
            'telephone'        => 'required|string|max:20',
        ]);

        Entreprise::findOrFail($this->entreprise_id)->update([
            'nom'               => $this->nom,
            'secteur_activite'  => $this->secteur_activite,
            'sous_secteur'      => $this->sous_secteur,
            'pays'              => $this->pays,
            'ville'             => $this->ville,
            'contact'           => $this->telephone,
        ]);

        session()->flash('success', 'Entreprise modifiée avec succès.');
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

    public function render()
    {
        return view('livewire.cdd.gestion-entreprises', [
            'entreprises' => Entreprise::when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('pays', 'like', '%'.$this->search.'%')
                      ->orWhere('ville', 'like', '%'.$this->search.'%')
                )
                ->latest()
                ->get(),
        ])->layout('layouts.cdd', ['title' => 'Mes Entreprises']);
    }
}