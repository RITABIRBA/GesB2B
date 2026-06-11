<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Souhait;
use App\Models\Participant;
use App\Models\Inscription;
use App\Models\Evenement;

class MesSouhaits extends Component
{
    public $id_participant_cible = '';
    public $priorite             = '';
    public $showModal            = false;

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

    // ← Monte la priorité
    public function monterPriorite($id)
    {
        $participant = Participant::findForUser(auth()->user());
        $souhait     = Souhait::findOrFail($id);

        if ($souhait->priorite <= 1) return;

        // ← Swap avec le souhait qui a priorité - 1
        $voisin = Souhait::where('id_participant', $participant->id)
            ->where('priorite', $souhait->priorite - 1)
            ->first();

        if ($voisin) {
            $voisin->update(['priorite' => $souhait->priorite]);
        }
        $souhait->update(['priorite' => $souhait->priorite - 1]);
    }

    // ← Descend la priorité
    public function descendrePriorite($id)
    {
        $participant  = Participant::findForUser(auth()->user());
        $souhait      = Souhait::findOrFail($id);
        $maxPriorite  = Souhait::where('id_participant', $participant->id)->max('priorite');

        if ($souhait->priorite >= $maxPriorite) return;

        $voisin = Souhait::where('id_participant', $participant->id)
            ->where('priorite', $souhait->priorite + 1)
            ->first();

        if ($voisin) {
            $voisin->update(['priorite' => $souhait->priorite]);
        }
        $souhait->update(['priorite' => $souhait->priorite + 1]);
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

        // ← Récupère l'événement avec ses limites
        $evenement = Evenement::find($participant->id_evenement);
        $maxSouhaits = $evenement->max_souhaits ?? 20;
        $minSouhaits = $evenement->min_souhaits ?? 5;

        // ← Vérifie si max atteint
        $nbSouhaits = Souhait::where('id_participant', $participant->id)->count();
        if ($nbSouhaits >= $maxSouhaits) {
            session()->flash('error', "Vous avez atteint le maximum de {$maxSouhaits} souhaits pour cet événement.");
            $this->closeModal();
            return;
        }

        $this->validate([
            'id_participant_cible' => 'required',
        ]);

        $existe = Souhait::where('id_participant', $participant->id)
            ->where('id_participant_cible', $this->id_participant_cible)
            ->exists();

        if ($existe) {
            session()->flash('error', 'Vous avez déjà émis un souhait pour ce participant.');
            $this->closeModal();
            return;
        }

        // ← Priorité automatique (dernier + 1)
        $dernierePriorite = Souhait::where('id_participant', $participant->id)
            ->max('priorite') ?? 0;

        Souhait::create([
            'id_participant'       => $participant->id,
            'id_participant_cible' => $this->id_participant_cible,
            'priorite'             => $dernierePriorite + 1,
            'type'                 => 'envoye',
        ]);

        session()->flash('success', 'Souhait ajouté avec priorité ' . ($dernierePriorite + 1) . '.');
        $this->closeModal();
    }

    public function supprimer($id)
    {
        $participant = Participant::findForUser(auth()->user());
        $souhait     = Souhait::findOrFail($id);
        $prioriteSupprimee = $souhait->priorite;

        $souhait->delete();

        // ← Réajuste les priorités après suppression
        Souhait::where('id_participant', $participant->id)
            ->where('priorite', '>', $prioriteSupprimee)
            ->orderBy('priorite')
            ->each(function($s) {
                $s->update(['priorite' => $s->priorite - 1]);
            });

        session()->flash('success', 'Souhait supprimé.');
    }

    private function verifierInscriptionValide($participant): bool
    {
        if (!$participant || !$participant->id_evenement) return false;

        return Inscription::where('id_participant', $participant->id)
            ->where('id_evenement', $participant->id_evenement)
            ->where(function($q) {
                $q->where('statut_paiement', 'paye')
                  ->orWhereHas('evenement', fn($q) =>
                      $q->where('type_paiement', 'par_entreprise')
                  )
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

        // ← Récupère l'événement avec ses limites
        $evenement   = $participant && $participant->id_evenement
            ? Evenement::find($participant->id_evenement)
            : null;
        $minSouhaits = $evenement->min_souhaits ?? 5;
        $maxSouhaits = $evenement->max_souhaits ?? 20;

        $souhaits = $participant
            ? Souhait::with(['participant', 'participantCible.entreprise'])
                ->where('id_participant', $participant->id)
                ->orderBy('priorite')
                ->get()
            : collect();

        $nbSouhaits = $souhaits->count();

        return view('livewire.participant.mes-souhaits', [
            'souhaits'           => $souhaits,
            'nbSouhaits'         => $nbSouhaits,
            'minSouhaits'        => $minSouhaits,
            'maxSouhaits'        => $maxSouhaits,
            'objectifAtteint'    => $nbSouhaits >= $minSouhaits,
            'maxAtteint'         => $nbSouhaits >= $maxSouhaits,

            'autresParticipants' => ($participant && $inscriptionValide)
                ? Participant::with('entreprise')
                    ->where('id_evenement', $participant->id_evenement)
                    ->where('id', '!=', $participant->id)
                    ->where('participation_rdv', true)
                    ->orderBy('nom')
                    ->get()
                : collect(),

            'participant'        => $participant,
            'inscriptionValide'  => $inscriptionValide,
        ])->layout('layouts.participant', ['title' => 'Mes Souhaits']);
    }
}