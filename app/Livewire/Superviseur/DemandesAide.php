<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\DemandeAide;
use App\Models\Notification;

class DemandesAide extends Component
{
    public string $filtre_statut = '';

    // ─── Modal "Traiter la demande" ────────────────────────────
    public bool $showTraiterModal  = false;
    public ?int $demande_id        = null;
    public $demande_courante       = null;
    public string $reponse_texte   = '';

    public function ouvrirTraiter(int $id): void
    {
        $this->demande_id       = $id;
        $this->demande_courante = DemandeAide::with(['participant.entreprise', 'evenement'])->findOrFail($id);
        $this->reponse_texte    = '';
        $this->resetErrorBag();
        $this->showTraiterModal = true;
    }

    public function fermerTraiter(): void
    {
        $this->showTraiterModal = false;
        $this->demande_id       = null;
        $this->demande_courante = null;
        $this->reponse_texte    = '';
        $this->resetErrorBag();
    }

    /**
     * Marque la demande comme traitée et enregistre le texte de
     * réponse décrivant l'action effectuée. Le participant est
     * notifié avec ce texte (et non plus un message générique).
     */
    public function confirmerTraiter(): void
    {
        $this->validate([
            'reponse_texte' => 'required|min:5|max:1000',
        ], [
            'reponse_texte.required' => 'Veuillez indiquer ce qui a été fait pour traiter cette demande.',
            'reponse_texte.min'      => 'La réponse est trop courte (5 caractères minimum).',
        ]);

        $demande = DemandeAide::findOrFail($this->demande_id);

        $demande->update([
            'statut'    => 'traite',
            'traite_le' => now(),
            'reponse'   => $this->reponse_texte,
        ]);

        Notification::create([
            'id_participant' => $demande->id_participant,
            'contenu'        => "✅ Votre demande d'aide a été traitée par la supervision : " . $this->reponse_texte,
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        session()->flash('success', 'Demande marquée comme traitée. Le participant a été notifié avec la réponse.');
        $this->fermerTraiter();
    }

    public function render()
    {
        return view('livewire.superviseur.demandes-aide', [
            'demandes' => DemandeAide::with(['participant.entreprise', 'evenement'])
                ->where('destinataire_type', 'admin_superviseur')
                ->when($this->filtre_statut, fn($q) => $q->where('statut', $this->filtre_statut))
                ->latest()
                ->get(),
        ])->layout('layouts.superviseur', ['title' => "Demandes d'aide"]);
    }
}