<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Souhait;
use App\Models\RendezVous;
use App\Models\Badge;
use App\Models\Inscription;
use App\Models\Evenement;
use App\Models\Entreprise;
use App\Models\Paiement;
use App\Models\Recu;
use App\Models\Remise;

class Dashboard extends Component
{
    public bool   $showModalPaiement   = false;
    public string $mode_paiement       = 'orange_money';
    public string $telephone_paiement  = '';
    public string $otp_code            = '';
    public string $otp_saisi           = '';
    public int    $etape_paiement      = 1;
    public float  $montant_paiement    = 0;
    public int    $inscription_id      = 0;
    public string $numero_cheque       = '';

    // ✅ NOUVEAU : infos remise pour le modal
    public float $montant_brut       = 0;
    public float $pourcentage_remise = 0;
    public float $montant_remise     = 0;

    public string $alertSuccess = '';
    public string $alertError   = '';

    public function openModalPaiement(): void
    {
        $this->alertSuccess = '';
        $this->alertError   = '';

        $participant = Participant::findForUser(auth()->user());

        if ($participant) {
            $inscription = Inscription::where('id_participant', $participant->id)
                ->where('statut_paiement', 'en_attente')
                ->with('evenement')
                ->first();

            if ($inscription) {
                $this->inscription_id = $inscription->id;
                $montantBrut = $inscription->evenement->montant_inscription ?? 0;

                $details = $participant->montantApresRemise($montantBrut);

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
                'type_paiement'  => 'individuel',
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
                'type_paiement'  => 'individuel',
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
        $participant = Participant::findForUser(auth()->user());

        $entreprise = $participant && $participant->id_entreprise
            ? Entreprise::find($participant->id_entreprise)
            : null;

        $totalSouhaits = $participant
            ? Souhait::where('id_participant', $participant->id)->count()
            : 0;

        $totalRdv = $participant
            ? RendezVous::where('id_participant1', $participant->id)
                ->orWhere('id_participant2', $participant->id)
                ->count()
            : 0;

        $badge = $participant
            ? Badge::where('id_participant', $participant->id)->first()
            : null;

        $prochainRdv = $participant
            ? RendezVous::with(['participant1', 'participant2'])
                ->where('statut', 'planifie')
                ->where(function ($q) use ($participant) {
                    $q->where('id_participant1', $participant->id)
                      ->orWhere('id_participant2', $participant->id);
                })
                ->orderBy('date')
                ->orderBy('heure_debut')
                ->take(3)
                ->get()
            : collect();

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
            ->map(function ($evenement) use ($participant) {
                $evenement->deja_inscrit = $participant
                    ? Inscription::where('id_participant', $participant->id)
                        ->where('id_evenement', $evenement->id)
                        ->exists()
                    : false;
                return $evenement;
            });

        $mesInscriptions = $participant
            ? Inscription::with('evenement')
                ->where('id_participant', $participant->id)
                ->latest()
                ->get()
            : collect();

        $estMembre = $participant && $participant->id_entreprise;

        $paiementEnAttente = false;
        $montantPaiement   = 0;
        $recuPaiement      = null;
        $statutPaiement    = null;

        // ✅ NOUVEAU : aperçu remise pour le bandeau
        $remiseApplicable   = 0;
        $montantBrutAffiche = 0;

        if ($participant && !$estMembre) {
            $inscription = Inscription::where('id_participant', $participant->id)
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
                    $remiseApplicable   = Remise::calculerMeilleureRemise($participant);
                    $montantPaiement    = $remiseApplicable > 0
                        ? $montantBrutAffiche - ($montantBrutAffiche * $remiseApplicable / 100)
                        : $montantBrutAffiche;
                } else {
                    $recuPaiement   = $dernierPaiement->recu ?? null;
                    $statutPaiement = $dernierPaiement->statut;
                }
            }
        }

        return view('livewire.participant.dashboard', [
            'participant'           => $participant,
            'entreprise'            => $entreprise,
            'totalSouhaits'         => $totalSouhaits,
            'totalRdv'              => $totalRdv,
            'badge'                 => $badge,
            'prochainRdv'           => $prochainRdv,
            'evenementsDisponibles' => $evenementsDisponibles,
            'mesInscriptions'       => $mesInscriptions,
            'estMembre'             => $estMembre,
            'paiementEnAttente'     => $paiementEnAttente,
            'montantPaiement'       => $montantPaiement,
            'montantBrutAffiche'    => $montantBrutAffiche,
            'remiseApplicable'      => $remiseApplicable,
            'recuPaiement'          => $recuPaiement,
            'statutPaiement'        => $statutPaiement,
        ])->layout('layouts.participant', ['title' => 'Mon Dashboard']);
    }
}