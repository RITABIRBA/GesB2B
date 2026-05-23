<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\Inscription;
use App\Models\Evenement;

class GestionInscriptions extends Component
{
    public $search = '';
    public $filtre_statut = '';

    public function valider($id)
    {
        Inscription::findOrFail($id)->update(['statut_paiement' => 'paye']);
        session()->flash('success', 'Inscription validée.');
    }

    public function annuler($id)
    {
        Inscription::findOrFail($id)->update(['statut_paiement' => 'annule']);
        session()->flash('success', 'Inscription annulée.');
    }

    public function render()
    {
        return view('livewire.cdd.gestion-inscriptions', [
            'inscriptions' => Inscription::with(['participant', 'participant.entreprise', 'evenement'])
                ->when($this->filtre_statut, fn($q) =>
                    $q->where('statut_paiement', $this->filtre_statut)
                )
                ->when($this->search, fn($q) =>
                    $q->whereHas('participant', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )
                )
                ->latest()
                ->get(),
        ])->layout('layouts.cdd', ['title' => 'Gestion des Inscriptions']);
    }
}