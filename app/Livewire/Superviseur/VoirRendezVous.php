<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\Participant;
use App\Models\Evenement;
use App\Models\Notification;

class VoirRendezVous extends Component
{
    public $search = '';
    public $filtre_statut = '';

    // ─── Match manuel (CAS 4) ─────────────────────────────
    public $showMatchManuelModal   = false;
    public $match_id_evenement     = '';
    public $match_participant1     = '';
    public $match_participant2     = '';
    public $match_compatibilite    = null;
    public $match_disponibilite_ok = true;

    // ─── MATCH MANUEL ──────────────────────────────────────

    public function ouvrirMatchManuel(): void
    {
        $this->showMatchManuelModal   = true;
        $this->match_id_evenement     = '';
        $this->match_participant1     = '';
        $this->match_participant2     = '';
        $this->match_compatibilite    = null;
        $this->match_disponibilite_ok = true;
        $this->resetErrorBag();
    }

    public function fermerMatchManuel(): void
    {
        $this->showMatchManuelModal   = false;
        $this->match_id_evenement     = '';
        $this->match_participant1     = '';
        $this->match_participant2     = '';
        $this->match_compatibilite    = null;
        $this->match_disponibilite_ok = true;
        $this->resetErrorBag();
    }

    public function updatedMatchIdEvenement(): void
    {
        $this->match_participant1  = '';
        $this->match_participant2  = '';
        $this->match_compatibilite = null;
    }

    public function updatedMatchParticipant1(): void
    {
        $this->calculerCompatibiliteMatch();
    }

    public function updatedMatchParticipant2(): void
    {
        $this->calculerCompatibiliteMatch();
    }

    /**
     * Calcule la compatibilité (0 à 3) et la disponibilité commune
     * entre les 2 participants sélectionnés pour le match manuel.
     */
    private function calculerCompatibiliteMatch(): void
    {
        $this->match_compatibilite    = null;
        $this->match_disponibilite_ok = true;

        if (!$this->match_participant1 || !$this->match_participant2) return;
        if ($this->match_participant1 == $this->match_participant2) return;

        $p1 = Participant::find($this->match_participant1);
        $p2 = Participant::find($this->match_participant2);
        if (!$p1 || !$p2) return;

        $this->match_compatibilite    = $this->calculerCompatibiliteProfils($p1, $p2);
        $this->match_disponibilite_ok = $this->ontDisponibiliteCommune($p1, $p2);
    }

    /**
     * Score de compatibilité (0 à 3) basé sur secteurs_recherche,
     * zone_geographique et types_partenariat.
     */
    private function calculerCompatibiliteProfils(Participant $a, Participant $b): int
    {
        $points = 0;

        $secteursA = is_array($a->secteurs_recherche)
            ? $a->secteurs_recherche
            : (json_decode($a->secteurs_recherche ?? '[]', true) ?: []);
        $secteursB = is_array($b->secteurs_recherche)
            ? $b->secteurs_recherche
            : (json_decode($b->secteurs_recherche ?? '[]', true) ?: []);

        $matchSecteur = (!empty($secteursA) && $b->secteur_activite && in_array($b->secteur_activite, $secteursA))
            || (!empty($secteursB) && $a->secteur_activite && in_array($a->secteur_activite, $secteursB));

        if ($matchSecteur) {
            $points++;
        } elseif (empty($secteursA) && empty($secteursB)) {
            $points++;
        }

        if ($a->zone_geographique && $b->zone_geographique) {
            if ($a->zone_geographique === $b->zone_geographique) $points++;
        } else {
            $points++;
        }

        $typesA = is_array($a->types_partenariat)
            ? $a->types_partenariat
            : (json_decode($a->types_partenariat ?? '[]', true) ?: []);
        $typesB = is_array($b->types_partenariat)
            ? $b->types_partenariat
            : (json_decode($b->types_partenariat ?? '[]', true) ?: []);

        if (!empty($typesA) && !empty($typesB)) {
            if (count(array_intersect($typesA, $typesB)) > 0) $points++;
        } else {
            $points++;
        }

        return $points;
    }

