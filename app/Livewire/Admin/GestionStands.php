<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Stand;
use App\Models\Evenement;
use App\Models\Entreprise;

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

    // ← Ouvre modal génération
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

    // ← Génère les stands automatiquement
    public function genererStands()
    {
        $this->validate([
            'id_evenement_generate' => 'required',
            'nombre_stands'         => 'required|integer|min:1|max:100',
            'superficie_default'    => 'required|numeric|min:1',
            'standing_default'      => 'required',
        ]);

        // Supprime les anciens stands de cet événement
        Stand::where('id_evenement', $this->id_evenement_generate)->delete();

        // Génère les nouveaux stands
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