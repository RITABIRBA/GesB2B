<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\Participant;
use App\Models\Souhait;
use App\Models\Traducteur;
use App\Models\Evenement;

class GestionRendezVous extends Component
{
    // PROPRIÉTÉS — GÉNÉRATION DU PLANNING
    public $id_evenement = '';
    public $duree_rdv = 30;

    // PROPRIÉTÉS — PAUSES
    public $pause_cafe_matin       = false;
    public $pause_cafe_matin_debut = '10:00';
    public $pause_cafe_matin_fin   = '10:15';
    public $pause_dejeuner         = false;
    public $pause_dejeuner_debut   = '12:00';
    public $pause_dejeuner_fin     = '14:00';
    public $pause_cafe_aprem       = false;
    public $pause_cafe_aprem_debut = '15:30';
    public $pause_cafe_aprem_fin   = '15:45';

    // PROPRIÉTÉS — RÉSUMÉ
    public $evenement_selectionne = null;
    public $nb_creneaux  = 0;
    public $nb_tables    = 0;
    public $nb_paires    = 0;

    // PROPRIÉTÉS — MODALS
    public $showGenerateModal   = false;
    public $showTraducteurModal = false;
    public $showRematchModal    = false;
    public $showAnnulerModal    = false;
    public $rdv_id;
    public $rdv_courant         = null;
    public $id_traducteur       = '';

    // PROPRIÉTÉS — ANNULATION
    public $annuler_rdv_id      = null;
    public $annuler_rdv         = null;
    public $absent_id           = '';

    // PROPRIÉTÉS — RE-MATCH
    public $rematch_rdv_id     = null;
    public $rematch_rdv        = null;
    public $nouveau_participant = '';
    public $erreur_rematch      = ''; // ← nouveau

    // PROPRIÉTÉS — FILTRES
    public $search        = '';
    public $filtre_statut = '';

    
    // CALCUL RÉSUMÉ
    

    public function updatedIdEvenement()
    {
        $this->calculerResume();
    }

    public function updatedDureeRdv()
    {
        $this->calculerResume();
    }

    public function calculerResume()
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

        $debut       = strtotime($evenement->heure_debut);
        $fin         = strtotime($evenement->heure_fin);
        $duree       = $this->duree_rdv * 60;
        $nb_creneaux = 0;
        $pauses      = $this->getPauses();

        while ($debut + $duree <= $fin) {
            $creneauDebut = $debut;
            $creneauFin   = $debut + $duree;
            $dansUnePause = false;

            foreach ($pauses as $pause) {
                if ($creneauDebut < $pause['fin'] && $creneauFin > $pause['debut']) {
                    $dansUnePause = true;
                    $debut = $pause['fin'];
                    break;
                }
            }

            if (!$dansUnePause) {
                $nb_creneaux++;
                $debut += $duree;
            }
        }

        $this->nb_creneaux = $nb_creneaux;
        $this->nb_tables   = $evenement->nombre_tables ?? 0;

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

    
    // HELPER PAUSES
    

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

    
    // MODAL GÉNÉRATION
    

    public function openGenerateModal()
    {
        $this->showGenerateModal     = true;
        $this->id_evenement          = '';
        $this->duree_rdv             = 30;
        $this->evenement_selectionne = null;
        $this->nb_creneaux           = 0;
        $this->nb_tables             = 0;
        $this->nb_paires             = 0;
        $this->pause_cafe_matin      = false;
        $this->pause_dejeuner        = false;
        $this->pause_cafe_aprem      = false;
    }

    public function closeGenerateModal()
    {
        $this->showGenerateModal     = false;
        $this->id_evenement          = '';
        $this->duree_rdv             = 30;
        $this->evenement_selectionne = null;
        $this->nb_creneaux           = 0;
        $this->nb_tables             = 0;
        $this->nb_paires             = 0;
        $this->pause_cafe_matin      = false;
        $this->pause_dejeuner        = false;
        $this->pause_cafe_aprem      = false;
        $this->resetErrorBag();
    }

    
    // MODAL TRADUCTEUR
    

