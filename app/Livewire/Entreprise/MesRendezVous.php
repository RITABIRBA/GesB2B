<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\Entreprise;
use App\Models\Participant;

class MesRendezVous extends Component
{
    public $search = '';
    public $filtre_statut = '';

    // ← Liaison par email au lieu du nom
    private function getEntreprise()
    {
        return Entreprise::where('email_responsable', auth()->user()->email)->first()
            ?? Entreprise::where('nom', auth()->user()->name)->first();
    }

    public function render()
    {
        $entreprise = $this->getEntreprise();

        // Si entreprise non trouvée → liste vide
        if (!$entreprise) {
            return view('livewire.entreprise.mes-rendez-vous', [
                'rendezVous' => collect(),
            ])->layout('layouts.entreprise', ['title' => 'Mes Rendez-vous']);
        }

        $participantIds = Participant::where('id_entreprise', $entreprise->id)
            ->pluck('id');

        return view('livewire.entreprise.mes-rendez-vous', [
            'rendezVous' => RendezVous::with([
                    'participant1',
                    'participant1.entreprise',
                    'participant2',
                    'participant2.entreprise',
                    'stand',
                    'traducteur',
                ])
                ->where(fn($q) =>
                    $q->whereIn('id_participant1', $participantIds)
                      ->orWhereIn('id_participant2', $participantIds)
                )
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
                ->latest()
                ->get(),
        ])->layout('layouts.entreprise', ['title' => 'Mes Rendez-vous']);
    }
}