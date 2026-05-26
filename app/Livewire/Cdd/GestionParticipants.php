<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;

class GestionParticipants extends Component
{
    public $search = '';

    public function render()
{
    $cddId = auth()->id();

    return view('livewire.cdd.gestion-participants', [
        'participants' => Participant::with(['entreprise', 'evenement'])
            ->where('id_cdd', $cddId) // ← FILTRE PAR CDD
            ->when($this->search, fn($q) =>
                $q->where('nom', 'like', '%'.$this->search.'%')
                  ->orWhere('prenom', 'like', '%'.$this->search.'%')
            )
            ->latest()
            ->get(),
    ])->layout('layouts.cdd', ['title' => 'Mes Participants']);
}
}