<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\Evenement;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MesParticipants extends Component
{
    public $participant_id;
    public $id_evenement = '';
    public $nom = '';
    public $prenom = '';
    public $genre = '';
    public $fonction = '';
    public $email = '';
    public $telephone = '';
    public $role = 'exposant';
    public $showModal = false;
    public $isEditing = false;
    public $search = '';

    public $roles = ['exposant', 'visiteur', 'vip', 'organisateur'];

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
        $this->genre          = '';
        $this->fonction       = '';
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
        $this->genre          = $p->genre;
        $this->fonction       = $p->fonction;
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

        // Liaison par nom ✅
        $entreprise = Entreprise::where('nom', auth()->user()->name)->first();

        if (!$entreprise) {
            session()->flash('error', 'Entreprise non trouvée.');
            return;
        }

        $code = strtoupper(substr($this->nom, 0, 3) . rand(1000, 9999));

        $data = [
            'id_entreprise'    => $entreprise->id,
            'id_cdd'           => $entreprise->id_cdd,
            'id_evenement'     => $this->id_evenement,
            'nom'              => $this->nom,
            'prenom'           => $this->prenom,
            'genre'            => $this->genre,
            'fonction'         => $this->fonction,
            'email'            => $this->email,
            'telephone'        => $this->telephone,
            'role'             => $this->role,
            'secteur_activite' => $entreprise->secteur_activite,
            'statut_historique' => 'actif',
        ];

        if ($this->isEditing) {
            Participant::findOrFail($this->participant_id)->update($data);
            session()->flash('success', 'Participant modifié.');
        } else {
            // Génère le code d'accès
            $data['code_acces'] = $code;

            // Crée le participant
            Participant::create($data);

            // Crée aussi un compte USER si email fourni
            if ($this->email) {
                $userExiste = User::where('email', $this->email)->exists();
                if (!$userExiste) {
                    $user = User::create([
                        'name'     => $this->nom . ' ' . $this->prenom,
                        'email'    => $this->email,
                        'password' => Hash::make($code), // Mot de passe = code d'accès
                    ]);
                    $user->assignRole('participant');
                }
            }

            session()->flash('success', "Participant ajouté ! Code d'accès : {$code}");
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
        // Liaison par nom ✅
        $entreprise = Entreprise::where('nom', auth()->user()->name)->first();

        return view('livewire.entreprise.mes-participants', [
            'participants' => $entreprise
                ? Participant::where('id_entreprise', $entreprise->id)
                    ->when($this->search, fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )
                    ->latest()
                    ->get()
                : collect(),
            'evenements'  => Evenement::orderBy('nom')->get(),
            'entreprise'  => $entreprise,
        ])->layout('layouts.entreprise', ['title' => 'Mes Participants']);
    }
}