<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Souhait;
use App\Models\Evenement;

class StatistiquesSouhaits extends Component
{
    public $filtre_evenement = '';
    public $search = '';

    public function render()
    {
        $participants = Participant::with(['entreprise', 'evenement'])
    ->where('id_cdd', auth()->id()) // ← FILTRE PAR CDD
    ->when($this->filtre_evenement, fn($q) =>
        $q->where('id_evenement', $this->filtre_evenement)
    )
    ->when($this->search, fn($q) =>
        $q->where('nom', 'like', '%'.$this->search.'%')
          ->orWhere('prenom', 'like', '%'.$this->search.'%')
    )
    ->get()
            ->map(function($participant) {
                $participant->nb_souhaits = Souhait::where('id_participant', $participant->id)->count();
                return $participant;
            });

        return view('livewire.cdd.statistiques-souhaits', [
            'participants' => $participants,
            'evenements'   => Evenement::orderBy('nom')->get(),
            'total'        => $participants->count(),
            'suffisant'    => $participants->filter(fn($p) => $p->nb_souhaits >= 10)->count(),
            'insuffisant'  => $participants->filter(fn($p) => $p->nb_souhaits < 10)->count(),
        ])->layout('layouts.cdd', ['title' => 'Statistiques Souhaits']);
    }
}