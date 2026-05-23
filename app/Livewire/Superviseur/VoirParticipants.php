<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Participant;

class VoirParticipants extends Component
{
    public $search = '';

    public function render()
    {
        return view('livewire.superviseur.voir-participants', [
            'participants' => Participant::with(['entreprise', 'evenement'])
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('prenom', 'like', '%'.$this->search.'%')
                )
                ->latest()->get(),
        ])->layout('layouts.superviseur', ['title' => 'Participants']);
    }
}