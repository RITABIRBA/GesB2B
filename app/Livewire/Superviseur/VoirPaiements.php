<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Paiement;

class VoirPaiements extends Component
{
    public $search = '';
    public $filtre_statut = '';
    public $filtre_mode = '';

    public function render()
    {
        return view('livewire.superviseur.voir-paiements', [
            'paiements' => Paiement::with([
                    'inscription',
                    'inscription.participant',
                    'inscription.participant.entreprise',
                    'inscription.evenement',
                    'recu',
                ])
                ->when($this->filtre_statut, fn($q) => $q->where('statut', $this->filtre_statut))
                ->when($this->filtre_mode, fn($q) => $q->where('mode_paiement', $this->filtre_mode))
                ->when($this->search, fn($q) =>
                    $q->whereHas('inscription.participant', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )
                )
                ->latest()
                ->get(),
        ])->layout('layouts.superviseur', ['title' => 'Paiements']);
    }
}