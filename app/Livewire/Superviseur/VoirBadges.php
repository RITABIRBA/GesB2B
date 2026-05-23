<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Badge;

class VoirBadges extends Component
{
    public $search = '';

    public function render()
    {
        return view('livewire.superviseur.voir-badges', [
            'badges' => Badge::with(['participant', 'participant.entreprise', 'typeBadge'])
                ->when($this->search, fn($q) =>
                    $q->whereHas('participant', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )
                )
                ->latest()->get(),
        ])->layout('layouts.superviseur', ['title' => 'Badges']);
    }
}