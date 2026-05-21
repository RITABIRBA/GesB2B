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
            'id_entreprise' => 'required',
            'numero_stand'  => 'required|integer',
            'superficie'    => 'required|numeric',
            'standing'      => 'required',
        ]);

        $data = [
            'id_evenement'  => $this->id_evenement,
            'id_entreprise' => $this->id_entreprise,
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
                    $q->whereHas('entreprise', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                    )->orWhereHas('evenement', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                    )
                )
                ->latest()->get(),
            'evenements'  => Evenement::orderBy('nom')->get(),
            'entreprises' => Entreprise::where('statut_validation', 'valide')->orderBy('nom')->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Stands']);
    }
}