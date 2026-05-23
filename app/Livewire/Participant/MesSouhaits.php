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
        $participant = Participant::first();

        $this->validate([
            'id_participant_cible' => 'required|different:' . $participant->id,
            'priorite'             => 'required|integer|min:1|max:20',
        ]);

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
        $participant = Participant::first();

        return view('livewire.participant.mes-souhaits', [
            'souhaits' => Souhait::with(['participant', 'participantCible'])
                ->where('id_participant', $participant?->id)
                ->orderBy('priorite')
                ->get(),
            'autresParticipants' => Participant::where('id', '!=', $participant?->id)
                ->orderBy('nom')->get(),
            'participant' => $participant,
        ])->layout('layouts.participant', ['title' => 'Mes Souhaits']);
    }
}