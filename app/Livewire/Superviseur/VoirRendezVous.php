<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\RendezVous;

class VoirRendezVous extends Component
{
    public $search = '';
    public $filtre_statut = '';

    public function render()
    {
        return view('livewire.superviseur.voir-rendez-vous', [
            'rendezVous' => RendezVous::with([
                    'participant1', 'participant2', 'stand', 'traducteur'
                ])
                ->when($this->filtre_statut, fn($q) => $q->where('statut', $this->filtre_statut))
                ->when($this->search, fn($q) =>
                    $q->whereHas('participant1', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                    )->orWhereHas('participant2', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                    )
                )
                ->latest()->get(),
        ])->layout('layouts.superviseur', ['title' => 'Rendez-vous']);
    }
}