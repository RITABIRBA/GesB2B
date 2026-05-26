<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Recu;
use App\Models\Evenement;

/**
 * Gestion des inscriptions par le CDD
 *
 * Flux correct selon le cahier des charges :
 * 1. Participant fait une préinscription
 * 2. CDD valide la préinscription
 * 3. Participant paie
 * 4. CDD confirme le paiement → reçu généré
 */
class GestionInscriptions extends Component
{
    public $search = '';
    public $filtre_statut = '';
    public $filtre_paiement = '';

    // =========================================================
    // ÉTAPE 1 — VALIDATION DE LA PRÉINSCRIPTION
    // =========================================================

    public function validerInscription($id)
    {
        Inscription::findOrFail($id)->update([
            'statut_presence' => 'present',
        ]);
        session()->flash('success', 'Préinscription validée ! Le participant peut maintenant payer.');
    }

    public function rejeterInscription($id)
    {
        Inscription::findOrFail($id)->update([
            'statut_paiement' => 'annule',
            'statut_presence'  => 'absent',
        ]);
        session()->flash('success', 'Inscription rejetée.');
    }

    // =========================================================
    // ÉTAPE 2 — VALIDATION DU PAIEMENT
    // =========================================================

    public function validerPaiement($id)
    {
        $inscription = Inscription::with('paiement')->findOrFail($id);

        if (!$inscription->paiement) {
            session()->flash('error', 'Aucun paiement soumis pour cette inscription.');
            return;
        }

        $inscription->paiement->update(['statut' => 'valide']);
        $inscription->update(['statut_paiement' => 'paye']);

        Recu::create([
            'id_paiement' => $inscription->paiement->id,
            'date'        => now()->toDateString(),
            'montant'     => $inscription->paiement->montant,
        ]);

        session()->flash('success', 'Paiement validé et reçu généré !');
    }

    public function rejeterPaiement($id)
    {
        $inscription = Inscription::with('paiement')->findOrFail($id);

        if ($inscription->paiement) {
            $inscription->paiement->update(['statut' => 'rejete']);
        }

        session()->flash('success', 'Paiement rejeté.');
    }

    public function render()
    {
        // Filtre par CDD connecté
        $cddId = auth()->id();

        return view('livewire.cdd.gestion-inscriptions', [
            'inscriptions' => Inscription::with([
                    'participant',
                    'participant.entreprise',
                    'evenement',
                    'paiement',
                    'paiement.recu',
                ])
                ->whereHas('participant', fn($q) =>
                    $q->where('id_cdd', $cddId)
                )
                ->when($this->filtre_statut, fn($q) =>
                    $q->where('statut_presence', $this->filtre_statut)
                )
                ->when($this->filtre_paiement, fn($q) =>
                    $q->where('statut_paiement', $this->filtre_paiement)
                )
                ->when($this->search, fn($q) =>
                    $q->whereHas('participant', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )
                )
                ->latest()
                ->get(),
            'evenements' => Evenement::orderBy('nom')->get(),
        ])->layout('layouts.cdd', ['title' => 'Gestion des Inscriptions']);
    }
}