<?php

namespace App\Livewire\Traducteur;

use Livewire\Component;
use App\Models\Traducteur;

class MonProfil extends Component
{
    public $traducteur_id;
    public $nom = '';
    public $prenom = '';
    public $telephone = '';
    public $email = '';
    public $langue = '';
    public $isEditing = false;

    public $langues = [
        'Français', 'Anglais', 'Arabe', 'Espagnol',
        'Portugais', 'Allemand', 'Chinois', 'Dioula',
        'Mooré', 'Fulfuldé',
    ];

    public function mount()
    {
        $traducteur = Traducteur::first();
        if ($traducteur) {
            $this->traducteur_id = $traducteur->id;
            $this->nom           = $traducteur->nom;
            $this->prenom        = $traducteur->prenom;
            $this->telephone     = $traducteur->telephone;
            $this->email         = $traducteur->email;
            $this->langue        = $traducteur->langue;
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
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email'     => 'nullable|email|max:255',
            'langue'    => 'required|string|max:255',
        ]);

        Traducteur::findOrFail($this->traducteur_id)->update([
            'nom'       => $this->nom,
            'prenom'    => $this->prenom,
            'telephone' => $this->telephone,
            'email'     => $this->email ?: null,
            'langue'    => $this->langue,
        ]);

        $this->isEditing = false;
        session()->flash('success', 'Profil mis à jour avec succès.');
    }

    public function render()
    {
        return view('livewire.traducteur.mon-profil')
            ->layout('layouts.traducteur', ['title' => 'Mon Profil']);
    }
}