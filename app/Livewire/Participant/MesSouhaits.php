<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Souhait;
use App\Models\Participant;
use App\Models\Inscription;

class MesSouhaits extends Component
{
    public $id_participant_cible = '';
    public $priorite = '';
    public $showModal = false;

    public function openModal()
    {
        $this->id_participant_cible = '';
        $this->priorite             = '';
        $this->showModal            = true;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function sauvegarder()
    {
        $participant = Participant::findForUser(auth()->user());

        if (!$participant) {
            session()->flash('error', 'Participant non trouvé.');
            return;
        }

        if (!$participant->id_evenement) {
            session()->flash('error', 'Vous devez d\'abord vous inscrire à un événement.');
            $this->closeModal();
            return;
        }

        // ← Vérifie inscription valide
        $inscriptionValide = $this->verifierInscriptionValide($participant);

        if (!$inscriptionValide) {
            session()->flash('error', 'Vous devez avoir une inscription validée pour émettre des souhaits.');
            $this->closeModal();
            return;
        }

        $this->validate([
            'id_participant_cible' => 'required',
            'priorite'             => 'required|integer|min:1|max:20',
        ]);

        $existe = Souhait::where('id_participant', $participant->id)
            ->where('id_participant_cible', $this->id_participant_cible)
            ->exists();

        if ($existe) {
            session()->flash('error', 'Vous avez déjà émis un souhait pour ce participant.');
            $this->closeModal();
            return;
        }

        Souhait::create([
            'id_participant'       => $participant->id,
            'id_participant_cible' => $this->id_participant_cible,
            'priorite'             => $this->priorite,
            'type'                 => 'envoye',
        ]);

        session()->flash('success', 'Souhait ajouté.');
        $this->closeModal();
    }

    public function supprimer($id)
    {
        Souhait::findOrFail($id)->delete();
        session()->flash('success', 'Souhait supprimé.');
    }

    // ← Helper pour vérifier si l'inscription est valide
    private function verifierInscriptionValide($participant): bool
    {
        if (!$participant || !$participant->id_evenement) return false;

        return Inscription::where('id_participant', $participant->id)
            ->where('id_evenement', $participant->id_evenement)
            ->where(function($q) {
                $q->where('statut_paiement', 'paye')
                  // ← Par entreprise → en_attente est valide aussi
                  ->orWhereHas('evenement', fn($q) =>
                      $q->where('type_paiement', 'par_entreprise')
                  )
                  // ← Gratuit → toujours valide
                  ->orWhereHas('evenement', fn($q) =>
                      $q->where('type_paiement', 'gratuit')
                  );
            })
            ->exists();
    }

    public function render()
    {
        $participant = Participant::findForUser(auth()->user());

        $inscriptionValide = $participant
            ? $this->verifierInscriptionValide($participant)
            : false;

        return view('livewire.participant.mes-souhaits', [
            'souhaits' => $participant
                ? Souhait::with(['participant', 'participantCible.entreprise'])
                    ->where('id_participant', $participant->id)
                    ->orderBy('priorite')
                    ->get()
                : collect(),

            'autresParticipants' => ($participant && $inscriptionValide)
                ? Participant::with('entreprise')
                    ->where('id_evenement', $participant->id_evenement)
                    ->where('id', '!=', $participant->id)
                    ->where('participation_rdv', true)
                    ->orderBy('nom')
                    ->get()
                : collect(),

            'participant'       => $participant,
            'inscriptionValide' => $inscriptionValide,
        ])->layout('layouts.participant', ['title' => 'Mes Souhaits']);
    }
}