<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Participant;
use Illuminate\Support\Facades\Hash;

class GestionChefsDelegation extends Component
{
    public $user_id;
    public string $name                  = '';
    public string $email                 = '';
    public string $password              = '';
    public string $password_confirmation = '';
    public bool   $showModal             = false;
    public bool   $isEditing             = false;
    public string $search                = '';

    public bool  $showIdentifiantsModal = false;
    public array $identifiants          = [];

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

    public function closeIdentifiantsModal(): void
    {
        $this->showIdentifiantsModal = false;
        $this->identifiants          = [];
    }

    public function resetFields(): void
    {
        $this->user_id               = null;
        $this->name                  = '';
        $this->email                 = '';
        $this->password              = '';
        $this->password_confirmation = '';
        $this->resetErrorBag();
    }

    public function modifier(int $id): void
    {
        $user            = User::findOrFail($id);
        $this->user_id   = $user->id;
        $this->name      = $user->name;
        $this->email     = $user->email;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function sauvegarder(): void
    {
        if ($this->isEditing) {
            $this->validate([
                'name'  => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $this->user_id,
            ]);

            User::findOrFail($this->user_id)->update([
                'name'  => $this->name,
                'email' => $this->email,
            ]);

            session()->flash('success', 'Chef de délégation modifié.');
            $this->closeModal();
        } else {
            $this->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
            ]);

            $user = User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $user->assignRole('cdd');

            $this->identifiants = [
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => $this->password,
            ];

            $this->closeModal();
            $this->showIdentifiantsModal = true;
        }
    }

    public function supprimer(int $id): void
    {
        $user = User::findOrFail($id);

        if (!$user->hasRole('cdd')) {
            session()->flash('error', 'Vous ne pouvez supprimer que des CDD.');
            return;
        }

        $user->delete();
        session()->flash('success', 'Chef de délégation supprimé.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-chefs-delegation', [
            'cdds' => User::with('roles')
                ->whereHas('roles', fn($q) => $q->where('name', 'cdd'))
                ->when($this->search, fn($q) =>
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                )
                ->latest()
                ->get(),
        ])->layout('layouts.admin', ['title' => 'Chefs de Délégation']);
    }
}