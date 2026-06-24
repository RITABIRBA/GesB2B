<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Souhait;
use App\Models\Participant;
use App\Models\Evenement;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;

class GestionSouhaits extends Component
{
    public string $search           = '';
    public string $filtre_evenement = '';

    public bool   $showModalMatch       = false;
    public int    $participant_match_id = 0;
    public string $search_cible         = '';

    public bool   $showModal             = false;
    public bool   $isEditing             = false;
    public $souhait_id                   = null;
    public $id_participant               = '';
    public $id_participant_cible         = '';
    public $priorite                     = '';
    public $type                         = 'envoye';
    public $participantsCibles           = [];

    public string $alertSuccess = '';
    public string $alertError   = '';

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

    public function ouvrirMatchmaking(int $id): void
    {
        $this->alertSuccess         = '';
        $this->alertError           = '';
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

    private function calculerCompatibilite(Participant $moi, Participant $cible): int
    {
        if (!$moi->profilB2BComplet()) return 0;

        $points = 0;

        $secteursRecherche = is_array($moi->secteurs_recherche)
            ? $moi->secteurs_recherche
            : (json_decode($moi->secteurs_recherche ?? '[]', true) ?: []);

        if (!empty($secteursRecherche) && $cible->secteur_activite
            && in_array($cible->secteur_activite, $secteursRecherche)) {
            $points++;
        }

        if ($moi->zone_geographique && $cible->zone_geographique
            && $moi->zone_geographique === $cible->zone_geographique) {
            $points++;
        }

        $typesPartenariatMoi = is_array($moi->types_partenariat)
            ? $moi->types_partenariat
            : (json_decode($moi->types_partenariat ?? '[]', true) ?: []);

        $typesPartenariatCible = is_array($cible->types_partenariat)
            ? $cible->types_partenariat
            : (json_decode($cible->types_partenariat ?? '[]', true) ?: []);

        if (!empty($typesPartenariatMoi) && !empty($typesPartenariatCible)
            && count(array_intersect($typesPartenariatMoi, $typesPartenariatCible)) > 0) {
            $points++;
        }

        return $points;
    }

    public function matchmaker(int $id_cible): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participant = Participant::find($this->participant_match_id);
        $cible       = Participant::find($id_cible);

        if (!$participant || !$cible) {
            $this->alertError = 'Participant non trouvé.';
            return;
        }

        $dejaEmis = Souhait::where('id_participant', $participant->id)
            ->where('id_participant_cible', $id_cible)
            ->exists();

        if ($dejaEmis) {
            $this->alertError = 'Un souhait existe déjà entre ces deux participants.';
            return;
        }

        $dernierePriorite = Souhait::where('id_participant', $participant->id)->max('priorite') ?? 0;
        $souhaitRetour    = Souhait::where('id_participant', $id_cible)->where('id_participant_cible', $participant->id)->first();
        $estMutuel        = (bool) $souhaitRetour;

        Souhait::create([
            'id_participant'       => $participant->id,
            'id_participant_cible' => $id_cible,
            'id_evenement'         => $participant->id_evenement,
            'priorite'             => $dernierePriorite + 1,
            'type'                 => $estMutuel ? 'mutuel' : 'envoye',
            'statut'               => $estMutuel ? 'accepte' : 'en_attente',
        ]);

        if ($estMutuel) {
            $souhaitRetour->update(['type' => 'mutuel', 'statut' => 'accepte']);

            Notification::create(['id_participant' => $participant->id, 'contenu' => "🎉 Souhait mutuel avec {$cible->nom} {$cible->prenom} ! Un rendez-vous va être planifié.", 'date_envoie' => now()->toDateString(), 'type' => 'systeme']);
            Notification::create(['id_participant' => $cible->id,        'contenu' => "🎉 Souhait mutuel avec {$participant->nom} {$participant->prenom} ! Un rendez-vous va être planifié.", 'date_envoie' => now()->toDateString(), 'type' => 'systeme']);

            $nomEvenement = Evenement::find($participant->id_evenement)?->nom ?? 'Business Forum';

            if ($participant->email) {
                try { Mail::to($participant->email)->send(new \App\Mail\MatchMutuelNotification($participant, $cible, $nomEvenement)); } catch (\Exception $e) {}
            }
            if ($cible->email) {
                try { Mail::to($cible->email)->send(new \App\Mail\MatchMutuelNotification($cible, $participant, $nomEvenement)); } catch (\Exception $e) {}
            }
        }

        $this->alertSuccess = 'Souhait créé : ' . $participant->nom . ' → ' . $cible->nom
            . ($estMutuel ? ' 🎉 Mutuel ! Notifications envoyées.' : ' (en attente, pas de notification envoyée)');
    }

