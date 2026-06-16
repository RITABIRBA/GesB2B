<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Souhait;
use App\Models\Participant;
use App\Models\Inscription;
use App\Models\Evenement;
use Carbon\Carbon;

class MesSouhaits extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $alertSuccess = '';
    public string $alertError   = '';

    /**
     * Réinitialise la pagination quand la recherche change.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function emettresouhait(int $id_cible): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participant = Participant::findForUser(auth()->user());

        if (!$participant) {
            $this->alertError = 'Participant non trouvé.';
            return;
        }

        if (!$this->inscriptionEstValide($participant)) {
            $this->alertError = 'Vous devez avoir une inscription validée pour émettre des souhaits.';
            return;
        }

        // Vérification fermeture 3 jours avant l'événement
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

        // Vérification compatibilité (3 critères)
        $cible = Participant::find($id_cible);
        $scoreCompatibilite = $this->calculerCompatibilite($participant, $cible);

        if ($scoreCompatibilite === 0) {
            $this->alertError = 'Ce participant n\'est pas compatible avec votre profil (secteur, zone géographique et types de partenariat ne correspondent pas).';
            return;
        }

        // Vérification disponibilités communes
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
            'statut'               => $statut,
        ]);

        // Mise à jour du souhait retour si mutuel
        if ($estMutuel) {
            $souhaitRetour->update([
                'type'   => 'mutuel',
                'statut' => $statut,
            ]);
        }

        $this->alertSuccess = $estMutuel
            ? '🎉 Souhait mutuel ! Ce participant vous cherche aussi. Un RDV sera généré.'
            : ($scoreCompatibilite >= 2
                ? '✅ Souhait émis ! Profils compatibles.'
                : '⚠️ Souhait émis avec compatibilité partielle.');
    }

    public function supprimer(int $id): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participant = Participant::findForUser(auth()->user());
        $souhait     = Souhait::findOrFail($id);

        if ($souhait->id_participant !== $participant->id) {
            $this->alertError = 'Action non autorisée.';
            return;
        }

        // Vérification fermeture
        $evenement = Evenement::find($participant->id_evenement);
        if ($evenement && $this->souhaitsfermes($evenement)) {
            $this->alertError = 'Les souhaits sont clôturés, vous ne pouvez plus les modifier.';
            return;
        }

        // Si souhait mutuel → remettre l'autre en "envoye"
        Souhait::where('id_participant', $souhait->id_participant_cible)
            ->where('id_participant_cible', $participant->id)
            ->where('type', 'mutuel')
            ->update(['type' => 'envoye']);

        $prioriteSupprimee = $souhait->priorite;
        $souhait->delete();

        // Réajustement des priorités
        Souhait::where('id_participant', $participant->id)
            ->where('priorite', '>', $prioriteSupprimee)
            ->orderBy('priorite')
            ->each(fn($s) => $s->update(['priorite' => $s->priorite - 1]));

        $this->alertSuccess = 'Souhait supprimé.';
    }

    public function monterPriorite(int $id): void
    {
        $participant = Participant::findForUser(auth()->user());
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
        $participant = Participant::findForUser(auth()->user());
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

    /**
     * Vérifie si les souhaits sont fermés (3 jours avant l'événement).
     */
    private function souhaitsfermes(Evenement $evenement): bool
    {
        if (!$evenement->date_debut) return false;
        $dateEvenement = Carbon::parse($evenement->date_debut);
        return Carbon::now()->diffInDays($dateEvenement, false) <= 3;
    }

    /**
     * Vérifie si le participant a une inscription valide.
     */
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

    /**
     * Retourne les disponibilités sous forme de tableau.
     */
    private function getDisponibilites(Participant $p): array
    {
        if (!$p->disponibilites) return [];
        $dispo = is_string($p->disponibilites)
            ? json_decode($p->disponibilites, true) ?? []
            : $p->disponibilites;
        return is_array($dispo) ? $dispo : [];
    }

    /**
     * Vérifie si deux participants ont au moins un jour commun.
     */
    private function ontDisponibiliteCommune(Participant $moi, Participant $cible): bool
    {
        $dispoMoi   = $this->getDisponibilites($moi);
        $dispoCible = $this->getDisponibilites($cible);

        if (empty($dispoMoi) || empty($dispoCible)) return true;

        return count(array_intersect($dispoMoi, $dispoCible)) > 0;
    }

    /**
     * Calcule le score de compatibilité (0 à 3).
     * Compare secteurs_recherche (JSON), zone_geographique, types_partenariat (JSON).
     */
    private function calculerCompatibilite(Participant $moi, Participant $cible): int
    {
        $points = 0;

        // 1. Secteur : mes secteurs recherchés vs son secteur d'activité
        $secteursRecherche = is_array($moi->secteurs_recherche)
            ? $moi->secteurs_recherche
            : (json_decode($moi->secteurs_recherche ?? '[]', true) ?: []);

        if (!empty($secteursRecherche) && $cible->secteur_activite) {
            if (in_array($cible->secteur_activite, $secteursRecherche)) {
                $points++;
            }
        } elseif (empty($secteursRecherche)) {
            $points++; // Pas de filtre secteur → point accordé
        }

        // 2. Zone géographique
        if ($moi->zone_geographique && $cible->zone_geographique) {
            if ($moi->zone_geographique === $cible->zone_geographique) {
                $points++;
            }
        } else {
            $points++; // Pas de filtre zone → point accordé
        }

        // 3. Types de partenariat : intersection des deux listes JSON
        $typesPartenariatMoi   = is_array($moi->types_partenariat)
            ? $moi->types_partenariat
            : (json_decode($moi->types_partenariat ?? '[]', true) ?: []);

        $typesPartenariatCible = is_array($cible->types_partenariat)
            ? $cible->types_partenariat
            : (json_decode($cible->types_partenariat ?? '[]', true) ?: []);

        if (!empty($typesPartenariatMoi) && !empty($typesPartenariatCible)) {
            if (count(array_intersect($typesPartenariatMoi, $typesPartenariatCible)) > 0) {
                $points++;
            }
        } else {
            $points++; // Pas de filtre type → point accordé
        }

        return $points;
    }

    public function render()
    {
        $participant = Participant::findForUser(auth()->user());

        $inscriptionValide = $participant
            ? $this->inscriptionEstValide($participant)
            : false;

        $evenement = $participant && $participant->id_evenement
            ? Evenement::find($participant->id_evenement)
            : null;

        $souhaitsfermes = $evenement ? $this->souhaitsfermes($evenement) : false;
        $minSouhaits    = $evenement->min_souhaits ?? 5;
        $maxSouhaits    = $evenement->max_souhaits ?? 20;

        // Jours restants avant fermeture
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
                ->when($participant->id_entreprise, fn($q) =>
                    $q->where(function ($q) use ($participant) {
                        $q->whereNull('id_entreprise')
                          ->orWhere('id_entreprise', '!=', $participant->id_entreprise);
                    })
                )
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

            // ─── Pagination manuelle (LOT D — liste divisée en pages) ───
            $perPage = 4;
            $page    = $this->getPage('page');

            $candidats = new \Illuminate\Pagination\LengthAwarePaginator(
                $candidatsTous->forPage($page, $perPage)->values(),
                $candidatsTous->count(),
                $perPage,
                $page,
                [
                    'path'     => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
                    'pageName' => 'page',
                ]
            );
        }

        return view('livewire.participant.mes-souhaits', [
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
        ])->layout('layouts.participant', ['title' => 'Mes Souhaits RDV']);
    }
}