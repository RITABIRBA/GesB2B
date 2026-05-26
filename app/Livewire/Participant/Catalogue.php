<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Entreprise;
use App\Models\Evenement;

class Catalogue extends Component
{
    public $search = '';
    public $secteur_filtre = '';
    public $id_evenement = '';

    public function render()
    {
        $evenement = $this->id_evenement
            ? Evenement::find($this->id_evenement)
            : Evenement::latest()->first();

        $catalogueDisponible = $evenement
            ? now()->toDateString() >= $evenement->date_fin
            : false;

        return view('livewire.participant.catalogue', [
            'entreprises' => $catalogueDisponible
                ? Entreprise::with('participants')
                    ->where('statut_validation', 'valide')
                    ->when($this->search, fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('secteur_activite', 'like', '%'.$this->search.'%')
                    )
                    ->when($this->secteur_filtre, fn($q) =>
                        $q->where('secteur_activite', $this->secteur_filtre)
                    )
                    ->get()
                : collect(),
            'secteurs'            => Entreprise::where('statut_validation', 'valide')->distinct()->pluck('secteur_activite'),
            'evenements'          => Evenement::orderBy('nom')->get(),
            'evenement'           => $evenement,
            'catalogueDisponible' => $catalogueDisponible,
        ])->layout('layouts.participant', ['title' => 'Catalogue']);
    }
}