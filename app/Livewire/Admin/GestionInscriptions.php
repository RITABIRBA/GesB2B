<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Inscription;
use App\Models\Participant;
use App\Models\Evenement;

class GestionInscriptions extends Component
{
    public $search = '';
    public $filtre_statut = '';
    public $filtre_evenement = '';

    public function validerPaiement($id)
    {
        $inscription = Inscription::findOrFail($id);
        $inscription->update(['statut_paiement' => 'paye']);
        session()->flash('success', 'Paiement validé avec succès.');
    }

    public function annuler($id)
    {
        Inscription::findOrFail($id)->update([
            'statut_paiement' => 'annule',
        ]);
        session()->flash('success', 'Inscription annulée.');
    }

    public function marquerPresent($id)
    {
        Inscription::findOrFail($id)->update(['statut_presence' => 'present']);
        session()->flash('success', 'Présence marquée.');
    }

    public function marquerAbsent($id)
    {
        Inscription::findOrFail($id)->update(['statut_presence' => 'absent']);
        session()->flash('success', 'Absence marquée.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-inscriptions', [
            'inscriptions' => Inscription::with(['participant', 'participant.entreprise', 'evenement', 'paiement'])
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
        ])->layout('layouts.admin', ['title' => 'Gestion des Inscriptions']);
    }
}