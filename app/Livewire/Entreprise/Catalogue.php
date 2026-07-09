<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\Evenement;

class Catalogue extends Component
{
    use WithPagination;

    public string $search         = '';
    public string $secteur_filtre = '';
    public string $pays_filtre    = '';
    public $id_evenement          = '';

    public function updatedSearch(): void        { $this->resetPage(); }
    public function updatedSecteurFiltre(): void { $this->resetPage(); }
    public function updatedPaysFiltre(): void    { $this->resetPage(); }
    public function updatedIdEvenement(): void   { $this->resetPage(); }

    public function render()
    {
        $user        = auth()->user();
        $representant = Participant::where('email', $user->email)->first();

        $evenement = $this->id_evenement
            ? Evenement::find($this->id_evenement)
            : ($representant && $representant->id_evenement
                ? Evenement::find($representant->id_evenement)
                : Evenement::latest()->first());

        // ✅ Disponible dès que le représentant est validé et payé
        $catalogueDisponible = $representant
            && $representant->statut_preinscription === 'valide'
            && \App\Models\Inscription::where('id_participant', $representant->id)
                ->where(fn($q) => $q->where('statut_paiement', 'paye')
                    ->orWhereHas('evenement', fn($q) => $q->where('type_paiement', 'gratuit'))
                )->exists();

        $entreprisesQuery = Entreprise::with(['participants' => function($q) use ($evenement) {
                if ($evenement) {
                    $q->where('id_evenement', $evenement->id)
                      ->where('statut_preinscription', 'valide');
                }
            }])
            ->where('statut_validation', 'valide')
            ->when($evenement, fn($q) =>
                $q->whereHas('participants', fn($p) =>
                    $p->where('id_evenement', $evenement->id)
                      ->where('statut_preinscription', 'valide')
                )
            )
            ->when($this->search, fn($q) =>
                $q->where(fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('secteur_activite', 'like', '%'.$this->search.'%')
                      ->orWhere('ville', 'like', '%'.$this->search.'%')
                )
            )
            ->when($this->secteur_filtre, fn($q) =>
                $q->where('secteur_activite', $this->secteur_filtre)
            )
            ->when($this->pays_filtre, fn($q) =>
                $q->where('pays', $this->pays_filtre)
            )
            ->orderBy('nom');

        $entreprises = $catalogueDisponible
            ? $entreprisesQuery->paginate(9)
            : collect();

        $participantsIndividuels = $catalogueDisponible && $evenement
            ? Participant::whereNull('id_entreprise')
                ->where('id_evenement', $evenement->id)
                ->where('statut_preinscription', 'valide')
                ->when($this->search, fn($q) =>
                    $q->where(fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                          ->orWhere('fonction', 'like', '%'.$this->search.'%')
                    )
                )
                ->paginate(9, ['*'], 'pageIndiv')
            : collect();

        return view('livewire.entreprise.catalogue', [
            'entreprises'             => $entreprises,
            'participantsIndividuels' => $participantsIndividuels,
            'secteurs'                => Entreprise::where('statut_validation', 'valide')->distinct()->pluck('secteur_activite')->filter()->sort()->values(),
            'pays'                    => Entreprise::where('statut_validation', 'valide')->distinct()->pluck('pays')->filter()->sort()->values(),
            'evenements'              => Evenement::orderBy('nom')->get(),
            'evenement'               => $evenement,
            'catalogueDisponible'     => $catalogueDisponible,
        ])->layout('layouts.entreprise', ['title' => 'Catalogue des participants']);
    }
}