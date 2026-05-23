<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Participant;
use App\Models\Evenement;

/**
 * Composant Livewire — Mon Inscription (Espace Participant)
 *
 * Permet au participant de :
 * - Voir ses inscriptions aux événements
 * - S'inscrire à un nouvel événement
 * - Effectuer un paiement
 * - Consulter et imprimer son reçu
 */
class MonInscription extends Component
{
    
    // PROPRIÉTÉS — MODAL INSCRIPTION
    

    /** @var string Identifiant de l'événement choisi */
    public $id_evenement = '';

    /** @var float Montant à payer */
    public $montant_paye = 0;

    /** @var bool Affichage du modal inscription */
    public $showModalInscription = false;

    
    // PROPRIÉTÉS — MODAL PAIEMENT
    

    /** @var string Mode de paiement choisi */
    public $mode_paiement = 'especes';

    /** @var float Montant du paiement */
    public $montant_paiement = 0;

    /** @var bool Affichage du modal paiement */
    public $showModalPaiement = false;

    /** @var int|null Identifiant de l'inscription à payer */
    public $inscription_id;

    
    // PROPRIÉTÉS — MODAL REÇU
    

    /** @var bool Affichage du modal reçu */
    public $showModalRecu = false;

    /** @var object|null Données de l'inscription avec reçu */
    public $recu_courant = null;

    
    // DONNÉES STATIQUES
    

    /** @var array Modes de paiement disponibles */
    public $modes_paiement = [
        'especes'      => 'Espèces',
        'virement'     => 'Virement bancaire',
        'mobile_money' => 'Mobile Money',
        'carte'        => 'Carte bancaire',
    ];

    
    // GESTION DU MODAL INSCRIPTION
    

    public function openModalInscription()
    {
        $this->id_evenement  = '';
        $this->montant_paye  = 0;
        $this->showModalInscription = true;
        $this->resetErrorBag();
    }

    public function closeModalInscription()
    {
        $this->showModalInscription = false;
    }

    
    // GESTION DU MODAL PAIEMENT
    

    public function openModalPaiement($id)
    {
        $this->inscription_id    = $id;
        $this->mode_paiement     = 'especes';
        $this->montant_paiement  = 0;
        $this->showModalPaiement = true;
        $this->resetErrorBag();
    }

    public function closeModalPaiement()
    {
        $this->showModalPaiement = false;
    }

    
    // GESTION DU MODAL REÇU
    

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

    
    // ACTIONS
    

    /**
     * Inscrit le participant à un événement.
     */
    public function inscrire()
    {
        $this->validate([
            'id_evenement' => 'required',
            'montant_paye' => 'required|numeric|min:0',
        ]);

        $participant = Participant::first();

        // Vérifie si déjà inscrit
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

        session()->flash('success', 'Inscription effectuée avec succès !');
        $this->closeModalInscription();
    }

    /**
     * Soumet un paiement pour une inscription.
     */
    public function payerInscription()
    {
        $this->validate([
            'mode_paiement'    => 'required',
            'montant_paiement' => 'required|numeric|min:1',
        ]);

        Paiement::create([
            'id_inscription' => $this->inscription_id,
            'montant'        => $this->montant_paiement,
            'date_paiement'  => now()->toDateString(),
            'mode_paiement'  => $this->mode_paiement,
            'statut'         => 'en_attente',
        ]);

        session()->flash('success', 'Paiement soumis. En attente de validation.');
        $this->closeModalPaiement();
    }


    // RENDU


    public function render()
    {
        $participant = Participant::first();

        return view('livewire.participant.mon-inscription', [
            'inscriptions' => Inscription::with([
                    'evenement',
                    'paiement',
                    'paiement.recu',
                ])
                ->where('id_participant', $participant?->id)
                ->latest()
                ->get(),
            'evenements' => Evenement::orderBy('nom')->get(),
        ])->layout('layouts.participant', ['title' => 'Mes Inscriptions']);
    }
}