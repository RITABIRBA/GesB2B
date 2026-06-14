<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Souhait;
use App\Models\RendezVous;
use App\Models\Badge;
use App\Models\Inscription;
use App\Models\Evenement;
use App\Models\Entreprise;

class Dashboard extends Component
{
    public function render()
    {
        $participant = Participant::findForUser(auth()->user());

        // ← Entreprise du participant si membre
        $entreprise = $participant && $participant->id_entreprise
            ? Entreprise::find($participant->id_entreprise)
            : null;

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
            ? RendezVous::with(['participant1', 'participant2'])
                ->where('statut', 'planifie')
                ->where(function ($q) use ($participant) {
                    $q->where('id_participant1', $participant->id)
                      ->orWhere('id_participant2', $participant->id);
                })
                ->orderBy('date')
                ->orderBy('heure_debut')
                ->take(3)
                ->get()
            : collect();

        $today = now()->toDateString();

        // ← Événements disponibles
        $evenementsDisponibles = Evenement::where('date_fin', '>=', $today)
            ->where(fn($q) =>
                $q->whereNull('date_ouverture_inscriptions')
                  ->orWhere('date_ouverture_inscriptions', '<=', $today)
            )
            ->where(fn($q) =>
                $q->whereNull('date_cloture_inscriptions')
                  ->orWhere('date_cloture_inscriptions', '>=', $today)
            )
            ->orderBy('date_debut')
            ->get()
            ->map(function ($evenement) use ($participant) {
                $evenement->deja_inscrit = $participant
                    ? Inscription::where('id_participant', $participant->id)
                        ->where('id_evenement', $evenement->id)
                        ->exists()
                    : false;
                return $evenement;
            });

        // ← Inscriptions du participant
        $mesInscriptions = $participant
            ? Inscription::with('evenement')
                ->where('id_participant', $participant->id)
                ->latest()
                ->get()
            : collect();

        // ← Statut badge
        // Le membre fait partie d'une entreprise :
        // → Pas besoin de payer individuellement
        // → Il voit son statut d'inscription
        $estMembre = $participant && $participant->id_entreprise;

        return view('livewire.participant.dashboard', [
            'participant'           => $participant,
            'entreprise'            => $entreprise,
            'totalSouhaits'         => $totalSouhaits,
            'totalRdv'              => $totalRdv,
            'badge'                 => $badge,
            'prochainRdv'           => $prochainRdv,
            'evenementsDisponibles' => $evenementsDisponibles,
            'mesInscriptions'       => $mesInscriptions,
            'estMembre'             => $estMembre,
        ])->layout('layouts.participant', ['title' => 'Mon Dashboard']);
    }
}