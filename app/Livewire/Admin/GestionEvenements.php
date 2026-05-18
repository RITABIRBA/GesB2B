<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Evenement;
use App\Models\TypeEvenement;

class GestionEvenements extends Component
{
    public $evenements;
    public $typeEvenements;

    // Champs du formulaire
    public $evenement_id;
    public $id_type_evenement;
    public $nom;
    public $annee;
    public $date_debut;
    public $date_fin;
    public $heure_debut;
    public $heure_fin;
    public $ville;
    public $lieu;

    // Contrôle du modal
    public $showModal = false;
    public $isEditing = false;

    public function mount()
    {
        $this->evenements    = Evenement::with('typeEvenement')->latest()->get();
        $this->typeEvenements = TypeEvenement::all();
    }

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
        $this->evenement_id       = null;
        $this->id_type_evenement  = null;
        $this->nom                = '';
        $this->annee              = '';
        $this->date_debut         = '';
        $this->date_fin           = '';
        $this->heure_debut        = '';
        $this->heure_fin          = '';
        $this->ville              = '';
        $this->lieu               = '';
    }

    public function sauvegarder()
    {
        $this->validate([
            'id_type_evenement' => 'required',
            'nom'               => 'required|string|max:255',
            'annee'             => 'required|integer',
            'date_debut'        => 'required|date',
            'date_fin'          => 'required|date|after:date_debut',
            'heure_debut'       => 'required',
            'heure_fin'         => 'required',
            'ville'             => 'required|string',
            'lieu'              => 'required|string',
        ]);

        if ($this->isEditing) {
            $evenement = Evenement::find($this->evenement_id);
            $evenement->update([
                'id_type_evenement' => $this->id_type_evenement,
                'nom'               => $this->nom,
                'annee'             => $this->annee,
                'date_debut'        => $this->date_debut,
                'date_fin'          => $this->date_fin,
                'heure_debut'       => $this->heure_debut,
                'heure_fin'         => $this->heure_fin,
                'ville'             => $this->ville,
                'lieu'              => $this->lieu,
            ]);
        } else {
            Evenement::create([
                'id_type_evenement' => $this->id_type_evenement,
                'nom'               => $this->nom,
                'annee'             => $this->annee,
                'date_debut'        => $this->date_debut,
                'date_fin'          => $this->date_fin,
                'heure_debut'       => $this->heure_debut,
                'heure_fin'         => $this->heure_fin,
                'ville'             => $this->ville,
                'lieu'              => $this->lieu,
            ]);
        }

        $this->evenements = Evenement::with('typeEvenement')->latest()->get();
        $this->closeModal();
    }

    public function modifier($id)
    {
        $evenement = Evenement::find($id);
        $this->evenement_id      = $evenement->id;
        $this->id_type_evenement = $evenement->id_type_evenement;
        $this->nom               = $evenement->nom;
        $this->annee             = $evenement->annee;
        $this->date_debut        = $evenement->date_debut;
        $this->date_fin          = $evenement->date_fin;
        $this->heure_debut       = $evenement->heure_debut;
        $this->heure_fin         = $evenement->heure_fin;
        $this->ville             = $evenement->ville;
        $this->lieu              = $evenement->lieu;
        $this->isEditing         = true;
        $this->showModal         = true;
    }

    public function supprimer($id)
    {
        Evenement::find($id)->delete();
        $this->evenements = Evenement::with('typeEvenement')->latest()->get();
    }

    public function render()
    {
        return view('livewire.admin.gestion-evenements')
            ->layout('layouts.app');
    }
}