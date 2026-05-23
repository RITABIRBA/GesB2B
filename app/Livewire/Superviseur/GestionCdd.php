<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

/**
 * Composant Superviseur — Gestion des CDD
 *
 * Le superviseur peut créer et gérer les comptes CDD,
 * Entreprises et Participants selon le cahier des charges.
 */
class GestionCdd extends Component
{
    public $user_id;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = 'cdd';
    public $showModal = false;
    public $isEditing = false;
    public $search = '';

    // Rôles que le superviseur peut créer
    public $roles_autorises = ['cdd', 'entreprise', 'participant'];

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
        $this->user_id               = null;
        $this->name                  = '';
        $this->email                 = '';
        $this->password              = '';
        $this->password_confirmation = '';
        $this->role                  = 'cdd';
        $this->resetErrorBag();
    }

    public function modifier($id)
    {
        $user = User::findOrFail($id);
        $this->user_id   = $user->id;
        $this->name      = $user->name;
        $this->email     = $user->email;
        $this->role      = $user->getRoleNames()->first() ?? 'cdd';
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function sauvegarder()
    {
        if ($this->isEditing) {
            $this->validate([
                'name'  => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,'.$this->user_id,
                'role'  => 'required|in:cdd,entreprise,participant',
            ]);

            $user = User::findOrFail($this->user_id);
            $user->update([
                'name'  => $this->name,
                'email' => $this->email,
            ]);
            $user->syncRoles([$this->role]);
            session()->flash('success', 'Utilisateur modifié avec succès.');
        } else {
            $this->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'role'     => 'required|in:cdd,entreprise,participant',
            ]);

            $user = User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $user->assignRole($this->role);
            session()->flash('success', 'Utilisateur créé avec succès.');
        }

        $this->closeModal();
    }

    public function supprimer($id)
    {
        $user = User::findOrFail($id);
        $role = $user->getRoleNames()->first();

        // Le superviseur ne peut supprimer que CDD, entreprise, participant
        if (!in_array($role, ['cdd', 'entreprise', 'participant'])) {
            session()->flash('error', 'Vous n\'avez pas le droit de supprimer cet utilisateur.');
            return;
        }

        $user->delete();
        session()->flash('success', 'Utilisateur supprimé.');
    }

    public function render()
    {
        return view('livewire.superviseur.gestion-cdd', [
            'utilisateurs' => User::with('roles')
                ->whereHas('roles', fn($q) =>
                    $q->whereIn('name', ['cdd', 'entreprise', 'participant'])
                )
                ->when($this->search, fn($q) =>
                    $q->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%')
                )
                ->latest()
                ->get(),
        ])->layout('layouts.superviseur', ['title' => 'Gestion des Accès']);
    }
}