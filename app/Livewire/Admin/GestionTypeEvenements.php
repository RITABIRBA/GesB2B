<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\TypeEvenement;

class GestionTypeEvenements extends Component
{
    public $types;
    public $nom;
    public $type_id;
    public $showModal = false;
    public $isEditing = false;

    public function mount()
    {
        $this->types = TypeEvenement::orderBy('nom')->get();
    }

    public function openModal()
    {
        $this->reset(['nom', 'type_id', 'isEditing']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['nom', 'type_id', 'isEditing']);
        $this->resetErrorBag();
    }

    public function sauvegarder()
    {
        $this->validate([
            'nom' => 'required|string|max:255',
        ], [
            'nom.required' => 'Le nom du type d\'événement est obligatoire.',
        ]);

        if ($this->isEditing) {
            TypeEvenement::find($this->type_id)->update(['nom' => $this->nom]);
            session()->flash('success', 'Type d\'événement modifié avec succès.');
        } else {
            TypeEvenement::create(['nom' => $this->nom]);
            session()->flash('success', 'Type d\'événement créé avec succès.');
        }

        $this->types = TypeEvenement::orderBy('nom')->get();
        $this->closeModal();
    }

    public function modifier($id)
    {
        $type            = TypeEvenement::find($id);
        $this->type_id   = $type->id;
        $this->nom       = $type->nom;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function supprimer($id)
    {
        $type = TypeEvenement::find($id);

        if ($type && $type->evenements()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer ce type : des événements y sont rattachés.');
            return;
        }

        $type?->delete();
        $this->types = TypeEvenement::orderBy('nom')->get();
        session()->flash('success', 'Type d\'événement supprimé.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-type-evenements')
            ->layout('layouts.admin', ['title' => 'Types d\'événements']);
    }
}