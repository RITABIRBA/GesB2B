<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Souhait;
use App\Models\RendezVous;
use App\Models\Stand;
use App\Models\Notification;
use App\Models\User;

class FicheParticipant extends Component
{
    public Participant $participant;

    public function mount(int $id): void
    {
        $this->participant = Participant::with(['entreprise', 'evenement'])->findOrFail($id);
    }

    public function render()
    {
        $inscriptions = Inscription::with('evenement')
            ->where('id_participant', $this->participant->id)
            ->latest()
            ->get();

        $paiements = Paiement::with(['inscription.evenement', 'recu'])
            ->whereIn('id_inscription', $inscriptions->pluck('id'))
            ->latest()
            ->get();

        $souhaitsEmis = Souhait::with('participantCible')
            ->where('id_participant', $this->participant->id)
            ->get();

        $souhaitsRecus = Souhait::with('participant')
            ->where('id_participant_cible', $this->participant->id)
            ->get();

        $rendezVous = RendezVous::with(['participant1', 'participant2'])
            ->where('id_participant1', $this->participant->id)
            ->orWhere('id_participant2', $this->participant->id)
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get();

        $stand = Stand::with('typeStand')
            ->where('id_participant', $this->participant->id)
            ->first();

        $notifications = Notification::where('id_participant', $this->participant->id)
            ->latest()
            ->take(15)
            ->get();

        $compteUser = $this->participant->email
            ? User::where('email', $this->participant->email)->first()
            : null;

        return view('livewire.admin.fiche-participant', [
            'inscriptions'  => $inscriptions,
            'paiements'     => $paiements,
            'souhaitsEmis'  => $souhaitsEmis,
            'souhaitsRecus' => $souhaitsRecus,
            'rendezVous'    => $rendezVous,
            'stand'         => $stand,
            'notifications' => $notifications,
            'compteUser'    => $compteUser,
        ])->layout('layouts.superviseur', ['title' => 'Fiche Participant']);
    }
}