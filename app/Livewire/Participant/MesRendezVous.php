<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\Participant;
use App\Models\Notification;
use App\Mail\AbsenceSignalee;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MesRendezVous extends Component
{
    public string $filtre_statut = '';
    public string $alertSuccess  = '';
    public string $alertError    = '';

    public function signalerAbsence(int $id): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participant = Participant::findForUser(auth()->user());
        if (!$participant) { $this->alertError = 'Participant non trouvé.'; return; }

        $rdv = RendezVous::with(['participant1.entreprise', 'participant2.entreprise'])->findOrFail($id);

        if (!in_array($rdv->statut, ['planifie', 'confirme', 'a_planifier'])) {
            $this->alertError = 'Vous ne pouvez pas signaler une absence pour ce rendez-vous.';
            return;
        }

        $autreId = $rdv->id_participant1 == $participant->id ? $rdv->id_participant2 : $rdv->id_participant1;
        $autre   = Participant::with('entreprise')->find($autreId);

        $rdv->update(['statut' => 'annule', 'absent_participant_id' => $participant->id]);

        // Notifications en base
        Notification::create([
            'id_participant' => $participant->id,
            'contenu'        => "Votre absence a été enregistrée. Le rendez-vous avec {$autre->nom} {$autre->prenom} a été annulé.",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        if ($autre) {
            Notification::create([
                'id_participant' => $autre->id,
                'contenu'        => "⚠️ Votre rendez-vous avec {$participant->nom} {$participant->prenom} a été annulé (absence signalée). Des remplaçants vous sont proposés.",
                'date_envoie'    => now()->toDateString(),
                'type'           => 'systeme',
            ]);

            // ✅ EMAIL au partenaire avec remplaçants
            if ($autre->email) {
                try {
                    $remplacants = $this->getCandidatsRemplacement($rdv, $autre);
                    $nomEvenement = $this->getNomEvenement($participant);

                    Mail::to($autre->email)->send(new AbsenceSignalee(
                        destinataire: $autre,
                        absent:       $participant,
                        dateRdv:      $rdv->date ?? now()->toDateString(),
                        heureDebut:   $rdv->heure_debut,
                        heureFin:     $rdv->heure_fin,
                        salle:        $rdv->salle,
                        table:        $rdv->numero_table,
                        nomEvenement: $nomEvenement,
                        remplacants:  $remplacants,
                    ));
                } catch (\Exception $e) {
                    Log::error('Email absence échoué', ['autre_id' => $autre->id, 'err' => $e->getMessage()]);
                }
            }
        }

        $this->alertSuccess = 'Absence signalée. Votre partenaire a été notifié par email et des remplaçants lui ont été proposés.';
    }

    public function signalerAbsenceJournee(string $date): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participant = Participant::findForUser(auth()->user());
        if (!$participant) { $this->alertError = 'Participant non trouvé.'; return; }

        $rdvsDuJour = RendezVous::where('date', $date)
            ->where(function ($q) use ($participant) {
                $q->where('id_participant1', $participant->id)->orWhere('id_participant2', $participant->id);
            })
            ->whereIn('statut', ['planifie', 'confirme', 'a_planifier'])
            ->with(['participant1.entreprise', 'participant2.entreprise'])
            ->get();

        if ($rdvsDuJour->isEmpty()) {
            $this->alertError = 'Aucun rendez-vous actif trouvé pour cette date.';
            return;
        }

        $nbAnnules    = 0;
        $nomEvenement = $this->getNomEvenement($participant);
        $dateFormatee = Carbon::parse($date)->format('d/m/Y');

        foreach ($rdvsDuJour as $rdv) {
            $autreId = $rdv->id_participant1 == $participant->id ? $rdv->id_participant2 : $rdv->id_participant1;
            $autre   = Participant::with('entreprise')->find($autreId);

            $rdv->update(['statut' => 'annule', 'absent_participant_id' => $participant->id]);

            if ($autre) {
                Notification::create([
                    'id_participant' => $autre->id,
                    'contenu'        => "⚠️ Votre rendez-vous du {$dateFormatee} avec {$participant->nom} {$participant->prenom} a été annulé (absence journée). Des remplaçants vous sont proposés.",
                    'date_envoie'    => now()->toDateString(),
                    'type'           => 'systeme',
                ]);

                // ✅ EMAIL au partenaire
                if ($autre->email) {
                    try {
                        $remplacants = $this->getCandidatsRemplacement($rdv, $autre);

                        Mail::to($autre->email)->send(new AbsenceSignalee(
                            destinataire: $autre,
                            absent:       $participant,
                            dateRdv:      $date,
                            heureDebut:   null, // absence journée entière
                            heureFin:     null,
                            salle:        null,
                            table:        null,
                            nomEvenement: $nomEvenement,
                            remplacants:  $remplacants,
                        ));
                    } catch (\Exception $e) {
                        Log::error('Email absence journée échoué', ['autre_id' => $autre->id, 'err' => $e->getMessage()]);
                    }
                }
            }

            $nbAnnules++;
        }

        Notification::create([
            'id_participant' => $participant->id,
            'contenu'        => "Votre absence pour la journée du {$dateFormatee} a été enregistrée. {$nbAnnules} rendez-vous annulés.",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        $this->alertSuccess = "Absence signalée pour toute la journée du {$dateFormatee} ({$nbAnnules} rendez-vous annulés). Les partenaires ont été notifiés par email.";
    }

    public function annulerAbsence(int $id): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $rdv = RendezVous::findOrFail($id);
        $rdv->update(['statut' => 'planifie', 'absent_participant_id' => null]);
        $this->alertSuccess = 'Présence rétablie. Le rendez-vous est de nouveau actif.';
    }

    public function choisirRemplacant(int $rdvAnnuleId, int $idRemplacant): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $moi = Participant::findForUser(auth()->user());
        $rdv = RendezVous::findOrFail($rdvAnnuleId);

        $autreId = $rdv->id_participant1 == $moi->id ? $rdv->id_participant2 : $rdv->id_participant1;

        if ($rdv->absent_participant_id != $autreId) {
            $this->alertError = 'Action non autorisée.'; return;
        }

        $remplacant = Participant::find($idRemplacant);
        if (!$remplacant) { $this->alertError = 'Participant introuvable.'; return; }

        $existeDeja = RendezVous::where(function ($q) use ($moi, $remplacant) {
            $q->where('id_participant1', $moi->id)->where('id_participant2', $remplacant->id);
        })->orWhere(function ($q) use ($moi, $remplacant) {
            $q->where('id_participant1', $remplacant->id)->where('id_participant2', $moi->id);
        })->where('statut', '!=', 'annule')->exists();

        if ($existeDeja) { $this->alertError = 'Un rendez-vous existe déjà avec ce participant.'; return; }

        RendezVous::create([
            'id_participant1' => $moi->id,
            'id_participant2' => $remplacant->id,
            'statut'          => 'a_planifier',
        ]);

        Notification::create(['id_participant' => $moi->id, 'contenu' => "✅ Nouveau rendez-vous créé avec {$remplacant->nom} {$remplacant->prenom} (remplacement).", 'date_envoie' => now()->toDateString(), 'type' => 'systeme']);
        Notification::create(['id_participant' => $remplacant->id, 'contenu' => "🎉 {$moi->nom} {$moi->prenom} vous propose un rendez-vous (remplacement).", 'date_envoie' => now()->toDateString(), 'type' => 'systeme']);

        $this->alertSuccess = "Nouveau rendez-vous créé avec {$remplacant->nom} {$remplacant->prenom} !";
    }

    // ─── Helpers ────────────────────────────────────────────────

    private function getNomEvenement(Participant $participant): string
    {
        if (!$participant->id_evenement) return 'Business Forum';
        $ev = \App\Models\Evenement::find($participant->id_evenement);
        return $ev->nom ?? 'Business Forum';
    }

    private function getDisponibilites(Participant $p): array
    {
        if (!$p->disponibilites) return [];
        $dispo = is_string($p->disponibilites) ? json_decode($p->disponibilites, true) ?? [] : $p->disponibilites;
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

        $secteursRecherche = is_array($moi->secteurs_recherche) ? $moi->secteurs_recherche : (json_decode($moi->secteurs_recherche ?? '[]', true) ?: []);
        if (!empty($secteursRecherche) && $cible->secteur_activite && in_array($cible->secteur_activite, $secteursRecherche)) $points++;
        elseif (empty($secteursRecherche)) $points++;

        if ($moi->zone_geographique && $cible->zone_geographique && $moi->zone_geographique === $cible->zone_geographique) $points++;
        else $points++;

        $typesMoi   = is_array($moi->types_partenariat)   ? $moi->types_partenariat   : (json_decode($moi->types_partenariat   ?? '[]', true) ?: []);
        $typesCible = is_array($cible->types_partenariat) ? $cible->types_partenariat : (json_decode($cible->types_partenariat ?? '[]', true) ?: []);
        if (!empty($typesMoi) && !empty($typesCible) && count(array_intersect($typesMoi, $typesCible)) > 0) $points++;
        else $points++;

        return $points;
    }

    private function profilSimilaire(Participant $a, Participant $candidat): bool
    {
        if ($a->secteur_activite && $candidat->secteur_activite && $a->secteur_activite === $candidat->secteur_activite) return true;
        if ($a->zone_geographique && $candidat->zone_geographique && $a->zone_geographique === $candidat->zone_geographique) return true;

        $typesA = is_array($a->types_partenariat)        ? $a->types_partenariat        : (json_decode($a->types_partenariat        ?? '[]', true) ?: []);
        $typesC = is_array($candidat->types_partenariat) ? $candidat->types_partenariat : (json_decode($candidat->types_partenariat ?? '[]', true) ?: []);
        if (!empty($typesA) && !empty($typesC) && count(array_intersect($typesA, $typesC)) > 0) return true;

        return false;
    }

    private function getCandidatsRemplacement(RendezVous $rdv, Participant $moi): \Illuminate\Support\Collection
    {
        $absentId = $rdv->absent_participant_id;
        $absent   = Participant::find($absentId);
        if (!$absent || !$moi->id_evenement) return collect();

        $idsDejaMatches = RendezVous::where(function ($q) use ($moi) {
            $q->where('id_participant1', $moi->id)->orWhere('id_participant2', $moi->id);
        })->where('statut', '!=', 'annule')
          ->get()
          ->map(fn($r) => $r->id_participant1 == $moi->id ? $r->id_participant2 : $r->id_participant1)
          ->toArray();

        return Participant::with('entreprise')
            ->where('id_evenement', $moi->id_evenement)
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

    private function getDatesAvecRdvActifs(Participant $participant): \Illuminate\Support\Collection
    {
        return RendezVous::whereNotNull('date')
            ->where(function ($q) use ($participant) {
                $q->where('id_participant1', $participant->id)->orWhere('id_participant2', $participant->id);
            })
            ->whereIn('statut', ['planifie', 'confirme', 'a_planifier'])
            ->distinct()->orderBy('date')->pluck('date');
    }

    public function render()
    {
        $participant = Participant::findForUser(auth()->user());

        $rendezVous = $participant
            ? RendezVous::with(['participant1', 'participant1.entreprise', 'participant2', 'participant2.entreprise', 'traducteur'])
                ->where(function ($q) use ($participant) {
                    $q->where('id_participant1', $participant->id)->orWhere('id_participant2', $participant->id);
                })
                ->when($this->filtre_statut, fn($q) => $q->where('statut', $this->filtre_statut))
                ->orderBy('date')->orderBy('heure_debut')
                ->get()
            : collect();

        $remplacants = [];
        if ($participant) {
            foreach ($rendezVous as $rdv) {
                if ($rdv->statut === 'annule' && $rdv->absent_participant_id && $rdv->absent_participant_id != $participant->id) {
                    $remplacants[$rdv->id] = $this->getCandidatsRemplacement($rdv, $participant);
                }
            }
        }

        $datesAvecRdv = $participant ? $this->getDatesAvecRdvActifs($participant) : collect();

        return view('livewire.participant.mes-rendez-vous', [
            'rendezVous'   => $rendezVous,
            'participant'  => $participant,
            'remplacants'  => $remplacants,
            'datesAvecRdv' => $datesAvecRdv,
        ])->layout('layouts.participant', ['title' => 'Rendez-vous']);
    }
}