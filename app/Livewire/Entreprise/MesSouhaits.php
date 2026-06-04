<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\Souhait;
use App\Models\Participant;
use App\Models\Entreprise;

class MesSouhaits extends Component
{
    public $id_participant = '';
    public $id_participant_cible = '';
    public $priorite = '';
    public $showModal = false;
    public $search = '';

    // ← Liaison par email
    private function getEntreprise()
    {
        return Entreprise::where('email_responsable', auth()->user()->email)->first()
            ?? Entreprise::where('nom', auth()->user()->name)->first();
    }

    public function openModal()
    {
        $this->resetFields();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->id_participant       = '';
        $this->id_participant_cible = '';
        $this->priorite             = '';
        $this->resetErrorBag();
    }

    public function sauvegarder()
    {
        $this->validate([
            'id_participant'       => 'required',
            'id_participant_cible' => 'required|different:id_participant',
            'priorite'             => 'required|integer|min:1|max:20',
        ]);

        Souhait::create([
            'id_participant'       => $this->id_participant,
            'id_participant_cible' => $this->id_participant_cible,
            'priorite'             => $this->priorite,
            'type'                 => 'envoye',
        ]);

        session()->flash('success', 'Souhait ajouté avec succès.');
        $this->closeModal();
    }

    public function supprimer($id)
    {
        Souhait::findOrFail($id)->delete();
        session()->flash('success', 'Souhait supprimé.');
    }

    public function render()
    {
        $entreprise = $this->getEntreprise();

        // ← Si entreprise non trouvée → liste vide
        if (!$entreprise) {
            return view('livewire.entreprise.mes-souhaits', [
                'souhaits'         => collect(),
                'mesParticipants'  => collect(),
                'tousParticipants' => collect(),
            ])->layout('layouts.entreprise', ['title' => 'Souhaits RDV']);
        }

        $participantIds = Participant::where('id_entreprise', $entreprise->id)
            ->pluck('id');

        // ← Participants du même événement que les participants de l'entreprise
        $id_evenements = Participant::where('id_entreprise', $entreprise->id)
            ->pluck('id_evenement')
            ->unique();

        return view('livewire.entreprise.mes-souhaits', [
            'souhaits' => Souhait::with([
                    'participant',
                    'participantCible',
                    'participantCible.entreprise',
                ])
                ->whereIn('id_participant', $participantIds)
                ->orderBy('priorite')
                ->get(),

            'mesParticipants' => Participant::where('id_entreprise', $entreprise->id)
                ->orderBy('nom')
                ->get(),

            // ← Seulement les participants du même événement
            'tousParticipants' => Participant::with('entreprise')
                ->whereIn('id_evenement', $id_evenements)
                ->where('id_entreprise', '!=', $entreprise->id)
                ->where('participation_rdv', true)
                ->orderBy('nom')
                ->get(),
        ])->layout('layouts.entreprise', ['title' => 'Souhaits RDV']);
    }
}