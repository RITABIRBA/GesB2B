<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Stand;
use App\Models\Evenement;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\Notification;

class GestionStands extends Component
{
    public $stand_id;
    public $id_evenement = '';
    public $id_entreprise = '';
    public $numero_stand = '';
    public $superficie = '';
    public $standing = 'standard';
    public $showModal = false;
    public $isEditing = false;
    public $search = '';

    // ← Génération automatique
    public $showGenerateModal = false;
    public $id_evenement_generate = '';
    public $nombre_stands = 10;
    public $superficie_default = 9;
    public $standing_default = 'standard';

    public $standings = ['standard', 'premium', 'vip'];

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

    
    // VALIDATION / REJET DES RÉSERVATIONS DE STANDS
    

    /**
     * Notifie le représentant de l'entreprise liée au stand.
     */
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

    /**
     * Valide la réservation d'un stand.
     * Si l'événement est gratuit, le stand est automatiquement réglé.
     */
    public function validerReservation($id)
    {
        $stand = Stand::with('evenement')->findOrFail($id);

        if (!$stand->id_entreprise || $stand->statut_reservation !== 'en_attente') {
            session()->flash('error', 'Cette réservation ne peut pas être validée.');
            return;
        }

        $data    = ['statut_reservation' => 'valide'];
        $gratuit = $stand->evenement && $stand->evenement->type_paiement == 'gratuit';

        if ($gratuit) {
            $data['statut_paiement_stand'] = 'paye';
        }

        $stand->update($data);

        $this->notifierRepresentant(
            $stand,
            $gratuit
                ? "✅ Votre réservation du Stand N°{$stand->numero_stand} a été validée. Aucun paiement n'est requis pour cet événement gratuit."
                : "✅ Votre réservation du Stand N°{$stand->numero_stand} a été validée. Vous pouvez maintenant procéder au paiement depuis « Mes Stands »."
        );

        session()->flash('success', "Réservation du Stand N°{$stand->numero_stand} validée.");
    }

    /**
     * Rejette la réservation : le stand redevient disponible.
     */
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
            'statut_reservation'    => null,
            'statut_paiement_stand' => null,
        ]);

        session()->flash('success', "Réservation du Stand N°{$stand->numero_stand} rejetée. Le stand est de nouveau disponible.");
    }

    public function render()
    {
        return view('livewire.admin.gestion-stands', [
            'stands' => Stand::with(['evenement', 'entreprise'])
                ->when($this->search, fn($q) =>
                    $q->where('numero_stand', 'like', '%'.$this->search.'%')
                    ->orWhereHas('entreprise', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                    )->orWhereHas('evenement', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                    )
                )
                ->orderBy('id_evenement')
                ->orderBy('numero_stand')
                ->get(),
            'evenements'  => Evenement::orderBy('nom')->get(),
            'entreprises' => Entreprise::where('statut_validation', 'valide')->orderBy('nom')->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Stands']);
    }
}