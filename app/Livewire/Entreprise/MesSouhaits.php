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
use App\Mail\MatchMutuelNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MesSouhaits extends Component
{
    use WithPagination;

    public string $onglet       = 'compatibles';
    public string $search       = '';
    public string $alertSuccess = '';
    public string $alertError   = '';

    public function updatedSearch(): void
    {
        $this->resetPage('pageCompatibles');
        $this->resetPage('pageTous');
    }

    public function changerOnglet(string $onglet): void
    {
        $this->onglet = $onglet;
        $this->search = '';
    }

    private function getRepresentant(): ?Participant
    {
        $entreprise = Entreprise::where('email_responsable', auth()->user()->email)->first();
        if (!$entreprise) return null;
        return Participant::where('id_entreprise', $entreprise->id)->where('role', 'representant')->first();
    }

    public function emettresouhait(int $id_cible): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participant = $this->getRepresentant();
        if (!$participant) { $this->alertError = 'Représentant non trouvé.'; return; }

        if (!$participant->profilB2BComplet()) {
            $this->alertError = 'Veuillez d\'abord compléter votre profil B2B.'; return;
        }

        if (!$this->inscriptionEstValide($participant)) {
            $this->alertError = 'Vous devez avoir une inscription validée pour émettre des souhaits.'; return;
        }

        $evenement = Evenement::find($participant->id_evenement);
        if ($evenement && $this->souhaitsfermes($evenement)) {
            $this->alertError = 'Les souhaits sont clôturés 3 jours avant l\'événement.'; return;
        }

        $maxSouhaits = $evenement->max_souhaits ?? 20;
        $nbSouhaits  = Souhait::where('id_participant', $participant->id)->count();
        if ($nbSouhaits >= $maxSouhaits) {
            $this->alertError = "Vous avez atteint le maximum de {$maxSouhaits} souhaits."; return;
        }

        $dejaEmis = Souhait::where('id_participant', $participant->id)->where('id_participant_cible', $id_cible)->exists();
        if ($dejaEmis) { $this->alertError = 'Vous avez déjà émis un souhait vers ce participant.'; return; }

        $cible = Participant::find($id_cible);
        if (!$cible) { $this->alertError = 'Participant introuvable.'; return; }

        if (!$this->ontDisponibiliteCommune($participant, $cible)) {
            $this->alertError = 'Vous n\'avez aucune disponibilité commune avec ce participant.'; return;
        }

        if (!$this->secteurCompatible($participant, $cible)) {
            $this->alertError = 'Vos secteurs d\'activité ne correspondent pas aux recherches de ce participant.'; return;
        }

        $scoreCompatibilite = $this->calculerCompatibilite($participant, $cible);
        $dernierePriorite   = Souhait::where('id_participant', $participant->id)->max('priorite') ?? 0;

        $souhaitRetour = Souhait::where('id_participant', $id_cible)->where('id_participant_cible', $participant->id)->first();
        $estMutuel     = (bool) $souhaitRetour;
        $statut        = $scoreCompatibilite >= 2 ? 'compatible' : 'en_attente';

        Souhait::create([
            'id_participant'       => $participant->id,
            'id_participant_cible' => $id_cible,
            'id_evenement'         => $participant->id_evenement,
            'priorite'             => $dernierePriorite + 1,
            'type'                 => $estMutuel ? 'mutuel' : 'envoye',
            'statut'               => $estMutuel ? 'accepte' : $statut,
        ]);

        if ($estMutuel) {
            $souhaitRetour->update(['type' => 'mutuel', 'statut' => 'accepte']);

            $rdvExiste = \App\Models\RendezVous::where(function ($q) use ($participant, $id_cible) {
                $q->where('id_participant1', $participant->id)->where('id_participant2', $id_cible);
            })->orWhere(function ($q) use ($participant, $id_cible) {
                $q->where('id_participant1', $id_cible)->where('id_participant2', $participant->id);
            })->exists();

            if (!$rdvExiste) {
                \App\Models\RendezVous::create([
                    'id_participant1' => $participant->id,
                    'id_participant2' => $id_cible,
                    'statut'          => 'planifie',
                ]);
            }

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

            $nomEvenement = $evenement->nom ?? 'Business Forum';
            if ($participant->email) {
                try { Mail::to($participant->email)->send(new MatchMutuelNotification($participant, $cible, $nomEvenement)); }
                catch (\Exception $e) { Log::error('Email mutuel échoué', ['id' => $participant->id, 'err' => $e->getMessage()]); }
            }
            if ($cible->email) {
                try { Mail::to($cible->email)->send(new MatchMutuelNotification($cible, $participant, $nomEvenement)); }
                catch (\Exception $e) { Log::error('Email mutuel échoué', ['id' => $cible->id, 'err' => $e->getMessage()]); }
            }
        }

        $this->alertSuccess = $estMutuel
            ? '🎉 Souhait mutuel ! Un email a été envoyé aux deux parties.'
            : ($scoreCompatibilite >= 2 ? '✅ Souhait émis ! Profils compatibles.' : '✅ Souhait émis avec succès.');
    }

    public function supprimer(int $id): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participant = $this->getRepresentant();
        $souhait     = Souhait::findOrFail($id);
        if ($souhait->id_participant !== $participant->id) { $this->alertError = 'Action non autorisée.'; return; }

        $evenement = Evenement::find($participant->id_evenement);
        if ($evenement && $this->souhaitsfermes($evenement)) { $this->alertError = 'Les souhaits sont clôturés.'; return; }

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
        $voisin = Souhait::where('id_participant', $participant->id)->where('priorite', $souhait->priorite - 1)->first();
        if ($voisin) $voisin->update(['priorite' => $souhait->priorite]);
        $souhait->update(['priorite' => $souhait->priorite - 1]);
    }

    public function descendrePriorite(int $id): void
    {
        $participant = $this->getRepresentant();
        $souhait     = Souhait::findOrFail($id);
        $max         = Souhait::where('id_participant', $participant->id)->max('priorite');
        if ($souhait->priorite >= $max) return;
        $voisin = Souhait::where('id_participant', $participant->id)->where('priorite', $souhait->priorite + 1)->first();
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
                  ->orWhereHas('evenement', fn($q) => $q->whereIn('type_paiement', ['gratuit', 'par_entreprise']));
            })->exists();
    }

    private function getDisponibilites(Participant $p): array
    {
        if (!$p->disponibilites) return [];
        $dispo = is_string($p->disponibilites) ? json_decode($p->disponibilites, true) ?? [] : $p->disponibilites;
        return is_array($dispo) ? $dispo : [];
    }

    private function ontDisponibiliteCommune(Participant $moi, Participant $cible): bool
    {
        $dA = $this->getDisponibilites($moi);
        $dB = $this->getDisponibilites($cible);
        if (empty($dA) || empty($dB)) return true;
        return count(array_intersect($dA, $dB)) > 0;
    }

    // ✅ Vérification bidirectionnelle du secteur
    private function secteurCompatible(Participant $moi, Participant $cible): bool
    {
        $mesSecteursRecherche   = is_array($moi->secteurs_recherche)   ? $moi->secteurs_recherche   : (json_decode($moi->secteurs_recherche   ?? '[]', true) ?: []);
        $secteursRechercheCible = is_array($cible->secteurs_recherche) ? $cible->secteurs_recherche : (json_decode($cible->secteurs_recherche  ?? '[]', true) ?: []);
        if (empty($mesSecteursRecherche) || empty($secteursRechercheCible)) return true;
        $monSecteurDansCible = $cible->secteur_activite && in_array($moi->secteur_activite, $secteursRechercheCible);
        $secteurCibleDansMoi = $moi->secteur_activite  && in_array($cible->secteur_activite, $mesSecteursRecherche);
        return $monSecteurDansCible || $secteurCibleDansMoi;
    }

    private function calculerCompatibilite(Participant $moi, Participant $cible): int
    {
        if (!$moi->profilB2BComplet()) return 0;
        $points = 0;

        // Secteur bidirectionnel
        $mesSecteursRecherche   = is_array($moi->secteurs_recherche)   ? $moi->secteurs_recherche   : (json_decode($moi->secteurs_recherche   ?? '[]', true) ?: []);
        $secteursRechercheCible = is_array($cible->secteurs_recherche) ? $cible->secteurs_recherche : (json_decode($cible->secteurs_recherche  ?? '[]', true) ?: []);
        $monSecteurDansCible = !empty($secteursRechercheCible) && $moi->secteur_activite  && in_array($moi->secteur_activite,  $secteursRechercheCible);
        $secteurCibleDansMoi = !empty($mesSecteursRecherche)   && $cible->secteur_activite && in_array($cible->secteur_activite, $mesSecteursRecherche);
        if ($monSecteurDansCible && $secteurCibleDansMoi) $points += 2;
        elseif ($monSecteurDansCible || $secteurCibleDansMoi) $points += 1;

        // Zone géographique
        if ($moi->zone_geographique && $cible->zone_geographique && $moi->zone_geographique === $cible->zone_geographique) $points++;

        // Profil partenaire bidirectionnel
        $mesProfilsRecherches  = is_array($moi->profils_partenaire)   ? $moi->profils_partenaire   : (json_decode($moi->profils_partenaire   ?? '[]', true) ?: []);
        $typesPartenariatCible = is_array($cible->types_partenariat)  ? $cible->types_partenariat  : (json_decode($cible->types_partenariat  ?? '[]', true) ?: []);
        $profilsRechercheCible = is_array($cible->profils_partenaire) ? $cible->profils_partenaire : (json_decode($cible->profils_partenaire ?? '[]', true) ?: []);
        $mesTypesPartenariat   = is_array($moi->types_partenariat)    ? $moi->types_partenariat    : (json_decode($moi->types_partenariat    ?? '[]', true) ?: []);
        if (!empty($mesProfilsRecherches) && !empty($typesPartenariatCible) && count(array_intersect($mesProfilsRecherches, $typesPartenariatCible)) > 0) $points++;
        if (!empty($profilsRechercheCible) && !empty($mesTypesPartenariat) && count(array_intersect($profilsRechercheCible, $mesTypesPartenariat)) > 0) $points++;

        return $points;
    }

    private function paginatorVide(string $pageName): LengthAwarePaginator
    {
        return new LengthAwarePaginator(collect(), 0, 4, 1, ['path' => Paginator::resolveCurrentPath(), 'pageName' => $pageName]);
    }

    private function paginerCollection($collection, string $pageName, int $perPage = 4): LengthAwarePaginator
    {
        $page = $this->getPage($pageName);
        return new LengthAwarePaginator($collection->forPage($page, $perPage)->values(), $collection->count(), $perPage, $page, ['path' => Paginator::resolveCurrentPath(), 'pageName' => $pageName]);
    }

    public function render()
    {
        $participant = $this->getRepresentant();

        $inscriptionValide = $participant ? $this->inscriptionEstValide($participant) : false;
        $evenement         = $participant && $participant->id_evenement ? Evenement::find($participant->id_evenement) : null;
        $evenementSansB2B  = $evenement && ($evenement->type_evenement ?? 'avec_b2b') === 'sans_b2b';
        $souhaitsfermes    = $evenement ? $this->souhaitsfermes($evenement) : false;
        $minSouhaits       = $evenement->min_souhaits ?? 5;
        $maxSouhaits       = $evenement->max_souhaits ?? 20;

        $joursRestants = null;
        if ($evenement && $evenement->date_debut) {
            $joursRestants = (int) Carbon::now()->diffInDays(Carbon::parse($evenement->date_debut), false);
        }

        $profilB2BComplet = $participant ? $participant->profilB2BComplet() : false;

        $souhaits   = $participant
            ? Souhait::with('participantCible.entreprise')->where('id_participant', $participant->id)->orderBy('priorite')->get()
            : collect();
        $nbSouhaits = $souhaits->count();
        $idsCibles  = $souhaits->pluck('id_participant_cible')->toArray();

        $candidatsCompatibles = $this->paginatorVide('pageCompatibles');
        $candidatsTous        = $this->paginatorVide('pageTous');
        $nbCompatibles        = 0;
        $nbTous               = 0;

        if ($participant && $inscriptionValide && !$souhaitsfermes && $profilB2BComplet && !$evenementSansB2B) {

            $baseQuery = fn($q) => $q->with('entreprise')
                ->where('id_evenement', $participant->id_evenement)
                ->where('id', '!=', $participant->id)
                ->where('participation_rdv', true)
                ->where(function ($q) use ($participant) {
                    $q->whereNull('id_entreprise')->orWhere('id_entreprise', '!=', $participant->id_entreprise);
                })
                ->when($this->search, fn($q) =>
                    $q->where(function ($q) {
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                          ->orWhereHas('entreprise', fn($q) => $q->where('nom', 'like', '%'.$this->search.'%'));
                    })
                );

            $mapper = function ($p) use ($participant, $idsCibles) {
                $p->score_compatibilite = $this->calculerCompatibilite($participant, $p);
                $p->souhait_emis        = in_array($p->id, $idsCibles);
                $p->est_mutuel          = Souhait::where('id_participant', $p->id)->where('id_participant_cible', $participant->id)->exists();
                return $p;
            };

            // Compatibles = filtrés par secteur + dispo
            $compatibles = Participant::query()->tap($baseQuery)->get()
                ->filter(fn($p) => $this->ontDisponibiliteCommune($participant, $p))
                ->filter(fn($p) => $this->secteurCompatible($participant, $p))
                ->map($mapper)
                ->sortBy([['souhait_emis', 'asc'], ['score_compatibilite', 'desc']])
                ->values();

            // Tous = filtrés uniquement par dispo, sans filtre secteur
            $tous = Participant::query()->tap($baseQuery)->get()
                ->filter(fn($p) => $this->ontDisponibiliteCommune($participant, $p))
                ->map($mapper)
                ->sortBy([['souhait_emis', 'asc'], ['score_compatibilite', 'desc']])
                ->values();

            $nbCompatibles        = $compatibles->count();
            $nbTous               = $tous->count();
            $candidatsCompatibles = $this->paginerCollection($compatibles, 'pageCompatibles');
            $candidatsTous        = $this->paginerCollection($tous, 'pageTous');
        }

        return view('livewire.entreprise.mes-souhaits', [
            'participant'          => $participant,
            'inscriptionValide'    => $inscriptionValide,
            'profilB2BComplet'     => $profilB2BComplet,
            'evenementSansB2B'     => $evenementSansB2B,
            'souhaits'             => $souhaits,
            'nbSouhaits'           => $nbSouhaits,
            'minSouhaits'          => $minSouhaits,
            'maxSouhaits'          => $maxSouhaits,
            'objectifAtteint'      => $nbSouhaits >= $minSouhaits,
            'maxAtteint'           => $nbSouhaits >= $maxSouhaits,
            'candidatsCompatibles' => $candidatsCompatibles,
            'candidatsTous'        => $candidatsTous,
            'nbCompatibles'        => $nbCompatibles,
            'nbTous'               => $nbTous,
            'evenement'            => $evenement,
            'souhaitsfermes'       => $souhaitsfermes,
            'joursRestants'        => $joursRestants,
        ])->layout('layouts.entreprise', ['title' => 'Mes Souhaits RDV']);
    }
}