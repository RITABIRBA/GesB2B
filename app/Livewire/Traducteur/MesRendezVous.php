<?php

namespace App\Livewire\Traducteur;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\Traducteur;

class MesRendezVous extends Component
{
    public $filtre_statut = '';
    public $filtre_date = '';

    public function render()
    {
        // Liaison par email ou nom
        $traducteur = Traducteur::where('email', auth()->user()->email)
            ->orWhere('nom', auth()->user()->name)
            ->first();

        return view('livewire.traducteur.mes-rendez-vous', [
            'rendezVous' => $traducteur
                ? RendezVous::with([
                        'participant1', 'participant1.entreprise',
                        'participant2', 'participant2.entreprise',
                        'stand'
                    ])
                    ->where('id_traducteur', $traducteur->id)
                    ->when($this->filtre_statut, fn($q) => $q->where('statut', $this->filtre_statut))
                    ->when($this->filtre_date, fn($q) => $q->where('date', $this->filtre_date))
                    ->orderBy('date')
                    ->orderBy('heure_debut')
                    ->get()
                : collect(),
            'traducteur' => $traducteur,
        ])->layout('layouts.traducteur', ['title' => 'Mes Rendez-vous']);
    }
}