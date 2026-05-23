<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\Evenement;

class MesParticipants extends Component
{
    public $participant_id;
    public $id_evenement = '';
    public $nom = '';
    public $prenom = '';
    public $email = '';
    public $telephone = '';
    public $role = 'exposant';
    public $showModal = false;
    public $isEditing = false;
    public $search = '';

    public $roles = ['exposant', 'visiteur'];

    public function openModal()
    {
        $this->resetFields();
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
        $this->participant_id = null;
        $this->id_evenement   = '';
        $this->nom            = '';
        $this->prenom         = '';
        $this->email          = '';
        $this->telephone      = '';
        $this->role           = 'exposant';
        $this->resetErrorBag();
    }

    public function modifier($id)
    {
        $p = Participant::findOrFail($id);
        $this->participant_id = $p->id;
        $this->id_evenement   = $p->id_evenement;
        $this->nom            = $p->nom;
        $this->prenom         = $p->prenom;
        $this->email          = $p->email;
        $this->telephone      = $p->telephone;
        $this->role           = $p->role;
        $this->isEditing      = true;
        $this->showModal      = true;
    }

    public function sauvegarder()
    {
        $this->validate([
            'id_evenement' => 'required',
            'nom'          => 'required|string|max:255',
            'prenom'       => 'required|string|max:255',
            'email'        => 'nullable|email|max:255',
            'telephone'    => 'required|string|max:20',
            'role'         => 'required',
        ]);

        $entreprise = Entreprise::first();

        $data = [
            'id_entreprise' => $entreprise->id,
            'id_evenement'  => $this->id_evenement,
            'nom'           => $this->nom,
            'prenom'        => $this->prenom,
            'email'         => $this->email,
            'telephone'     => $this->telephone,
            'role'          => $this->role,
        ];

        if ($this->isEditing) {
            Participant::findOrFail($this->participant_id)->update($data);
            session()->flash('success', 'Participant modifié.');
        } else {
            $data['code_acces']      = strtoupper(substr($this->nom, 0, 3) . rand(1000, 9999));
            $data['secteur_activite'] = $entreprise->secteur_activite;
            Participant::create($data);
            session()->flash('success', 'Participant ajouté.');
        }

        $this->closeModal();
    }

    public function supprimer($id)
    {
        Participant::findOrFail($id)->delete();
        session()->flash('success', 'Participant supprimé.');
    }

    public function render()
    {
        $entreprise = Entreprise::first();

        return view('livewire.entreprise.mes-participants', [
            'participants' => Participant::where('id_entreprise', $entreprise->id)
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('prenom', 'like', '%'.$this->search.'%')
                )
                ->latest()->get(),
            'evenements' => Evenement::orderBy('nom')->get(),
        ])->layout('layouts.entreprise', ['title' => 'Mes Participants']);
    }
}