<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Stand;
use App\Models\TypeStand;
use App\Models\Evenement;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\Notification;

class GestionStands extends Component
{
    public $stand_id;
    public $id_evenement  = '';
    public $id_entreprise = '';
    public $numero_stand  = '';
    public $superficie    = '';
    public $standing      = 'standard';
    public $showModal     = false;
    public $isEditing     = false;
    public $search        = '';

    // Génération automatique simple (sans types)
    public $showGenerateModal     = false;
    public $id_evenement_generate = '';
    public $nombre_stands         = 10;
    public $superficie_default    = 9;
    public $standing_default      = 'standard';

    public $standings = ['standard', 'premium', 'vip'];

    // ════════════════════════════════════════════════════════
    // ✅ GÉNÉRATION DE STANDS PAR TYPE (par lot, numéro auto)
    // ════════════════════════════════════════════════════════

    public bool   $showGenererParTypeModal = false;
    public        $genererpartype_id_evenement = '';
    public array  $quantitesParType = []; // [type_stand_id => quantite]

    public function ouvrirGenererParType(): void
    {
        $this->genererpartype_id_evenement = '';
        $this->quantitesParType = [];
        $this->showGenererParTypeModal = true;
        $this->resetErrorBag();
    }

    public function fermerGenererParType(): void
    {
        $this->showGenererParTypeModal = false;
    }

    public function updatedGenererpartypeIdEvenement(): void
    {
        $types = TypeStand::where('id_evenement', $this->genererpartype_id_evenement)->get();
        $this->quantitesParType = [];
        foreach ($types as $t) {
            $this->quantitesParType[$t->id] = 0;
        }
    }

    public function getTotalAGenererProperty(): int
    {
        return collect($this->quantitesParType)->sum(fn($q) => (int) $q);
    }

    public function genererStandsParType(): void
    {
        if (!$this->genererpartype_id_evenement) {
            $this->addError('genererpartype_id_evenement', 'Sélectionnez un événement.');
            return;
        }

        if ($this->totalAGenerer < 1) {
            session()->flash('error', 'Indiquez au moins 1 stand à générer.');
            return;
        }

        // ✅ Numéro automatique : dernier numéro existant + 1
        $dernierNumero = Stand::where('id_evenement', $this->genererpartype_id_evenement)
            ->max('numero_stand') ?? 0;

        $compteur = $dernierNumero + 1;

        foreach ($this->quantitesParType as $typeStandId => $quantite) {
            $quantite = (int) $quantite;
            if ($quantite < 1) continue;

            $typeStand = TypeStand::find($typeStandId);
            if (!$typeStand) continue;

            for ($i = 0; $i < $quantite; $i++) {
                Stand::create([
                    'id_evenement'  => $this->genererpartype_id_evenement,
                    'id_type_stand' => $typeStand->id,
                    'numero_stand'  => $compteur,
                    'standing'      => $typeStand->standing,
                    'superficie'    => $typeStand->superficie,
                    'composants'    => $typeStand->composants,
                    'est_gratuit'   => $typeStand->est_gratuit,
                    'prix'          => $typeStand->montant,
                ]);
                $compteur++;
            }
        }

        $this->fermerGenererParType();
        session()->flash('success', "{$this->totalAGenerer} stand(s) généré(s) avec succès.");
    }

    // ════════════════════════════════════════════════════════
    // ✅ ASSIGNATION D'UN STAND À UN PARTICIPANT
    // ════════════════════════════════════════════════════════

    public bool $showAssignerModal = false;
    public $stand_a_assigner       = null;
    public string $rechercheParticipantAssign = '';
    public bool $assign_motif_requis = false;
    public string $assign_motif_gratuite = '';

    public function ouvrirAssignerStand(int $standId): void
    {
        $this->stand_a_assigner = Stand::with('evenement')->findOrFail($standId);
        $this->rechercheParticipantAssign = '';
        $this->assign_motif_gratuite = '';
        $this->assign_motif_requis = (bool) $this->stand_a_assigner->est_gratuit;
        $this->showAssignerModal = true;
        $this->resetErrorBag();
    }

    public function fermerAssignerStand(): void
    {
        $this->showAssignerModal = false;
        $this->stand_a_assigner = null;
    }

