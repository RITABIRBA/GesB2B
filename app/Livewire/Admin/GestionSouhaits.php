<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Souhait;
use App\Models\Participant;

class GestionSouhaits extends Component
{
    public $souhait_id;
    public $id_participant = '';
    public $id_participant_cible = '';
    public $priorite = '';
    public $type = 'envoye';
    public $showModal = false;
    public $isEditing = false;
    public $search = '';

    // ← Participants cibles filtrés par événement
    public $participantsCibles = [];

    public function openModal()
    {
        $this->resetFields();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->souhait_id           = null;
        $this->id_participant       = '';
        $this->id_participant_cible = '';
        $this->priorite             = '';
        $this->type                 = 'envoye';
        $this->participantsCibles   = [];
        $this->resetErrorBag();
    }

    // ← Quand le participant change → filtre les cibles
    public function updatedIdParticipant($value)
    {
        if ($value) {
            $participant = Participant::find($value);
            if ($participant) {
                $this->participantsCibles = Participant::with('entreprise')
                    ->where('id_evenement', $participant->id_evenement)
                    ->where('id', '!=', $value)
                    ->where('participation_rdv', true)
                    ->orderBy('nom')
                    ->get()
                    ->toArray();
            }
        } else {
            $this->participantsCibles = [];
        }
        $this->id_participant_cible = '';
    }

    public function modifier($id)
    {
        $s = Souhait::findOrFail($id);
        $this->souhait_id           = $s->id;
        $this->id_participant       = $s->id_participant;
        $this->id_participant_cible = $s->id_participant_cible;
        $this->priorite             = $s->priorite;
        $this->type                 = $s->type;
        $this->isEditing            = true;
        $this->showModal            = true;

        // Charge les cibles pour ce participant
        $this->updatedIdParticipant($s->id_participant);
    }

    public function sauvegarder()
    {
        $this->validate([
            'id_participant'       => 'required',
            'id_participant_cible' => 'required|different:id_participant',
            'priorite'             => 'required|integer|min:1|max:20',
        ]);

        $data = [
            'id_participant'       => $this->id_participant,
            'id_participant_cible' => $this->id_participant_cible,
            'priorite'             => $this->priorite,
            'type'                 => $this->type,
        ];

        if ($this->isEditing) {
            Souhait::findOrFail($this->souhait_id)->update($data);
            session()->flash('success', 'Souhait modifié avec succès.');
        } else {
            Souhait::create($data);
            session()->flash('success', 'Souhait créé avec succès.');
        }

        $this->closeModal();
    }

    public function supprimer($id)
    {
        Souhait::findOrFail($id)->delete();
        session()->flash('success', 'Souhait supprimé.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-souhaits', [
            'souhaits' => Souhait::with([
                    'participant',
                    'participant.entreprise',
                    'participantCible',
                    'participantCible.entreprise',
                ])
                ->when($this->search, fn($q) =>
                    $q->whereHas('participant', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )
                )
                ->orderBy('priorite')
                ->get(),

            // Tous les participants pour la liste du demandeur
            'participants' => Participant::with('entreprise')
                ->orderBy('nom')
                ->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Souhaits RDV']);
    }
}