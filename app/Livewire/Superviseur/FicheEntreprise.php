<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\Stand;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\RendezVous;

class FicheEntreprise extends Component
{
    public Entreprise $entreprise;

    public function mount(int $id): void
    {
        $this->entreprise = Entreprise::findOrFail($id);
    }

    public function render()
    {
        $membres = Participant::where('id_entreprise', $this->entreprise->id)
            ->orderByRaw("role = 'representant' DESC")
            ->orderBy('nom')
            ->get();

        $membreIds = $membres->pluck('id');

        $stands = Stand::with('typeStand')
            ->where('id_entreprise', $this->entreprise->id)
            ->get();

        $inscriptions = Inscription::with(['evenement', 'participant'])
            ->whereIn('id_participant', $membreIds)
            ->latest()
            ->get();

        $paiements = Paiement::with(['inscription.evenement', 'inscription.participant', 'recu'])
            ->whereIn('id_inscription', $inscriptions->pluck('id'))
            ->latest()
            ->get();

        $totalRdv = $membreIds->isNotEmpty()
            ? RendezVous::where(function ($q) use ($membreIds) {
                $q->whereIn('id_participant1', $membreIds)
                  ->orWhereIn('id_participant2', $membreIds);
            })->count()
            : 0;

        return view('livewire.admin.fiche-entreprise', [
            'membres'      => $membres,
            'stands'       => $stands,
            'inscriptions' => $inscriptions,
            'paiements'    => $paiements,
            'totalRdv'     => $totalRdv,
        ])->layout('layouts.superviseur', ['title' => 'Fiche Entreprise']);
    }
}