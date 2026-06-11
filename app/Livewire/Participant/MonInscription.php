<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Participant;
use App\Models\Evenement;

class MonInscription extends Component
{
    // ============================================================
    // MODAL INSCRIPTION
    // ============================================================
    public string $id_evenement         = '';
    public float  $montant_paye         = 0;
    public bool   $showModalInscription = false;

    // ============================================================
    // MODAL PAIEMENT
    // ============================================================
    public string $mode_paiement     = 'orange_money';
    public float  $montant_paiement  = 0;
    public bool   $showModalPaiement = false;
    public $inscription_id;

    // Simulation paiement mobile
    public string $telephone_paiement = '';
    public string $otp_code           = '';
    public string $otp_saisi          = '';
    public int    $etape_paiement     = 1;

    // Carte bancaire
    public string $carte_numero     = '';
    public string $carte_nom        = '';
    public string $carte_expiration = '';
    public string $carte_cvv        = '';

    // ============================================================
    // MODAL REÇU
    // ============================================================
    public bool $showModalRecu = false;
    public $recu_courant       = null;

    // ============================================================
    // HELPERS
    // ============================================================

    /**
     * Met à jour le montant quand l'événement change.
     */
    public function updatedIdEvenement(string $value): void
    {
        if ($value) {
            $evenement   = Evenement::find($value);
            $participant = Participant::findForUser(auth()->user());

            if ($evenement?->type_paiement == 'par_entreprise'
                && $participant?->id_entreprise) {
                $this->montant_paye = 0;
            } else {
                $this->montant_paye = $evenement?->montant_inscription ?? 0;
            }
        } else {
            $this->montant_paye = 0;
        }
    }

    // ============================================================
    // MODAL INSCRIPTION
    // ============================================================

    public function openModalInscription(): void
    {
        $this->id_evenement         = '';
        $this->montant_paye         = 0;
        $this->showModalInscription = true;
        $this->resetErrorBag();
    }

    public function closeModalInscription(): void
    {
        $this->showModalInscription = false;
    }

    // ============================================================
    // MODAL PAIEMENT
    // ============================================================