    private function getDisponibilites(Participant $p): array
    {
        if (!$p->disponibilites) return [];
        $dispo = is_string($p->disponibilites)
            ? json_decode($p->disponibilites, true) ?? []
            : $p->disponibilites;
        return is_array($dispo) ? $dispo : [];
    }

    private function ontDisponibiliteCommune(Participant $a, Participant $b): bool
    {
        $dA = $this->getDisponibilites($a);
        $dB = $this->getDisponibilites($b);
        if (empty($dA) || empty($dB)) return true;
        return count(array_intersect($dA, $dB)) > 0;
    }

    /**
     * Crée un RDV manuel entre 2 participants choisis par le superviseur.
     * Notifie les 2 participants via la table notifications.
     */
    public function creerMatchManuel(): void
    {
        $this->validate([
            'match_id_evenement' => 'required',
            'match_participant1' => 'required',
            'match_participant2' => 'required|different:match_participant1',
        ], [
            'match_id_evenement.required'  => 'Sélectionnez un événement.',
            'match_participant1.required'  => 'Sélectionnez le premier participant.',
            'match_participant2.required'  => 'Sélectionnez le second participant.',
            'match_participant2.different' => 'Choisissez deux participants différents.',
        ]);

        $p1 = Participant::find($this->match_participant1);
        $p2 = Participant::find($this->match_participant2);

        $existeDeja = RendezVous::where(function ($q) {
                $q->where('id_participant1', $this->match_participant1)
                  ->where('id_participant2', $this->match_participant2);
            })
            ->orWhere(function ($q) {
                $q->where('id_participant1', $this->match_participant2)
                  ->where('id_participant2', $this->match_participant1);
            })
            ->where('statut', '!=', 'annule')
            ->exists();

        if ($existeDeja) {
            $this->addError('match_participant2', 'Un rendez-vous existe déjà entre ces deux participants.');
            return;
        }

        RendezVous::create([
            'id_participant1' => $this->match_participant1,
            'id_participant2' => $this->match_participant2,
            'statut'          => 'a_planifier',
        ]);

        Notification::create([
            'id_participant' => $p1->id,
            'contenu'        => "📅 Un rendez-vous a été organisé par la supervision avec " . ($p2->nom ?? '') . ' ' . ($p2->prenom ?? '') . ".",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        Notification::create([
            'id_participant' => $p2->id,
            'contenu'        => "📅 Un rendez-vous a été organisé par la supervision avec " . ($p1->nom ?? '') . ' ' . ($p1->prenom ?? '') . ".",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        session()->flash('success', 'Match manuel créé avec succès ! Les deux participants ont été notifiés.');
        $this->fermerMatchManuel();
    }

    public function render()
    {
        $evenements = Evenement::orderBy('nom')->get();

        $participantsMatchManuel = collect();
        if ($this->match_id_evenement) {
            $participantsMatchManuel = Participant::with('entreprise')
                ->where('id_evenement', $this->match_id_evenement)
                ->where('participation_rdv', true)
                ->orderBy('nom')
                ->get();
        }

        return view('livewire.superviseur.voir-rendez-vous', [
            'rendezVous' => RendezVous::with([
                    'participant1', 'participant1.entreprise',
                    'participant2', 'participant2.entreprise',
                    'stand', 'traducteur'
                ])
                ->when($this->filtre_statut, fn($q) => $q->where('statut', $this->filtre_statut))
                ->when($this->search, fn($q) =>
                    $q->whereHas('participant1', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )->orWhereHas('participant2', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )
                )
                ->latest()->get(),
            'evenements'              => $evenements,
            'participantsMatchManuel' => $participantsMatchManuel,
        ])->layout('layouts.superviseur', ['title' => 'Rendez-vous']);
    }
}