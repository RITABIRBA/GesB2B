<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Participant;
use App\Models\Evenement;

class MonInscription extends Component
{
    // =========================================================
    // PROPRIÉTÉS — MODAL INSCRIPTION
    // =========================================================
    public $id_evenement         = '';
    public $montant_paye         = 0;
    public $showModalInscription = false;

    // =========================================================
    // PROPRIÉTÉS — MODAL PAIEMENT
    // =========================================================
    public $mode_paiement        = 'orange_money';
    public $montant_paiement     = 0;
    public $showModalPaiement    = false;
    public $inscription_id;

    // =========================================================
    // PROPRIÉTÉS — SIMULATION PAIEMENT
    // =========================================================
    public $telephone_paiement   = '';
    public $otp_code             = '';
    public $otp_saisi            = '';
    public $etape_paiement       = 1; // 1=choix, 2=telephone, 3=otp, 4=carte
    public $showOtpInput         = false;

    // Carte bleue
    public $carte_numero         = '';
    public $carte_nom            = '';
    public $carte_expiration     = '';
    public $carte_cvv            = '';

    // =========================================================
    // PROPRIÉTÉS — MODAL REÇU
    // =========================================================
    public $showModalRecu  = false;
    public $recu_courant   = null;

    // =========================================================
    // QUAND L'ÉVÉNEMENT CHANGE → MONTANT AUTO
    // =========================================================
    public function updatedIdEvenement($value)
    {
        if ($value) {
            $evenement          = Evenement::find($value);
            $this->montant_paye = $evenement?->montant_inscription ?? 0;
        } else {
            $this->montant_paye = 0;
        }
    }

    // =========================================================
    // GESTION DU MODAL INSCRIPTION
    // =========================================================
    public function openModalInscription()
    {
        $this->id_evenement         = '';
        $this->montant_paye         = 0;
        $this->showModalInscription = true;
        $this->resetErrorBag();
    }

    public function closeModalInscription()
    {
        $this->showModalInscription = false;
    }

    // =========================================================
    // GESTION DU MODAL PAIEMENT
    // =========================================================
    public function openModalPaiement($id)
    {
        $inscription             = Inscription::findOrFail($id);
        $this->inscription_id   = $id;
        $this->mode_paiement    = 'orange_money';
        $this->montant_paiement = $inscription->montant_paye;
        $this->etape_paiement   = 1;
        $this->telephone_paiement = '';
        $this->otp_saisi        = '';
        $this->otp_code         = '';
        $this->carte_numero     = '';
        $this->carte_nom        = '';
        $this->carte_expiration = '';
        $this->carte_cvv        = '';
        $this->showModalPaiement = true;
        $this->resetErrorBag();
    }

    public function closeModalPaiement()
    {
        $this->showModalPaiement = false;
        $this->etape_paiement    = 1;
    }

    // =========================================================
    // SIMULATION PAIEMENT MOBILE MONEY
    // =========================================================

    /**
     * Étape 2 — Participant saisit son numéro
     * → On génère un code OTP simulé
     */
    public function envoyerOtp()
    {
        $this->validate([
            'telephone_paiement' => 'required|string|min:8|max:15',
        ]);

        // Génère un code OTP simulé (6 chiffres)
        $this->otp_code     = rand(100000, 999999);
        $this->etape_paiement = 3;

        session()->flash('info', "Code OTP envoyé au {$this->telephone_paiement} : {$this->otp_code}");
    }

    /**
     * Étape 3 — Participant saisit le code OTP
     */
    public function confirmerOtp()
    {
        $this->validate([
            'otp_saisi' => 'required|string',
        ]);

        if ($this->otp_saisi != $this->otp_code) {
            $this->addError('otp_saisi', 'Code OTP incorrect. Veuillez réessayer.');
            return;
        }

        $this->enregistrerPaiement();
    }

    /**
     * Paiement par carte bleue
     */
    public function payerCarte()
    {
        $this->validate([
            'carte_numero'     => 'required|string|min:16|max:19',
            'carte_nom'        => 'required|string|max:255',
            'carte_expiration' => 'required|string',
            'carte_cvv'        => 'required|string|min:3|max:4',
        ]);

        $this->enregistrerPaiement();
    }

    /**
     * Enregistre le paiement en BDD
     */
    private function enregistrerPaiement()
    {
        Paiement::create([
            'id_inscription' => $this->inscription_id,
            'montant'        => $this->montant_paiement,
            'date_paiement'  => now()->toDateString(),
            'mode_paiement'  => $this->mode_paiement,
            'statut'         => 'en_attente',
        ]);

        $this->closeModalPaiement();
        session()->flash('success', 'Paiement soumis avec succès ! En attente de confirmation.');
    }

    // =========================================================
    // GESTION DU MODAL REÇU
    // =========================================================
    public function voirRecu($inscription_id)
    {
        $this->recu_courant = Inscription::with([
            'paiement',
            'paiement.recu',
            'evenement',
            'participant',
        ])->findOrFail($inscription_id);

        $this->showModalRecu = true;
    }

    public function closeModalRecu()
    {
        $this->showModalRecu = false;
        $this->recu_courant  = null;
    }

    // =========================================================
    // INSCRIPTION
    // =========================================================
    public function inscrire()
    {
        $this->validate([
            'id_evenement' => 'required',
        ]);

        $participant = Participant::where('email', auth()->user()->email)->first();

        if (!$participant) {
            session()->flash('error', 'Profil participant non trouvé. Contactez votre CDD.');
            $this->closeModalInscription();
            return;
        }

        $existe = Inscription::where('id_participant', $participant->id)
            ->where('id_evenement', $this->id_evenement)
            ->exists();

        if ($existe) {
            session()->flash('error', 'Vous êtes déjà inscrit à cet événement !');
            $this->closeModalInscription();
            return;
        }

        Inscription::create([
            'id_participant'   => $participant->id,
            'id_evenement'     => $this->id_evenement,
            'date_inscription' => now()->toDateString(),
            'montant_paye'     => $this->montant_paye,
            'statut_paiement'  => 'en_attente',
            'statut_presence'  => 'absent',
        ]);

        session()->flash('success', 'Préinscription envoyée ! En attente de validation par votre CDD.');
        $this->closeModalInscription();
    }

    // =========================================================
    // RENDU
    // =========================================================
    public function render()
    {
        $participant = Participant::where('email', auth()->user()->email)->first();

        return view('livewire.participant.mon-inscription', [
            'inscriptions' => $participant
                ? Inscription::with([
                        'evenement',
                        'paiement',
                        'paiement.recu',
                    ])
                    ->where('id_participant', $participant->id)
                    ->latest()
                    ->get()
                : collect(),
            'evenements'  => Evenement::orderBy('nom')->get(),
            'participant' => $participant,
        ])->layout('layouts.participant', ['title' => 'Mes Inscriptions']);
    }
}