<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Paiement;
use App\Models\Recu;

class GestionPaiements extends Component
{
    public string $search        = '';
    public string $filtre_statut = '';
    public string $filtre_mode   = '';

    public function valider(int $id): void
    {
        $paiement = Paiement::with(['recu', 'inscription'])->findOrFail($id);

        // ← Met à jour le statut du paiement
        $paiement->update(['statut' => 'valide']);

        // ← Crée le reçu seulement s'il n'existe pas déjà
        if (!$paiement->recu) {
            Recu::create([
                'id_paiement' => $paiement->id,
                'date'        => now()->toDateString(),
                'montant'     => $paiement->montant,
            ]);
        }

        // ← Met à jour le statut de l'inscription
        if ($paiement->inscription) {
            $paiement->inscription->update(['statut_paiement' => 'paye']);
        }

        session()->flash('success', 'Paiement validé et reçu confirmé.');
    }

    public function rejeter(int $id): void
    {
        Paiement::findOrFail($id)->update(['statut' => 'rejete']);
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
        ])->layout('layouts.admin', ['title' => 'Gestion des Paiements']);
    }
}