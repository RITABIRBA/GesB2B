<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Evenement;
use App\Models\TypeEvenement;

class GestionEvenements extends Component
{
    public $evenement_id;
    public $id_type_evenement           = '';
    public $nouveau_type                = '';
    public $utiliser_nouveau_type       = '';
    public $nom                         = '';
    public $annee                       = '';
    public $date_debut                  = '';
    public $date_fin                    = '';
    public $date_ouverture_inscriptions = '';
    public $date_cloture_inscriptions   = '';
    public $heure_debut                 = '';
    public $heure_fin                   = '';
    public $ville                       = '';
    public $lieu                        = '';
    public $nom_salle                   = '';
    public $nombre_tables               = 10;
    public $montant_inscription         = 0;
    public $type_paiement               = 'gratuit';
    public $nombre_stands               = 0;
    public $prix_stand_standard         = 0;
    public $prix_stand_premium          = 0;
    public $prix_stand_vip              = 0;
    public $min_souhaits                = 5;
    public $max_souhaits                = 20;
    public $duree_rdv                   = 20;
    public $duree_pause                 = 5;
    public $showModal                   = false;
    public $isEditing                   = false;
    public $search                      = '';

    public function openModal(): void
    {
        $this->resetFields();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function resetFields(): void
    {
        $this->evenement_id                 = null;
        $this->id_type_evenement            = '';
        $this->nouveau_type                 = '';
        $this->utiliser_nouveau_type        = '';
        $this->nom                          = '';
        $this->annee                        = '';
        $this->date_debut                   = '';
        $this->date_fin                     = '';
        $this->date_ouverture_inscriptions  = '';
        $this->date_cloture_inscriptions    = '';
        $this->heure_debut                  = '';
        $this->heure_fin                    = '';
        $this->ville                        = '';
        $this->lieu                         = '';
        $this->nom_salle                    = '';
        $this->nombre_tables                = 10;
        $this->montant_inscription          = 0;
        $this->type_paiement                = 'gratuit';
        $this->nombre_stands                = 0;
        $this->prix_stand_standard          = 0;
        $this->prix_stand_premium           = 0;
        $this->prix_stand_vip               = 0;
        $this->min_souhaits                 = 5;
        $this->max_souhaits                 = 20;
        $this->duree_rdv                    = 20;
        $this->duree_pause                  = 5;
        $this->resetErrorBag();
    }

    public function modifier($id): void
    {
        $e = Evenement::findOrFail($id);
        $this->evenement_id                 = $e->id;
        $this->id_type_evenement            = $e->id_type_evenement;
        $this->nom                          = $e->nom;
        $this->annee                        = $e->annee;
        $this->date_debut                   = $e->date_debut;
        $this->date_fin                     = $e->date_fin;
        $this->date_ouverture_inscriptions  = $e->date_ouverture_inscriptions ?? '';
        $this->date_cloture_inscriptions    = $e->date_cloture_inscriptions ?? '';
        $this->heure_debut                  = $e->heure_debut;
        $this->heure_fin                    = $e->heure_fin;
        $this->ville                        = $e->ville;
        $this->lieu                         = $e->lieu;
        $this->nom_salle                    = $e->nom_salle ?? '';
        $this->nombre_tables                = (int) ($e->nombre_tables ?? 10);
        $this->montant_inscription          = $e->montant_inscription;
        $this->type_paiement                = $e->type_paiement;
        $this->nombre_stands                = (int) ($e->nombre_stands ?? 0);
        $this->prix_stand_standard          = $e->prix_stand_standard ?? 0;
        $this->prix_stand_premium           = $e->prix_stand_premium ?? 0;
        $this->prix_stand_vip               = $e->prix_stand_vip ?? 0;
        $this->min_souhaits                 = $e->min_souhaits ?? 5;
        $this->max_souhaits                 = $e->max_souhaits ?? 20;
        $this->duree_rdv                    = $e->duree_rdv ?? 20;
        $this->duree_pause                  = $e->duree_pause ?? 5;
        $this->isEditing                    = true;
        $this->showModal                    = true;
    }

    public function sauvegarder(): void
    {
        $regles = [
            'nom'           => 'required|string|max:255',
            'annee'         => 'required|integer|min:2000|max:2100',
            'date_debut'    => 'required|date',
            'date_fin'      => 'required|date|after_or_equal:date_debut',
            'heure_debut'   => 'required',
            'heure_fin'     => 'required',
            'ville'         => 'required|string|max:255',
            'lieu'          => 'required|string|max:255',
            'type_paiement' => 'required|in:gratuit,par_entreprise',
            'date_ouverture_inscriptions' => 'nullable|date',
            'date_cloture_inscriptions'   => 'nullable|date|after_or_equal:date_ouverture_inscriptions',
            'nom_salle'                   => 'nullable|string|max:255',
            'nombre_tables'               => 'nullable|integer|min:1|max:500',
            'min_souhaits'                => 'required|integer|min:1|max:50',
            'max_souhaits'                => 'required|integer|min:1|max:100',
            'duree_rdv'                   => 'required|integer|min:5|max:120',
            'duree_pause'                 => 'required|integer|min:0|max:60',
            'nombre_stands'               => 'nullable|integer|min:0|max:500',
            'prix_stand_standard'         => 'nullable|numeric|min:0',
            'prix_stand_premium'          => 'nullable|numeric|min:0',
            'prix_stand_vip'              => 'nullable|numeric|min:0',
        ];

        $messages = [
            'type_paiement.in' => "Le type de paiement doit être 'Gratuit' ou 'Payant'.",
        ];

        if ($this->type_paiement !== 'gratuit') {
            $regles['montant_inscription'] = 'required|numeric|min:1';
        }

        if ($this->utiliser_nouveau_type === '1') {
            $regles['nouveau_type'] = 'required|string|max:255';
            $this->validate($regles, $messages);
            $type    = TypeEvenement::create(['nom' => $this->nouveau_type]);
            $id_type = $type->id;
        } else {
            $regles['id_type_evenement'] = 'required';
            $this->validate($regles, $messages);
            $id_type = $this->id_type_evenement;
        }
        $data = [
            'id_type_evenement'           => $id_type,
            'nom'                         => $this->nom,
            'annee'                       => $this->annee,
            'date_debut'                  => $this->date_debut,
            'date_fin'                    => $this->date_fin,
            'date_ouverture_inscriptions' => $this->date_ouverture_inscriptions ?: null,
            'date_cloture_inscriptions'   => $this->date_cloture_inscriptions ?: null,
            'heure_debut'                 => $this->heure_debut,
            'heure_fin'                   => $this->heure_fin,
            'ville'                       => $this->ville,
            'lieu'                        => $this->lieu,
            'nom_salle'                   => $this->nom_salle ?: null,
            'nombre_tables'               => (int) ($this->nombre_tables ?: 10),
            'type_paiement'               => $this->type_paiement,
            'montant_inscription'         => $this->type_paiement === 'gratuit'
                ? 0
                : $this->montant_inscription,
            'nombre_stands'               => (int) ($this->nombre_stands ?: 0),
            'prix_stand_standard'         => $this->prix_stand_standard ?: 0,
            'prix_stand_premium'          => $this->prix_stand_premium ?: 0,
            'prix_stand_vip'              => $this->prix_stand_vip ?: 0,
            'min_souhaits'                => (int) $this->min_souhaits,
            'max_souhaits'                => (int) $this->max_souhaits,
            'duree_rdv'                   => (int) $this->duree_rdv,
            'duree_pause'                 => (int) $this->duree_pause,
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

    public function supprimer($id): void
    {
        Evenement::findOrFail($id)->delete();
        session()->flash('success', 'Événement supprimé avec succès.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-evenements', [
            'evenements' => Evenement::with('typeEvenement')
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('ville', 'like', '%' . $this->search . '%')
                )
                ->latest()
                ->get(),
            'typeEvenements' => TypeEvenement::orderBy('nom')->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Événements']);
    }
}