<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\Participant;

class MonPlanning extends Component
{
    public function render()
    {
        // ← Utilise le helper
        $participant = Participant::findForUser(auth()->user());

        $rendezVous = $participant
            ? RendezVous::with([
                    'participant1', 'participant1.entreprise',
                    'participant2', 'participant2.entreprise',
                    'stand', 'traducteur',
                ])
                ->where(function($q) use ($participant) {
                    $q->where('id_participant1', $participant->id)
                      ->orWhere('id_participant2', $participant->id);
                })
                ->orderBy('date')
                ->orderBy('heure_debut')
                ->get()
            : collect();

        return view('livewire.participant.mon-planning', [
            'participant' => $participant,
            'rendezVous'  => $rendezVous,
        ])->layout('layouts.participant', ['title' => 'Mon Planning']);
    }
}