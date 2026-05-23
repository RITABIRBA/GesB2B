<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Badge;
use App\Models\Participant;

class MonBadge extends Component
{
    public function render()
    {
        $participant = Participant::first();
        $badge = $participant
            ? Badge::with('typeBadge')->where('id_participant', $participant->id)->first()
            : null;

        return view('livewire.participant.mon-badge', [
            'participant' => $participant,
            'badge'       => $badge,
        ])->layout('layouts.participant', ['title' => 'Mon Badge']);
    }
}