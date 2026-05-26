<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\Entreprise;

class MonProfil extends Component
{
    public $entreprise_id;
    public $nom = '';
    public $secteur_activite = '';
    public $sous_secteur = '';
    public $pays = '';
    public $ville = '';
    public $contact = '';
    public $isEditing = false;

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

    public function mount()
    {
        // Liaison par nom
        $entreprise = Entreprise::where('nom', auth()->user()->name)->first();

        if ($entreprise) {
            $this->entreprise_id    = $entreprise->id;
            $this->nom              = $entreprise->nom;
            $this->secteur_activite = $entreprise->secteur_activite;
            $this->sous_secteur     = $entreprise->sous_secteur;
            $this->pays             = $entreprise->pays;
            $this->ville            = $entreprise->ville;
            $this->contact          = $entreprise->contact;
        }
    }

    public function activer() { $this->isEditing = true; }

    public function annuler()
    {
        $this->isEditing = false;
        $this->mount();
        $this->resetErrorBag();
    }

    public function sauvegarder()
    {
        $this->validate([
            'nom'              => 'required|string|max:255',
            'secteur_activite' => 'required|string|max:255',
            'pays'             => 'required|string|max:255',
            'ville'            => 'required|string|max:255',
            'contact'          => 'required|string|max:255',
        ]);

        Entreprise::findOrFail($this->entreprise_id)->update([
            'nom'              => $this->nom,
            'secteur_activite' => $this->secteur_activite,
            'sous_secteur'     => $this->sous_secteur,
            'pays'             => $this->pays,
            'ville'            => $this->ville,
            'contact'          => $this->contact,
        ]);

        // Met à jour aussi le nom du user
        auth()->user()->update(['name' => $this->nom]);

        $this->isEditing = false;
        session()->flash('success', 'Profil mis à jour avec succès.');
    }

    public function render()
    {
        return view('livewire.entreprise.mon-profil')
            ->layout('layouts.entreprise', ['title' => 'Mon Profil']);
    }
}