<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Inscription;
use App\Models\Evenement;

class VoirInscriptions extends Component
{
    public $search = '';
    public $filtre_statut = '';
    public $filtre_evenement = '';

    public function render()
    {
        return view('livewire.superviseur.voir-inscriptions', [
            'inscriptions' => Inscription::with([
                    'participant',
                    'participant.entreprise',
                    'evenement',
                    'paiement',
                ])
                ->when($this->filtre_statut, fn($q) =>
                    $q->where('statut_paiement', $this->filtre_statut)
                )
                ->when($this->filtre_evenement, fn($q) =>
                    $q->where('id_evenement', $this->filtre_evenement)
                )
                ->when($this->search, fn($q) =>
                    $q->whereHas('participant', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )
                )
                ->latest()
                ->get(),
            'evenements' => Evenement::orderBy('nom')->get(),
        ])->layout('layouts.superviseur', ['title' => 'Inscriptions']);
    }
}