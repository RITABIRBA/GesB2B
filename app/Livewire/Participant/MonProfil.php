<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Participant;

class MonProfil extends Component
{
    public $participant_id;
    public $nom = '';
    public $prenom = '';
    public $email = '';
    public $telephone = '';
    public $secteur_activite = '';
    public $isEditing = false;

    public function mount()
    {
        // Liaison par email
        $participant = Participant::where('email', auth()->user()->email)->first();

        if ($participant) {
            $this->participant_id   = $participant->id;
            $this->nom              = $participant->nom;
            $this->prenom           = $participant->prenom;
            $this->email            = $participant->email;
            $this->telephone        = $participant->telephone;
            $this->secteur_activite = $participant->secteur_activite;
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
            'nom'    => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email'  => 'required|email|max:255',
        ]);

        Participant::findOrFail($this->participant_id)->update([
            'nom'              => $this->nom,
            'prenom'           => $this->prenom,
            'email'            => $this->email,
            'secteur_activite' => $this->secteur_activite,
        ]);

        // Met à jour aussi le user
        auth()->user()->update(['email' => $this->email]);

        $this->isEditing = false;
        session()->flash('success', 'Profil mis à jour.');
    }

    public function render()
    {
        return view('livewire.participant.mon-profil')
            ->layout('layouts.participant', ['title' => 'Mon Profil']);
    }
}