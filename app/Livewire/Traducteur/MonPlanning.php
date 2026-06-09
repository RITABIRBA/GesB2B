<?php

namespace App\Livewire\Traducteur;

use Livewire\Component;
use App\Models\Traducteur;
use App\Models\RendezVous;

class MonPlanning extends Component
{
    public $search = '';
    public $filtre_statut = '';

    public function render()
    {
        // ← Liaison par email
        $traducteur = Traducteur::where('email', auth()->user()->email)->first();

        $rendezVous = $traducteur
            ? RendezVous::with([
                    'participant1', 'participant1.entreprise',
                    'participant2', 'participant2.entreprise',
                ])
                ->where('id_traducteur', $traducteur->id)
                ->when($this->filtre_statut, fn($q) =>
                    $q->where('statut', $this->filtre_statut)
                )
                ->when($this->search, fn($q) =>
                    $q->whereHas('participant1', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )->orWhereHas('participant2', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )
                )
                ->orderBy('date')
                ->orderBy('heure_debut')
                ->get()
            : collect();

        return view('livewire.traducteur.mon-planning', [
            'traducteur' => $traducteur,
            'rendezVous' => $rendezVous,
        ])->layout('layouts.traducteur', ['title' => 'Mon Planning']);
    }
}