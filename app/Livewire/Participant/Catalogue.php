<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Entreprise;

class Catalogue extends Component
{
    public $search = '';
    public $secteur_filtre = '';

    public function render()
    {
        return view('livewire.participant.catalogue', [
            'entreprises' => Entreprise::with('participants')
                ->where('statut_validation', 'valide')
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('secteur_activite', 'like', '%'.$this->search.'%')
                )
                ->when($this->secteur_filtre, fn($q) =>
                    $q->where('secteur_activite', $this->secteur_filtre)
                )
                ->get(),
            'secteurs' => Entreprise::where('statut_validation', 'valide')
                ->distinct()->pluck('secteur_activite'),
        ])->layout('layouts.participant', ['title' => 'Catalogue']);
    }
}