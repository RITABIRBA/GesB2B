<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Paiement;
use App\Models\Recu;

class GestionPaiements extends Component
{
    public $search = '';
    public $filtre_statut = '';
    public $filtre_mode = '';

    public function valider($id)
    {
        $paiement = Paiement::findOrFail($id);
        $paiement->update(['statut' => 'valide']);

        // Génère automatiquement un reçu
        Recu::create([
            'id_paiement' => $paiement->id,
            'date'        => now()->toDateString(),
            'montant'     => $paiement->montant,
        ]);

        // Met à jour le statut de l'inscription
        $paiement->inscription->update(['statut_paiement' => 'paye']);

        session()->flash('success', 'Paiement validé et reçu généré.');
    }

    public function rejeter($id)
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
        ])->layout('layouts.admin', ['title' => 'Gestion des Paiements']);
    }
}