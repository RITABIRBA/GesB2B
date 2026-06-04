<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\Entreprise;

class MonProfil extends Component
{
    public $entreprise_id;
    public $nom = '';
    public $nom_responsable = '';
    public $prenom_responsable = '';
    public $fonction_responsable = '';
    public $ifu = '';
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

    // ← Liaison par email au lieu du nom
    private function getEntreprise()
    {
        return Entreprise::where('email_responsable', auth()->user()->email)->first()
            ?? Entreprise::where('nom', auth()->user()->name)->first();
    }

    public function mount()
    {
        $entreprise = $this->getEntreprise();

        if ($entreprise) {
            $this->entreprise_id        = $entreprise->id;
            $this->nom                  = $entreprise->nom;
            $this->nom_responsable      = $entreprise->nom_responsable ?? '';
            $this->prenom_responsable   = $entreprise->prenom_responsable ?? '';
            $this->fonction_responsable = $entreprise->fonction_responsable ?? '';
            $this->ifu                  = $entreprise->ifu ?? '';
            $this->secteur_activite     = $entreprise->secteur_activite;
            $this->sous_secteur         = $entreprise->sous_secteur;
            $this->pays                 = $entreprise->pays;
            $this->ville                = $entreprise->ville;
            $this->contact              = $entreprise->contact;
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
            'ifu'              => 'nullable|string|max:255',
        ]);

        Entreprise::findOrFail($this->entreprise_id)->update([
            'nom'                   => $this->nom,
            'nom_responsable'       => $this->nom_responsable,
            'prenom_responsable'    => $this->prenom_responsable,
            'fonction_responsable'  => $this->fonction_responsable,
            'ifu'                   => $this->ifu ?: null,
            'secteur_activite'      => $this->secteur_activite,
            'sous_secteur'          => $this->sous_secteur,
            'pays'                  => $this->pays,
            'ville'                 => $this->ville,
            'contact'               => $this->contact,
        ]);

        $this->isEditing = false;
        session()->flash('success', 'Profil mis à jour avec succès.');
    }

    public function render()
    {
        $entreprise = $this->getEntreprise();

        return view('livewire.entreprise.mon-profil', [
            'entreprise' => $entreprise,
        ])->layout('layouts.entreprise', ['title' => 'Mon Profil']);
    }
}