<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GestionCdd extends Component
{
    public $user_id;
    public $name     = '';
    public $email    = '';
    public $password = '';
    public $password_confirmation = '';
    public $showModal  = false;
    public $isEditing  = false;
    public $search     = '';

    // Modal identifiants après création
    public $showIdentifiantsModal = false;
    public $identifiants          = [];

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

    public function closeIdentifiantsModal()
    {
        $this->showIdentifiantsModal = false;
        $this->identifiants          = [];
    }

    public function resetFields()
    {
        $this->user_id               = null;
        $this->name                  = '';
        $this->email                 = '';
        $this->password              = '';
        $this->password_confirmation = '';
        $this->resetErrorBag();
    }

    public function modifier($id)
    {
        $user = User::findOrFail($id);
        $this->user_id   = $user->id;
        $this->name      = $user->name;
        $this->email     = $user->email;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function sauvegarder()
    {
        if ($this->isEditing) {
            $this->validate([
                'name'  => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,'.$this->user_id,
            ]);

            User::findOrFail($this->user_id)->update([
                'name'  => $this->name,
                'email' => $this->email,
            ]);

            session()->flash('success', 'CDD modifié avec succès.');
            $this->closeModal();

        } else {
            $this->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
            ]);

            // ← Crée uniquement un compte CDD
            $user = User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $user->assignRole('cdd'); // ← Toujours CDD

            // ← Affiche les identifiants
            $this->identifiants = [
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => $this->password,
            ];

            $this->closeModal();
            $this->showIdentifiantsModal = true;
        }
    }

    public function supprimer($id)
    {
        $user = User::findOrFail($id);

        // ← Vérifie que c'est bien un CDD
        if (!$user->hasRole('cdd')) {
            session()->flash('error', 'Vous ne pouvez supprimer que des comptes CDD.');
            return;
        }

        $user->delete();
        session()->flash('success', 'CDD supprimé.');
    }

    public function render()
    {
        return view('livewire.superviseur.gestion-cdd', [
            // ← Affiche uniquement les CDD
            'cdds' => User::with('roles')
                ->whereHas('roles', fn($q) =>
                    $q->where('name', 'cdd')
                )
                ->when($this->search, fn($q) =>
                    $q->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%')
                )
                ->latest()
                ->get(),
        ])->layout('layouts.superviseur', ['title' => 'Gestion des CDD']);
    }
}