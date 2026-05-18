<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Evenement;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\RendezVous;

class Dashboard extends Component
{
    public $totalEvenements;
    public $totalEntreprises;
    public $totalParticipants;
    public $totalRendezVous;
    public $derniersEvenements;

    public function mount()
    {
        $this->totalEvenements   = Evenement::count();
        $this->totalEntreprises  = Entreprise::count();
        $this->totalParticipants = Participant::count();
        $this->totalRendezVous   = RendezVous::count();
        $this->derniersEvenements = Evenement::latest()->take(5)->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.app');
    }
}