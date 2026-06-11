<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\Stand;
use App\Models\RendezVous;
use App\Models\Evenement;
use App\Models\Inscription;
use App\Models\Notification;
use App\Models\Paiement;

class Dashboard extends Component
{
    
    // MODAL PAIEMENT LIGDICASH
    

    public bool   $showModalPaiement  = false;
    public string $mode_paiement      = 'orange_money';
    public string $telephone_paiement = '';
    public string $otp_code           = '';
    public string $otp_saisi          = '';
    public int    $etape_paiement     = 1;
    public float  $montant_paiement   = 0;
    public $inscription_id            = null;

    
    // MODAL PAIEMENT
    

    public function openModalPaiement(): void
    {
        // ← Récupère le montant depuis la première inscription en attente
        $entreprise = Entreprise::where('email_responsable', auth()->user()->email)->first();
        $representant = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)
                ->where('role', 'representant')
                ->first()
            : null;

        if ($representant) {
            $inscription = Inscription::where('id_participant', $representant->id)
                ->where('statut_paiement', 'en_attente')
                ->with('evenement')
                ->first();

            if ($inscription) {
                $this->inscription_id   = $inscription->id;
                $this->montant_paiement = $inscription->evenement->montant_inscription ?? 0;
            }
        }

        $this->etape_paiement     = 1;
        $this->mode_paiement      = 'orange_money';
        $this->telephone_paiement = '';
        $this->otp_saisi          = '';
        $this->otp_code           = '';
        $this->showModalPaiement  = true;
        $this->resetErrorBag();
    }

    public function closeModalPaiement(): void
    {
        $this->showModalPaiement = false;
        $this->etape_paiement    = 1;
    }

    public function envoyerOtp(): void
    {
        $this->validate([
            'telephone_paiement' => 'required|string|min:8|max:15',
        ]);
        $this->otp_code       = (string) rand(100000, 999999);
        $this->etape_paiement = 2;
    }

    public function confirmerOtp(): void
    {
        $this->validate(['otp_saisi' => 'required|string']);

        if ($this->otp_saisi != $this->otp_code) {
            $this->addError('otp_saisi', 'Code OTP incorrect.');
            return;
        }

        $this->enregistrerPaiement();
    }

    /**
     * Enregistre le paiement LigdiCash.
     *
     * NOTE POUR L'INTÉGRATION RÉELLE :
     * Remplacer cette méthode par un appel API LigdiCash.
     * Documentation : https://developers.ligdicash.com
     *
     * Exemple d'appel API :
     * POST https://api.ligdicash.com/pay/v1/gate/push-in/plain/execute
     * Headers : Authorization: Bearer {token}
     * Body : { amount, customer_phone_number, otp, ... }
     */
    private function enregistrerPaiement(): void
    {
        if ($this->inscription_id) {
            $inscription = Inscription::findOrFail($this->inscription_id);

            Paiement::create([
                'id_inscription' => $this->inscription_id,
                'montant'        => $this->montant_paiement,
                'date_paiement'  => now()->toDateString(),
                'mode_paiement'  => 'ligdicash_' . $this->mode_paiement,
                'statut'         => 'en_attente',
            ]);
        }

        $this->closeModalPaiement();
        session()->flash('success', 'Paiement LigdiCash soumis ! En attente de confirmation.');
    }

    
    // RENDER
    

    public function render()
    {
        // ← Récupère l'entreprise du représentant connecté
        $entreprise = Entreprise::where('email_responsable', auth()->user()->email)->first();

        // ← Récupère le représentant connecté
        $representant = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)
                ->where(function ($q) {
                    $q->where('email', auth()->user()->email)
                      ->orWhere('role', 'representant');
                })
                ->first()
            : null;

        // ← Statistiques
        $totalMembres = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)->count()
            : 0;

        $totalStands = $entreprise
            ? Stand::where('id_entreprise', $entreprise->id)->count()
            : 0;

        $participantIds = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)->pluck('id')
            : collect();

        $totalRdv = $participantIds->isNotEmpty()
            ? RendezVous::where(function ($q) use ($participantIds) {
                $q->whereIn('id_participant1', $participantIds)
                  ->orWhereIn('id_participant2', $participantIds);
            })->count()
            : 0;

        // ← Derniers membres acceptés
        $derniersMembres = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)
                ->whereIn('statut_adhesion', ['accepte', null])
                ->latest()
                ->take(5)
                ->get()
            : collect();

        // ← Demandes d'adhésion en attente
        $demandesEnAttente = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)
                ->where('statut_adhesion', 'en_attente')
                ->count()
            : 0;

        // ← Notifications non lues pour le représentant
        $notifications = $representant
            ? Notification::where('id_participant', $representant->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
            : collect();

        // ← Vérifie si l'entreprise est validée et a des paiements en attente
        $paiementEnAttente = false;
        $montantPaiement   = 0;

        if ($entreprise && $entreprise->statut_validation == 'valide' && $representant) {
            $inscriptionEnAttente = Inscription::where('id_participant', $representant->id)
                ->where('statut_paiement', 'en_attente')
                ->with('evenement')
                ->first();

            if ($inscriptionEnAttente && $inscriptionEnAttente->evenement?->type_paiement != 'gratuit') {
                $paiementEnAttente = true;
                $montantPaiement   = $inscriptionEnAttente->evenement->montant_inscription ?? 0;
            }
        }

        $today = now()->toDateString();

        // ← Événements disponibles
        $evenementsDisponibles = Evenement::where('date_fin', '>=', $today)
            ->where(fn($q) =>
                $q->whereNull('date_ouverture_inscriptions')
                  ->orWhere('date_ouverture_inscriptions', '<=', $today)
            )
            ->where(fn($q) =>
                $q->whereNull('date_cloture_inscriptions')
                  ->orWhere('date_cloture_inscriptions', '>=', $today)
            )
            ->orderBy('date_debut')
            ->get()
            ->map(function ($evenement) use ($representant) {
                $evenement->deja_inscrit = $representant
                    ? Inscription::where('id_participant', $representant->id)
                        ->where('id_evenement', $evenement->id)
                        ->exists()
                    : false;
                return $evenement;
            });

        return view('livewire.entreprise.dashboard', [
            'entreprise'            => $entreprise,
            'representant'          => $representant,
            'totalMembres'          => $totalMembres,
            'totalStands'           => $totalStands,
            'totalRdv'              => $totalRdv,
            'derniersMembres'       => $derniersMembres,
            'demandesEnAttente'     => $demandesEnAttente,
            'notifications'         => $notifications,
            'paiementEnAttente'     => $paiementEnAttente,
            'montantPaiement'       => $montantPaiement,
            'evenementsDisponibles' => $evenementsDisponibles,
        ])->layout('layouts.entreprise', ['title' => 'Dashboard Entreprise']);
    }
}