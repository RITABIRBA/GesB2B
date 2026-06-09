<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Traducteur;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GestionTraducteurs extends Component
{
    public $traducteur_id;
    public $nom       = '';
    public $prenom    = '';
    public $telephone = '';
    public $email     = '';
    public $langue    = '';
    public $showModal  = false;
    public $isEditing  = false;
    public $search     = '';

    // Modal compte créé
    public $showModalCompte   = false;
    public $compte_email      = '';
    public $compte_password   = '';

    // ← Modal voir compte
    public $showModalVoirCompte  = false;
    public $voir_compte_nom      = '';
    public $voir_compte_email    = '';
    public $nouveau_mot_de_passe = '';
    public $traducteur_reset_id  = null;

    public $langues = [
        'Français', 'Anglais', 'Arabe', 'Espagnol',
        'Portugais', 'Allemand', 'Chinois', 'Dioula',
        'Mooré', 'Fulfuldé',
    ];

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

    public function closeModalCompte()
    {
        $this->showModalCompte = false;
    }

    public function resetFields()
    {
        $this->traducteur_id = null;
        $this->nom           = '';
        $this->prenom        = '';
        $this->telephone     = '';
        $this->email         = '';
        $this->langue        = '';
        $this->resetErrorBag();
    }

    // ← Voir le compte d'un traducteur
    public function voirCompte($id)
    {
        $traducteur = Traducteur::findOrFail($id);
        $this->traducteur_reset_id  = $id;
        $this->voir_compte_nom      = $traducteur->nom . ' ' . $traducteur->prenom;
        $this->voir_compte_email    = $traducteur->email;
        $this->nouveau_mot_de_passe = '';
        $this->showModalVoirCompte  = true;
    }

    public function closeModalVoirCompte()
    {
        $this->showModalVoirCompte  = false;
        $this->traducteur_reset_id  = null;
        $this->voir_compte_nom      = '';
        $this->voir_compte_email    = '';
        $this->nouveau_mot_de_passe = '';
    }

    // ← Réinitialiser le mot de passe
    public function reinitialiserMotDePasse()
    {
        $traducteur = Traducteur::findOrFail($this->traducteur_reset_id);
        $user = User::find($traducteur->user_id);

        if (!$user) {
            session()->flash('error', 'Aucun compte trouvé pour ce traducteur.');
            $this->closeModalVoirCompte();
            return;
        }

        $nouveau_mdp = substr(str_shuffle(
            'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
        ), 0, 8);

        $user->update(['password' => Hash::make($nouveau_mdp)]);
        $this->nouveau_mot_de_passe = $nouveau_mdp;
    }

    public function modifier($id)
    {
        $t = Traducteur::findOrFail($id);
        $this->traducteur_id = $t->id;
        $this->nom           = $t->nom;
        $this->prenom        = $t->prenom;
        $this->telephone     = $t->telephone;
        $this->email         = $t->email;
        $this->langue        = $t->langue;
        $this->isEditing     = true;
        $this->showModal     = true;
    }

    public function sauvegarder()
    {
        $this->validate([
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email'     => $this->isEditing
                ? 'nullable|email|max:255'
                : 'required|email|max:255|unique:users,email',
            'langue'    => 'required|string|max:255',
        ]);

        $data = [
            'nom'       => $this->nom,
            'prenom'    => $this->prenom,
            'telephone' => $this->telephone,
            'email'     => $this->email ?: null,
            'langue'    => $this->langue,
        ];

        if ($this->isEditing) {
            Traducteur::findOrFail($this->traducteur_id)->update($data);
            session()->flash('success', 'Traducteur modifié avec succès.');
            $this->closeModal();
        } else {
            $password = substr(str_shuffle(
                'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
            ), 0, 8);

            $user = User::create([
                'name'     => $this->nom . ' ' . $this->prenom,
                'email'    => $this->email,
                'password' => Hash::make($password),
            ]);
            $user->assignRole('traducteur');

            $data['user_id'] = $user->id;
            Traducteur::create($data);

            $this->compte_email    = $this->email;
            $this->compte_password = $password;
            $this->showModalCompte = true;

            $this->closeModal();
        }
    }

    public function supprimer($id)
    {
        $traducteur = Traducteur::findOrFail($id);
        if ($traducteur->user_id) {
            User::find($traducteur->user_id)?->delete();
        }
        $traducteur->delete();
        session()->flash('success', 'Traducteur supprimé.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-traducteurs', [
            'traducteurs' => Traducteur::when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('prenom', 'like', '%'.$this->search.'%')
                      ->orWhere('langue', 'like', '%'.$this->search.'%')
                )
                ->latest()
                ->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Traducteurs']);
    }
}