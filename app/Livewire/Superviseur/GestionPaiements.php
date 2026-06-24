<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Paiement;
use App\Models\Recu;
use App\Mail\PaiementConfirme;
use Illuminate\Support\Facades\Mail;

class GestionPaiements extends Component
{
    public string $search        = '';
    public string $filtre_statut = '';
    public string $filtre_mode   = '';

    public bool $showChequeModal = false;
    public $paiement_cheque      = null;

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

    public function valider(int $id): void
    {
        $paiement = Paiement::with(['recu', 'inscription', 'inscription.participant', 'inscription.evenement'])->findOrFail($id);

        $paiement->update(['statut' => 'valide']);

        if (!$paiement->recu) {
            Recu::create([
                'id_paiement' => $paiement->id,
                'date'        => now()->toDateString(),
                'montant'     => $paiement->montant,
            ]);
        }

        if ($paiement->inscription) {
            $paiement->inscription->update(['statut_paiement' => 'paye']);
        }

        // ✅ ENVOI EMAIL — paiement confirmé
        $participant = $paiement->inscription?->participant;
        $evenement   = $paiement->inscription?->evenement;

        if ($participant?->email && $evenement) {
            try {
                Mail::to($participant->email)->send(
                    new PaiementConfirme($participant, $paiement, $evenement->nom)
                );
            } catch (\Exception $e) {
                // L'email a échoué mais on continue
            }
        }

        $this->fermerChequeModal();
        session()->flash('success', 'Paiement validé et reçu confirmé.');
    }

    public function rejeter(int $id): void
    {
        Paiement::findOrFail($id)->update(['statut' => 'rejete']);
        $this->fermerChequeModal();
        session()->flash('success', 'Paiement rejeté.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-paiements', [
            'paiements' => Paiement::with([
                    'inscription',
                    'inscription.participant',
                    'inscription.participant.entreprise',
                    'inscription.evenement',
                    'recu',
                ])
                ->when($this->filtre_statut, fn($q) =>
                    $q->where('statut', $this->filtre_statut)
                )
                ->when($this->filtre_mode, fn($q) =>
                    $q->where('mode_paiement', $this->filtre_mode)
                )
                ->when($this->search, fn($q) =>
                    $q->whereHas('inscription.participant', fn($q) =>
                        $q->where('nom', 'like', '%' . $this->search . '%')
                          ->orWhere('prenom', 'like', '%' . $this->search . '%')
                    )
                )
                ->latest()
                ->get(),
        ])->layout('layouts.superviseur', ['title' => 'Gestion des Paiements']);
    }
}