    public function assignerAuParticipant(int $participantId): void
    {
        if ($this->assign_motif_requis && empty(trim($this->assign_motif_gratuite))) {
            $this->addError('assign_motif_gratuite', 'Veuillez indiquer le motif de la gratuité.');
            return;
        }

        $participant = Participant::find($participantId);
        if (!$participant || !$this->stand_a_assigner) return;

        $data = [
            'id_participant'     => $participant->id,
            'id_entreprise'      => $participant->id_entreprise,
            'statut_reservation' => 'valide',
        ];

        if ($this->stand_a_assigner->est_gratuit) {
            $data['motif_gratuite']        = $this->assign_motif_gratuite;
            $data['statut_paiement_stand'] = 'paye';
        } else {
            $data['statut_paiement_stand'] = null;
        }

        $numeroStand = $this->stand_a_assigner->numero_stand;
        $this->stand_a_assigner->update($data);

        $nomComplet = $participant->nom . ' ' . $participant->prenom;
        $message = $this->stand_a_assigner->est_gratuit
            ? "🎉 Le Stand N°{$numeroStand} ({$this->stand_a_assigner->standing}) vous a été attribué gratuitement. Motif : {$this->assign_motif_gratuite}"
            : "📋 Le Stand N°{$numeroStand} ({$this->stand_a_assigner->standing}) vous a été assigné. Merci de procéder au paiement.";

        Notification::create([
            'id_participant' => $participant->id,
            'contenu'        => $message,
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        $this->fermerAssignerStand();
        session()->flash('success', "Stand N°{$numeroStand} assigné à {$nomComplet}.");
    }

    // ════════════════════════════════════════════════════════
    // Génération automatique simple (héritée, optionnelle)
    // ════════════════════════════════════════════════════════

    public function openGenerateModal()
    {
        $this->id_evenement_generate = '';
        $this->nombre_stands         = 10;
        $this->superficie_default    = 9;
        $this->standing_default      = 'standard';
        $this->showGenerateModal     = true;
    }

    public function closeGenerateModal()
    {
        $this->showGenerateModal = false;
    }

    public function genererStands()
    {
        $this->validate([
            'id_evenement_generate' => 'required',
            'nombre_stands'         => 'required|integer|min:1|max:100',
            'superficie_default'    => 'required|numeric|min:1',
            'standing_default'      => 'required',
        ]);

        Stand::where('id_evenement', $this->id_evenement_generate)->delete();

        for ($i = 1; $i <= $this->nombre_stands; $i++) {
            Stand::create([
                'id_evenement'  => $this->id_evenement_generate,
                'id_entreprise' => null,
                'numero_stand'  => $i,
                'superficie'    => $this->superficie_default,
                'standing'      => $this->standing_default,
            ]);
        }

        $this->closeGenerateModal();
        session()->flash('success', "{$this->nombre_stands} stands générés automatiquement !");
    }

    // ════════════════════════════════════════════════════════
    // Modal modification stand (manuel, déjà existant)
    // ════════════════════════════════════════════════════════

    public function openModal()
    {
        $this->resetFields();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->stand_id      = null;
        $this->id_evenement  = '';
        $this->id_entreprise = '';
        $this->numero_stand  = '';
        $this->superficie    = '';
        $this->standing      = 'standard';
        $this->resetErrorBag();
    }

    public function modifier($id)
    {
        $stand = Stand::findOrFail($id);
        $this->stand_id      = $stand->id;
        $this->id_evenement  = $stand->id_evenement;
        $this->id_entreprise = $stand->id_entreprise;
        $this->numero_stand  = $stand->numero_stand;
        $this->superficie    = $stand->superficie;
        $this->standing      = $stand->standing;
        $this->isEditing     = true;
        $this->showModal     = true;
    }

    public function sauvegarder()
    {
        $this->validate([
            'id_evenement'  => 'required',
            'numero_stand'  => 'required|integer',
            'superficie'    => 'required',
            'standing'      => 'required',
        ]);

        $data = [
            'id_evenement'  => $this->id_evenement,
            'id_entreprise' => $this->id_entreprise ?: null,
            'numero_stand'  => $this->numero_stand,
            'superficie'    => $this->superficie,
            'standing'      => $this->standing,
        ];

        if ($this->isEditing) {
            Stand::findOrFail($this->stand_id)->update($data);
            session()->flash('success', 'Stand modifié avec succès.');
        } else {
            Stand::create($data);
            session()->flash('success', 'Stand créé avec succès.');
        }

        $this->closeModal();
    }

    public function supprimer($id)
    {
        Stand::findOrFail($id)->delete();
        session()->flash('success', 'Stand supprimé.');
    }

    // ────────────────────────────────────────────────────────
    // VALIDATION / REJET DES RÉSERVATIONS DE STANDS
    // ────────────────────────────────────────────────────────

    private function notifierRepresentant(Stand $stand, string $message): void
    {
        if (!$stand->id_entreprise) return;

        $representant = Participant::where('id_entreprise', $stand->id_entreprise)
            ->where('role', 'representant')
            ->first();

        if ($representant) {
            Notification::create([
                'id_participant' => $representant->id,
                'contenu'        => $message,
                'date_envoie'    => now()->toDateString(),
                'type'           => 'systeme',
            ]);
        }
    }

    public function validerReservation($id)
    {
        $stand = Stand::with('evenement')->findOrFail($id);

        if (!$stand->id_entreprise || $stand->statut_reservation !== 'en_attente') {
            session()->flash('error', 'Cette réservation ne peut pas être validée.');
            return;
        }

        $data    = ['statut_reservation' => 'valide'];
        $gratuit = $stand->est_gratuit
            || ($stand->evenement && $stand->evenement->type_paiement == 'gratuit');

        if ($gratuit) {
            $data['statut_paiement_stand'] = 'paye';
        }

        $stand->update($data);

        $this->notifierRepresentant(
            $stand,
            $gratuit
                ? "✅ Votre réservation du Stand N°{$stand->numero_stand} a été validée. Aucun paiement n'est requis."
                : "✅ Votre réservation du Stand N°{$stand->numero_stand} a été validée. Vous pouvez maintenant procéder au paiement depuis « Mes Stands »."
        );

        session()->flash('success', "Réservation du Stand N°{$stand->numero_stand} validée.");
    }

    public function rejeterReservation($id)
    {
        $stand = Stand::with('evenement')->findOrFail($id);

        if (!$stand->id_entreprise || $stand->statut_reservation !== 'en_attente') {
            session()->flash('error', 'Cette réservation ne peut pas être rejetée.');
            return;
        }

        $this->notifierRepresentant(
            $stand,
            "❌ Votre réservation du Stand N°{$stand->numero_stand} a été rejetée par l'administration. Vous pouvez réserver un autre stand disponible."
        );

        $stand->update([
            'id_entreprise'         => null,
            'id_participant'        => null,
            'statut_reservation'    => null,
            'statut_paiement_stand' => null,
            'motif_gratuite'        => null,
        ]);

        session()->flash('success', "Réservation du Stand N°{$stand->numero_stand} rejetée. Le stand est de nouveau disponible.");
    }

    public function render()
    {
        $participantsPourAssignation = collect();
        if ($this->stand_a_assigner) {
            $participantsPourAssignation = Participant::with('entreprise')
                ->where('id_evenement', $this->stand_a_assigner->id_evenement)
                ->when($this->rechercheParticipantAssign, fn($q) =>
                    $q->where(function ($q) {
                        $q->where('nom', 'like', '%' . $this->rechercheParticipantAssign . '%')
                          ->orWhere('prenom', 'like', '%' . $this->rechercheParticipantAssign . '%')
                          ->orWhere('fonction', 'like', '%' . $this->rechercheParticipantAssign . '%');
                    })
                )
                ->orderBy('nom')
                ->get();
        }

        $typesStandsEvenementGenerer = $this->genererpartype_id_evenement
            ? TypeStand::where('id_evenement', $this->genererpartype_id_evenement)->get()
            : collect();

        return view('livewire.admin.gestion-stands', [
            'stands' => Stand::with(['evenement', 'entreprise', 'typeStand', 'participant'])
                ->when($this->search, fn($q) =>
                    $q->where('numero_stand', 'like', '%'.$this->search.'%')
                    ->orWhereHas('entreprise', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                    )->orWhereHas('participant', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                    )->orWhereHas('evenement', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                    )
                )
                ->orderBy('id_evenement')
                ->orderBy('numero_stand')
                ->get(),
            'evenements'                   => Evenement::orderBy('nom')->get(),
            'entreprises'                  => Entreprise::where('statut_validation', 'valide')->orderBy('nom')->get(),
            'typesStandsEvenementGenerer'  => $typesStandsEvenementGenerer,
            'participantsPourAssignation'  => $participantsPourAssignation,
        ])->layout('layouts.admin', ['title' => 'Gestion des Stands']);
    }
}