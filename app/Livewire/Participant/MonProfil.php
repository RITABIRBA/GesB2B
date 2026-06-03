<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Participant;

class MonProfil extends Component
{
    public $participant_id;
    public $nom = '';
    public $prenom = '';
    public $genre = '';
    public $fonction = '';
    public $email = '';
    public $telephone = '';
    public $secteur_activite = '';
    public $participation_rdv = true;
    public $isEditing = false;

    public function mount()
    {
        $participant = Participant::where('email', auth()->user()->email)->first();

        if ($participant) {
            $this->participant_id   = $participant->id;
            $this->nom              = $participant->nom;
            $this->prenom           = $participant->prenom;
            $this->genre            = $participant->genre;
            $this->fonction         = $participant->fonction;
            $this->email            = $participant->email;
            $this->telephone        = $participant->telephone;
            $this->secteur_activite = $participant->secteur_activite;
            $this->participation_rdv = $participant->participation_rdv;
        }
    }

    public function activer() { $this->isEditing = true; }

    public function annuler()
    {
        $this->isEditing = false;
        $this->mount();
        $this->resetErrorBag();
    }

    public function toggleParticipationRdv()
    {
        $participant = Participant::findOrFail($this->participant_id);
        $this->participation_rdv = !$this->participation_rdv;
        $participant->update(['participation_rdv' => $this->participation_rdv]);

        $message = $this->participation_rdv
            ? 'Vous participez maintenant aux rendez-vous d\'affaire !'
            : 'Vous ne participez plus aux rendez-vous d\'affaire.';

        session()->flash('success', $message);
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
            'genre'            => $this->genre,
            'fonction'         => $this->fonction,
            'email'            => $this->email,
            'telephone'        => $this->telephone,
            'secteur_activite' => $this->secteur_activite,
            'participation_rdv'=> $this->participation_rdv,
        ]);

        auth()->user()->update(['email' => $this->email]);

        $this->isEditing = false;
        session()->flash('success', 'Profil mis à jour avec succès.');
    }

    public function render()
    {
        return view('livewire.participant.mon-profil')
            ->layout('layouts.participant', ['title' => 'Mon Profil']);
    }
}