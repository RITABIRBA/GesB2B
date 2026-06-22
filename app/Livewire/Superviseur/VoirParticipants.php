<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Evenement;
use App\Models\Notification;

class VoirParticipants extends Component
{
    public $search           = '';
    public $filtre_evenement = '';

    // ─── Activation / Désactivation RDV ───────────────────
    public function toggleRdv(int $id): void
    {
        $participant = Participant::findOrFail($id);
        $participant->update([
            'participation_rdv' => !$participant->participation_rdv,
        ]);

        $statut = $participant->participation_rdv
            ? 'activée'
            : 'désactivée';

        Notification::create([
            'id_participant' => $participant->id,
            'contenu'        => "ℹ️ Votre participation aux rendez-vous a été {$statut} par la supervision.",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        session()->flash('success', "Participation RDV {$statut} pour {$participant->nom} {$participant->prenom}.");
    }

    // ─── Suppression participant ───────────────────────────
    public function supprimer(int $id): void
    {
        $participant = Participant::findOrFail($id);
        $nom         = "{$participant->nom} {$participant->prenom}";
        $participant->delete();
        session()->flash('success', "Participant {$nom} supprimé.");
    }

    public function render()
    {
        return view('livewire.superviseur.voir-participants', [
            'participants' => Participant::with(['entreprise', 'evenement'])
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('prenom', 'like', '%'.$this->search.'%')
                      ->orWhereHas('entreprise', fn($q) =>
                          $q->where('nom', 'like', '%'.$this->search.'%')
                      )
                )
                ->when($this->filtre_evenement, fn($q) =>
                    $q->where('id_evenement', $this->filtre_evenement)
                )
                ->latest()
                ->get(),
            'evenements' => Evenement::orderBy('nom')->get(),
        ])->layout('layouts.superviseur', ['title' => 'Participants']);
    }
}