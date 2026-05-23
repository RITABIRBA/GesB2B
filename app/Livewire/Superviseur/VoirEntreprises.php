<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Entreprise;

class VoirEntreprises extends Component
{
    public $search = '';

    public function valider($id)
    {
        Entreprise::findOrFail($id)->update(['statut_validation' => 'valide']);
        session()->flash('success', 'Entreprise validée.');
    }

    public function rejeter($id)
    {
        Entreprise::findOrFail($id)->update(['statut_validation' => 'rejete']);
        session()->flash('success', 'Entreprise rejetée.');
    }

    public function render()
    {
        return view('livewire.superviseur.voir-entreprises', [
            'entreprises' => Entreprise::when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('pays', 'like', '%'.$this->search.'%')
                )
                ->latest()->get(),
        ])->layout('layouts.superviseur', ['title' => 'Entreprises']);
    }
}