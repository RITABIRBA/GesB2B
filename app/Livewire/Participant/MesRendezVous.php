<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\Participant;

class MesRendezVous extends Component
{
    public $filtre_statut = '';

    // =========================================================
    // GESTION DES ABSENCES
    // =========================================================

    /**
     * Le participant signale son absence pour un RDV
     */
    public function signalerAbsence($id)
    {
        // Liaison par email
        $participant = Participant::where('email', auth()->user()->email)->first();

        $rdv = RendezVous::findOrFail($id);

        if ($rdv->statut !== 'planifie') {
            session()->flash('error', 'Vous ne pouvez pas signaler une absence pour ce rendez-vous.');
            return;
        }

        $rdv->update([
            'statut'                => 'annule',
            'absent_participant_id' => $participant->id,
        ]);

        session()->flash('success', 'Absence signalée. Le rendez-vous a été annulé.');
    }

    /**
     * Le participant annule son signalement d'absence
     */
    public function annulerAbsence($id)
    {
        $rdv = RendezVous::findOrFail($id);
        $rdv->update([
            'statut'                => 'planifie',
            'absent_participant_id' => null,
        ]);
        session()->flash('success', 'Absence annulée. Le rendez-vous est rétabli.');
    }

    public function render()
    {
        // Liaison par email
        $participant = Participant::where('email', auth()->user()->email)->first();

        return view('livewire.participant.mes-rendez-vous', [
            'rendezVous' => $participant
                ? RendezVous::with([
                        'participant1', 'participant1.entreprise',
                        'participant2', 'participant2.entreprise',
                        'stand', 'traducteur',
                    ])
                    ->where(function($q) use ($participant) {
                        $q->where('id_participant1', $participant->id)
                          ->orWhere('id_participant2', $participant->id);
                    })
                    ->when($this->filtre_statut, fn($q) => $q->where('statut', $this->filtre_statut))
                    ->orderBy('date')
                    ->orderBy('heure_debut')
                    ->get()
                : collect(),
            'participant' => $participant,
        ])->layout('layouts.participant', ['title' => 'Mes Rendez-vous']);
    }
}