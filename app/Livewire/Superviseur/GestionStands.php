<?php

namespace App\Livewire\Superviseur;

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

    // Génération automatique
    public $showGenerateModal     = false;
    public $id_evenement_generate = '';
    public $nombre_stands         = 10;
    public $superficie_default    = 9;
    public $standing_default      = 'standard';

    public $standings = ['standard', 'premium', 'vip'];

    //  NOUVEAU : Création manuelle avec type + assignation
    public bool   $showCreateModal      = false;
    public $create_id_evenement         = '';
    public $create_id_type_stand        = '';
    public $create_numero_stand         = '';
    public $create_id_participant       = '';
    public string $create_recherche_participant = '';
    public bool   $create_est_gratuit   = false;
    public string $create_motif_gratuite = '';

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

    
    //  NOUVEAU : CRÉATION MANUELLE + ASSIGNATION
    

    public function ouvrirCreateModal(): void
    {
        $this->create_id_evenement          = '';
        $this->create_id_type_stand         = '';
        $this->create_numero_stand          = '';
        $this->create_id_participant        = '';
        $this->create_recherche_participant = '';
        $this->create_est_gratuit           = false;
        $this->create_motif_gratuite        = '';
        $this->showCreateModal              = true;
        $this->resetErrorBag();
    }

    public function fermerCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function updatedCreateIdEvenement(): void
    {
        $this->create_id_type_stand  = '';
        $this->create_id_participant = '';
    }

    public function updatedCreateIdTypeStand(): void
    {
        if ($this->create_id_type_stand) {
            $type = TypeStand::find($this->create_id_type_stand);
            $this->create_est_gratuit = $type?->est_gratuit ?? false;
        }
    }

    public function selectionnerParticipant(int $id): void
    {
        $this->create_id_participant        = $id;
        $participant                        = Participant::find($id);
        $this->create_recherche_participant = $participant
            ? $participant->nom . ' ' . $participant->prenom
            : '';
    }

    public function viderParticipant(): void
    {
        $this->create_id_participant        = '';
        $this->create_recherche_participant = '';
    }

    public function creerStandManuel(): void
    {
        $regles = [
            'create_id_evenement'  => 'required',
            'create_id_type_stand' => 'required',
            'create_numero_stand'  => 'required|integer|min:1',
        ];

        if ($this->create_est_gratuit && $this->create_id_participant) {
            $regles['create_motif_gratuite'] = 'required|string|min:5';
        }

        $this->validate($regles, [
            'create_id_evenement.required'  => 'Sélectionnez un événement.',
            'create_id_type_stand.required' => 'Sélectionnez un type de stand.',
            'create_numero_stand.required'  => 'Le numéro de stand est obligatoire.',
            'create_motif_gratuite.required'=> 'Veuillez indiquer le motif de la gratuité.',
            'create_motif_gratuite.min'     => 'Le motif est trop court.',
        ]);

        // Vérifie que le numéro n'existe pas déjà pour cet événement
        $existe = Stand::where('id_evenement', $this->create_id_evenement)
            ->where('numero_stand', $this->create_numero_stand)
            ->exists();

        if ($existe) {
            $this->addError('create_numero_stand', 'Ce numéro de stand existe déjà pour cet événement.');
            return;
        }

        $typeStand = TypeStand::find($this->create_id_type_stand);

        $data = [
            'id_evenement'  => $this->create_id_evenement,
            'numero_stand'  => $this->create_numero_stand,
            'id_type_stand' => $typeStand->id,
            'standing'      => $typeStand->standing,
            'superficie'    => $typeStand->superficie,
            'composants'    => $typeStand->composants,
            'est_gratuit'   => $typeStand->est_gratuit,
            'prix'          => $typeStand->montant,
        ];

        if ($this->create_id_participant) {
            $participant = Participant::find($this->create_id_participant);

            $data['id_participant'] = $participant->id;
            $data['id_entreprise']  = $participant->id_entreprise;
            $data['statut_reservation'] = 'valide';

            if ($typeStand->est_gratuit) {
                $data['motif_gratuite']        = $this->create_motif_gratuite;
                $data['statut_paiement_stand'] = 'paye';
            } else {
                $data['statut_paiement_stand'] = null; // en attente
            }
        }

        $stand = Stand::create($data);

        if ($this->create_id_participant) {
            $nomComplet = $participant->nom . ' ' . $participant->prenom;
            $message = $typeStand->est_gratuit
                ? "🎉 Le Stand N°{$stand->numero_stand} ({$typeStand->standing}) vous a été attribué gratuitement. Motif : {$this->create_motif_gratuite}"
                : "📋 Le Stand N°{$stand->numero_stand} ({$typeStand->standing}) vous a été assigné. Merci de procéder au paiement.";

            Notification::create([
                'id_participant' => $participant->id,
                'contenu'        => $message,
                'date_envoie'    => now()->toDateString(),
                'type'           => 'systeme',
            ]);

            session()->flash('success', "Stand N°{$stand->numero_stand} créé et assigné à {$nomComplet}.");
        } else {
            session()->flash('success', "Stand N°{$stand->numero_stand} créé et disponible.");
        }

        $this->fermerCreateModal();
    }

    // ════════════════════════════════════════════════════════

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
            'superficie'    => 'required|numeric',
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
                ? " Votre réservation du Stand N°{$stand->numero_stand} a été validée. Aucun paiement n'est requis."
                : " Votre réservation du Stand N°{$stand->numero_stand} a été validée. Vous pouvez maintenant procéder au paiement depuis « Mes Stands »."
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
            "Votre réservation du Stand N°{$stand->numero_stand} a été rejetée par l'administration. Vous pouvez réserver un autre stand disponible."
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
        $participantsRecherche = collect();
        if ($this->create_id_evenement && strlen($this->create_recherche_participant) >= 2
            && !$this->create_id_participant) {
            $participantsRecherche = Participant::with('entreprise')
                ->where('id_evenement', $this->create_id_evenement)
                ->where(function ($q) {
                    $q->where('nom', 'like', '%' . $this->create_recherche_participant . '%')
                      ->orWhere('prenom', 'like', '%' . $this->create_recherche_participant . '%');
                })
                ->limit(8)
                ->get();
        }

        $typesStandsEvenement = $this->create_id_evenement
            ? TypeStand::where('id_evenement', $this->create_id_evenement)->get()
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
            'evenements'             => Evenement::orderBy('nom')->get(),
            'entreprises'            => Entreprise::where('statut_validation', 'valide')->orderBy('nom')->get(),
            'typesStandsEvenement'   => $typesStandsEvenement,
            'participantsRecherche'  => $participantsRecherche,
        ])->layout('layouts.superviseur', ['title' => 'Gestion des Stands']);
    }
}