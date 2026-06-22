<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\RendezVous;
use App\Models\Participant;
use App\Models\Souhait;
use App\Models\Traducteur;
use App\Models\Evenement;
use App\Models\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class GestionRendezVous extends Component
{
    use WithPagination;

    // Génération du planning
    public $id_evenement          = '';
    public $evenement_selectionne = null;
    public $nb_creneaux           = 0;
    public $nb_tables             = 0;
    public $nb_paires             = 0;

    // Pauses
    public $pause_cafe_matin       = false;
    public $pause_cafe_matin_debut = '10:00';
    public $pause_cafe_matin_fin   = '10:15';
    public $pause_dejeuner         = false;
    public $pause_dejeuner_debut   = '12:00';
    public $pause_dejeuner_fin     = '14:00';
    public $pause_cafe_aprem       = false;
    public $pause_cafe_aprem_debut = '15:30';
    public $pause_cafe_aprem_fin   = '15:45';

    // Modals
    public $showGenerateModal   = false;
    public $showTraducteurModal = false;
    public $showRematchModal    = false;
    public $showAnnulerModal    = false;
    public $rdv_id;
    public $rdv_courant         = null;
    public $id_traducteur       = '';

    // Annulation
    public $annuler_rdv_id = null;
    public $annuler_rdv    = null;
    public $absent_id      = '';

    // Re-match
    public $rematch_rdv_id      = null;
    public $rematch_rdv         = null;
    public $nouveau_participant  = '';
    public $erreur_rematch       = '';

    // ─── Match manuel (CAS 4) ─────────────────────────────
    public $showMatchManuelModal = false;
    public $match_id_evenement   = '';
    public $match_participant1   = '';
    public $match_participant2   = '';
    public $match_compatibilite  = null;
    public $match_disponibilite_ok = true;

    // Filtres
    public $search        = '';
    public $filtre_statut = '';

    // ─── Tri & Point des RDV par événement ─────────────────
    public $sort_field       = 'date';
    public $sort_direction   = 'asc';
    public $filtre_evenement = '';

    // ─── Pagination (par événement) ────────────────────────
    protected $perPagePlanning = 10;

    /**
     * Réinitialise toutes les pages de pagination (une par
     * événement) — appelé quand un filtre ou le tri change,
     * pour éviter de rester sur une page devenue inexistante.
     */
    private function resetAllPages(): void
    {
        foreach (array_keys($this->paginators ?? []) as $pageName) {
            $this->resetPage($pageName);
        }
    }

    public function updatedSearch(): void
    {
        $this->resetAllPages();
    }

    public function updatedFiltreStatut(): void
    {
        $this->resetAllPages();
    }

    public function updatedFiltreEvenement(): void
    {
        $this->resetAllPages();
    }

    /**
     * Quand l'événement change, recalcule le résumé automatiquement.
     * Utilise duree_rdv et duree_pause de l'événement.
     */
    public function updatedIdEvenement(): void
    {
        $this->calculerResume();
    }

    /**
     * Calcule le nombre de créneaux disponibles, tables et paires de RDV.
     * La durée du RDV et la pause sont lues depuis l'événement.
     */
    public function calculerResume(): void
    {
        if (!$this->id_evenement) {
            $this->evenement_selectionne = null;
            $this->nb_creneaux           = 0;
            $this->nb_tables             = 0;
            $this->nb_paires             = 0;
            return;
        }

        $evenement = Evenement::find($this->id_evenement);
        if (!$evenement) return;

        $this->evenement_selectionne = $evenement;

        // Durée du RDV et pause depuis l'événement
        $dureeRdv   = ($evenement->duree_rdv ?? 20) * 60;
        $dureePause = ($evenement->duree_pause ?? 5) * 60;
        $dureeSlot  = $dureeRdv + $dureePause;

        $debut      = strtotime($evenement->heure_debut);
        $fin        = strtotime($evenement->heure_fin);
        $pauses     = $this->getPauses();
        $nb_creneaux = 0;

        while ($debut + $dureeRdv <= $fin) {
            $creneauDebut = $debut;
            $creneauFin   = $debut + $dureeRdv;
            $dansUnePause = false;

            foreach ($pauses as $pause) {
                if ($creneauDebut < $pause['fin'] && $creneauFin > $pause['debut']) {
                    $dansUnePause = true;
                    $debut        = $pause['fin'];
                    break;
                }
            }

            if (!$dansUnePause) {
                $nb_creneaux++;
                $debut += $dureeSlot;
            }
        }

        $this->nb_creneaux = $nb_creneaux;
        $this->nb_tables   = $evenement->nombre_tables ?? 0;

        // Calcule les paires de souhaits uniques
        $participants    = Participant::where('id_evenement', $this->id_evenement)
            ->where('participation_rdv', true)
            ->pluck('id');
        $souhaitsTraites = [];
        $nb_paires       = 0;

        foreach ($participants as $id_participant) {
            $souhaits = Souhait::where('id_participant', $id_participant)
                ->orderBy('priorite')
                ->get();

            foreach ($souhaits as $souhait) {
                $paire     = collect([
                    $souhait->id_participant,
                    $souhait->id_participant_cible,
                ])->sort()->values()->toArray();
                $cleUnique = $paire[0] . '-' . $paire[1];

                if (!in_array($cleUnique, $souhaitsTraites)) {
                    $souhaitsTraites[] = $cleUnique;
                    $nb_paires++;
                }
            }
        }

        $this->nb_paires = $nb_paires;
    }

    /**
     * Retourne les pauses configurées sous forme de tableau.
     */
    private function getPauses(): array
    {
        $pauses = [];

        if ($this->pause_cafe_matin) {
            $pauses[] = [
                'debut' => strtotime($this->pause_cafe_matin_debut),
                'fin'   => strtotime($this->pause_cafe_matin_fin),
            ];
        }

        if ($this->pause_dejeuner) {
            $pauses[] = [
                'debut' => strtotime($this->pause_dejeuner_debut),
                'fin'   => strtotime($this->pause_dejeuner_fin),
            ];
        }

        if ($this->pause_cafe_aprem) {
            $pauses[] = [
                'debut' => strtotime($this->pause_cafe_aprem_debut),
                'fin'   => strtotime($this->pause_cafe_aprem_fin),
            ];
        }

        return $pauses;
    }

    public function openGenerateModal(): void
    {
        $this->showGenerateModal     = true;
        $this->id_evenement          = '';
        $this->evenement_selectionne = null;
        $this->nb_creneaux           = 0;
        $this->nb_tables             = 0;
        $this->nb_paires             = 0;
        $this->pause_cafe_matin      = false;
        $this->pause_dejeuner        = false;
        $this->pause_cafe_aprem      = false;
    }

    public function closeGenerateModal(): void
    {
        $this->showGenerateModal     = false;
        $this->id_evenement          = '';
        $this->evenement_selectionne = null;
        $this->nb_creneaux           = 0;
        $this->nb_tables             = 0;
        $this->nb_paires             = 0;
        $this->pause_cafe_matin      = false;
        $this->pause_dejeuner        = false;
        $this->pause_cafe_aprem      = false;
        $this->resetErrorBag();
    }

    public function ouvrirModalTraducteur($id): void
    {
        $this->rdv_id        = $id;
        $this->id_traducteur = '';
        $this->rdv_courant   = RendezVous::with([
            'participant1', 'participant1.entreprise',
            'participant2', 'participant2.entreprise',
            'traducteur',
        ])->find($id);
        $this->showTraducteurModal = true;
    }

    public function fermerModalTraducteur(): void
    {
        $this->showTraducteurModal = false;
        $this->rdv_id              = null;
        $this->rdv_courant         = null;
        $this->id_traducteur       = '';
    }

    public function assignerTraducteur(): void
    {
        $this->validate(['id_traducteur' => 'required']);
        RendezVous::findOrFail($this->rdv_id)->update(['id_traducteur' => $this->id_traducteur]);
        $this->fermerModalTraducteur();
        session()->flash('success', 'Traducteur assigné avec succès !');
    }

    public function ouvrirModalAnnuler($id): void
    {
        $this->annuler_rdv_id = $id;
        $this->absent_id      = '';
        $this->annuler_rdv    = RendezVous::with([
            'participant1', 'participant1.entreprise',
            'participant2', 'participant2.entreprise',
        ])->findOrFail($id);
        $this->showAnnulerModal = true;
    }

    public function fermerModalAnnuler(): void
    {
        $this->showAnnulerModal = false;
        $this->annuler_rdv_id  = null;
        $this->annuler_rdv     = null;
        $this->absent_id       = '';
    }

    public function confirmerAnnulation(): void
    {
        $this->validate([
            'absent_id' => 'required',
        ], [
            'absent_id.required' => 'Veuillez indiquer qui est absent.',
        ]);

        $rdv = RendezVous::findOrFail($this->annuler_rdv_id);

        $rdv->update([
            'statut'                => 'annule',
            'absent_participant_id' => $this->absent_id,
        ]);

        // Notifications aux 2 participants
        $autreId = $rdv->id_participant1 == $this->absent_id
            ? $rdv->id_participant2
            : $rdv->id_participant1;

        $absent = Participant::find($this->absent_id);
        $autre  = Participant::find($autreId);

        if ($absent) {
            Notification::create([
                'id_participant' => $absent->id,
                'contenu'        => "Votre rendez-vous avec " . ($autre->nom ?? '') . ' ' . ($autre->prenom ?? '') . " a été annulé par l'administration (absence).",
                'date_envoie'    => now()->toDateString(),
                'type'           => 'systeme',
            ]);
        }

        if ($autre) {
            Notification::create([
                'id_participant' => $autre->id,
                'contenu'        => "⚠️ Votre rendez-vous avec " . ($absent->nom ?? '') . ' ' . ($absent->prenom ?? '') . " a été annulé (absence signalée). Des remplaçants compatibles vous seront proposés.",
                'date_envoie'    => now()->toDateString(),
                'type'           => 'systeme',
            ]);
        }

        $this->fermerModalAnnuler();
        session()->flash('success', 'Rendez-vous annulé. Les participants ont été notifiés. Vous pouvez effectuer un re-match.');
    }

    public function ouvrirRematch($id): void
    {
        $this->rematch_rdv_id      = $id;
        $this->nouveau_participant  = '';
        $this->erreur_rematch       = '';
        $this->rematch_rdv         = RendezVous::with([
            'participant1', 'participant1.entreprise',
            'participant2', 'participant2.entreprise',
            'participantAbsent',
        ])->findOrFail($id);
        $this->showRematchModal = true;
    }

    public function fermerRematch(): void
    {
        $this->showRematchModal   = false;
        $this->rematch_rdv_id     = null;
        $this->rematch_rdv        = null;
        $this->nouveau_participant = '';
        $this->erreur_rematch      = '';
    }

    public function effectuerRematch(): void
    {
        $this->validate(['nouveau_participant' => 'required']);

        $rdv = RendezVous::findOrFail($this->rematch_rdv_id);

        // Vérifie que le remplaçant n'a pas de conflit sur ce créneau
        $conflit = RendezVous::where('date', $rdv->date)
            ->where('heure_debut', $rdv->heure_debut)
            ->where('id', '!=', $rdv->id)
            ->where(function ($q) {
                $q->where('id_participant1', $this->nouveau_participant)
                  ->orWhere('id_participant2', $this->nouveau_participant);
            })
            ->exists();

        if ($conflit) {
            $participant          = Participant::find($this->nouveau_participant);
            $this->erreur_rematch = ($participant->nom ?? '') . ' ' .
                ($participant->prenom ?? '') .
                ' a déjà un RDV sur ce créneau (' .
                $rdv->heure_debut . ' - ' . $rdv->heure_fin .
                '). Choisissez un autre participant !';
            return;
        }

        $this->erreur_rematch = '';

        $ancienAbsentId = $rdv->absent_participant_id;
        $autreId = $rdv->id_participant1 == $ancienAbsentId
            ? $rdv->id_participant2
            : $rdv->id_participant1;

        if ($rdv->absent_participant_id == $rdv->id_participant1) {
            $rdv->update([
                'id_participant1'       => $this->nouveau_participant,
                'statut'                => 'planifie',
                'absent_participant_id' => null,
            ]);
        } else {
            $rdv->update([
                'id_participant2'       => $this->nouveau_participant,
                'statut'                => 'planifie',
                'absent_participant_id' => null,
            ]);
        }

        // Notifications
        $nouveau = Participant::find($this->nouveau_participant);
        $autre   = Participant::find($autreId);

        if ($nouveau) {
            Notification::create([
                'id_participant' => $nouveau->id,
                'contenu'        => "🎉 Un nouveau rendez-vous vous a été attribué avec " . ($autre->nom ?? '') . ' ' . ($autre->prenom ?? '') . " le {$rdv->date} de {$rdv->heure_debut} à {$rdv->heure_fin}.",
                'date_envoie'    => now()->toDateString(),
                'type'           => 'systeme',
            ]);
        }

        if ($autre) {
            Notification::create([
                'id_participant' => $autre->id,
                'contenu'        => "✅ Votre rendez-vous a été rétabli avec " . ($nouveau->nom ?? '') . ' ' . ($nouveau->prenom ?? '') . " le {$rdv->date} de {$rdv->heure_debut} à {$rdv->heure_fin}.",
                'date_envoie'    => now()->toDateString(),
                'type'           => 'systeme',
            ]);
        }

        session()->flash('success', 'Re-match effectué ! Le RDV est rétabli et les participants notifiés.');
        $this->fermerRematch();
    }

    public function confirmer($id): void
    {
        RendezVous::findOrFail($id)->update(['statut' => 'confirme']);
        session()->flash('success', 'Rendez-vous confirmé !');
    }

    public function terminer($id): void
    {
        RendezVous::findOrFail($id)->update(['statut' => 'termine']);
        session()->flash('success', 'Rendez-vous terminé !');
    }

    public function supprimer($id): void
    {
        RendezVous::findOrFail($id)->delete();
        session()->flash('success', 'Rendez-vous supprimé.');
    }

    // ─── TRI DES COLONNES ───────────────────────────────────────

    /**
     * Bascule le tri sur la colonne cliquée : si c'est déjà la
     * colonne triée, inverse le sens (asc/desc), sinon trie cette
     * nouvelle colonne en ascendant. Réinitialise les pages.
     */
    public function sortBy(string $field): void
    {
        if ($this->sort_field === $field) {
            $this->sort_direction = $this->sort_direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort_field     = $field;
            $this->sort_direction = 'asc';
        }

        $this->resetAllPages();
    }

    /**
     * Applique le tri courant ($sort_field / $sort_direction) sur
     * une collection de RendezVous (avec relations participant1/2
     * déjà chargées).
     */
    private function sortCollection($collection)
    {
        $field = $this->sort_field;

        return $collection->sortBy(function ($rdv) use ($field) {
            return match ($field) {
                'nom1'   => mb_strtolower(trim(($rdv->participant1->nom ?? '') . ' ' . ($rdv->participant1->prenom ?? ''))),
                'nom2'   => mb_strtolower(trim(($rdv->participant2->nom ?? '') . ' ' . ($rdv->participant2->prenom ?? ''))),
                'date'   => trim(($rdv->date ?? '') . ' ' . ($rdv->heure_debut ?? '')),
                'statut' => $rdv->statut,
                default  => $rdv->id,
            };
        }, SORT_REGULAR, $this->sort_direction === 'desc')->values();
    }

    /**
     * Libellé français d'un statut de RDV (pour exports).
     */
    private function libelleStatut(string $statut): string
    {
        return match ($statut) {
            'a_planifier' => 'A planifier',
            'planifie'    => 'Planifie',
            'confirme'    => 'Confirme',
            'annule'      => 'Annule',
            'termine'     => 'Termine',
            default       => $statut,
        };
    }

    /**
     * Requête de base des RDV, avec relations et filtres
     * (statut, événement, recherche) appliqués — partagée entre
     * l'affichage et les exports.
     */
    private function buildRendezVousQuery()
    {
        return RendezVous::with([
                'participant1', 'participant1.entreprise',
                'participant2', 'participant2.entreprise',
                'traducteur', 'participantAbsent',
            ])
            ->when($this->filtre_statut, fn($q) =>
                $q->where('statut', $this->filtre_statut)
            )
            ->when($this->filtre_evenement, function ($q) {
                $q->where(function ($qq) {
                    $qq->whereHas('participant1', fn($q2) =>
                            $q2->where('id_evenement', $this->filtre_evenement)
                        )
                        ->orWhereHas('participant2', fn($q2) =>
                            $q2->where('id_evenement', $this->filtre_evenement)
                        );
                });
            })
            ->when($this->search, fn($q) =>
                $q->whereHas('participant1', fn($q) =>
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('prenom', 'like', '%' . $this->search . '%')
                )->orWhereHas('participant2', fn($q) =>
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('prenom', 'like', '%' . $this->search . '%')
                )
            );
    }

    // ─── EXPORTS ────────────────────────────────────────────────

    /**
     * Export CSV ("Excel") du planning filtré/trié actuel.
     */
    public function exportExcel()
    {
        $rdvs = $this->sortCollection($this->buildRendezVousQuery()->get());

        $filename = 'planning_rdv_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($rdvs) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 pour qu'Excel affiche correctement les accents
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Nom participant 1', 'Prenom participant 1', 'Entreprise 1',
                'Nom participant 2', 'Prenom participant 2', 'Entreprise 2',
                'Date', 'Heure debut', 'Heure fin', 'Salle', 'Table',
                'Traducteur', 'Statut',
            ], ';');

            foreach ($rdvs as $rdv) {
                fputcsv($handle, [
                    $rdv->participant1->nom ?? '',
                    $rdv->participant1->prenom ?? '',
                    $rdv->participant1->entreprise->nom ?? 'Independant',
                    $rdv->participant2->nom ?? '',
                    $rdv->participant2->prenom ?? '',
                    $rdv->participant2->entreprise->nom ?? 'Independant',
                    $rdv->date ?? '',
                    $rdv->heure_debut ?? '',
                    $rdv->heure_fin ?? '',
                    $rdv->salle ?? '',
                    $rdv->numero_table ?? '',
                    $rdv->traducteur->nom ?? '',
                    $this->libelleStatut($rdv->statut),
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Export PDF du planning filtré/trié actuel.
     * Nécessite : composer require barryvdh/laravel-dompdf
     */
    public function exportPdf()
    {
        $rdvs = $this->sortCollection($this->buildRendezVousQuery()->get());

        $evenementFiltre = $this->filtre_evenement
            ? Evenement::find($this->filtre_evenement)
            : null;

        $pdf = Pdf::loadView('exports.planning-rdv-pdf', [
            'rendezVous'    => $rdvs,
            'evenement'     => $evenementFiltre,
            'libelleStatut' => fn ($s) => $this->libelleStatut($s),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'planning_rdv_' . now()->format('Y-m-d_His') . '.pdf'
        );
    }

    // ─── MATCH MANUEL (CAS 4) ──────────────────────────────────

    public function ouvrirMatchManuel(): void
    {
        $this->showMatchManuelModal  = true;
        $this->match_id_evenement    = '';
        $this->match_participant1    = '';
        $this->match_participant2    = '';
        $this->match_compatibilite   = null;
        $this->match_disponibilite_ok = true;
        $this->resetErrorBag();
    }

    public function fermerMatchManuel(): void
    {
        $this->showMatchManuelModal  = false;
        $this->match_id_evenement    = '';
        $this->match_participant1    = '';
        $this->match_participant2    = '';
        $this->match_compatibilite   = null;
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
     * zone_geographique et types_partenariat (mêmes critères que
     * côté participant).
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
     * Cherche un créneau (date, heure, salle, table) disponible pour
     * les 2 participants donnés, en respectant leurs disponibilités,
     * les pauses configurées, et l'absence de conflit/table occupée.
     */
    private function trouverCreneauDisponible(Evenement $evenement, int $id1, int $id2): ?array
    {
        $p1 = Participant::find($id1);
        $p2 = Participant::find($id2);

        $dureeRdv   = ($evenement->duree_rdv ?? 20) * 60;
        $dureePause = ($evenement->duree_pause ?? 5) * 60;
        $dureeSlot  = $dureeRdv + $dureePause;
        $pauses     = $this->getPauses();

        $debutEvt = \Carbon\Carbon::parse($evenement->date_debut);
        $finEvt   = \Carbon\Carbon::parse($evenement->date_fin);
        $jours    = [];
        $cursor   = $debutEvt->copy();
        while ($cursor->lte($finEvt)) {
            $jours[] = $cursor->toDateString();
            $cursor->addDay();
        }

        $dispoP1 = $this->getDisponibilites($p1);
        $dispoP2 = $this->getDisponibilites($p2);

        foreach ($jours as $jour) {
            if (!empty($dispoP1) && !in_array($jour, $dispoP1)) continue;
            if (!empty($dispoP2) && !in_array($jour, $dispoP2)) continue;

            $debut = strtotime($evenement->heure_debut);
            $fin   = strtotime($evenement->heure_fin);

            while ($debut + $dureeRdv <= $fin) {
                $creneauDebut = $debut;
                $creneauFin   = $debut + $dureeRdv;
                $dansUnePause = false;

                foreach ($pauses as $pause) {
                    if ($creneauDebut < $pause['fin'] && $creneauFin > $pause['debut']) {
                        $dansUnePause = true;
                        $debut        = $pause['fin'];
                        break;
                    }
                }

                if ($dansUnePause) continue;

                $heureDebut = date('H:i', $creneauDebut);
                $heureFin   = date('H:i', $creneauFin);

                $conflit = RendezVous::where('date', $jour)
                    ->where('heure_debut', $heureDebut)
                    ->where('statut', '!=', 'annule')
                    ->where(function ($q) use ($id1, $id2) {
                        $q->whereIn('id_participant1', [$id1, $id2])
                          ->orWhereIn('id_participant2', [$id1, $id2]);
                    })
                    ->exists();

                if (!$conflit) {
                    $tablesUtilisees = RendezVous::where('date', $jour)
                        ->where('heure_debut', $heureDebut)
                        ->where('statut', '!=', 'annule')
                        ->whereNotNull('numero_table')
                        ->pluck('numero_table')
                        ->toArray();

                    for ($t = 1; $t <= ($evenement->nombre_tables ?? 0); $t++) {
                        if (!in_array($t, $tablesUtilisees)) {
                            return [
                                'date'         => $jour,
                                'heure_debut'  => $heureDebut,
                                'heure_fin'    => $heureFin,
                                'salle'        => $evenement->nom_salle,
                                'numero_table' => $t,
                            ];
                        }
                    }
                }

                $debut += $dureeSlot;
            }
        }

        return null;
    }

    /**
     * Crée un RDV manuel entre 2 participants choisis par l'admin/superviseur.
     * Notifie les 2 participants par email interne (table notifications).
     */
    public function creerMatchManuel(): void
    {
        $this->validate([
            'match_id_evenement' => 'required',
            'match_participant1' => 'required',
            'match_participant2' => 'required|different:match_participant1',
        ], [
            'match_id_evenement.required' => 'Sélectionnez un événement.',
            'match_participant1.required' => 'Sélectionnez le premier participant.',
            'match_participant2.required' => 'Sélectionnez le second participant.',
            'match_participant2.different' => 'Choisissez deux participants différents.',
        ]);

        $p1 = Participant::find($this->match_participant1);
        $p2 = Participant::find($this->match_participant2);

        // Vérifie qu'il n'existe pas déjà un RDV actif entre les deux
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

        $evenement = Evenement::findOrFail($this->match_id_evenement);

        $creneau = $this->trouverCreneauDisponible(
            $evenement,
            (int) $this->match_participant1,
            (int) $this->match_participant2
        );

        if (!$creneau) {
            $this->addError('match_participant2', 'Aucun créneau disponible pour ces deux participants (emplois du temps complets ou disponibilités incompatibles).');
            return;
        }

        RendezVous::create([
            'id_participant1' => $this->match_participant1,
            'id_participant2' => $this->match_participant2,
            'date'            => $creneau['date'],
            'heure_debut'     => $creneau['heure_debut'],
            'heure_fin'       => $creneau['heure_fin'],
            'salle'           => $creneau['salle'],
            'numero_table'    => $creneau['numero_table'],
            'statut'          => 'planifie',
        ]);

        // Notifications
        $infoCreneau = "le {$creneau['date']} de {$creneau['heure_debut']} à {$creneau['heure_fin']} (Table {$creneau['numero_table']})";

        Notification::create([
            'id_participant' => $p1->id,
            'contenu'        => "📅 Un rendez-vous a été organisé par l'administration avec " . ($p2->nom ?? '') . ' ' . ($p2->prenom ?? '') . " {$infoCreneau}.",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        Notification::create([
            'id_participant' => $p2->id,
            'contenu'        => "📅 Un rendez-vous a été organisé par l'administration avec " . ($p1->nom ?? '') . ' ' . ($p1->prenom ?? '') . " {$infoCreneau}.",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        session()->flash('success', 'Match manuel créé avec succès ! Les deux participants ont été notifiés.');
        $this->fermerMatchManuel();
    }

    /**
     * Génère le planning des RDV pour un événement.
     * ✅ CORRIGÉ : tri global par score de priorité combiné de
     * chaque paire (mutuel ou unilatéral), pas seulement par
     * priorité interne à chaque participant.
     */
    public function genererPlanning(): void
    {
        $this->validate([
            'id_evenement' => 'required',
        ]);

        $evenement = Evenement::findOrFail($this->id_evenement);

        if (!$evenement->nom_salle || !$evenement->nombre_tables) {
            session()->flash('error',
                'Veuillez définir la salle et le nombre de tables dans la gestion des événements !'
            );
            $this->closeGenerateModal();
            return;
        }

        $participants = Participant::where('id_evenement', $this->id_evenement)
            ->where('participation_rdv', true)
            ->get();

        if ($participants->isEmpty()) {
            session()->flash('error', 'Aucun participant avec participation RDV activée !');
            $this->closeGenerateModal();
            return;
        }

        // Durée depuis l'événement (pas de saisie manuelle)
        $dureeRdv   = ($evenement->duree_rdv ?? 20) * 60;
        $dureePause = ($evenement->duree_pause ?? 5) * 60;
        $dureeSlot  = $dureeRdv + $dureePause;

        // Génère tous les créneaux disponibles
        $creneaux = [];
        $debut    = strtotime($evenement->heure_debut);
        $fin      = strtotime($evenement->heure_fin);
        $pauses   = $this->getPauses();

        while ($debut + $dureeRdv <= $fin) {
            $creneauDebut = $debut;
            $creneauFin   = $debut + $dureeRdv;
            $dansUnePause = false;

            foreach ($pauses as $pause) {
                if ($creneauDebut < $pause['fin'] && $creneauFin > $pause['debut']) {
                    $dansUnePause = true;
                    $debut        = $pause['fin'];
                    break;
                }
            }

            if (!$dansUnePause) {
                $creneaux[] = [
                    'debut' => date('H:i', $creneauDebut),
                    'fin'   => date('H:i', $creneauFin),
                ];
                $debut += $dureeSlot;
            }
        }

        // Supprime les anciens RDV de cet événement
        $participantIds = $participants->pluck('id')->toArray();
        RendezVous::whereIn('id_participant1', $participantIds)
            ->orWhereIn('id_participant2', $participantIds)
            ->delete();

        // Collecte toutes les paires uniques de souhaits
        $souhaitsTraites = [];
        $rdvMutuels      = [];
        $rdvUnilateraux  = [];

        foreach ($participants as $participant) {
            $souhaits = Souhait::where('id_participant', $participant->id)
                ->orderBy('priorite')
                ->get();

            foreach ($souhaits as $souhait) {
                $paire     = collect([
                    $souhait->id_participant,
                    $souhait->id_participant_cible,
                ])->sort()->values()->toArray();
                $cleUnique = $paire[0] . '-' . $paire[1];

                if (in_array($cleUnique, $souhaitsTraites)) continue;
                $souhaitsTraites[] = $cleUnique;

                $souhaitRetour = Souhait::where('id_participant', $souhait->id_participant_cible)
                    ->where('id_participant_cible', $souhait->id_participant)
                    ->first();

                $estMutuel = (bool) $souhaitRetour;

                // ✅ NOUVEAU : score de priorité combiné de la paire
                // (plus petit = plus prioritaire). Pour un souhait
                // mutuel, on additionne les priorités des deux côtés.
                $scorePriorite = $estMutuel
                    ? $souhait->priorite + $souhaitRetour->priorite
                    : $souhait->priorite;

                $entry = [
                    'id_participant1' => $souhait->id_participant,
                    'id_participant2' => $souhait->id_participant_cible,
                    'score_priorite'  => $scorePriorite,
                ];

                if ($estMutuel) {
                    $rdvMutuels[] = $entry;
                } else {
                    $rdvUnilateraux[] = $entry;
                }
            }
        }

        // ✅ NOUVEAU : trie chaque groupe par score de priorité combiné
        // (les souhaits les plus prioritaires sont traités en premier
        // dans le remplissage du planning, peu importe l'ID du participant)
        usort($rdvMutuels, fn($a, $b) => $a['score_priorite'] <=> $b['score_priorite']);
        usort($rdvUnilateraux, fn($a, $b) => $a['score_priorite'] <=> $b['score_priorite']);

        $planning     = array_merge($rdvMutuels, $rdvUnilateraux);
        $date         = $evenement->date_debut;
        $salle        = $evenement->nom_salle;
        $nombreTables = $evenement->nombre_tables;

        $tablesByCreneau = [];

        foreach ($planning as $rdv) {
            foreach ($creneaux as $ci => $creneau) {

                $conflit = RendezVous::where('date', $date)
                    ->where('heure_debut', $creneau['debut'])
                    ->where(function ($q) use ($rdv) {
                        $q->whereIn('id_participant1', [
                                $rdv['id_participant1'],
                                $rdv['id_participant2'],
                            ])
                          ->orWhereIn('id_participant2', [
                                $rdv['id_participant1'],
                                $rdv['id_participant2'],
                            ]);
                    })
                    ->exists();

                if ($conflit) continue;

                $tablesUtilisees = $tablesByCreneau[$ci] ?? [];
                $tableTrouvee    = null;

                for ($t = 1; $t <= $nombreTables; $t++) {
                    if (!in_array($t, $tablesUtilisees)) {
                        $tableTrouvee = $t;
                        break;
                    }
                }

                if ($tableTrouvee === null) continue;

                RendezVous::create([
                    'id_participant1' => $rdv['id_participant1'],
                    'id_participant2' => $rdv['id_participant2'],
                    'salle'           => $salle,
                    'numero_table'    => $tableTrouvee,
                    'date'            => $date,
                    'heure_debut'     => $creneau['debut'],
                    'heure_fin'       => $creneau['fin'],
                    'statut'          => 'planifie',
                ]);

                $tablesByCreneau[$ci][] = $tableTrouvee;
                break;
            }
        }

        $this->closeGenerateModal();
        $this->resetAllPages();

        $nbMutuels     = count($rdvMutuels);
        $nbUnilateraux = count($rdvUnilateraux);
        session()->flash('success',
            "Planning généré dans {$salle} ! "
            . "{$nbMutuels} RDV mutuels + {$nbUnilateraux} RDV unilatéraux. "
            . "Durée : {$evenement->duree_rdv} min / RDV."
        );
    }

    public function render()
    {
        $traducteurs = collect();

        if ($this->rdv_courant) {
            $rdv = $this->rdv_courant;
            $traducteurs_occupes = RendezVous::where('date', $rdv->date)
                ->where('heure_debut', $rdv->heure_debut)
                ->where('id', '!=', $rdv->id)
                ->whereNotNull('id_traducteur')
                ->pluck('id_traducteur')
                ->toArray();

            $traducteurs = Traducteur::orderBy('nom')->get()
                ->map(function ($t) use ($traducteurs_occupes) {
                    $t->disponible = !in_array($t->id, $traducteurs_occupes);
                    return $t;
                });
        }

        $participantsDisponibles = collect();
        if ($this->rematch_rdv) {
            $id_evenement = $this->rematch_rdv->participant1->id_evenement
                ?? $this->rematch_rdv->participant2->id_evenement
                ?? null;

            $participantsDisponibles = Participant::with('entreprise')
                ->when($id_evenement, fn($q) =>
                    $q->where('id_evenement', $id_evenement)
                )
                ->where('participation_rdv', true)
                ->where('id', '!=', $this->rematch_rdv->id_participant1)
                ->where('id', '!=', $this->rematch_rdv->id_participant2)
                ->whereDoesntHave('rendezVous1', fn($q) =>
                    $q->where('date', $this->rematch_rdv->date)
                      ->where('heure_debut', $this->rematch_rdv->heure_debut)
                      ->where('statut', '!=', 'annule')
                )
                ->whereDoesntHave('rendezVous2', fn($q) =>
                    $q->where('date', $this->rematch_rdv->date)
                      ->where('heure_debut', $this->rematch_rdv->heure_debut)
                      ->where('statut', '!=', 'annule')
                )
                ->orderBy('nom')
                ->get();
        }

        // Participants pour le match manuel, filtrés par événement
        $participantsMatchManuel = collect();
        if ($this->match_id_evenement) {
            $participantsMatchManuel = Participant::with('entreprise')
                ->where('id_evenement', $this->match_id_evenement)
                ->where('participation_rdv', true)
                ->orderBy('nom')
                ->get();
        }

        // ── Liste complète filtrée + triée (sert aux stats globales et exports) ──
        $rendezVous = $this->sortCollection(
            $this->buildRendezVousQuery()->latest('id')->get()
        );

        // ── Groupement par événement + pagination indépendante par groupe ──
        $rdvParEvenementBrut = $rendezVous->groupBy(function($rdv) {
            return $rdv->participant1->id_evenement
                ?? $rdv->participant2->id_evenement
                ?? 0;
        });

        $rdvGroupesPagines = [];
        foreach ($rdvParEvenementBrut as $id_evenement => $rdvsGroupe) {
            $pageName = 'page_evt_' . $id_evenement;
            $page     = $this->getPage($pageName);

            $rdvGroupesPagines[$id_evenement] = [
                'tous' => $rdvsGroupe,
                'page' => new LengthAwarePaginator(
                    $rdvsGroupe->forPage($page, $this->perPagePlanning)->values(),
                    $rdvsGroupe->count(),
                    $this->perPagePlanning,
                    $page,
                    [
                        'path'     => Paginator::resolveCurrentPath(),
                        'pageName' => $pageName,
                    ]
                ),
            ];
        }

        // Événement sélectionné pour le "Point des RDV"
        $evenementFiltre = $this->filtre_evenement
            ? Evenement::find($this->filtre_evenement)
            : null;

        return view('livewire.admin.gestion-rendez-vous', [
            'rendezVous'              => $rendezVous,
            'rdvGroupesPagines'       => $rdvGroupesPagines,
            'evenements'              => Evenement::orderBy('nom')->get(),
            'evenementFiltre'         => $evenementFiltre,
            'traducteurs'             => $traducteurs,
            'participantsDisponibles' => $participantsDisponibles,
            'participantsMatchManuel' => $participantsMatchManuel,
            'erreur_rematch'          => $this->erreur_rematch,
        ])->layout('layouts.admin', ['title' => 'Gestion des Rendez-vous']);
    }
}