<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\Participant;

class MesRendezVous extends Component
{
    public $filtre_statut = '';

    public function render()
    {
        $participant = Participant::first();

        return view('livewire.participant.mes-rendez-vous', [
            'rendezVous' => RendezVous::with(['participant1', 'participant2', 'stand', 'traducteur'])
                ->where(function($q) use ($participant) {
                    $q->where('id_participant1', $participant?->id)
                      ->orWhere('id_participant2', $participant?->id);
                })
                ->when($this->filtre_statut, fn($q) => $q->where('statut', $this->filtre_statut))
                ->orderBy('date')->orderBy('heure_debut')
                ->get(),
        ])->layout('layouts.participant', ['title' => 'Mes Rendez-vous']);
    }
}