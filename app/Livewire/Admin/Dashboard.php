<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Evenement;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\RendezVous;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalEvenements'   => Evenement::count(),
            'totalEntreprises'  => Entreprise::count(),
            'totalParticipants' => Participant::count(),
            'totalRendezVous'   => RendezVous::count(),
            'derniersEvenements' => Evenement::latest()->take(5)->get(),
        ])->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}