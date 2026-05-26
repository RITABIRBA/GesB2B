<?php

namespace App\Livewire\Traducteur;

use Livewire\Component;
use App\Models\Traducteur;
use App\Models\RendezVous;

class Dashboard extends Component
{
    public function render()
    {
        // Liaison par email ou nom
        $traducteur = Traducteur::where('email', auth()->user()->email)
            ->orWhere('nom', auth()->user()->name)
            ->first();

        $totalRdv = $traducteur
            ? RendezVous::where('id_traducteur', $traducteur->id)->count()
            : 0;

        $rdvAujourdhui = $traducteur
            ? RendezVous::where('id_traducteur', $traducteur->id)
                ->where('date', today())
                ->count()
            : 0;

        $prochainRdv = $traducteur
            ? RendezVous::with(['participant1', 'participant2', 'stand'])
                ->where('id_traducteur', $traducteur->id)
                ->where('statut', 'planifie')
                ->orderBy('date')
                ->orderBy('heure_debut')
                ->take(5)
                ->get()
            : collect();

        return view('livewire.traducteur.dashboard', [
            'traducteur'    => $traducteur,
            'totalRdv'      => $totalRdv,
            'rdvAujourdhui' => $rdvAujourdhui,
            'prochainRdv'   => $prochainRdv,
        ])->layout('layouts.traducteur', ['title' => 'Dashboard Traducteur']);
    }
}