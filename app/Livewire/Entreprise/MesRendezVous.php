<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\Entreprise;
use App\Models\Participant;

class MesRendezVous extends Component
{
    public $search = '';
    public $filtre_statut = '';

    public function render()
    {
        $entreprise     = Entreprise::first();
        $participantIds = Participant::where('id_entreprise', $entreprise->id)->pluck('id');

        return view('livewire.entreprise.mes-rendez-vous', [
            'rendezVous' => RendezVous::with([
                    'participant1', 'participant2', 'stand', 'traducteur'
                ])
                ->where(fn($q) =>
                    $q->whereIn('id_participant1', $participantIds)
                      ->orWhereIn('id_participant2', $participantIds)
                )
                ->when($this->filtre_statut, fn($q) => $q->where('statut', $this->filtre_statut))
                ->latest()->get(),
        ])->layout('layouts.entreprise', ['title' => 'Mes Rendez-vous']);
    }
}