<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\Participant;
use App\Models\Souhait;
use App\Models\Traducteur;
use App\Models\Evenement;
use App\Models\Notification;

class GestionRendezVous extends Component
{
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

        RendezVous::create([
            'id_participant1' => $this->match_participant1,
            'id_participant2' => $this->match_participant2,
            'statut'          => 'a_planifier',
        ]);

        // Notifications
        Notification::create([
            'id_participant' => $p1->id,
            'contenu'        => "📅 Un rendez-vous a été organisé par l'administration avec " . ($p2->nom ?? '') . ' ' . ($p2->prenom ?? '') . ".",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        Notification::create([
            'id_participant' => $p2->id,
            'contenu'        => "📅 Un rendez-vous a été organisé par l'administration avec " . ($p1->nom ?? '') . ' ' . ($p1->prenom ?? '') . ".",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        session()->flash('success', 'Match manuel créé avec succès ! Les deux participants ont été notifiés.');
        $this->fermerMatchManuel();
    }

    /**
     * Génère le planning des RDV pour un événement.
     *
     * ALGORITHME :
     * 1. Lit duree_rdv et duree_pause depuis l'événement
     * 2. Calcule tous les créneaux disponibles (heure_debut → heure_fin)
     *    en sautant les pauses configurées
     * 3. Traite d'abord les souhaits MUTUELS (priorité absolue)
     * 4. Puis les souhaits unilatéraux par priorité décroissante
     * 5. Pour chaque paire, cherche un créneau ET une table libre
     * 6. Crée le RDV avec salle, table, date et horaire
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
        // Mutuels en premier, puis unilatéraux
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

                $estMutuel = Souhait::where('id_participant', $souhait->id_participant_cible)
                    ->where('id_participant_cible', $souhait->id_participant)
                    ->exists();

                if ($estMutuel) {
                    $rdvMutuels[] = [
                        'id_participant1' => $souhait->id_participant,
                        'id_participant2' => $souhait->id_participant_cible,
                    ];
                } else {
                    $rdvUnilateraux[] = [
                        'id_participant1' => $souhait->id_participant,
                        'id_participant2' => $souhait->id_participant_cible,
                    ];
                }
            }
        }

        // Mutuels en priorité, puis unilatéraux
        $planning     = array_merge($rdvMutuels, $rdvUnilateraux);
        $date         = $evenement->date_debut;
        $salle        = $evenement->nom_salle;
        $nombreTables = $evenement->nombre_tables;

        // Suivi des tables utilisées par créneau
        $tablesByCreneau = [];

        foreach ($planning as $rdv) {
            foreach ($creneaux as $ci => $creneau) {

                // Vérifie qu'aucun des 2 participants n'est déjà occupé
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

                // Cherche une table libre sur ce créneau
                $tablesUtilisees = $tablesByCreneau[$ci] ?? [];
                $tableTrouvee    = null;

                for ($t = 1; $t <= $nombreTables; $t++) {
                    if (!in_array($t, $tablesUtilisees)) {
                        $tableTrouvee = $t;
                        break;
                    }
                }

                if ($tableTrouvee === null) continue;

                // Crée le RDV
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

        return view('livewire.admin.gestion-rendez-vous', [
            'rendezVous' => RendezVous::with([
                    'participant1', 'participant1.entreprise',
                    'participant2', 'participant2.entreprise',
                    'traducteur', 'participantAbsent',
                ])
                ->when($this->filtre_statut, fn($q) =>
                    $q->where('statut', $this->filtre_statut)
                )
                ->when($this->search, fn($q) =>
                    $q->whereHas('participant1', fn($q) =>
                        $q->where('nom', 'like', '%' . $this->search . '%')
                          ->orWhere('prenom', 'like', '%' . $this->search . '%')
                    )->orWhereHas('participant2', fn($q) =>
                        $q->where('nom', 'like', '%' . $this->search . '%')
                          ->orWhere('prenom', 'like', '%' . $this->search . '%')
                    )
                )
                ->latest()
                ->get(),
            'evenements'              => Evenement::orderBy('nom')->get(),
            'traducteurs'             => $traducteurs,
            'participantsDisponibles' => $participantsDisponibles,
            'participantsMatchManuel' => $participantsMatchManuel,
            'erreur_rematch'          => $this->erreur_rematch,
        ])->layout('layouts.admin', ['title' => 'Gestion des Rendez-vous']);
    }
}