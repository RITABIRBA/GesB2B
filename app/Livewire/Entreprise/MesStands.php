<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\Stand;
use App\Models\Entreprise;
use App\Models\Evenement;
use App\Models\Paiement;

class MesStands extends Component
{
    // Modal paiement stand
    public $showModalPaiement    = false;
    public $stand_id_paiement    = null;
    public $mode_paiement        = 'orange_money';
    public $montant_paiement     = 0;
    public $etape_paiement       = 1;
    public $telephone_paiement   = '';
    public $otp_code             = '';
    public $otp_saisi            = '';
    public $carte_numero         = '';
    public $carte_nom            = '';
    public $carte_expiration     = '';
    public $carte_cvv            = '';

    private function getEntreprise()
    {
        return Entreprise::where('email_responsable', auth()->user()->email)->first()
            ?? Entreprise::where('nom', auth()->user()->name)->first();
    }

    /**
     * Réservation d'un stand par le représentant.
     * → statut_reservation = 'en_attente' (en attente de validation admin).
     */
    public function reserverStand($stand_id)
    {
        $entreprise = $this->getEntreprise();
        if (!$entreprise) return;

        $stand = Stand::with('evenement')->findOrFail($stand_id);

        if ($stand->id_entreprise) {
            session()->flash('error', 'Ce stand est déjà occupé.');
            return;
        }

        if ($stand->evenement) {
            $veille = \Carbon\Carbon::parse($stand->evenement->date_debut)->subDay()->toDateString();
            if (now()->toDateString() > $veille) {
                session()->flash('error', 'Les réservations de stands sont fermées. La date limite était le ' . $veille . '.');
                return;
            }
        }

        $stand->update([
            'id_entreprise'         => $entreprise->id,
            'statut_reservation'    => 'en_attente',
            'statut_paiement_stand' => null,
        ]);

        session()->flash('success', 'Stand N°' . $stand->numero_stand . ' réservé ! Votre réservation est en attente de validation par l\'administration.');
    }

    /**
     * Annule une réservation, uniquement si elle n'a pas encore été payée.
     */
    public function annulerReservation($stand_id)
    {
        $entreprise = $this->getEntreprise();
        if (!$entreprise) return;

        $stand = Stand::findOrFail($stand_id);

        if ($stand->id_entreprise != $entreprise->id) {
            session()->flash('error', 'Ce stand ne vous appartient pas.');
            return;
        }

        if ($stand->statut_paiement_stand == 'paye') {
            session()->flash('error', 'Ce stand a déjà été payé, vous ne pouvez plus annuler la réservation.');
            return;
        }

        $stand->update([
            'id_entreprise'         => null,
            'statut_reservation'    => null,
            'statut_paiement_stand' => null,
        ]);
        session()->flash('success', 'Réservation du Stand N°' . $stand->numero_stand . ' annulée.');
    }

    /**
     * Ouvre le modal de paiement — uniquement si la réservation
     * a été validée par l'admin et qu'un paiement est requis.
     */
    public function payerStand($stand_id)
    {
        $stand = Stand::with('evenement')->findOrFail($stand_id);

        if ($stand->statut_reservation !== 'valide') {
            session()->flash('error', 'Votre réservation doit d\'abord être validée par l\'administration.');
            return;
        }

        if ($stand->statut_paiement_stand == 'paye') {
            session()->flash('error', 'Ce stand a déjà été payé.');
            return;
        }

        $prix = $stand->prix_calcule;

        if ($prix <= 0) {
            session()->flash('error', 'Aucun paiement n\'est requis pour ce stand.');
            return;
        }

        $this->stand_id_paiement  = $stand_id;
        $this->montant_paiement   = $prix;
        $this->mode_paiement      = 'orange_money';
        $this->etape_paiement     = 1;
        $this->telephone_paiement = '';
        $this->otp_saisi          = '';
        $this->otp_code           = '';
        $this->carte_numero       = '';
        $this->carte_nom          = '';
        $this->carte_expiration   = '';
        $this->carte_cvv          = '';
        $this->showModalPaiement  = true;
    }

    public function closeModalPaiement()
    {
        $this->showModalPaiement = false;
        $this->etape_paiement    = 1;
    }

    public function envoyerOtp()
    {
        $this->validate([
            'telephone_paiement' => 'required|string|min:8|max:15',
        ]);
        $this->otp_code       = rand(100000, 999999);
        $this->etape_paiement = 3;
    }

    public function confirmerOtp()
    {
        $this->validate(['otp_saisi' => 'required|string']);

        if ($this->otp_saisi != $this->otp_code) {
            $this->addError('otp_saisi', 'Code OTP incorrect.');
            return;
        }

        $this->enregistrerPaiementStand();
    }

    public function payerCarte()
    {
        $this->validate([
            'carte_numero'     => 'required|string|min:16|max:19',
            'carte_nom'        => 'required|string|max:255',
            'carte_expiration' => 'required|string',
            'carte_cvv'        => 'required|string|min:3|max:4',
        ]);

        $this->enregistrerPaiementStand();
    }

    private function enregistrerPaiementStand()
    {
        Stand::findOrFail($this->stand_id_paiement)->update([
            'statut_paiement_stand' => 'paye',
        ]);

        $this->closeModalPaiement();
        session()->flash('success', 'Paiement du stand effectué avec succès !');
    }

    public function render()
    {
        $entreprise = $this->getEntreprise();

        $mesStands = $entreprise
            ? Stand::with('evenement')
                ->where('id_entreprise', $entreprise->id)
                ->orderBy('id_evenement')
                ->orderBy('numero_stand')
                ->get()
            : collect();

        $standsDisponibles = Stand::with('evenement')
            ->whereNull('id_entreprise')
            ->whereHas('evenement', fn($q) =>
                $q->where('date_fin', '>=', now()->toDateString())
            )
            ->orderBy('id_evenement')
            ->orderBy('numero_stand')
            ->get()
            ->groupBy('id_evenement');

        return view('livewire.entreprise.mes-stands', [
            'entreprise'        => $entreprise,
            'mesStands'         => $mesStands,
            'standsDisponibles' => $standsDisponibles,
        ])->layout('layouts.entreprise', ['title' => 'Mes Stands']);
    }
}