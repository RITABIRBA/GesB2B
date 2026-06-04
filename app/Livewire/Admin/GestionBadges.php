<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Badge;
use App\Models\TypeBadge;
use App\Models\Participant;
use App\Models\Evenement;
use Illuminate\Support\Str;

class GestionBadges extends Component
{
    public $badge_id;
    public $id_participant = '';
    public $id_type_badge  = '';
    public $qr_code        = '';
    public $showModal      = false;
    public $isEditing      = false;
    public $search         = '';

    // ← Génération par type et événement
    public $showGenererModal   = false;
    public $type_badge_generer = '';
    public $id_evenement_generer = '';
    public $participants_selectionnes = [];
    public $participants_disponibles  = [];

    public function openModal()
    {
        $this->resetFields();
        $this->qr_code   = strtoupper('BADGE-' . Str::random(8));
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->badge_id       = null;
        $this->id_participant = '';
        $this->id_type_badge  = '';
        $this->qr_code        = '';
        $this->resetErrorBag();
    }

    // =========================================================
    // MODAL GÉNÉRATION PAR TYPE
    // =========================================================

    public function openGenererModal()
    {
        $this->showGenererModal         = true;
        $this->type_badge_generer       = '';
        $this->id_evenement_generer     = '';
        $this->participants_selectionnes = [];
        $this->participants_disponibles  = [];
    }

    public function closeGenererModal()
    {
        $this->showGenererModal         = false;
        $this->type_badge_generer       = '';
        $this->id_evenement_generer     = '';
        $this->participants_selectionnes = [];
        $this->participants_disponibles  = [];
    }

    // ← Quand l'événement change → charge les participants
    public function updatedIdEvenementGenerer($value)
    {
        if ($value) {
            $this->participants_disponibles = Participant::with('entreprise')
                ->where('id_evenement', $value)
                ->orderBy('nom')
                ->get()
                ->toArray();
        } else {
            $this->participants_disponibles = [];
        }
        $this->participants_selectionnes = [];
    }

    // ← Sélectionner tous les participants
    public function selectionnerTous()
    {
        $this->participants_selectionnes = collect($this->participants_disponibles)
            ->pluck('id')
            ->toArray();
    }

    // ← Désélectionner tous
    public function deselectionnerTous()
    {
        $this->participants_selectionnes = [];
    }

    // ← Génère les badges pour les participants sélectionnés
    public function genererParType()
    {
        $this->validate([
            'type_badge_generer'       => 'required',
            'id_evenement_generer'     => 'required',
            'participants_selectionnes' => 'required|array|min:1',
        ], [
            'participants_selectionnes.required' => 'Sélectionnez au moins un participant.',
            'participants_selectionnes.min'      => 'Sélectionnez au moins un participant.',
        ]);

        // Trouve ou crée le type de badge
        $typeBadge = TypeBadge::firstOrCreate(
            ['libelle' => $this->type_badge_generer],
            ['description' => 'Badge ' . $this->type_badge_generer]
        );

        $count = 0;
        foreach ($this->participants_selectionnes as $id_participant) {
            // Vérifie si badge existe déjà
            $existe = Badge::where('id_participant', $id_participant)
                           ->where('id_type_badge', $typeBadge->id)
                           ->exists();

            if (!$existe) {
                $participant = Participant::find($id_participant);
                if (!$participant) continue;

                $qrCode = strtoupper(
                    substr($participant->nom, 0, 2) .
                    substr($participant->prenom ?? 'XX', 0, 2) .
                    '-' .
                    $this->id_evenement_generer .
                    '-' .
                    Str::random(6)
                );

                Badge::create([
                    'id_participant' => $id_participant,
                    'id_type_badge'  => $typeBadge->id,
                    'qr_code'        => $qrCode,
                ]);
                $count++;
            }
        }

        $this->closeGenererModal();

        if ($count === 0) {
            session()->flash('error', 'Ces participants ont déjà des badges de ce type !');
        } else {
            session()->flash('success', $count . ' badge(s) de type "' . $this->type_badge_generer . '" générés !');
        }
    }

    
    // ACTIONS INDIVIDUELLES
    
    public function modifier($id)
    {
        $badge = Badge::findOrFail($id);
        $this->badge_id       = $badge->id;
        $this->id_participant = $badge->id_participant;
        $this->id_type_badge  = $badge->id_type_badge;
        $this->qr_code        = $badge->qr_code;
        $this->isEditing      = true;
        $this->showModal      = true;
    }

    public function regenererQrCode()
    {
        $this->qr_code = strtoupper('BADGE-' . Str::random(8));
    }

    public function sauvegarder()
    {
        $this->validate([
            'id_participant' => 'required',
            'id_type_badge'  => 'required',
            'qr_code'        => 'required|string|max:255',
        ]);

        $data = [
            'id_participant' => $this->id_participant,
            'id_type_badge'  => $this->id_type_badge,
            'qr_code'        => $this->qr_code,
        ];

        if ($this->isEditing) {
            Badge::findOrFail($this->badge_id)->update($data);
            session()->flash('success', 'Badge modifié avec succès.');
        } else {
            $existe = Badge::where('id_participant', $this->id_participant)
                           ->where('id_type_badge', $this->id_type_badge)
                           ->exists();

            if ($existe) {
                session()->flash('error', 'Ce participant a déjà un badge de ce type !');
                $this->closeModal();
                return;
            }

            Badge::create($data);
            session()->flash('success', 'Badge créé avec succès.');
        }

        $this->closeModal();
    }

    public function supprimer($id)
    {
        Badge::findOrFail($id)->delete();
        session()->flash('success', 'Badge supprimé.');
    }

    public function genererTousBadges()
    {
        $typeBadgeDefaut = TypeBadge::first();

        if (!$typeBadgeDefaut) {
            session()->flash('error', 'Aucun type de badge configuré !');
            return;
        }

        $participants = Participant::whereDoesntHave('badge')->get();
        $count        = 0;

        foreach ($participants as $participant) {
            Badge::create([
                'id_participant' => $participant->id,
                'id_type_badge'  => $typeBadgeDefaut->id,
                'qr_code'        => strtoupper('BADGE-' . Str::random(8)),
            ]);
            $count++;
        }

        if ($count === 0) {
            session()->flash('error', 'Tous les participants ont déjà un badge !');
            return;
        }

        session()->flash('success', $count . ' badge(s) généré(s) avec succès.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-badges', [
            'badges' => Badge::with([
                    'participant',
                    'participant.entreprise',
                    'typeBadge',
                ])
                ->when($this->search, fn($q) =>
                    $q->whereHas('participant', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )->orWhere('qr_code', 'like', '%'.$this->search.'%')
                )
                ->latest()
                ->get(),
            'participants' => Participant::with('entreprise')->orderBy('nom')->get(),
            'typesBadges'  => TypeBadge::orderBy('libelle')->get(),
            'evenements'   => Evenement::orderBy('nom')->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Badges']);
    }
}