    public function openModalPaiement(int $id): void
    {
        $inscription              = Inscription::findOrFail($id);
        $this->inscription_id    = $id;
        $this->mode_paiement     = 'orange_money';
        $this->montant_paiement  = $inscription->montant_paye;
        $this->etape_paiement    = 1;
        $this->telephone_paiement = '';
        $this->otp_saisi         = '';
        $this->otp_code          = '';
        $this->carte_numero      = '';
        $this->carte_nom         = '';
        $this->carte_expiration  = '';
        $this->carte_cvv         = '';
        $this->showModalPaiement = true;
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
        $this->etape_paiement = 3;
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

    public function payerCarte(): void
    {
        $this->validate([
            'carte_numero'     => 'required|string|min:16|max:19',
            'carte_nom'        => 'required|string|max:255',
            'carte_expiration' => 'required|string',
            'carte_cvv'        => 'required|string|min:3|max:4',
        ]);

        $this->enregistrerPaiement();
    }

    private function enregistrerPaiement(): void
    {
        $inscription = Inscription::with('evenement')->findOrFail($this->inscription_id);

        if ($inscription->montant_paye == 0) {
            $inscription->update(['statut_paiement' => 'paye']);
            $this->closeModalPaiement();
            session()->flash('success', 'Inscription confirmée !');
            return;
        }

        Paiement::create([
            'id_inscription' => $this->inscription_id,
            'montant'        => $this->montant_paiement,
            'date_paiement'  => now()->toDateString(),
            'mode_paiement'  => $this->mode_paiement,
            'statut'         => 'en_attente',
        ]);

        $this->closeModalPaiement();
        session()->flash('success', 'Paiement soumis avec succès !');
    }

    // ============================================================
    // MODAL REÇU
    // ============================================================

    public function voirRecu(int $inscription_id): void
    {
        $this->recu_courant = Inscription::with([
            'paiement', 'paiement.recu', 'evenement', 'participant',
        ])->findOrFail($inscription_id);

        $this->showModalRecu = true;
    }

    public function closeModalRecu(): void
    {
        $this->showModalRecu = false;
        $this->recu_courant  = null;
    }

    // ============================================================
    // INSCRIPTION
    // ============================================================

    public function inscrire(): void
    {
        $this->validate([
            'id_evenement' => 'required',
        ]);

        $participant = Participant::findForUser(auth()->user());

        if (!$participant) {
            session()->flash('error', 'Profil participant non trouvé.');
            $this->closeModalInscription();
            return;
        }

        $evenement = Evenement::find($this->id_evenement);

        // ← Vérifie dates inscriptions
        if ($evenement->date_ouverture_inscriptions &&
            now()->toDateString() < $evenement->date_ouverture_inscriptions) {
            session()->flash('error', 'Les inscriptions ne sont pas encore ouvertes.');
            $this->closeModalInscription();
            return;
        }

        if ($evenement->date_cloture_inscriptions &&
            now()->toDateString() > $evenement->date_cloture_inscriptions) {
            session()->flash('error', 'Les inscriptions sont clôturées.');
            $this->closeModalInscription();
            return;
        }

        // ← Vérifie si déjà inscrit
        $existe = Inscription::where('id_participant', $participant->id)
            ->where('id_evenement', $this->id_evenement)
            ->exists();

        if ($existe) {
            session()->flash('error', 'Vous êtes déjà inscrit à cet événement !');
            $this->closeModalInscription();
            return;
        }

        // ← Détermine montant et statut
        $montant = $evenement->montant_inscription ?? 0;
        $statut  = 'en_attente';

        if ($evenement->type_paiement == 'gratuit') {
            $montant = 0;
            $statut  = 'paye';
        } elseif ($evenement->type_paiement == 'par_entreprise'
            && $participant->id_entreprise) {
            $montant = 0;
            $statut  = 'paye';
        }

        // ← Met à jour l'événement du participant
        $participant->update(['id_evenement' => $this->id_evenement]);

        Inscription::create([
            'id_participant'   => $participant->id,
            'id_evenement'     => $this->id_evenement,
            'date_inscription' => now()->toDateString(),
            'montant_paye'     => $montant,
            'statut_paiement'  => $statut,
            'statut_presence'  => 'absent',
        ]);

        if ($evenement->type_paiement == 'gratuit') {
            session()->flash('success', 'Inscription confirmée ! Événement gratuit.');
        } elseif ($evenement->type_paiement == 'par_entreprise') {
            session()->flash('success', 'Inscription confirmée ! Le paiement est géré par votre entreprise.');
        } else {
            session()->flash('success', 'Préinscription envoyée ! Procédez au paiement.');
        }

        $this->closeModalInscription();
    }

    // ============================================================
    // RENDER
    // ============================================================

    public function render()
    {
        $participant = Participant::findForUser(auth()->user());
        $today       = now()->toDateString();

        return view('livewire.participant.mon-inscription', [

            // ← Inscriptions du participant
            'inscriptions' => $participant
                ? Inscription::with(['evenement', 'paiement', 'paiement.recu'])
                    ->where('id_participant', $participant->id)
                    ->latest()
                    ->get()
                : collect(),

            // ← Événements avec inscriptions ouvertes (pour le modal)
            'evenements' => Evenement::where('date_fin', '>=', $today)
                ->where(fn($q) =>
                    $q->whereNull('date_ouverture_inscriptions')
                      ->orWhere('date_ouverture_inscriptions', '<=', $today)
                )
                ->where(fn($q) =>
                    $q->whereNull('date_cloture_inscriptions')
                      ->orWhere('date_cloture_inscriptions', '>=', $today)
                )
                ->orderBy('nom')
                ->get(),

            'participant' => $participant,

            // ← tousEvenements supprimé !

        ])->layout('layouts.participant', ['title' => 'Mes Inscriptions']);
    }
}