<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\ChefDelegation;
use App\Models\DemandeAide;

class DemandesAide extends Component
{
    public $cdd = null;
    public string $filtre_statut = '';

    // Modal traiter
    public bool $showTraiterModal   = false;
    public $demande_courante        = null;
    public string $reponse_texte    = '';

    public function mount(): void
    {
        $this->cdd = ChefDelegation::where('user_id', auth()->id())->first();
    }

    public function ouvrirTraiter(int $id): void
    {
        $this->demande_courante = DemandeAide::with(['participant.entreprise', 'evenement'])
            ->findOrFail($id);
        $this->reponse_texte    = '';
        $this->showTraiterModal = true;
    }

    public function fermerTraiter(): void
    {
        $this->showTraiterModal = false;
        $this->demande_courante = null;
        $this->reponse_texte    = '';
    }

    public function confirmerTraiter(): void
    {
        $this->validate([
            'reponse_texte' => 'required|string|min:5',
        ], [
            'reponse_texte.required' => 'Veuillez indiquer ce que vous avez fait.',
            'reponse_texte.min'      => 'Réponse trop courte.',
        ]);

        $demande = DemandeAide::findOrFail($this->demande_courante->id);
        $demande->update([
            'statut'    => 'traite',
            'reponse'   => $this->reponse_texte,
            'traite_le' => now(),
        ]);

        $this->fermerTraiter();
        session()->flash('success', 'Demande traitée avec succès.');
    }

    public function render()
    {
        $demandes = collect();

        if ($this->cdd) {
            $membreIds = $this->cdd->membres()->pluck('id');

            $demandes = DemandeAide::with(['participant.entreprise', 'evenement'])
                ->whereIn('participant_id', $membreIds)
                ->when($this->filtre_statut, fn($q) =>
                    $q->where('statut', $this->filtre_statut)
                )
                ->latest()
                ->get();
        }

        return view('livewire.cdd.demandes-aide', [
            'demandes' => $demandes,
        ])->layout('layouts.cdd', ['title' => "Demandes d'aide de ma délégation"]);
    }
}