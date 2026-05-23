<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\Souhait;
use App\Models\Participant;
use App\Models\Entreprise;

class MesSouhaits extends Component
{
    public $id_participant = '';
    public $id_participant_cible = '';
    public $priorite = '';
    public $showModal = false;
    public $search = '';

    public function openModal()
    {
        $this->resetFields();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->id_participant       = '';
        $this->id_participant_cible = '';
        $this->priorite             = '';
        $this->resetErrorBag();
    }

    public function sauvegarder()
    {
        $this->validate([
            'id_participant'       => 'required',
            'id_participant_cible' => 'required|different:id_participant',
            'priorite'             => 'required|integer|min:1|max:20',
        ]);

        Souhait::create([
            'id_participant'       => $this->id_participant,
            'id_participant_cible' => $this->id_participant_cible,
            'priorite'             => $this->priorite,
            'type'                 => 'envoye',
        ]);

        session()->flash('success', 'Souhait ajouté avec succès.');
        $this->closeModal();
    }

    public function supprimer($id)
    {
        Souhait::findOrFail($id)->delete();
        session()->flash('success', 'Souhait supprimé.');
    }

    public function render()
    {
        $entreprise   = Entreprise::first();
        $participants = Participant::where('id_entreprise', $entreprise->id)->pluck('id');

        return view('livewire.entreprise.mes-souhaits', [
            'souhaits' => Souhait::with(['participant', 'participantCible'])
                ->whereIn('id_participant', $participants)
                ->orderBy('priorite')
                ->get(),
            'mesParticipants' => Participant::where('id_entreprise', $entreprise->id)->get(),
            'tousParticipants' => Participant::where('id_entreprise', '!=', $entreprise->id)->get(),
        ])->layout('layouts.entreprise', ['title' => 'Souhaits RDV']);
    }
}