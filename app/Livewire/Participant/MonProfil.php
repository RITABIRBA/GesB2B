<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;

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
    public $ifu = ''; // ← nouveau
    public $isEditing = false;

    // Entreprise trouvée via IFU
    public $entreprise_trouvee = null; // ← nouveau
    public $entreprise_actuelle = null; // ← nouveau

    public function mount()
    {
        $participant = Participant::where('email', auth()->user()->email)
            ->with('entreprise')
            ->first();

        if ($participant) {
            $this->participant_id    = $participant->id;
            $this->nom               = $participant->nom;
            $this->prenom            = $participant->prenom;
            $this->genre             = $participant->genre;
            $this->fonction          = $participant->fonction;
            $this->email             = $participant->email;
            $this->telephone         = $participant->telephone;
            $this->secteur_activite  = $participant->secteur_activite;
            $this->participation_rdv = $participant->participation_rdv;
            $this->ifu               = $participant->ifu ?? '';
            $this->entreprise_actuelle = $participant->entreprise;
        }
    }

    public function activer() { $this->isEditing = true; }

    public function annuler()
    {
        $this->isEditing = false;
        $this->entreprise_trouvee = null;
        $this->mount();
        $this->resetErrorBag();
    }

    // ← Quand IFU change → cherche l'entreprise
    public function updatedIfu($value)
    {
        if ($value && strlen($value) >= 3) {
            $this->entreprise_trouvee = Entreprise::where('ifu', $value)->first();
        } else {
            $this->entreprise_trouvee = null;
        }
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
            'ifu'    => 'nullable|string|max:255',
        ]);

        // Cherche l'entreprise via IFU
        $id_entreprise = null;
        if ($this->ifu) {
            $entreprise = Entreprise::where('ifu', $this->ifu)->first();
            if ($entreprise) {
                $id_entreprise = $entreprise->id;
                $this->entreprise_actuelle = $entreprise;
            }
        }

        Participant::findOrFail($this->participant_id)->update([
            'nom'               => $this->nom,
            'prenom'            => $this->prenom,
            'genre'             => $this->genre,
            'fonction'          => $this->fonction,
            'email'             => $this->email,
            'telephone'         => $this->telephone,
            'secteur_activite'  => $this->secteur_activite,
            'participation_rdv' => $this->participation_rdv,
            'ifu'               => $this->ifu ?: null,
            'id_entreprise'     => $id_entreprise,
        ]);

        auth()->user()->update(['email' => $this->email]);

        $this->isEditing = false;
        $this->entreprise_trouvee = null;

        if ($id_entreprise) {
            session()->flash('success', 'Profil mis à jour ! Vous êtes lié à ' . $this->entreprise_actuelle->nom);
        } else {
            session()->flash('success', 'Profil mis à jour avec succès.');
        }
    }

    public function render()
    {
        return view('livewire.participant.mon-profil')
            ->layout('layouts.participant', ['title' => 'Mon Profil']);
    }
}