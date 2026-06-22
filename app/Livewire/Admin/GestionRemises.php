<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Remise;
use App\Models\Evenement;

class GestionRemises extends Component
{
    public $remise_id;
    public $id_evenement = '';
    public string $libelle    = '';
    public string $type       = 'nb_participants';
    public $seuil_min         = '';
    public $age_min           = '';
    public $age_max           = '';
    public string $genre      = 'femme';
    public $pourcentage       = '';
    public bool $actif        = true;

    public bool $showModal  = false;
    public bool $isEditing  = false;
    public string $filtre_evenement = '';

    public function openModal(): void
    {
        $this->resetFields();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function resetFields(): void
    {
        $this->remise_id   = null;
        $this->id_evenement = '';
        $this->libelle      = '';
        $this->type         = 'nb_participants';
        $this->seuil_min    = '';
        $this->age_min      = '';
        $this->age_max      = '';
        $this->genre        = 'femme';
        $this->pourcentage  = '';
        $this->actif        = true;
        $this->resetErrorBag();
    }

    public function modifier(int $id): void
    {
        $r = Remise::findOrFail($id);
        $this->remise_id    = $r->id;
        $this->id_evenement = $r->id_evenement;
        $this->libelle      = $r->libelle;
        $this->type         = $r->type;
        $this->seuil_min    = $r->seuil_min;
        $this->age_min      = $r->age_min;
        $this->age_max      = $r->age_max;
        $this->genre        = $r->genre ?? 'femme';
        $this->pourcentage  = $r->pourcentage;
        $this->actif        = $r->actif;
        $this->isEditing    = true;
        $this->showModal    = true;
    }

    public function sauvegarder(): void
    {
        $regles = [
            'id_evenement' => 'required',
            'libelle'      => 'required|string|max:255',
            'type'         => 'required|in:nb_participants,age,genre',
            'pourcentage'  => 'required|numeric|min:0|max:100',
        ];

        if ($this->type === 'nb_participants') {
            $regles['seuil_min'] = 'required|integer|min:1';
        } elseif ($this->type === 'age') {
            $regles['age_min'] = 'required|integer|min:0';
            $regles['age_max'] = 'required|integer|gte:age_min';
        } elseif ($this->type === 'genre') {
            $regles['genre'] = 'required|in:homme,femme';
        }

        $this->validate($regles);

        $data = [
            'id_evenement' => $this->id_evenement,
            'libelle'      => $this->libelle,
            'type'         => $this->type,
            'seuil_min'    => $this->type === 'nb_participants' ? $this->seuil_min : null,
            'age_min'      => $this->type === 'age' ? $this->age_min : null,
            'age_max'      => $this->type === 'age' ? $this->age_max : null,
            'genre'        => $this->type === 'genre' ? $this->genre : null,
            'pourcentage'  => $this->pourcentage,
            'actif'        => $this->actif,
        ];

        if ($this->isEditing) {
            Remise::findOrFail($this->remise_id)->update($data);
            session()->flash('success', 'Remise modifiée.');
        } else {
            Remise::create($data);
            session()->flash('success', 'Remise créée.');
        }

        $this->closeModal();
    }

    public function supprimer(int $id): void
    {
        Remise::findOrFail($id)->delete();
        session()->flash('success', 'Remise supprimée.');
    }

    public function toggleActif(int $id): void
    {
        $r = Remise::findOrFail($id);
        $r->update(['actif' => !$r->actif]);
    }

    public function render()
{
    $layout = request()->is('superviseur/*') ? 'layouts.superviseur' : 'layouts.admin';

    return view('livewire.admin.gestion-remises', [
        'remises' => Remise::with('evenement')
            ->when($this->filtre_evenement, fn($q) =>
                $q->where('id_evenement', $this->filtre_evenement)
            )
            ->latest()
            ->get(),
        'evenements' => Evenement::orderBy('nom')->get(),
    ])->layout($layout, ['title' => 'Remises']);
}
}