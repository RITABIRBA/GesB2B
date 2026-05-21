<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\Entreprise;

class Catalogue extends Component
{
    public $search = '';
    public $secteur_filtre = '';

    public function render()
    {
        return view('livewire.cdd.catalogue', [
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
        ])->layout('layouts.cdd', ['title' => 'Catalogue']);
    }
}