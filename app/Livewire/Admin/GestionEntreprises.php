<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Entreprise;

class GestionEntreprises extends Component
{
    public $entreprises;

    public $entreprise_id;
    public $nom;
    public $secteur_activite;
    public $sous_secteur;
    public $pays;
    public $ville;
    public $telephone;
    public $email;
    public $statut_validation = 'en_attente';

    public $showModal = false;
    public $isEditing = false;

    public $secteurs = [
        'Agriculture',
        'Industrie',
        'Commerce',
        'Services',
        'Technologie',
        'Transport',
        'Construction',
        'Tourisme',
        'Santé',
        'Education',
        'Finance',
        'Energie',
        'Mines',
        'Artisanat',
        'Autre',
    ];

    public $pays_liste = [
        'Burkina Faso',
        'Côte d\'Ivoire',
        'Mali',
        'Sénégal',
        'Ghana',
        'Togo',
        'Bénin',
        'Niger',
        'Guinée',
        'Cameroun',
        'Nigeria',
        'France',
        'Allemagne',
        'États-Unis',
        'Chine',
        'Autre',
    ];

    public function mount()
    {
        $this->entreprises = Entreprise::latest()->get();
    }

    public function openModal()
    {
        $this->reset(['entreprise_id', 'nom', 'secteur_activite', 'sous_secteur', 'pays', 'ville', 'telephone', 'email', 'isEditing']);
        $this->statut_validation = 'en_attente';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['entreprise_id', 'nom', 'secteur_activite', 'sous_secteur', 'pays', 'ville', 'telephone', 'email', 'isEditing']);
    }

    public function sauvegarder()
    {
        $this->validate([
            'nom'              => 'required|string|max:255',
            'secteur_activite' => 'required|string|max:255',
            'sous_secteur'     => 'nullable|string|max:255',
            'pays'             => 'required|string|max:255',
            'ville'            => 'required|string|max:255',
            'telephone'        => 'required|string|max:20',
            'email'            => 'required|email|max:255',
        ]);

        if ($this->isEditing) {
            Entreprise::find($this->entreprise_id)->update([
                'nom'               => $this->nom,
                'secteur_activite'  => $this->secteur_activite,
                'sous_secteur'      => $this->sous_secteur,
                'pays'              => $this->pays,
                'ville'             => $this->ville,
                'contact'           => $this->telephone . ' / ' . $this->email,
                'statut_validation' => $this->statut_validation,
            ]);
        } else {
            Entreprise::create([
                'nom'               => $this->nom,
                'secteur_activite'  => $this->secteur_activite,
                'sous_secteur'      => $this->sous_secteur,
                'pays'              => $this->pays,
                'ville'             => $this->ville,
                'contact'           => $this->telephone . ' / ' . $this->email,
                'statut_validation' => 'en_attente',
            ]);
        }

        $this->entreprises = Entreprise::latest()->get();
        $this->closeModal();
    }

    public function modifier($id)
    {
        $entreprise = Entreprise::find($id);
        $this->entreprise_id     = $entreprise->id;
        $this->nom               = $entreprise->nom;
        $this->secteur_activite  = $entreprise->secteur_activite;
        $this->sous_secteur      = $entreprise->sous_secteur;
        $this->pays              = $entreprise->pays;
        $this->ville             = $entreprise->ville;
        $this->telephone         = $entreprise->contact;
        $this->statut_validation = $entreprise->statut_validation;
        $this->isEditing         = true;
        $this->showModal         = true;
    }

    public function valider($id)
    {
        Entreprise::find($id)->update(['statut_validation' => 'valide']);
        $this->entreprises = Entreprise::latest()->get();
    }

    public function rejeter($id)
    {
        Entreprise::find($id)->update(['statut_validation' => 'rejete']);
        $this->entreprises = Entreprise::latest()->get();
    }

    public function supprimer($id)
    {
        Entreprise::find($id)->delete();
        $this->entreprises = Entreprise::latest()->get();
    }

    public function render()
    {
        return view('livewire.admin.gestion-entreprises')
            ->layout('layouts.app');
    }
}