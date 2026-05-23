<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

/**
 * Composant Livewire — Gestion des Utilisateurs
 *
 * Permet à l'administrateur de gérer les comptes utilisateurs.
 * Fonctionnalités :
 * - Lister les utilisateurs avec leurs rôles
 * - Créer un utilisateur avec un rôle
 * - Modifier le rôle et les infos d'un utilisateur
 * - Changer le mot de passe
 * - Supprimer un utilisateur
 */
class GestionUtilisateurs extends Component
{
    // =========================================================
    // PROPRIÉTÉS DU FORMULAIRE
    // =========================================================

    /** @var int|null Identifiant de l'utilisateur en cours de modification */
    public $user_id;

    /** @var string Nom de l'utilisateur */
    public $name = '';

    /** @var string Email de l'utilisateur */
    public $email = '';

    /** @var string Mot de passe */
    public $password = '';

    /** @var string Confirmation du mot de passe */
    public $password_confirmation = '';

    /** @var string Rôle sélectionné */
    public $role = '';

    // =========================================================
    // PROPRIÉTÉS DE L'INTERFACE
    // =========================================================

    /** @var bool Affichage du modal création/modification */
    public $showModal = false;

    /** @var bool Mode modification */
    public $isEditing = false;

    /** @var bool Affichage du modal changement mot de passe */
    public $showPasswordModal = false;

    /** @var int|null ID de l'utilisateur pour changer le mot de passe */
    public $password_user_id;

    /** @var string Nouveau mot de passe */
    public $new_password = '';

    /** @var string Confirmation nouveau mot de passe */
    public $new_password_confirmation = '';

    /** @var string Texte de recherche */
    public $search = '';

    // =========================================================
    // GESTION DU MODAL PRINCIPAL
    // =========================================================

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
        $this->role                  = '';
        $this->resetErrorBag();
    }

    // =========================================================
    // GESTION DU MODAL MOT DE PASSE
    // =========================================================

    public function ouvrirModalPassword($id)
    {
        $this->password_user_id          = $id;
        $this->new_password              = '';
        $this->new_password_confirmation = '';
        $this->showPasswordModal         = true;
    }

    public function fermerModalPassword()
    {
        $this->showPasswordModal         = false;
        $this->password_user_id          = null;
        $this->new_password              = '';
        $this->new_password_confirmation = '';
        $this->resetErrorBag();
    }

    // =========================================================
    // ACTIONS CRUD
    // =========================================================

    /**
     * Charge les données d'un utilisateur pour modification.
     */
    public function modifier($id)
    {
        $user = User::findOrFail($id);

        $this->user_id   = $user->id;
        $this->name      = $user->name;
        $this->email     = $user->email;
        $this->role      = $user->getRoleNames()->first() ?? '';
        $this->isEditing = true;
        $this->showModal = true;
    }

    /**
     * Valide et sauvegarde l'utilisateur.
     */
    public function sauvegarder()
    {
        if ($this->isEditing) {
            $this->validate([
                'name'  => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,'.$this->user_id,
                'role'  => 'required|string',
            ]);

            $user = User::findOrFail($this->user_id);
            $user->update([
                'name'  => $this->name,
                'email' => $this->email,
            ]);

            // Met à jour le rôle
            $user->syncRoles([$this->role]);
            session()->flash('success', 'Utilisateur modifié avec succès.');

        } else {
            $this->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'role'     => 'required|string',
            ]);

            $user = User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => Hash::make($this->password),
            ]);

            // Assigne le rôle
            $user->assignRole($this->role);
            session()->flash('success', 'Utilisateur créé avec succès.');
        }

        $this->closeModal();
    }

    /**
     * Change le mot de passe d'un utilisateur.
     */
    public function changerMotDePasse()
    {
        $this->validate([
            'new_password' => 'required|min:8|confirmed',
        ]);

        User::findOrFail($this->password_user_id)->update([
            'password' => Hash::make($this->new_password),
        ]);

        session()->flash('success', 'Mot de passe changé avec succès.');
        $this->fermerModalPassword();
    }

    /**
     * Supprime un utilisateur.
     */
    public function supprimer($id)
    {
        // Empêche la suppression de son propre compte
        if ($id === auth()->id()) {
            session()->flash('error', 'Vous ne pouvez pas supprimer votre propre compte !');
            return;
        }

        User::findOrFail($id)->delete();
        session()->flash('success', 'Utilisateur supprimé.');
    }



    public function render()
    {
        return view('livewire.admin.gestion-utilisateurs', [
            'utilisateurs' => User::with('roles')
                ->when($this->search, fn($q) =>
                    $q->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%')
                )
                ->latest()
                ->get(),
            'roles' => Role::orderBy('name')->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Utilisateurs']);
    }
}