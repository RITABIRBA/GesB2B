<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Evenement;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\RendezVous;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.superviseur.dashboard', [
            'totalEvenements'    => Evenement::count(),
            'totalEntreprises'   => Entreprise::count(),
            'totalParticipants'  => Participant::count(),
            'totalRendezVous'    => RendezVous::count(),
            'entreprisesAttente' => Entreprise::where('statut_validation', 'en_attente')->count(),
            'rdvPlanifies'       => RendezVous::where('statut', 'planifie')->count(),
            'derniersEvenements' => Evenement::latest()->take(5)->get(),
            'dernieresEntreprises' => Entreprise::where('statut_validation', 'en_attente')->latest()->take(5)->get(),
        ])->layout('layouts.superviseur', ['title' => 'Dashboard Superviseur']);
    }
}