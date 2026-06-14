<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Souhait;
use App\Models\Participant;
use App\Models\Evenement;

class GestionSouhaits extends Component
{
    // Filtres liste participants
    public string $search          = '';
    public string $filtre_evenement = '';

    // Modal matchmaking
    public bool   $showModalMatch        = false;
    public int    $participant_match_id  = 0;
    public string $search_cible          = '';

    // Modal souhait manuel
    public bool   $showModal             = false;
    public bool   $isEditing             = false;
    public $souhait_id                   = null;
    public $id_participant               = '';
    public $id_participant_cible         = '';
    public $priorite                     = '';
    public $type                         = 'envoye';
    public $participantsCibles           = [];

    // Messages
    public string $alertSuccess = '';
    public string $alertError   = '';

    /**
     * Quand le participant change, filtre les cibles du même événement.
     */
    public function updatedIdParticipant($value): void
    {
        $this->participantsCibles   = [];
        $this->id_participant_cible = '';

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
        }
    }

    /**
     * Ouvre le modal de matchmaking pour un participant.
     * Affiche tous les participants compatibles du même événement.
     */
    public function ouvrirMatchmaking(int $id): void
    {
        $this->alertSuccess        = '';
        $this->alertError          = '';
        $this->participant_match_id = $id;
        $this->search_cible         = '';
        $this->showModalMatch       = true;
    }

    public function fermerMatchmaking(): void
    {
        $this->showModalMatch       = false;
        $this->participant_match_id = 0;
        $this->search_cible         = '';
    }

    /**
     * Crée un souhait depuis le matchmaking admin.
     * Le souhait est créé en faveur du participant sélectionné.
     * Détecte automatiquement si le souhait est mutuel.
     */
    public function matchmaker(int $id_cible): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participant = Participant::find($this->participant_match_id);

        if (!$participant) {
            $this->alertError = 'Participant non trouvé.';
            return;
        }

        // Vérifie si le souhait existe déjà
        $dejaEmis = Souhait::where('id_participant', $participant->id)
            ->where('id_participant_cible', $id_cible)
            ->exists();

        if ($dejaEmis) {
            $this->alertError = 'Un souhait existe déjà entre ces deux participants.';
            return;
        }

        // Calcule la priorité automatiquement
        $dernierePriorite = Souhait::where('id_participant', $participant->id)
            ->max('priorite') ?? 0;

        // Vérifie si le souhait est mutuel
        $estMutuel = Souhait::where('id_participant', $id_cible)
            ->where('id_participant_cible', $participant->id)
            ->exists();

        Souhait::create([
            'id_participant'       => $participant->id,
            'id_participant_cible' => $id_cible,
            'priorite'             => $dernierePriorite + 1,
            'type'                 => $estMutuel ? 'mutuel' : 'envoye',
        ]);

        // Si mutuel, met à jour le souhait de la cible
        if ($estMutuel) {
            Souhait::where('id_participant', $id_cible)
                ->where('id_participant_cible', $participant->id)
                ->update(['type' => 'mutuel']);
        }

        $cible = Participant::find($id_cible);
        $this->alertSuccess = 'Souhait créé : '
            . $participant->nom . ' → ' . ($cible->nom ?? '-')
            . ($estMutuel ? ' 🎉 Mutuel !' : '');
    }

    public function openModal(): void
    {
        $this->resetFields();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function resetFields(): void
    {
        $this->souhait_id           = null;
        $this->id_participant       = '';
        $this->id_participant_cible = '';
        $this->priorite             = '';
        $this->type                 = 'envoye';
        $this->participantsCibles   = [];
        $this->resetErrorBag();
    }

    public function modifier(int $id): void
    {
        $s = Souhait::findOrFail($id);
        $this->souhait_id           = $s->id;
        $this->id_participant       = $s->id_participant;
        $this->id_participant_cible = $s->id_participant_cible;
        $this->priorite             = $s->priorite;
        $this->type                 = $s->type;
        $this->isEditing            = true;
        $this->showModal            = true;
        $this->updatedIdParticipant($s->id_participant);
    }

    public function sauvegarder(): void
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
            session()->flash('success', 'Souhait modifié.');
        } else {
            Souhait::create($data);
            session()->flash('success', 'Souhait créé.');
        }

        $this->closeModal();
    }

    public function supprimer(int $id): void
    {
        Souhait::findOrFail($id)->delete();
        session()->flash('success', 'Souhait supprimé.');
    }

    public function render()
    {
        // Participant en cours de matchmaking
        $participantMatch = $this->participant_match_id
            ? Participant::with('entreprise')->find($this->participant_match_id)
            : null;

        // Candidats pour le matchmaking (même événement, pas même entreprise)
        $candidatsMatch = collect();
        if ($participantMatch) {
            $idsCibles = Souhait::where('id_participant', $participantMatch->id)
                ->pluck('id_participant_cible')
                ->toArray();

            $candidatsMatch = Participant::with('entreprise')
                ->where('id_evenement', $participantMatch->id_evenement)
                ->where('id', '!=', $participantMatch->id)
                ->where('participation_rdv', true)
                ->when($participantMatch->id_entreprise, fn($q) =>
                    $q->where(function ($q) use ($participantMatch) {
                        $q->whereNull('id_entreprise')
                          ->orWhere('id_entreprise', '!=', $participantMatch->id_entreprise);
                    })
                )
                ->when($this->search_cible, function ($q) {
                    $q->where(function ($q) {
                        $q->where('nom', 'like', '%' . $this->search_cible . '%')
                          ->orWhere('prenom', 'like', '%' . $this->search_cible . '%')
                          ->orWhereHas('entreprise', fn($q) =>
                              $q->where('nom', 'like', '%' . $this->search_cible . '%')
                          );
                    });
                })
                ->get()
                ->map(function ($p) use ($participantMatch, $idsCibles) {
                    // Score de compatibilité
                    $points = 0;
                    if ($participantMatch->secteur_recherche && $p->secteur_activite == $participantMatch->secteur_recherche) $points++;
                    if ($participantMatch->zone_geographique && $p->zone_geographique == $participantMatch->zone_geographique) $points++;
                    if ($participantMatch->type_partenaire && $p->type_partenaire == $participantMatch->type_partenaire) $points++;
                    $p->score_compatibilite = $points;
                    $p->souhait_emis        = in_array($p->id, $idsCibles);
                    return $p;
                })
                ->sortBy([
                    ['souhait_emis', 'asc'],
                    ['score_compatibilite', 'desc'],
                ])
                ->values();
        }

        return view('livewire.admin.gestion-souhaits', [
            // Liste de tous les participants avec leurs infos complètes
            'participants' => Participant::with('entreprise')
                ->when($this->search, function ($q) {
                    $q->where(function ($q) {
                        $q->where('nom', 'like', '%' . $this->search . '%')
                          ->orWhere('prenom', 'like', '%' . $this->search . '%')
                          ->orWhereHas('entreprise', fn($q) =>
                              $q->where('nom', 'like', '%' . $this->search . '%')
                          );
                    });
                })
                ->when($this->filtre_evenement, fn($q) =>
                    $q->where('id_evenement', $this->filtre_evenement)
                )
                ->where('participation_rdv', true)
                ->orderBy('nom')
                ->get()
                ->map(function ($p) {
                    $p->nb_souhaits = Souhait::where('id_participant', $p->id)->count();
                    $p->nb_mutuels  = Souhait::where('id_participant', $p->id)
                        ->where('type', 'mutuel')->count();
                    return $p;
                }),

            // Liste des souhaits
            'souhaits' => Souhait::with([
                    'participant.entreprise',
                    'participantCible.entreprise',
                ])
                ->when($this->search, fn($q) =>
                    $q->whereHas('participant', fn($q) =>
                        $q->where('nom', 'like', '%' . $this->search . '%')
                          ->orWhere('prenom', 'like', '%' . $this->search . '%')
                    )
                )
                ->orderBy('id_participant')
                ->orderBy('priorite')
                ->get(),

            'evenements'      => Evenement::orderBy('nom')->get(),
            'participantMatch' => $participantMatch,
            'candidatsMatch'   => $candidatsMatch,

            // Pour le modal souhait manuel
            'tousParticipants' => Participant::with('entreprise')->orderBy('nom')->get(),
        ])->layout('layouts.admin', ['title' => 'Souhaits & Matchmaking']);
    }
}