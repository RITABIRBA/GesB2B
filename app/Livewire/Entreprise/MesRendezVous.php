<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\Notification;

class MesRendezVous extends Component
{
    public $search = '';
    public $filtre_statut = '';

    public string $alertSuccess = '';
    public string $alertError   = '';

    // ← Liaison par email au lieu du nom
    private function getEntreprise()
    {
        return Entreprise::where('email_responsable', auth()->user()->email)->first()
            ?? Entreprise::where('nom', auth()->user()->name)->first();
    }

    /**
     * Retourne les IDs des participants de mon entreprise.
     */
    private function getParticipantIds(): array
    {
        $entreprise = $this->getEntreprise();
        if (!$entreprise) return [];

        return Participant::where('id_entreprise', $entreprise->id)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Pour un RDV donné, retourne le participant de MON entreprise impliqué
     * (id_participant1 ou id_participant2, selon lequel m'appartient).
     */
    private function getMonParticipantDansRdv(RendezVous $rdv, array $participantIds): ?Participant
    {
        if (in_array($rdv->id_participant1, $participantIds)) {
            return $rdv->participant1;
        }
        if (in_array($rdv->id_participant2, $participantIds)) {
            return $rdv->participant2;
        }
        return null;
    }

    /**
     * Signale l'absence du participant de mon entreprise pour ce RDV.
     */
    public function signalerAbsence(int $id): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participantIds = $this->getParticipantIds();
        $rdv = RendezVous::findOrFail($id);

        $moi = $this->getMonParticipantDansRdv($rdv, $participantIds);

        if (!$moi) {
            $this->alertError = 'Action non autorisée.';
            return;
        }

        if (!in_array($rdv->statut, ['planifie', 'confirme', 'a_planifier'])) {
            $this->alertError = 'Vous ne pouvez pas signaler une absence pour ce rendez-vous.';
            return;
        }

        $autreId = $rdv->id_participant1 == $moi->id
            ? $rdv->id_participant2
            : $rdv->id_participant1;

        $autre = Participant::find($autreId);

        $rdv->update([
            'statut'                => 'annule',
            'absent_participant_id' => $moi->id,
        ]);

        Notification::create([
            'id_participant' => $moi->id,
            'contenu'        => "Votre absence a été enregistrée. Le rendez-vous avec " . ($autre->nom ?? '') . ' ' . ($autre->prenom ?? '') . " a été annulé.",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        if ($autre) {
            Notification::create([
                'id_participant' => $autre->id,
                'contenu'        => "⚠️ Votre rendez-vous avec " . ($moi->nom ?? '') . ' ' . ($moi->prenom ?? '') . " a été annulé (absence signalée). Des participants de remplacement compatibles vous sont proposés.",
                'date_envoie'    => now()->toDateString(),
                'type'           => 'systeme',
            ]);
        }

        $this->alertSuccess = 'Absence signalée. L\'autre participant a été notifié et des remplaçants lui seront proposés.';
    }

    /**
     * Annule le signalement d'absence pour ce RDV.
     */
    public function annulerAbsence(int $id): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participantIds = $this->getParticipantIds();
        $rdv = RendezVous::findOrFail($id);

        $moi = $this->getMonParticipantDansRdv($rdv, $participantIds);

        if (!$moi || $rdv->absent_participant_id != $moi->id) {
            $this->alertError = 'Action non autorisée.';
            return;
        }

        $rdv->update([
            'statut'                => 'planifie',
            'absent_participant_id' => null,
        ]);

        $this->alertSuccess = 'Présence rétablie. Le rendez-vous est de nouveau actif.';
    }

    /**
     * Choisit un remplaçant proposé pour un RDV annulé où l'autre
     * participant a signalé son absence.
     */
    public function choisirRemplacant(int $rdvAnnuleId, int $idRemplacant): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participantIds = $this->getParticipantIds();
        $rdv = RendezVous::findOrFail($rdvAnnuleId);

        $moi = $this->getMonParticipantDansRdv($rdv, $participantIds);

        if (!$moi) {
            $this->alertError = 'Action non autorisée.';
            return;
        }

        // Vérifie que c'est bien l'autre participant qui est absent
        if ($rdv->absent_participant_id == $moi->id || !$rdv->absent_participant_id) {
            $this->alertError = 'Action non autorisée.';
            return;
        }

        $remplacant = Participant::find($idRemplacant);
        if (!$remplacant) {
            $this->alertError = 'Participant introuvable.';
            return;
        }

        // Vérifie qu'un RDV n'existe pas déjà entre les deux
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
            'id_stand'        => $rdv->id_stand,
            'salle'           => $rdv->salle,
            'numero_table'    => $rdv->numero_table,
            'date'            => $rdv->date,
            'heure_debut'     => $rdv->heure_debut,
            'heure_fin'       => $rdv->heure_fin,
            'statut'          => 'planifie',
        ]);

