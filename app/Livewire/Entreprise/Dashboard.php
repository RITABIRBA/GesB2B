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
        // Liaison par nom de l'utilisateur connecté
        $entreprise = Entreprise::where('nom', auth()->user()->name)->first();

        $totalParticipants = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)->count()
            : 0;

        $totalStands = $entreprise
            ? Stand::where('id_entreprise', $entreprise->id)->count()
            : 0;

        $participantIds = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)->pluck('id')
            : collect();

        $totalRdv = $participantIds->isNotEmpty()
            ? RendezVous::where(function($q) use ($participantIds) {
                $q->whereIn('id_participant1', $participantIds)
                  ->orWhereIn('id_participant2', $participantIds);
            })->count()
            : 0;

        $derniersParticipants = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)->latest()->take(5)->get()
            : collect();

        return view('livewire.entreprise.dashboard', [
            'entreprise'           => $entreprise,
            'totalParticipants'    => $totalParticipants,
            'totalStands'          => $totalStands,
            'totalRdv'             => $totalRdv,
            'derniersParticipants' => $derniersParticipants,
        ])->layout('layouts.entreprise', ['title' => 'Dashboard Entreprise']);
    }
}