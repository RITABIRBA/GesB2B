<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Evenement;

class VoirEvenements extends Component
{
    public $search = '';

    public function render()
    {
        return view('livewire.superviseur.voir-evenements', [
            'evenements' => Evenement::with('typeEvenement')
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('ville', 'like', '%'.$this->search.'%')
                )
                ->latest()->get(),
        ])->layout('layouts.superviseur', ['title' => 'Événements']);
    }
}