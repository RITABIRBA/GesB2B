<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Souhait;
use App\Models\RendezVous;
use App\Models\Badge;
use App\Models\Inscription;

class Dashboard extends Component
{
    public function render()
    {
        // Liaison par email
        $participant = Participant::where('email', auth()->user()->email)->first();

        $totalSouhaits = $participant
            ? Souhait::where('id_participant', $participant->id)->count()
            : 0;

        $totalRdv = $participant
            ? RendezVous::where('id_participant1', $participant->id)
                ->orWhere('id_participant2', $participant->id)
                ->count()
            : 0;

        $badge = $participant
            ? Badge::where('id_participant', $participant->id)->first()
            : null;

        $prochainRdv = $participant
            ? RendezVous::with(['participant1', 'participant2', 'stand'])
                ->where('statut', 'planifie')
                ->where(function($q) use ($participant) {
                    $q->where('id_participant1', $participant->id)
                      ->orWhere('id_participant2', $participant->id);
                })
                ->orderBy('date')
                ->orderBy('heure_debut')
                ->take(3)
                ->get()
            : collect();

        // Notifications — inscriptions validées par CDD
        $inscriptionsValidees = $participant
            ? Inscription::with('evenement')
                ->where('id_participant', $participant->id)
                ->where('statut_presence', 'present')
                ->where('statut_paiement', 'en_attente')
                ->whereDoesntHave('paiement')
                ->get()
            : collect();

        // Inscriptions dont le paiement a été validé
        $paiementsValides = $participant
            ? Inscription::with(['evenement', 'paiement'])
                ->where('id_participant', $participant->id)
                ->where('statut_paiement', 'paye')
                ->get()
            : collect();

        return view('livewire.participant.dashboard', [
            'participant'          => $participant,
            'totalSouhaits'        => $totalSouhaits,
            'totalRdv'             => $totalRdv,
            'badge'                => $badge,
            'prochainRdv'          => $prochainRdv,
            'inscriptionsValidees' => $inscriptionsValidees,
            'paiementsValides'     => $paiementsValides,
        ])->layout('layouts.participant', ['title' => 'Mon Dashboard']);
    }
}