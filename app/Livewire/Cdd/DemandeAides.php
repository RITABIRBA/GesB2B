<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\ChefDelegation;
use App\Models\DemandeAide;

class DemandesAide extends Component
{
    public $cdd = null;
    public string $sujet   = '';
    public string $message = '';
    public string $alertSuccess = '';

    public function mount(): void
    {
        $this->cdd = ChefDelegation::where('user_id', auth()->id())->first();
    }

    public function envoyer(): void
    {
        $this->validate([
            'sujet'   => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        DemandeAide::create([
            'nom'     => $this->cdd->nom ?? auth()->user()->name,
            'email'   => auth()->user()->email,
            'sujet'   => $this->sujet,
            'message' => $this->message,
            'statut'  => 'en_attente',
        ]);

        $this->sujet   = '';
        $this->message = '';
        $this->alertSuccess = 'Votre demande a été envoyée à l\'administration.';
    }

    public function render()
    {
        return view('livewire.cdd.demandes-aide')
            ->layout('layouts.cdd', ['title' => "Demandes d'aide"]);
    }
}