    public function openModal(): void  { $this->resetFields(); $this->showModal = true; $this->isEditing = false; }
    public function closeModal(): void { $this->showModal = false; $this->resetFields(); }

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

        $participant = Participant::find($this->id_participant);

        $data = [
            'id_participant'       => $this->id_participant,
            'id_participant_cible' => $this->id_participant_cible,
            'id_evenement'         => $participant->id_evenement ?? null,
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
        $participantMatch = $this->participant_match_id
            ? Participant::with('entreprise')->find($this->participant_match_id)
            : null;

        $candidatsMatch = collect();
        if ($participantMatch) {
            $idsCibles = Souhait::where('id_participant', $participantMatch->id)->pluck('id_participant_cible')->toArray();

            $candidatsMatch = Participant::with('entreprise')
                ->where('id_evenement', $participantMatch->id_evenement)
                ->where('id', '!=', $participantMatch->id)
                ->where('participation_rdv', true)
                ->when($participantMatch->id_entreprise, fn($q) =>
                    $q->where(function ($q) use ($participantMatch) {
                        $q->whereNull('id_entreprise')->orWhere('id_entreprise', '!=', $participantMatch->id_entreprise);
                    })
                )
                ->when($this->search_cible, function ($q) {
                    $q->where(function ($q) {
                        $q->where('nom', 'like', '%'.$this->search_cible.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search_cible.'%')
                          ->orWhereHas('entreprise', fn($q) => $q->where('nom', 'like', '%'.$this->search_cible.'%'));
                    });
                })
                ->get()
                ->map(function ($p) use ($participantMatch, $idsCibles) {
                    $p->score_compatibilite = $this->calculerCompatibilite($participantMatch, $p);
                    $p->souhait_emis        = in_array($p->id, $idsCibles);
                    return $p;
                })
                ->sortBy([['souhait_emis', 'asc'], ['score_compatibilite', 'desc']])
                ->values();
        }

        return view('livewire.admin.gestion-souhaits', [
            'participants' => Participant::with('entreprise')
                ->when($this->search, function ($q) {
                    $q->where(function ($q) {
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                          ->orWhereHas('entreprise', fn($q) => $q->where('nom', 'like', '%'.$this->search.'%'));
                    });
                })
                ->when($this->filtre_evenement, fn($q) => $q->where('id_evenement', $this->filtre_evenement))
                ->where('participation_rdv', true)
                ->orderBy('nom')
                ->get()
                ->map(function ($p) {
                    $p->nb_souhaits    = Souhait::where('id_participant', $p->id)->count();
                    $p->nb_mutuels     = Souhait::where('id_participant', $p->id)->where('type', 'mutuel')->count();
                    $p->profil_complet = $p->profilB2BComplet();
                    return $p;
                }),

            'souhaits' => Souhait::with(['participant.entreprise', 'participantCible.entreprise'])
                ->when($this->filtre_evenement, fn($q) => $q->where('id_evenement', $this->filtre_evenement))
                ->when($this->search, fn($q) =>
                    $q->whereHas('participant', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )
                )
                ->orderBy('id_participant')
                ->orderBy('priorite')
                ->get(),

            'evenements'       => Evenement::orderBy('nom')->get(),
            'participantMatch' => $participantMatch,
            'candidatsMatch'   => $candidatsMatch,
            'tousParticipants' => Participant::with('entreprise')->orderBy('nom')->get(),
        ])->layout('layouts.superviseur', ['title' => 'Souhaits & Matchmaking']);
    }
}