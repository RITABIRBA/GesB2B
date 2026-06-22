<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Paiement;
use App\Models\Notification;

class VoirPaiements extends Component
{
    public $search        = '';
    public $filtre_statut = '';
    public $filtre_mode   = '';

    public bool $showValiderModal  = false;
    public $paiement_id            = null;
    public $paiement_courant       = null;

    // ✅ NOUVEAU : modal chèque
    public bool $showChequeModal   = false;
    public $paiement_cheque        = null;

    public function voirCheque(int $id): void
    {
        $this->paiement_cheque = Paiement::with([
            'inscription', 'inscription.participant', 'inscription.evenement',
        ])->findOrFail($id);
        $this->showChequeModal = true;
    }

    public function fermerChequeModal(): void
    {
        $this->showChequeModal = false;
        $this->paiement_cheque = null;
    }

    public function ouvrirValider(int $id): void
    {
        $this->paiement_id      = $id;
        $this->paiement_courant = Paiement::with([
            'inscription',
            'inscription.participant',
            'inscription.participant.entreprise',
            'inscription.evenement',
        ])->findOrFail($id);
        $this->showValiderModal = true;
    }

    public function fermerValider(): void
    {
        $this->showValiderModal = false;
        $this->paiement_id      = null;
        $this->paiement_courant = null;
    }

    public function confirmerValider(): void
    {
        $paiement = Paiement::findOrFail($this->paiement_id);

        $paiement->update(['statut' => 'valide']);

        if ($paiement->inscription) {
            $paiement->inscription->update(['statut_paiement' => 'paye']);

            Notification::create([
                'id_participant' => $paiement->inscription->id_participant,
                'contenu'        => "✅ Votre paiement de {$paiement->montant} FCFA a été validé par la supervision.",
                'date_envoie'    => now()->toDateString(),
                'type'           => 'systeme',
            ]);
        }

        $this->fermerValider();
        $this->fermerChequeModal();
        session()->flash('success', 'Paiement validé. Le participant a été notifié.');
    }

    public function rejeterPaiement(int $id): void
    {
        $paiement = Paiement::findOrFail($id);
        $paiement->update(['statut' => 'rejete']);

        if ($paiement->inscription) {
            Notification::create([
                'id_participant' => $paiement->inscription->id_participant,
                'contenu'        => "❌ Votre paiement a été rejeté par la supervision. Veuillez contacter l'administration.",
                'date_envoie'    => now()->toDateString(),
                'type'           => 'systeme',
            ]);
        }

        $this->fermerChequeModal();
        session()->flash('success', 'Paiement rejeté. Le participant a été notifié.');
    }

    public function render()
    {
        return view('livewire.superviseur.voir-paiements', [
            'paiements' => Paiement::with([
                    'inscription',
                    'inscription.participant',
                    'inscription.participant.entreprise',
                    'inscription.evenement',
                    'recu',
                ])
                ->when($this->filtre_statut, fn($q) => $q->where('statut', $this->filtre_statut))
                ->when($this->filtre_mode, fn($q) => $q->where('mode_paiement', $this->filtre_mode))
                ->when($this->search, fn($q) =>
                    $q->whereHas('inscription.participant', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )
                )
                ->latest()
                ->get(),
        ])->layout('layouts.superviseur', ['title' => 'Paiements']);
    }
}