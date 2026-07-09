<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\Evenement;
use App\Models\Inscription;

class Catalogue extends Component
{
    use WithPagination;

    public string $search          = '';
    public string $secteur_filtre  = '';
    public string $pays_filtre     = '';
    public $id_evenement           = '';

    public function updatedSearch(): void        { $this->resetPage(); }
    public function updatedSecteurFiltre(): void { $this->resetPage(); }
    public function updatedPaysFiltre(): void    { $this->resetPage(); }
    public function updatedIdEvenement(): void   { $this->resetPage(); }

    public function render()
    {
        // ✅ Récupérer le participant connecté
        $user        = auth()->user();
        $participant = Participant::where('email', $user->email)->first();

        // ✅ Événement sélectionné ou le plus récent
        $evenement = $this->id_evenement
            ? Evenement::find($this->id_evenement)
            : ($participant && $participant->id_evenement
                ? Evenement::find($participant->id_evenement)
                : Evenement::latest()->first());

        // ✅ Disponible dès que le participant est validé et payé
        $catalogueDisponible = $participant
            && $participant->statut_preinscription === 'valide'
            && \App\Models\Inscription::where('id_participant', $participant->id)
                ->where(fn($q) => $q->where('statut_paiement', 'paye')
                    ->orWhereHas('evenement', fn($q) => $q->where('type_paiement', 'gratuit'))
                )->exists();

        // ✅ Entreprises inscrites à l'événement avec leurs représentants
        $entreprisesQuery = Entreprise::with(['participants' => function($q) use ($evenement) {
                if ($evenement) {
                    $q->where('id_evenement', $evenement->id)
                      ->where('statut_preinscription', 'valide');
                }
            }])
            ->where('statut_validation', 'valide')
            ->when($evenement, function($q) use ($evenement) {
                // Seulement les entreprises dont un participant est inscrit à cet événement
                $q->whereHas('participants', fn($p) =>
                    $p->where('id_evenement', $evenement->id)
                      ->where('statut_preinscription', 'valide')
                );
            })
            ->when($this->search, fn($q) =>
                $q->where(function($q) {
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('secteur_activite', 'like', '%'.$this->search.'%')
                      ->orWhere('ville', 'like', '%'.$this->search.'%');
                })
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

        // ✅ Participants individuels inscrits à l'événement (sans entreprise)
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

        return view('livewire.participant.catalogue', [
            'entreprises'             => $entreprises,
            'participantsIndividuels' => $participantsIndividuels,
            'secteurs'                => Entreprise::where('statut_validation', 'valide')->distinct()->pluck('secteur_activite')->filter()->sort()->values(),
            'pays'                    => Entreprise::where('statut_validation', 'valide')->distinct()->pluck('pays')->filter()->sort()->values(),
            'evenements'              => Evenement::orderBy('nom')->get(),
            'evenement'               => $evenement,
            'catalogueDisponible'     => $catalogueDisponible,
        ])->layout('layouts.participant', ['title' => 'Catalogue des participants']);
    }
}