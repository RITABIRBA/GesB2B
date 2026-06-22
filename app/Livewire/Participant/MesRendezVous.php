<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\Participant;
use App\Models\Notification;
use Carbon\Carbon;

class MesRendezVous extends Component
{
    public string $filtre_statut = '';
    public string $alertSuccess  = '';
    public string $alertError    = '';

    /**
     * Le participant signale son absence pour un RDV.
     */
    public function signalerAbsence(int $id): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participant = Participant::findForUser(auth()->user());

        if (!$participant) {
            $this->alertError = 'Participant non trouvé.';
            return;
        }

        $rdv = RendezVous::findOrFail($id);

        if (!in_array($rdv->statut, ['planifie', 'confirme', 'a_planifier'])) {
            $this->alertError = 'Vous ne pouvez pas signaler une absence pour ce rendez-vous.';
            return;
        }

        $autreId = $rdv->id_participant1 == $participant->id
            ? $rdv->id_participant2
            : $rdv->id_participant1;

        $autre = Participant::find($autreId);

        $rdv->update([
            'statut'                => 'annule',
            'absent_participant_id' => $participant->id,
        ]);

        Notification::create([
            'id_participant' => $participant->id,
            'contenu'        => "Votre absence a été enregistrée. Le rendez-vous avec {$autre->nom} {$autre->prenom} a été annulé.",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        if ($autre) {
            Notification::create([
                'id_participant' => $autre->id,
                'contenu'        => "⚠️ Votre rendez-vous avec {$participant->nom} {$participant->prenom} a été annulé (absence signalée). Des participants de remplacement compatibles vous sont proposés.",
                'date_envoie'    => now()->toDateString(),
                'type'           => 'systeme',
            ]);
        }

        $this->alertSuccess = 'Absence signalée. L\'autre participant a été notifié et des remplaçants lui seront proposés.';
    }

    /**
     * ✅ NOUVEAU : Signale l'absence du participant pour TOUTE une journée.
     * Annule tous les RDV de cette date et notifie chaque partenaire concerné.
     */
    public function signalerAbsenceJournee(string $date): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participant = Participant::findForUser(auth()->user());

        if (!$participant) {
            $this->alertError = 'Participant non trouvé.';
            return;
        }

        $rdvsDuJour = RendezVous::where('date', $date)
            ->where(function ($q) use ($participant) {
                $q->where('id_participant1', $participant->id)
                  ->orWhere('id_participant2', $participant->id);
            })
            ->whereIn('statut', ['planifie', 'confirme', 'a_planifier'])
            ->get();

        if ($rdvsDuJour->isEmpty()) {
            $this->alertError = 'Aucun rendez-vous actif trouvé pour cette date.';
            return;
        }

        $nbAnnules = 0;

        foreach ($rdvsDuJour as $rdv) {
            $autreId = $rdv->id_participant1 == $participant->id
                ? $rdv->id_participant2
                : $rdv->id_participant1;

            $autre = Participant::find($autreId);

            $rdv->update([
                'statut'                => 'annule',
                'absent_participant_id' => $participant->id,
            ]);

            if ($autre) {
                Notification::create([
                    'id_participant' => $autre->id,
                    'contenu'        => "⚠️ Votre rendez-vous du " . Carbon::parse($date)->format('d/m/Y')
                        . " avec {$participant->nom} {$participant->prenom} a été annulé (absence signalée pour la journée). "
                        . "Des participants de remplacement compatibles vous sont proposés.",
                    'date_envoie'    => now()->toDateString(),
                    'type'           => 'systeme',
                ]);
            }

            $nbAnnules++;
        }

        Notification::create([
            'id_participant' => $participant->id,
            'contenu'        => "Votre absence pour la journée du " . Carbon::parse($date)->format('d/m/Y')
                . " a été enregistrée. {$nbAnnules} rendez-vous ont été annulés et les partenaires concernés ont été notifiés.",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        $this->alertSuccess = "Absence signalée pour toute la journée du "
            . Carbon::parse($date)->format('d/m/Y')
            . " ({$nbAnnules} rendez-vous annulés). Les partenaires concernés ont été notifiés.";
    }

    /**
     * Le participant annule son signalement d'absence.
     */
    public function annulerAbsence(int $id): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $rdv = RendezVous::findOrFail($id);

        $rdv->update([
            'statut'                => 'planifie',
            'absent_participant_id' => null,
        ]);

        $this->alertSuccess = 'Présence rétablie. Le rendez-vous est de nouveau actif.';
    }

    /**
     * B choisit un remplaçant proposé pour le participant absent A.
     */
    public function choisirRemplacant(int $rdvAnnuleId, int $idRemplacant): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $moi = Participant::findForUser(auth()->user());
        $rdv = RendezVous::findOrFail($rdvAnnuleId);

        $autreId = $rdv->id_participant1 == $moi->id
            ? $rdv->id_participant2
            : $rdv->id_participant1;

        if ($rdv->absent_participant_id != $autreId) {
            $this->alertError = 'Action non autorisée.';
            return;
        }

        $remplacant = Participant::find($idRemplacant);
        if (!$remplacant) {
            $this->alertError = 'Participant introuvable.';
            return;
        }

        $existeDeja = RendezVous::where(function ($q) use ($moi, $remplacant) {
                $q->where('id_participant1', $moi->id)->where('id_participant2', $remplacant->id);
            })
            ->orWhere(function ($q) use ($moi, $remplacant) {
                $q->where('id_participant1', $remplacant->id)->where('id_participant2', $moi->id);
            })
            ->where('statut', '!=', 'annule')
            ->exists();

        if ($existeDeja) {
            $this->alertError = 'Un rendez-vous existe déjà avec ce participant.';
            return;
        }

        RendezVous::create([
            'id_participant1' => $moi->id,
            'id_participant2' => $remplacant->id,
            'statut'          => 'a_planifier',
        ]);

        Notification::create([
            'id_participant' => $moi->id,
            'contenu'        => "✅ Nouveau rendez-vous créé avec {$remplacant->nom} {$remplacant->prenom} (remplacement).",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        Notification::create([
            'id_participant' => $remplacant->id,
            'contenu'        => "🎉 {$moi->nom} {$moi->prenom} vous propose un rendez-vous (remplacement). Consultez vos rendez-vous.",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        $this->alertSuccess = "Nouveau rendez-vous créé avec {$remplacant->nom} {$remplacant->prenom} !";
    }

    // ─── Helpers compatibilité / disponibilité ─────────────────

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

    private function calculerCompatibilite(Participant $moi, Participant $cible): int
    {
        $points = 0;

        $secteursRecherche = is_array($moi->secteurs_recherche)
            ? $moi->secteurs_recherche
            : (json_decode($moi->secteurs_recherche ?? '[]', true) ?: []);

        if (!empty($secteursRecherche) && $cible->secteur_activite) {
            if (in_array($cible->secteur_activite, $secteursRecherche)) $points++;
        } elseif (empty($secteursRecherche)) {
            $points++;
        }

        if ($moi->zone_geographique && $cible->zone_geographique) {
            if ($moi->zone_geographique === $cible->zone_geographique) $points++;
        } else {
            $points++;
        }

        $typesMoi = is_array($moi->types_partenariat)
            ? $moi->types_partenariat
            : (json_decode($moi->types_partenariat ?? '[]', true) ?: []);
        $typesCible = is_array($cible->types_partenariat)
            ? $cible->types_partenariat
            : (json_decode($cible->types_partenariat ?? '[]', true) ?: []);

        if (!empty($typesMoi) && !empty($typesCible)) {
            if (count(array_intersect($typesMoi, $typesCible)) > 0) $points++;
        } else {
            $points++;
        }

        return $points;
    }

    private function profilSimilaire(Participant $a, Participant $candidat): bool
    {
        if ($a->secteur_activite && $candidat->secteur_activite
            && $a->secteur_activite === $candidat->secteur_activite) {
            return true;
        }

        if ($a->zone_geographique && $candidat->zone_geographique
            && $a->zone_geographique === $candidat->zone_geographique) {
            return true;
        }

        $typesA = is_array($a->types_partenariat)
            ? $a->types_partenariat
            : (json_decode($a->types_partenariat ?? '[]', true) ?: []);
        $typesC = is_array($candidat->types_partenariat)
            ? $candidat->types_partenariat
            : (json_decode($candidat->types_partenariat ?? '[]', true) ?: []);

        if (!empty($typesA) && !empty($typesC)
            && count(array_intersect($typesA, $typesC)) > 0) {
            return true;
        }

        return false;
    }

    private function getCandidatsRemplacement(RendezVous $rdv, Participant $moi): \Illuminate\Support\Collection
    {
        $absentId = $rdv->absent_participant_id;
        $absent   = Participant::find($absentId);

        if (!$absent || !$moi->id_evenement) return collect();

        $idsDejaMatches = RendezVous::where(function ($q) use ($moi) {
                $q->where('id_participant1', $moi->id)
                  ->orWhere('id_participant2', $moi->id);
            })
            ->where('statut', '!=', 'annule')
            ->get()
            ->map(fn($r) => $r->id_participant1 == $moi->id ? $r->id_participant2 : $r->id_participant1)
            ->toArray();

        return Participant::where('id_evenement', $moi->id_evenement)
            ->where('id', '!=', $moi->id)
            ->where('id', '!=', $absentId)
            ->where('participation_rdv', true)
            ->whereNotIn('id', $idsDejaMatches)
            ->get()
            ->filter(fn($c) => $this->profilSimilaire($absent, $c))
            ->filter(fn($c) => $this->ontDisponibiliteCommune($moi, $c))
            ->map(function ($c) use ($moi) {
                $c->score_compatibilite = $this->calculerCompatibilite($moi, $c);
                return $c;
            })
            ->filter(fn($c) => $c->score_compatibilite > 0)
            ->sortByDesc('score_compatibilite')
            ->values();
    }

    /**
     * ✅ NOUVEAU : Retourne les dates distinctes ayant au moins
     * un RDV actif pour ce participant.
     */
    private function getDatesAvecRdvActifs(Participant $participant): \Illuminate\Support\Collection
    {
        return RendezVous::where('date', '!=', null)
            ->where(function ($q) use ($participant) {
                $q->where('id_participant1', $participant->id)
                  ->orWhere('id_participant2', $participant->id);
            })
            ->whereIn('statut', ['planifie', 'confirme', 'a_planifier'])
            ->distinct()
            ->orderBy('date')
            ->pluck('date');
    }

    public function render()
    {
        $participant = Participant::findForUser(auth()->user());

        $rendezVous = $participant
            ? RendezVous::with([
                    'participant1', 'participant1.entreprise',
                    'participant2', 'participant2.entreprise',
                    'traducteur',
                ])
                ->where(function ($q) use ($participant) {
                    $q->where('id_participant1', $participant->id)
                      ->orWhere('id_participant2', $participant->id);
                })
                ->when($this->filtre_statut, fn($q) =>
                    $q->where('statut', $this->filtre_statut)
                )
                ->orderBy('date')
                ->orderBy('heure_debut')
                ->get()
            : collect();

        $remplacants = [];
        if ($participant) {
            foreach ($rendezVous as $rdv) {
                if ($rdv->statut === 'annule'
                    && $rdv->absent_participant_id
                    && $rdv->absent_participant_id != $participant->id) {
                    $remplacants[$rdv->id] = $this->getCandidatsRemplacement($rdv, $participant);
                }
            }
        }

        $datesAvecRdv = $participant
            ? $this->getDatesAvecRdvActifs($participant)
            : collect();

        return view('livewire.participant.mes-rendez-vous', [
            'rendezVous'   => $rendezVous,
            'participant'  => $participant,
            'remplacants'  => $remplacants,
            'datesAvecRdv' => $datesAvecRdv,
        ])->layout('layouts.participant', ['title' => 'Rendez-vous']);
    }
}