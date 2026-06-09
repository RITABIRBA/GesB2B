<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Evenement;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\RendezVous;
use App\Models\Inscription;
use App\Models\Badge;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            // Stats globales
            'totalEvenements'   => Evenement::count(),
            'totalEntreprises'  => Entreprise::count(),
            'totalParticipants' => Participant::count(),
            'totalRendezVous'   => RendezVous::count(),

            // Stats supplémentaires
            'totalBadges'           => Badge::count(),
            'inscriptionsEnAttente' => Inscription::where('statut_paiement', 'en_attente')->count(),
            'participantsActifs'    => Participant::where('statut_historique', 'actif')->count(),
            'evenementsActifs'      => Evenement::where('date_fin', '>=', now()->toDateString())->count(),

            // Listes
            'derniersEvenements' => Evenement::latest()->take(5)->get(),
            'dernieresInscriptions' => Inscription::with(['participant', 'evenement'])
                ->latest()
                ->take(5)
                ->get(),
            'prochainsEvenements' => Evenement::where('date_debut', '>=', now()->toDateString())
                ->orderBy('date_debut')
                ->take(3)
                ->get(),
        ])->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}