    public function ouvrirModalTraducteur($id)
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

    public function fermerModalTraducteur()
    {
        $this->showTraducteurModal = false;
        $this->rdv_id              = null;
        $this->rdv_courant         = null;
        $this->id_traducteur       = '';
    }

    public function assignerTraducteur()
    {
        $this->validate(['id_traducteur' => 'required']);
        RendezVous::findOrFail($this->rdv_id)->update(['id_traducteur' => $this->id_traducteur]);
        $this->fermerModalTraducteur();
        session()->flash('success', 'Traducteur assigné avec succès !');
    }

    // MODAL ANNULATION

    public function ouvrirModalAnnuler($id)
    {
        $this->annuler_rdv_id = $id;
        $this->absent_id      = '';
        $this->annuler_rdv    = RendezVous::with([
            'participant1', 'participant1.entreprise',
            'participant2', 'participant2.entreprise',
        ])->findOrFail($id);
        $this->showAnnulerModal = true;
    }

    public function fermerModalAnnuler()
    {
        $this->showAnnulerModal = false;
        $this->annuler_rdv_id  = null;
        $this->annuler_rdv     = null;
        $this->absent_id       = '';
    }

    public function confirmerAnnulation()
    {
        $this->validate([
            'absent_id' => 'required',
        ], [
            'absent_id.required' => 'Veuillez indiquer qui est absent.',
        ]);

        RendezVous::findOrFail($this->annuler_rdv_id)->update([
            'statut'                => 'annule',
            'absent_participant_id' => $this->absent_id,
        ]);

        $this->fermerModalAnnuler();
        session()->flash('success', 'Rendez-vous annulé. Vous pouvez effectuer un re-match.');
    }

    
    // RE-MATCH
    

    public function ouvrirRematch($id)
    {
        $this->rematch_rdv_id     = $id;
        $this->nouveau_participant = '';
        $this->erreur_rematch      = '';
        $this->rematch_rdv        = RendezVous::with([
            'participant1', 'participant1.entreprise',
            'participant2', 'participant2.entreprise',
            'participantAbsent',
        ])->findOrFail($id);
        $this->showRematchModal = true;
    }

    public function fermerRematch()
    {
        $this->showRematchModal    = false;
        $this->rematch_rdv_id      = null;
        $this->rematch_rdv         = null;
        $this->nouveau_participant  = '';
        $this->erreur_rematch       = '';
    }

    public function effectuerRematch()
    {
        $this->validate(['nouveau_participant' => 'required']);

        $rdv = RendezVous::findOrFail($this->rematch_rdv_id);

        // ← Vérifie que le remplaçant n'a pas déjà un RDV
        // sur le même créneau horaire
        $conflit = RendezVous::where('date', $rdv->date)
            ->where('heure_debut', $rdv->heure_debut)
            ->where('id', '!=', $rdv->id) // ← exclut le RDV annulé
            ->where(function($q) {
                $q->where('id_participant1', $this->nouveau_participant)
                  ->orWhere('id_participant2', $this->nouveau_participant);
            })
            ->exists();

        if ($conflit) {
            // ← Trouve le nom du participant pour le message
            $participant = Participant::find($this->nouveau_participant);
            $this->erreur_rematch = ($participant->nom ?? '') . ' ' .
                ($participant->prenom ?? '') .
                ' a déjà un RDV sur ce créneau (' .
                $rdv->heure_debut . ' - ' . $rdv->heure_fin .
                '). Choisissez un autre participant !';
            return;
        }

        // ← Pas de conflit → effectue le re-match
        $this->erreur_rematch = '';

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

        session()->flash('success', 'Re-match effectué ! Le RDV est rétabli.');
        $this->fermerRematch();
    }

   
    // ACTIONS SUR LES RDV
    

    public function confirmer($id)
    {
        RendezVous::findOrFail($id)->update(['statut' => 'confirme']);
        session()->flash('success', 'Rendez-vous confirmé !');
    }

