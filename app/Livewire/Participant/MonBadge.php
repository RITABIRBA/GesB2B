<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Badge;
use App\Models\Participant;
use App\Models\Evenement;

class MonBadge extends Component
{
    public function render()
    {
        // Liaison par email
        $participant = Participant::where('email', auth()->user()->email)->first();

        $badge = $participant
            ? Badge::with('typeBadge')->where('id_participant', $participant->id)->first()
            : null;

        // ✅ Récupérer l'événement du participant
        $evenement = $participant && $participant->id_evenement
            ? Evenement::find($participant->id_evenement)
            : null;

        return view('livewire.participant.mon-badge', [
            'participant' => $participant,
            'badge'       => $badge,
            'evenement'   => $evenement,
        ])->layout('layouts.participant', ['title' => 'Mon Badge']);
    }
}