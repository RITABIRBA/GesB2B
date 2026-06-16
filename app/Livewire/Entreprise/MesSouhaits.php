<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\Souhait;
use App\Models\Participant;
use App\Models\Inscription;
use App\Models\Evenement;
use App\Models\Entreprise;
use App\Models\Notification;
use Carbon\Carbon;

class MesSouhaits extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $alertSuccess = '';
    public string $alertError   = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    private function getRepresentant(): ?Participant
    {
        $entreprise = Entreprise::where('email_responsable', auth()->user()->email)->first();
        if (!$entreprise) return null;

        return Participant::where('id_entreprise', $entreprise->id)
            ->where('role', 'representant')
            ->first();
    }

    public function emettresouhait(int $id_cible): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participant = $this->getRepresentant();

        if (!$participant) {
            $this->alertError = 'Représentant non trouvé.';
            return;
        }

        if (!$this->inscriptionEstValide($participant)) {
            $this->alertError = 'Vous devez avoir une inscription validée pour émettre des souhaits.';
            return;
        }

        $evenement = Evenement::find($participant->id_evenement);
        if ($evenement && $this->souhaitsfermes($evenement)) {
            $this->alertError = 'Les souhaits sont clôturés 3 jours avant l\'événement.';
            return;
        }

        $maxSouhaits = $evenement->max_souhaits ?? 20;
        $nbSouhaits  = Souhait::where('id_participant', $participant->id)->count();

        if ($nbSouhaits >= $maxSouhaits) {
            $this->alertError = "Vous avez atteint le maximum de {$maxSouhaits} souhaits.";
            return;
        }

        $dejaEmis = Souhait::where('id_participant', $participant->id)
            ->where('id_participant_cible', $id_cible)
            ->exists();

        if ($dejaEmis) {
            $this->alertError = 'Vous avez déjà émis un souhait vers ce participant.';
            return;
        }

        $cible = Participant::find($id_cible);
        $scoreCompatibilite = $this->calculerCompatibilite($participant, $cible);

        if ($scoreCompatibilite === 0) {
            $this->alertError = 'Ce participant n\'est pas compatible avec votre profil.';
            return;
        }

        if (!$this->ontDisponibiliteCommune($participant, $cible)) {
            $this->alertError = 'Vous n\'avez aucune disponibilité commune avec ce participant.';
            return;
        }

        $dernierePriorite = Souhait::where('id_participant', $participant->id)
            ->max('priorite') ?? 0;

        $souhaitRetour = Souhait::where('id_participant', $id_cible)
            ->where('id_participant_cible', $participant->id)
            ->first();

        $estMutuel = (bool) $souhaitRetour;
        $statut    = $scoreCompatibilite >= 2 ? 'compatible' : 'en_attente';

        Souhait::create([
            'id_participant'       => $participant->id,
            'id_participant_cible' => $id_cible,
            'id_evenement'         => $participant->id_evenement,
            'priorite'             => $dernierePriorite + 1,
            'type'                 => $estMutuel ? 'mutuel' : 'envoye',
            'statut'               => $estMutuel ? 'accepte' : $statut,
        ]);

        if ($estMutuel) {
            $souhaitRetour->update([
                'type'   => 'mutuel',
                'statut' => 'accepte',
            ]);

            $rdvExiste = \App\Models\RendezVous::where(function ($q) use ($participant, $id_cible) {
                    $q->where('id_participant1', $participant->id)->where('id_participant2', $id_cible);
                })
                ->orWhere(function ($q) use ($participant, $id_cible) {
                    $q->where('id_participant1', $id_cible)->where('id_participant2', $participant->id);
                })
                ->exists();

            if (!$rdvExiste) {
                \App\Models\RendezVous::create([
                    'id_participant1' => $participant->id,
                    'id_participant2' => $id_cible,
                    'statut'          => 'planifie',
                ]);

                Notification::create([
                    'id_participant' => $participant->id,
                    'contenu'        => "🎉 Souhait mutuel avec {$cible->nom} {$cible->prenom} ! Un rendez-vous va être planifié.",
                    'date_envoie'    => now()->toDateString(),
                    'type'           => 'systeme',
                ]);

                Notification::create([
                    'id_participant' => $id_cible,
                    'contenu'        => "🎉 Souhait mutuel avec {$participant->nom} {$participant->prenom} ! Un rendez-vous va être planifié.",
                    'date_envoie'    => now()->toDateString(),
                    'type'           => 'systeme',
                ]);
            }
        }

        $this->alertSuccess = $estMutuel
            ? '🎉 Souhait mutuel ! Ce participant vous cherche aussi. Un RDV a été créé.'
            : ($scoreCompatibilite >= 2
                ? '✅ Souhait émis ! Profils compatibles.'
                : '⚠️ Souhait émis avec compatibilité partielle.');
    }

    public function supprimer(int $id): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participant = $this->getRepresentant();
        $souhait     = Souhait::findOrFail($id);

        if ($souhait->id_participant !== $participant->id) {
            $this->alertError = 'Action non autorisée.';
            return;
        }

        $evenement = Evenement::find($participant->id_evenement);
        if ($evenement && $this->souhaitsfermes($evenement)) {
            $this->alertError = 'Les souhaits sont clôturés, vous ne pouvez plus les modifier.';
            return;
        }

        Souhait::where('id_participant', $souhait->id_participant_cible)
            ->where('id_participant_cible', $participant->id)
            ->where('type', 'mutuel')
            ->update(['type' => 'envoye']);

        $prioriteSupprimee = $souhait->priorite;
        $souhait->delete();

        Souhait::where('id_participant', $participant->id)
            ->where('priorite', '>', $prioriteSupprimee)
            ->orderBy('priorite')
            ->each(fn($s) => $s->update(['priorite' => $s->priorite - 1]));

        $this->alertSuccess = 'Souhait supprimé.';
    }

    public function monterPriorite(int $id): void
    {
        $participant = $this->getRepresentant();
        $souhait     = Souhait::findOrFail($id);

        if ($souhait->priorite <= 1) return;

        $voisin = Souhait::where('id_participant', $participant->id)
            ->where('priorite', $souhait->priorite - 1)
            ->first();

        if ($voisin) $voisin->update(['priorite' => $souhait->priorite]);
        $souhait->update(['priorite' => $souhait->priorite - 1]);
    }

    public function descendrePriorite(int $id): void
    {
        $participant = $this->getRepresentant();
        $souhait     = Souhait::findOrFail($id);
        $max         = Souhait::where('id_participant', $participant->id)->max('priorite');

        if ($souhait->priorite >= $max) return;

        $voisin = Souhait::where('id_participant', $participant->id)
            ->where('priorite', $souhait->priorite + 1)
            ->first();

        if ($voisin) $voisin->update(['priorite' => $souhait->priorite]);
        $souhait->update(['priorite' => $souhait->priorite + 1]);
    }

    // ─── Helpers ────────────────────────────────────────────────

    private function souhaitsfermes(Evenement $evenement): bool
    {
        if (!$evenement->date_debut) return false;
        return Carbon::now()->diffInDays(Carbon::parse($evenement->date_debut), false) <= 3;
    }

    private function inscriptionEstValide(Participant $participant): bool
    {
        if (!$participant->id_evenement) return false;

        return Inscription::where('id_participant', $participant->id)
            ->where('id_evenement', $participant->id_evenement)
            ->where(function ($q) {
                $q->where('statut_paiement', 'paye')
                  ->orWhereHas('evenement', fn($q) =>
                      $q->whereIn('type_paiement', ['gratuit', 'par_entreprise'])
                  );
            })
            ->exists();
    }

    private function getDisponibilites(Participant $p): array
    {
        if (!$p->disponibilites) return [];
        $dispo = is_string($p->disponibilites)
            ? json_decode($p->disponibilites, true) ?? []
            : $p->disponibilites;
        return is_array($dispo) ? $dispo : [];
    }

    private function ontDisponibiliteCommune(Participant $moi, Participant $cible): bool
    {
        $dispoMoi   = $this->getDisponibilites($moi);
        $dispoCible = $this->getDisponibilites($cible);
        if (empty($dispoMoi) || empty($dispoCible)) return true;
        return count(array_intersect($dispoMoi, $dispoCible)) > 0;
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

        $typesPartenariatMoi = is_array($moi->types_partenariat)
            ? $moi->types_partenariat
            : (json_decode($moi->types_partenariat ?? '[]', true) ?: []);

        $typesPartenariatCible = is_array($cible->types_partenariat)
            ? $cible->types_partenariat
            : (json_decode($cible->types_partenariat ?? '[]', true) ?: []);

        if (!empty($typesPartenariatMoi) && !empty($typesPartenariatCible)) {
            if (count(array_intersect($typesPartenariatMoi, $typesPartenariatCible)) > 0) $points++;
        } else {
            $points++;
        }

        return $points;
    }

    public function render()
    {
        $participant = $this->getRepresentant();

        $inscriptionValide = $participant
            ? $this->inscriptionEstValide($participant)
            : false;

        $evenement = $participant && $participant->id_evenement
            ? Evenement::find($participant->id_evenement)
            : null;

        $souhaitsfermes = $evenement ? $this->souhaitsfermes($evenement) : false;
        $minSouhaits    = $evenement->min_souhaits ?? 5;
        $maxSouhaits    = $evenement->max_souhaits ?? 20;

        $joursRestants = null;
        if ($evenement && $evenement->date_debut) {
            $joursRestants = (int) Carbon::now()->diffInDays(
                Carbon::parse($evenement->date_debut), false
            );
        }

        $souhaits = $participant
            ? Souhait::with('participantCible.entreprise')
                ->where('id_participant', $participant->id)
                ->orderBy('priorite')
                ->get()
            : collect();

        $nbSouhaits = $souhaits->count();
        $idsCibles  = $souhaits->pluck('id_participant_cible')->toArray();

        $candidats = collect();

        if ($participant && $inscriptionValide && !$souhaitsfermes) {
            $candidatsTous = Participant::with('entreprise')
                ->where('id_evenement', $participant->id_evenement)
                ->where('id', '!=', $participant->id)
                ->where('participation_rdv', true)
                ->where(function ($q) use ($participant) {
                    $q->whereNull('id_entreprise')
                      ->orWhere('id_entreprise', '!=', $participant->id_entreprise);
                })
                ->when($this->search, fn($q) =>
                    $q->where(function ($q) {
                        $q->where('nom', 'like', '%' . $this->search . '%')
                          ->orWhere('prenom', 'like', '%' . $this->search . '%')
                          ->orWhereHas('entreprise', fn($q) =>
                              $q->where('nom', 'like', '%' . $this->search . '%')
                          );
                    })
                )
                ->get()
                ->filter(fn($p) => $this->ontDisponibiliteCommune($participant, $p))
                ->map(function ($p) use ($participant, $idsCibles) {
                    $p->score_compatibilite = $this->calculerCompatibilite($participant, $p);
                    $p->souhait_emis        = in_array($p->id, $idsCibles);
                    $p->est_mutuel          = Souhait::where('id_participant', $p->id)
                        ->where('id_participant_cible', $participant->id)
                        ->exists();
                    return $p;
                })
                ->sortBy([
                    ['souhait_emis', 'asc'],
                    ['score_compatibilite', 'desc'],
                ])
                ->values();

            // ─── Pagination manuelle (LOT D) ──────────────────────
            $perPage = 4;
            $page    = $this->getPage('page');

            $candidats = new LengthAwarePaginator(
                $candidatsTous->forPage($page, $perPage)->values(),
                $candidatsTous->count(),
                $perPage,
                $page,
                [
                    'path'     => Paginator::resolveCurrentPath(),
                    'pageName' => 'page',
                ]
            );
        }

        return view('livewire.entreprise.mes-souhaits', [
            'participant'       => $participant,
            'inscriptionValide' => $inscriptionValide,
            'souhaits'          => $souhaits,
            'nbSouhaits'        => $nbSouhaits,
            'minSouhaits'       => $minSouhaits,
            'maxSouhaits'       => $maxSouhaits,
            'objectifAtteint'   => $nbSouhaits >= $minSouhaits,
            'maxAtteint'        => $nbSouhaits >= $maxSouhaits,
            'candidats'         => $candidats,
            'evenement'         => $evenement,
            'souhaitsfermes'    => $souhaitsfermes,
            'joursRestants'     => $joursRestants,
        ])->layout('layouts.entreprise', ['title' => 'Mes Souhaits RDV']);
    }
}