    public function terminer($id)
    {
        RendezVous::findOrFail($id)->update(['statut' => 'termine']);
        session()->flash('success', 'Rendez-vous terminé !');
    }

    public function supprimer($id)
    {
        RendezVous::findOrFail($id)->delete();
        session()->flash('success', 'Rendez-vous supprimé.');
    }

    
    // GÉNÉRATION DU PLANNING
    
    public function genererPlanning()
    {
        $this->validate([
            'id_evenement' => 'required',
            'duree_rdv'    => 'required|integer|min:15|max:120',
        ]);

        $evenement = Evenement::findOrFail($this->id_evenement);

        if (!$evenement->nom_salle || !$evenement->nombre_tables) {
            session()->flash('error',
                'Veuillez d\'abord définir la salle et le nombre de tables dans la gestion des événements !'
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

        $creneaux = [];
        $debut    = strtotime($evenement->heure_debut);
        $fin      = strtotime($evenement->heure_fin);
        $duree    = $this->duree_rdv * 60;
        $pauses   = $this->getPauses();

        while ($debut + $duree <= $fin) {
            $creneauDebut = $debut;
            $creneauFin   = $debut + $duree;
            $dansUnePause = false;

            foreach ($pauses as $pause) {
                if ($creneauDebut < $pause['fin'] && $creneauFin > $pause['debut']) {
                    $dansUnePause = true;
                    $debut = $pause['fin'];
                    break;
                }
            }

            if (!$dansUnePause) {
                $creneaux[] = [
                    'debut' => date('H:i', $creneauDebut),
                    'fin'   => date('H:i', $creneauFin),
                ];
                $debut += $duree;
            }
        }

        $participantIds = $participants->pluck('id')->toArray();
        RendezVous::whereIn('id_participant1', $participantIds)
                  ->orWhereIn('id_participant2', $participantIds)
                  ->delete();

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

        $planning        = array_merge($rdvMutuels, $rdvUnilateraux);
        $date            = $evenement->date_debut;
        $salle           = $evenement->nom_salle;
        $nombreTables    = $evenement->nombre_tables;
        $tablesByCreneau = [];

        foreach ($planning as $rdv) {
            for ($ci = 0; $ci < count($creneaux); $ci++) {
                $tablesUtilisees = $tablesByCreneau[$ci] ?? [];

                $conflit = RendezVous::where('date', $date)
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
                    ->where('heure_debut', $creneaux[$ci]['debut'])
                    ->exists();

                if ($conflit) continue;

                $tableTrouvee = null;
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
                    'heure_debut'     => $creneaux[$ci]['debut'],
                    'heure_fin'       => $creneaux[$ci]['fin'],
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
            "Planning généré dans {$salle} ! {$nbMutuels} RDV mutuels + {$nbUnilateraux} RDV unilatéraux."
        );
    }

    
    // RENDU
   
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

            $traducteurs = Traducteur::orderBy('nom')->get()->map(function($t) use ($traducteurs_occupes) {
                $t->disponible = !in_array($t->id, $traducteurs_occupes);
                return $t;
            });
        }

        $participantsDisponibles = collect();
        if ($this->rematch_rdv) {
            $id_evenement = $this->rematch_rdv->participant1->id_evenement
                         ?? $this->rematch_rdv->participant2->id_evenement
                         ?? null;

            // ← Exclut les participants qui ont déjà un RDV
            // sur le même créneau que le RDV annulé
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

        return view('livewire.admin.gestion-rendez-vous', [
            'rendezVous' => RendezVous::with([
                    'participant1', 'participant1.entreprise',
                    'participant2', 'participant2.entreprise',
                    'traducteur', 'participantAbsent',
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
            'evenements'              => Evenement::orderBy('nom')->get(),
            'traducteurs'             => $traducteurs,
            'participantsDisponibles' => $participantsDisponibles,
            'erreur_rematch'          => $this->erreur_rematch,
        ])->layout('layouts.admin', ['title' => 'Gestion des Rendez-vous']);
    }
}