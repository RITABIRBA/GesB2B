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
        $this->types = TypeEvenement::all();
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
    }

    public function sauvegarder()
    {
        $this->validate(['nom' => 'required|string|max:255']);

        if ($this->isEditing) {
            TypeEvenement::find($this->type_id)->update(['nom' => $this->nom]);
        } else {
            TypeEvenement::create(['nom' => $this->nom]);
        }

        $this->types = TypeEvenement::all();
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
        TypeEvenement::find($id)->delete();
        $this->types = TypeEvenement::all();
    }

    public function render()
    {
        return view('livewire.admin.gestion-type-evenements')
            ->layout('layouts.app');
    }
}