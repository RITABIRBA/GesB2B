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
use App\Models\Recu;

class Dashboard extends Component
{
    public bool   $showModalPaiement  = false;
    public string $mode_paiement      = 'orange_money';
    public string $telephone_paiement = '';
    public string $otp_code           = '';
    public string $otp_saisi          = '';
    public int    $etape_paiement     = 1;
    public float  $montant_paiement   = 0;
    public int    $inscription_id     = 0;

    public string $alertSuccess = '';
    public string $alertError   = '';

    public function openModalPaiement(): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

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
            } else {
                $this->inscription_id   = 0;
                $this->montant_paiement = 0;
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
        $this->inscription_id    = 0;
        $this->otp_code          = '';
        $this->otp_saisi         = '';
    }

    public function envoyerOtp(): void
    {
        $this->validate([
            'telephone_paiement' => 'required|string|min:8|max:15',
        ], [
            'telephone_paiement.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone_paiement.min'      => 'Numéro trop court.',
        ]);

        $this->otp_code       = (string) rand(100000, 999999);
        $this->etape_paiement = 2;
    }

    /**
     * Vérifie l'OTP et enregistre le paiement LigdiCash.
     *
     * NOTE INTÉGRATION RÉELLE LIGDICASH :
     * POST https://api.ligdicash.com/pay/v1/gate/push-in/plain/execute
     * Headers : Authorization: Bearer {apiToken}
     * Body    : { amount, customer_phone_number, otp, description }
     * Docs    : https://developers.ligdicash.com
     */
    public function confirmerOtp(): void
    {
        $this->validate([
            'otp_saisi' => 'required|string',
        ]);

        if (trim($this->otp_saisi) !== trim($this->otp_code)) {
            $this->addError('otp_saisi', 'Code OTP incorrect. Essayez encore.');
            return;
        }

        if (!$this->inscription_id) {
            $this->alertError = 'Aucune inscription en attente de paiement trouvée.';
            $this->closeModalPaiement();
            return;
        }

        try {
            $paiement = Paiement::create([
                'id_inscription' => $this->inscription_id,
                'montant'        => $this->montant_paiement,
                'date_paiement'  => now()->toDateString(),
                'mode_paiement'  => 'ligdicash_' . $this->mode_paiement,
                'statut'         => 'en_attente',
            ]);

            Recu::create([
                'id_paiement' => $paiement->id,
                'montant'     => $this->montant_paiement,
                'date'        => now()->toDateString(),
            ]);

            $this->closeModalPaiement();

            $this->alertSuccess = 'Paiement LigdiCash soumis ! Reçu généré : REC-'
                . str_pad($paiement->id, 6, '0', STR_PAD_LEFT);

        } catch (\Exception $e) {
            $this->alertError = 'Erreur : ' . $e->getMessage();
        }
    }

    public function render()
    {
        $entreprise = Entreprise::where('email_responsable', auth()->user()->email)->first();

        $representant = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)
                ->where(function ($q) {
                    $q->where('email', auth()->user()->email)
                      ->orWhere('role', 'representant');
                })
                ->first()
            : null;

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

        $derniersMembres = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)
                ->whereIn('statut_adhesion', ['accepte', null])
                ->latest()
                ->take(5)
                ->get()
            : collect();

        $demandesEnAttente = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)
                ->where('statut_adhesion', 'en_attente')
                ->count()
            : 0;

        $notifications = $representant
            ? Notification::where('id_participant', $representant->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
            : collect();

        // Logique paiement et reçu
        $paiementEnAttente = false;
        $montantPaiement   = 0;
        $recuPaiement      = null;
        $statutPaiement    = null;

        if ($entreprise && $entreprise->statut_validation == 'valide' && $representant) {

            $inscription = Inscription::where('id_participant', $representant->id)
                ->whereIn('statut_paiement', ['en_attente', 'paye'])
                ->with('evenement')
                ->latest()
                ->first();

            if ($inscription && $inscription->evenement?->type_paiement != 'gratuit') {

                $dernierPaiement = Paiement::where('id_inscription', $inscription->id)
                    ->with('recu')
                    ->latest()
                    ->first();

                if (!$dernierPaiement) {
                    $paiementEnAttente = true;
                    $montantPaiement   = $inscription->evenement->montant_inscription ?? 0;
                } else {
                    $recuPaiement   = $dernierPaiement->recu ?? null;
                    $statutPaiement = $dernierPaiement->statut;
                }
            }
        }

        $today = now()->toDateString();

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
            'recuPaiement'          => $recuPaiement,
            'statutPaiement'        => $statutPaiement,
            'evenementsDisponibles' => $evenementsDisponibles,
        ])->layout('layouts.entreprise', ['title' => 'Dashboard Entreprise']);
    }
}