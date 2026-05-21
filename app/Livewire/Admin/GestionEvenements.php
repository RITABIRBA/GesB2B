<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Evenement;
use App\Models\TypeEvenement;

class GestionEvenements extends Component
{
    public $evenement_id;
    public $id_type_evenement = '';
    public $nouveau_type = '';
    public $utiliser_nouveau_type = '';
    public $nom = '';
    public $annee = '';
    public $date_debut = '';
    public $date_fin = '';
    public $heure_debut = '';
    public $heure_fin = '';
    public $ville = '';
    public $lieu = '';
    public $showModal = false;
    public $isEditing = false;
    public $search = '';

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
        $this->evenement_id          = null;
        $this->id_type_evenement     = '';
        $this->nouveau_type          = '';
        $this->utiliser_nouveau_type = '';
        $this->nom                   = '';
        $this->annee                 = '';
        $this->date_debut            = '';
        $this->date_fin              = '';
        $this->heure_debut           = '';
        $this->heure_fin             = '';
        $this->ville                 = '';
        $this->lieu                  = '';
        $this->resetErrorBag();
    }

    public function modifier($id)
    {
        $e = Evenement::findOrFail($id);
        $this->evenement_id          = $e->id;
        $this->id_type_evenement     = $e->id_type_evenement;
        $this->nom                   = $e->nom;
        $this->annee                 = $e->annee;
        $this->date_debut            = $e->date_debut;
        $this->date_fin              = $e->date_fin;
        $this->heure_debut           = $e->heure_debut;
        $this->heure_fin             = $e->heure_fin;
        $this->ville                 = $e->ville;
        $this->lieu                  = $e->lieu;
        $this->isEditing             = true;
        $this->showModal             = true;
    }

    public function sauvegarder()
    {
        if ($this->utiliser_nouveau_type === '1') {
            $this->validate([
                'nouveau_type' => 'required|string|max:255',
                'nom'          => 'required|string|max:255',
                'annee'        => 'required|integer|min:2000|max:2100',
                'date_debut'   => 'required|date',
                'date_fin'     => 'required|date|after_or_equal:date_debut',
                'heure_debut'  => 'required',
                'heure_fin'    => 'required',
                'ville'        => 'required|string|max:255',
                'lieu'         => 'required|string|max:255',
            ]);
            $type    = TypeEvenement::create(['nom' => $this->nouveau_type]);
            $id_type = $type->id;
        } else {
            $this->validate([
                'id_type_evenement' => 'required',
                'nom'               => 'required|string|max:255',
                'annee'             => 'required|integer|min:2000|max:2100',
                'date_debut'        => 'required|date',
                'date_fin'          => 'required|date|after_or_equal:date_debut',
                'heure_debut'       => 'required',
                'heure_fin'         => 'required',
                'ville'             => 'required|string|max:255',
                'lieu'              => 'required|string|max:255',
            ]);
            $id_type = $this->id_type_evenement;
        }

        $data = [
            'id_type_evenement' => $id_type,
            'nom'               => $this->nom,
            'annee'             => $this->annee,
            'date_debut'        => $this->date_debut,
            'date_fin'          => $this->date_fin,
            'heure_debut'       => $this->heure_debut,
            'heure_fin'         => $this->heure_fin,
            'ville'             => $this->ville,
            'lieu'              => $this->lieu,
        ];

        if ($this->isEditing) {
            Evenement::findOrFail($this->evenement_id)->update($data);
            session()->flash('success', 'Événement modifié avec succès.');
        } else {
            Evenement::create($data);
            session()->flash('success', 'Événement créé avec succès.');
        }

        $this->closeModal();
    }

    public function supprimer($id)
    {
        Evenement::findOrFail($id)->delete();
        session()->flash('success', 'Événement supprimé avec succès.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-evenements', [
            'evenements'     => Evenement::with('typeEvenement')
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('ville', 'like', '%'.$this->search.'%')
                )
                ->latest()->get(),
            'typeEvenements' => TypeEvenement::orderBy('nom')->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Événements']);
    }
}