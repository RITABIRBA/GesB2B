<?php

namespace App\Livewire\Traducteur;

use Livewire\Component;
use App\Models\Traducteur;
use App\Models\RendezVous;

class Dashboard extends Component
{
    public function render()
    {
        // ← Liaison par email
        $traducteur = Traducteur::where('email', auth()->user()->email)->first();

        $totalRdv = $traducteur
            ? RendezVous::where('id_traducteur', $traducteur->id)->count()
            : 0;

        $rdvAujourdhui = $traducteur
            ? RendezVous::where('id_traducteur', $traducteur->id)
                ->where('date', today())
                ->count()
            : 0;

        $rdvConfirmes = $traducteur
            ? RendezVous::where('id_traducteur', $traducteur->id)
                ->where('statut', 'confirme')
                ->count()
            : 0;

        $prochainRdv = $traducteur
            ? RendezVous::with([
                    'participant1', 'participant1.entreprise',
                    'participant2', 'participant2.entreprise',
                ])
                ->where('id_traducteur', $traducteur->id)
                ->where('statut', '!=', 'annule')
                ->orderBy('date')
                ->orderBy('heure_debut')
                ->take(5)
                ->get()
            : collect();

        return view('livewire.traducteur.dashboard', [
            'traducteur'    => $traducteur,
            'totalRdv'      => $totalRdv,
            'rdvAujourdhui' => $rdvAujourdhui,
            'rdvConfirmes'  => $rdvConfirmes,
            'prochainRdv'   => $prochainRdv,
        ])->layout('layouts.traducteur', ['title' => 'Dashboard Traducteur']);
    }
}