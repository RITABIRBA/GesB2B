<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\Evenement;

/**
 * Dashboard CDD — Chef de Délégation
 *
 * Affiche un résumé des entreprises et participants
 * dont le CDD a la responsabilité.
 */
class Dashboard extends Component
{
    public function render()
    {
        // Le CDD voit uniquement les données de sa délégation
        // Pour l'instant on affiche toutes les données
        // (on affinera avec le pays/région du CDD plus tard)

        return view('livewire.cdd.dashboard', [
            'totalEntreprises'   => Entreprise::count(),
            'totalParticipants'  => Participant::count(),
            'entreprisesAttente' => Entreprise::where('statut_validation', 'en_attente')->count(),
            'dernieresEntreprises' => Entreprise::latest()->take(5)->get(),
        ])->layout('layouts.cdd', ['title' => 'Dashboard CDD']);
    }
}