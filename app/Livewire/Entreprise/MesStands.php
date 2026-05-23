<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\Stand;
use App\Models\Entreprise;

class MesStands extends Component
{
    public function render()
    {
        $entreprise = Entreprise::first();

        return view('livewire.entreprise.mes-stands', [
            'stands' => Stand::with('evenement')
                ->where('id_entreprise', $entreprise->id)
                ->get(),
        ])->layout('layouts.entreprise', ['title' => 'Mes Stands']);
    }
}