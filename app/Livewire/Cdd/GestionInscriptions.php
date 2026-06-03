<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Recu;
use App\Models\Badge;
use App\Models\TypeBadge;
use App\Models\Evenement;
use Illuminate\Support\Str;

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
        $inscription = Inscription::with([
            'paiement',
            'participant',
            'evenement',
        ])->findOrFail($id);

        if (!$inscription->paiement) {
            session()->flash('error', 'Aucun paiement soumis pour cette inscription.');
            return;
        }

        // 1. Valide le paiement
        $inscription->paiement->update(['statut' => 'valide']);
        $inscription->update(['statut_paiement' => 'paye']);

        // 2. Génère le reçu automatiquement
        Recu::create([
            'id_paiement' => $inscription->paiement->id,
            'date'        => now()->toDateString(),
            'montant'     => $inscription->paiement->montant,
        ]);

        // 3. Génère le badge automatiquement
        $this->genererBadge($inscription);

        session()->flash('success', 'Paiement validé ! Reçu et badge générés automatiquement !');
    }

    /**
     * Génère automatiquement le badge
     * après validation du paiement
     */
    private function genererBadge(Inscription $inscription)
    {
        $participant = $inscription->participant;

        if (!$participant) return;

        // Vérifie si badge existe déjà
        $badgeExiste = Badge::where('id_participant', $participant->id)->exists();
        if ($badgeExiste) return;

        // Type de badge selon le rôle du participant
        $libelle = match($participant->role) {
            'vip'          => 'VIP',
            'organisateur' => 'Organisateur',
            'exposant'     => 'Exposant',
            default        => 'Visiteur',
        };

        // Trouve ou crée le type de badge
        $typeBadge = TypeBadge::firstOrCreate(
            ['libelle' => $libelle],
            ['description' => 'Badge ' . $libelle]
        );

        // Génère un QR code unique
        $qrCode = strtoupper(
            substr($participant->nom, 0, 2) .
            substr($participant->prenom ?? 'XX', 0, 2) .
            '-' .
            $inscription->id_evenement .
            '-' .
            Str::random(6)
        );

        // Crée le badge
        Badge::create([
            'id_participant' => $participant->id,
            'id_type_badge'  => $typeBadge->id,
            'qr_code'        => $qrCode,
        ]);
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