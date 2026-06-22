<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\ChefDelegation;
use App\Models\Inscription;

class Inscriptions extends Component
{
    public $cdd = null;

    public function mount(): void
    {
        $this->cdd = ChefDelegation::where('user_id', auth()->id())->first();
    }

    public function render()
    {
        $inscriptions = collect();

        if ($this->cdd) {
            $membreIds = $this->cdd->membres()->pluck('id');

            $inscriptions = Inscription::with(['participant', 'evenement'])
                ->whereIn('id_participant', $membreIds)
                ->latest()
                ->get();
        }

        return view('livewire.cdd.inscriptions', [
            'inscriptions' => $inscriptions,
        ])->layout('layouts.cdd', ['title' => 'Mes Inscriptions']);
    }
}