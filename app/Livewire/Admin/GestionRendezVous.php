<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\Participant;
use App\Models\Souhait;
use App\Models\Stand;
use App\Models\Traducteur;
use App\Models\Evenement;

class GestionRendezVous extends Component
{
    // =========================================================
    // PROPRIÉTÉS — GÉNÉRATION DU PLANNING
    // =========================================================
    public $id_evenement = '';
    public $duree_rdv = 30;

    // =========================================================
    // PROPRIÉTÉS — RÉSUMÉ AUTOMATIQUE
    // =========================================================

    /** @var object|null Événement sélectionné avec ses infos */
    public $evenement_selectionne = null;

    /** @var int Nombre de créneaux calculés */
    public $nb_creneaux = 0;

    /** @var int Nombre de stands disponibles */
    public $nb_stands = 0;

    /** @var int Nombre de paires à planifier */
    public $nb_paires = 0;

    // =========================================================
    // PROPRIÉTÉS — MODALS
    // =========================================================
    public $showGenerateModal   = false;
    public $showTraducteurModal = false;
    public $rdv_id;
    public $rdv_courant         = null;
    public $id_traducteur       = '';

    // =========================================================
    // PROPRIÉTÉS — FILTRES
    // =========================================================
    public $search        = '';
    public $filtre_statut = '';

    // =========================================================
    // CALCUL AUTOMATIQUE DU RÉSUMÉ
    // Déclenché quand l'événement ou la durée change
    // =========================================================

    /**
     * Recalcule le résumé quand l'événement change.
     */
    public function updatedIdEvenement()
    {
        $this->calculerResume();
    }

    /**
     * Recalcule le résumé quand la durée change.
     */
    public function updatedDureeRdv()
    {
        $this->calculerResume();
    }

