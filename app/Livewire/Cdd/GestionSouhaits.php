<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\Souhait;
use App\Models\Participant;

class GestionSouhaits extends Component
{
    public $search = '';

    public function render()
    {
        return view('livewire.cdd.gestion-souhaits', [
            'souhaits' => Souhait::with(['participant', 'participantCible'])
                ->orderBy('priorite')
                ->get(),
            'participants' => Participant::orderBy('nom')->get(),
        ])->layout('layouts.cdd', ['title' => 'Souhaits RDV']);
    }
}