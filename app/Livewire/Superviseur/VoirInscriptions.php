<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Inscription;
use App\Models\Evenement;
use App\Models\Notification;

class VoirInscriptions extends Component
{
    public $search           = '';
    public $filtre_statut    = '';
    public $filtre_evenement = '';

    // ─── Validation/Rejet ─────────────────────────────────
    public $showValiderModal  = false;
    public $showRejeterModal  = false;
    public $inscription_id    = null;
    public $inscription_courante = null;
    public $motif_rejet       = '';

    public function ouvrirValider(int $id): void
    {
        $this->inscription_id       = $id;
        $this->inscription_courante = Inscription::with([
            'participant', 'participant.entreprise', 'evenement'
        ])->findOrFail($id);
        $this->showValiderModal = true;
    }

    public function fermerValider(): void
    {
        $this->showValiderModal     = false;
        $this->inscription_id       = null;
        $this->inscription_courante = null;
    }

    public function confirmerValider(): void
    {
        $inscription = Inscription::findOrFail($this->inscription_id);

        $inscription->update(['statut_paiement' => 'valide']);

        Notification::create([
            'id_participant' => $inscription->id_participant,
            'contenu'        => "✅ Votre inscription à {$inscription->evenement->nom} a été validée par la supervision.",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        $this->fermerValider();
        session()->flash('success', 'Inscription validée. Le participant a été notifié.');
    }

    public function ouvrirRejeter(int $id): void
    {
        $this->inscription_id       = $id;
        $this->motif_rejet          = '';
        $this->inscription_courante = Inscription::with([
            'participant', 'participant.entreprise', 'evenement'
        ])->findOrFail($id);
        $this->showRejeterModal = true;
    }

    public function fermerRejeter(): void
    {
        $this->showRejeterModal     = false;
        $this->inscription_id       = null;
        $this->inscription_courante = null;
        $this->motif_rejet          = '';
    }

    public function confirmerRejeter(): void
    {
        $this->validate([
            'motif_rejet' => 'required|min:5',
        ], [
            'motif_rejet.required' => 'Veuillez indiquer le motif du rejet.',
            'motif_rejet.min'      => 'Le motif est trop court (5 caractères minimum).',
        ]);

        $inscription = Inscription::findOrFail($this->inscription_id);

        $inscription->update(['statut_paiement' => 'rejete']);

        Notification::create([
            'id_participant' => $inscription->id_participant,
            'contenu'        => "❌ Votre inscription à {$inscription->evenement->nom} a été rejetée. Motif : {$this->motif_rejet}",
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        $this->fermerRejeter();
        session()->flash('success', 'Inscription rejetée. Le participant a été notifié.');
    }

    public function render()
    {
        return view('livewire.superviseur.voir-inscriptions', [
            'inscriptions' => Inscription::with([
                    'participant',
                    'participant.entreprise',
                    'evenement',
                    'paiement',
                ])
                ->when($this->filtre_statut, fn($q) =>
                    $q->where('statut_paiement', $this->filtre_statut)
                )
                ->when($this->filtre_evenement, fn($q) =>
                    $q->where('id_evenement', $this->filtre_evenement)
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
        ])->layout('layouts.superviseur', ['title' => 'Inscriptions']);
    }
}