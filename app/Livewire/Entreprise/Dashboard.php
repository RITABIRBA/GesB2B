<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\Stand;
use App\Models\RendezVous;

class Dashboard extends Component
{
    public function render()
    {
        // Récupère l'entreprise liée à l'utilisateur connecté
        // Pour l'instant on prend la première entreprise
        // (on affinera avec la liaison user-entreprise plus tard)
        $entreprise = Entreprise::first();

        return view('livewire.entreprise.dashboard', [
            'entreprise'        => $entreprise,
            'totalParticipants' => $entreprise ? Participant::where('id_entreprise', $entreprise->id)->count() : 0,
            'totalStands'       => $entreprise ? Stand::where('id_entreprise', $entreprise->id)->count() : 0,
            'totalRdv'          => $entreprise ? RendezVous::whereHas('participant1', fn($q) => $q->where('id_entreprise', $entreprise->id))->count() : 0,
            'derniersParticipants' => $entreprise ? Participant::where('id_entreprise', $entreprise->id)->latest()->take(5)->get() : collect(),
        ])->layout('layouts.entreprise', ['title' => 'Dashboard Entreprise']);
    }
}