        Notification::create([
            'id_participant' => $moi->id,
            'contenu'        => "Nouveau rendez-vous créé avec {$remplacant->nom} {$remplacant->prenom} (remplacement).",
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

    /**
     * Score de compatibilité entre 2 participants (0 à 3).
     */
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

    /**
     * Profil similaire entre le participant absent A et un candidat :
     * même secteur OU même zone OU intersection types_partenariat.
     */
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

    /**
     * Pour un RDV annulé (absence de l'autre participant), retourne les
     * candidats de remplacement pour moi : présents, disponibles, profil
     * similaire à l'absent, compatibles avec moi.
     */
    private function getCandidatsRemplacement(RendezVous $rdv, Participant $moi): \Illuminate\Support\Collection
    {
        $absentId = $rdv->absent_participant_id;
        $absent   = Participant::find($absentId);

        if (!$absent || !$moi->id_evenement) return collect();

        // IDs déjà en RDV (non annulé) avec moi
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

    public function render()
    {
        $entreprise = $this->getEntreprise();

        if (!$entreprise) {
            return view('livewire.entreprise.mes-rendez-vous', [
                'rendezVous'  => collect(),
                'remplacants' => [],
                'participantIds' => [],
            ])->layout('layouts.entreprise', ['title' => 'Mes Rendez-vous']);
        }

        $participantIds = Participant::where('id_entreprise', $entreprise->id)
            ->pluck('id')
            ->toArray();

        $rendezVous = RendezVous::with([
                'participant1',
                'participant1.entreprise',
                'participant2',
                'participant2.entreprise',
                'stand',
                'traducteur',
            ])
            ->where(fn($q) =>
                $q->whereIn('id_participant1', $participantIds)
                  ->orWhereIn('id_participant2', $participantIds)
            )
            ->when($this->filtre_statut, fn($q) =>
                $q->where('statut', $this->filtre_statut)
            )
            ->when($this->search, fn($q) =>
                $q->whereHas('participant1', fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('prenom', 'like', '%'.$this->search.'%')
                )->orWhereHas('participant2', fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('prenom', 'like', '%'.$this->search.'%')
                )
            )
            ->latest()
            ->get();

        // Calcule les remplaçants proposés pour chaque RDV annulé
        // où l'AUTRE participant (pas le mien) est absent
        $remplacants = [];
        foreach ($rendezVous as $rdv) {
            if ($rdv->statut === 'annule' && $rdv->absent_participant_id) {
                $moi = $this->getMonParticipantDansRdv($rdv, $participantIds);
                if ($moi && $rdv->absent_participant_id != $moi->id) {
                    $remplacants[$rdv->id] = $this->getCandidatsRemplacement($rdv, $moi);
                }
            }
        }

        return view('livewire.entreprise.mes-rendez-vous', [
            'rendezVous'     => $rendezVous,
            'remplacants'    => $remplacants,
            'participantIds' => $participantIds,
        ])->layout('layouts.entreprise', ['title' => 'Mes Rendez-vous']);
    }
}