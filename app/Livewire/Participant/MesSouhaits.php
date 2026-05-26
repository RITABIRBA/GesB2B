<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Souhait;
use App\Models\Participant;

class MesSouhaits extends Component
{
    public $id_participant_cible = '';
    public $priorite = '';
    public $showModal = false;

    public function openModal()
    {
        $this->id_participant_cible = '';
        $this->priorite             = '';
        $this->showModal            = true;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function sauvegarder()
    {
        // Liaison par email
        $participant = Participant::where('email', auth()->user()->email)->first();

        if (!$participant) {
            session()->flash('error', 'Participant non trouvé.');
            return;
        }

        $this->validate([
            'id_participant_cible' => 'required',
            'priorite'             => 'required|integer|min:1|max:20',
        ]);

        // Vérifie si souhait déjà existant
        $existe = Souhait::where('id_participant', $participant->id)
            ->where('id_participant_cible', $this->id_participant_cible)
            ->exists();

        if ($existe) {
            session()->flash('error', 'Vous avez déjà émis un souhait pour ce participant.');
            $this->closeModal();
            return;
        }

        Souhait::create([
            'id_participant'       => $participant->id,
            'id_participant_cible' => $this->id_participant_cible,
            'priorite'             => $this->priorite,
            'type'                 => 'envoye',
        ]);

        session()->flash('success', 'Souhait ajouté.');
        $this->closeModal();
    }

    public function supprimer($id)
    {
        Souhait::findOrFail($id)->delete();
        session()->flash('success', 'Souhait supprimé.');
    }

    public function render()
    {
        // Liaison par email
        $participant = Participant::where('email', auth()->user()->email)->first();

        return view('livewire.participant.mes-souhaits', [
            'souhaits' => $participant
                ? Souhait::with(['participant', 'participantCible.entreprise'])
                    ->where('id_participant', $participant->id)
                    ->orderBy('priorite')
                    ->get()
                : collect(),
            'autresParticipants' => Participant::where('id', '!=', $participant?->id)
                ->orderBy('nom')->get(),
            'participant' => $participant,
        ])->layout('layouts.participant', ['title' => 'Mes Souhaits']);
    }
}