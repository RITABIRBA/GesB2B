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
use App\Models\Remise;

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
    public string $numero_cheque      = '';
    public float  $montant_brut       = 0;
    public float  $pourcentage_remise = 0;
    public float  $montant_remise     = 0;
    public string $alertSuccess       = '';
    public string $alertError         = '';

    // ── Helper : retrouve l'entreprise de l'utilisateur connecté ──
    private function getEntreprise(): ?Entreprise
    {
        $user = auth()->user();

        // 1. Par email_responsable
        $entreprise = Entreprise::where('email_responsable', $user->email)->first();
        if ($entreprise) return $entreprise;

        // 2. Par participant lié (email du participant = email du user)
        $participant = Participant::where('email', $user->email)->first();
        if ($participant && $participant->id_entreprise) {
            return Entreprise::find($participant->id_entreprise);
        }

        return null;
    }

    // ── Helper : retrouve le représentant de l'entreprise ──
    private function getRepresentant(?Entreprise $entreprise): ?Participant
    {
        if (!$entreprise) return null;

        // 1. Par rôle representant
        $rep = Participant::where('id_entreprise', $entreprise->id)
            ->where('role', 'representant')
            ->first();
        if ($rep) return $rep;

        // 2. Par email du user connecté
        $rep = Participant::where('id_entreprise', $entreprise->id)
            ->where('email', auth()->user()->email)
            ->first();
        if ($rep) return $rep;

        // 3. Premier participant de l'entreprise
        return Participant::where('id_entreprise', $entreprise->id)->first();
    }

    public function openModalPaiement(): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $entreprise   = $this->getEntreprise();
        $representant = $this->getRepresentant($entreprise);

        if ($representant) {
            // ✅ CORRECTION : statut_presence = 'present' obligatoire
            $inscription = Inscription::where('id_participant', $representant->id)
                ->where('statut_presence', 'present')
                ->where('statut_paiement', 'en_attente')
                ->with('evenement')
                ->first();

            if ($inscription) {
                $this->inscription_id     = $inscription->id;
                $montantBrut              = $inscription->evenement->montant_inscription ?? 0;
                $details                  = $representant->montantApresRemise($montantBrut);
                $this->montant_brut       = $details['montant_brut'];
                $this->pourcentage_remise = $details['pourcentage'];
                $this->montant_remise     = $details['montant_remise'];
                $this->montant_paiement   = $details['montant_net'];
            } else {
                $this->inscription_id     = 0;
                $this->montant_paiement   = 0;
                $this->montant_brut       = 0;
                $this->pourcentage_remise = 0;
                $this->montant_remise     = 0;
            }
        }

        $this->etape_paiement     = 1;
        $this->mode_paiement      = 'orange_money';
        $this->telephone_paiement = '';
        $this->otp_saisi          = '';
        $this->otp_code           = '';
        $this->numero_cheque      = '';
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
        $this->numero_cheque     = '';
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

    public function confirmerOtp(): void
    {
        $this->validate(['otp_saisi' => 'required|string']);

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
                'type_paiement'  => 'entreprise',
                'statut'         => 'en_attente',
            ]);

            Recu::create([
                'id_paiement' => $paiement->id,
                'montant'     => $this->montant_paiement,
                'date'        => now()->toDateString(),
            ]);

            Inscription::find($this->inscription_id)?->update([
                'montant_paye' => $this->montant_paiement,
            ]);

            $this->closeModalPaiement();

            $messageRemise = $this->pourcentage_remise > 0
                ? " (remise de {$this->pourcentage_remise}% appliquée, -" . number_format($this->montant_remise, 0, ',', ' ') . " FCFA)"
                : '';

            $this->alertSuccess = 'Paiement LigdiCash soumis ! Reçu généré : REC-'
                . str_pad($paiement->id, 6, '0', STR_PAD_LEFT) . $messageRemise;

        } catch (\Exception $e) {
            $this->alertError = 'Erreur : ' . $e->getMessage();
        }
    }

    public function payerParCheque(): void
    {
        $this->validate([
            'numero_cheque' => 'required|string|min:3|max:50',
        ], [
            'numero_cheque.required' => 'Le numéro de chèque est obligatoire.',
            'numero_cheque.min'      => 'Numéro de chèque trop court.',
        ]);

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
                'mode_paiement'  => 'cheque',
                'numero_cheque'  => $this->numero_cheque,
                'type_paiement'  => 'entreprise',
                'statut'         => 'en_attente',
            ]);

            Inscription::find($this->inscription_id)?->update([
                'montant_paye' => $this->montant_paiement,
            ]);

            $this->closeModalPaiement();

            $this->alertSuccess = 'Paiement par chèque soumis ! N° '
                . $this->numero_cheque
                . '. L\'administration vérifiera la réception du chèque avant validation.';

        } catch (\Exception $e) {
            $this->alertError = 'Erreur : ' . $e->getMessage();
        }
    }

    public function render()
    {
        $entreprise   = $this->getEntreprise();
        $representant = $this->getRepresentant($entreprise);

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

        $paiementEnAttente  = false;
        $montantPaiement    = 0;
        $recuPaiement       = null;
        $statutPaiement     = null;
        $remiseApplicable   = 0;
        $montantBrutAffiche = 0;

        if ($entreprise && $entreprise->statut_validation == 'valide' && $representant) {
            // ✅ CORRECTION : statut_presence doit être 'present' pour afficher le bouton payer
            $inscription = Inscription::where('id_participant', $representant->id)
                ->where('statut_presence', 'present')
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
                    $paiementEnAttente  = true;
                    $montantBrutAffiche = $inscription->evenement->montant_inscription ?? 0;
                    $remiseApplicable   = Remise::calculerMeilleureRemise($representant);
                    $montantPaiement    = $remiseApplicable > 0
                        ? $montantBrutAffiche - ($montantBrutAffiche * $remiseApplicable / 100)
                        : $montantBrutAffiche;
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
                // ✅ Vérifie si déjà inscrit — fonctionne même si representant est null
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
            'montantBrutAffiche'    => $montantBrutAffiche,
            'remiseApplicable'      => $remiseApplicable,
            'recuPaiement'          => $recuPaiement,
            'statutPaiement'        => $statutPaiement,
            'evenementsDisponibles' => $evenementsDisponibles,
        ])->layout('layouts.entreprise', ['title' => 'Dashboard Entreprise']);
    }
}