    /**
     * Calcule et affiche le résumé du planning avant génération.
     */
    public function calculerResume()
    {
        if (!$this->id_evenement) {
            $this->evenement_selectionne = null;
            $this->nb_creneaux           = 0;
            $this->nb_stands             = 0;
            $this->nb_paires             = 0;
            return;
        }

        $evenement = Evenement::find($this->id_evenement);
        if (!$evenement) return;

        $this->evenement_selectionne = $evenement;

        // Calcule le nombre de créneaux
        $debut        = strtotime($evenement->heure_debut);
        $fin          = strtotime($evenement->heure_fin);
        $duree        = $this->duree_rdv * 60;
        $nb_creneaux  = 0;

        while ($debut + $duree <= $fin) {
            $nb_creneaux++;
            $debut += $duree;
        }

        $this->nb_creneaux = $nb_creneaux;

        // Nombre de stands disponibles
        $this->nb_stands = Stand::where('id_evenement', $this->id_evenement)->count();

        // Nombre de paires uniques à planifier
        $participants    = Participant::where('id_evenement', $this->id_evenement)->pluck('id');
        $souhaitsTraites = [];
        $nb_paires       = 0;

        foreach ($participants as $id_participant) {
            $souhaits = Souhait::where('id_participant', $id_participant)
                               ->orderBy('priorite')
                               ->get();

            foreach ($souhaits as $souhait) {
                $paire     = collect([
                    $souhait->id_participant,
                    $souhait->id_participant_cible
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

    // =========================================================
    // GESTION DU MODAL DE GÉNÉRATION
    // =========================================================

    public function openGenerateModal()
    {
        $this->showGenerateModal     = true;
        $this->id_evenement          = '';
        $this->duree_rdv             = 30;
        $this->evenement_selectionne = null;
        $this->nb_creneaux           = 0;
        $this->nb_stands             = 0;
        $this->nb_paires             = 0;
    }

    public function closeGenerateModal()
    {
        $this->showGenerateModal     = false;
        $this->id_evenement          = '';
        $this->duree_rdv             = 30;
        $this->evenement_selectionne = null;
        $this->nb_creneaux           = 0;
        $this->nb_stands             = 0;
        $this->nb_paires             = 0;
        $this->resetErrorBag();
    }

    // =========================================================
    // GESTION DU MODAL TRADUCTEUR
    // =========================================================

    public function ouvrirModalTraducteur($id)
    {
        $this->rdv_id        = $id;
        $this->id_traducteur = '';
        $this->rdv_courant   = RendezVous::with([
            'participant1',
            'participant1.entreprise',
            'participant2',
            'participant2.entreprise',
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

    // =========================================================
    // ACTIONS SUR LES RENDEZ-VOUS
    // =========================================================

    public function confirmer($id)
    {
        RendezVous::findOrFail($id)->update(['statut' => 'confirme']);
        session()->flash('success', 'Rendez-vous confirmé !');
    }

    public function annuler($id)
    {
        RendezVous::findOrFail($id)->update(['statut' => 'annule']);
        session()->flash('success', 'Rendez-vous annulé !');
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

    // =========================================================
    // GÉNÉRATION DU PLANNING
    // =========================================================

    public function genererPlanning()
    {
        $this->validate([
            'id_evenement' => 'required',
            'duree_rdv'    => 'required|integer|min:15|max:120',
        ]);

        $evenement    = Evenement::findOrFail($this->id_evenement);
        $participants = Participant::where('id_evenement', $this->id_evenement)->get();
        $stands       = Stand::where('id_evenement', $this->id_evenement)->get();

        if ($participants->isEmpty() || $stands->isEmpty()) {
            session()->flash('error', 'Pas assez de participants ou de stands !');
            $this->closeGenerateModal();
            return;
        }

        // Calcule les créneaux depuis les heures de l'événement
        $creneaux = [];
        $debut    = strtotime($evenement->heure_debut);
        $fin      = strtotime($evenement->heure_fin);
        $duree    = $this->duree_rdv * 60;

        while ($debut + $duree <= $fin) {
            $creneaux[] = [
                'debut' => date('H:i', $debut),
                'fin'   => date('H:i', $debut + $duree),
            ];
            $debut += $duree;
        }

        // Supprime les anciens RDV
        $participantIds = $participants->pluck('id')->toArray();
        RendezVous::whereIn('id_participant1', $participantIds)
                  ->orWhereIn('id_participant2', $participantIds)
                  ->delete();

        // Construit le planning
        $souhaitsTraites = [];
        $planning        = [];

        foreach ($participants as $participant) {
            $souhaits = Souhait::where('id_participant', $participant->id)
                               ->orderBy('priorite')
                               ->get();

            foreach ($souhaits as $souhait) {
                $paire     = collect([
                    $souhait->id_participant,
                    $souhait->id_participant_cible
                ])->sort()->values()->toArray();
                $cleUnique = $paire[0] . '-' . $paire[1];

                if (in_array($cleUnique, $souhaitsTraites)) continue;
                $souhaitsTraites[] = $cleUnique;
                $planning[]        = [
                    'id_participant1' => $souhait->id_participant,
                    'id_participant2' => $souhait->id_participant_cible,
                ];
            }
        }

        // Assigne créneaux et stands
        $standIndex   = 0;
        $creneauIndex = 0;
        $date         = $evenement->date_debut;

        foreach ($planning as $rdv) {
            if ($creneauIndex >= count($creneaux)) break;
            if ($standIndex >= count($stands)) $standIndex = 0;

            $conflit = RendezVous::where('date', $date)
                ->where('heure_debut', $creneaux[$creneauIndex]['debut'])
                ->where(function ($q) use ($rdv) {
                    $q->where('id_participant1', $rdv['id_participant1'])
                      ->orWhere('id_participant1', $rdv['id_participant2'])
                      ->orWhere('id_participant2', $rdv['id_participant1'])
                      ->orWhere('id_participant2', $rdv['id_participant2']);
                })->exists();

            if (!$conflit) {
                RendezVous::create([
                    'id_participant1' => $rdv['id_participant1'],
                    'id_participant2' => $rdv['id_participant2'],
                    'id_stand'        => $stands[$standIndex]->id,
                    'date'            => $date,
                    'heure_debut'     => $creneaux[$creneauIndex]['debut'],
                    'heure_fin'       => $creneaux[$creneauIndex]['fin'],
                    'statut'          => 'planifie',
                ]);
                $standIndex++;
                $creneauIndex++;
            }
        }

        $this->closeGenerateModal();
        session()->flash('success', 'Planning généré avec succès !');
    }

    // =========================================================
    // RENDU
    // =========================================================

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

        return view('livewire.admin.gestion-rendez-vous', [
            'rendezVous' => RendezVous::with([
                    'participant1', 'participant1.entreprise',
                    'participant2', 'participant2.entreprise',
                    'stand', 'traducteur',
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
            'evenements'  => Evenement::orderBy('nom')->get(),
            'traducteurs' => $traducteurs,
        ])->layout('layouts.admin', ['title' => 'Gestion des Rendez-vous']);
    }
}