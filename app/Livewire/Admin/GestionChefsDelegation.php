<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\ChefDelegation;
use App\Models\Evenement;
use Illuminate\Support\Facades\Hash;

class GestionChefsDelegation extends Component
{
    public $cdd_id;
    public string $nom        = '';
    public string $email      = '';
    public string $password   = '';
    public string $password_confirmation = '';
    public string $telephone  = '';
    public string $pays       = '';
    public string $zone_autre = '';
    public bool   $est_admin  = false;
    public $id_evenement      = ''; // ✅ un seul événement (colonne directe)

    public bool   $showModal  = false;
    public bool   $isEditing  = false;
    public string $search     = '';

    public bool  $showIdentifiantsModal = false;
    public array $identifiants          = [];

    public array $pays_liste = [
        'Bénin', 'Burkina Faso', 'Côte d\'Ivoire', 'Ghana', 'Mali',
        'Niger', 'Nigeria', 'Sénégal', 'Togo', 'France', 'Autre',
    ];

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
        $this->cdd_id      = null;
        $this->nom         = '';
        $this->email       = '';
        $this->password    = '';
        $this->password_confirmation = '';
        $this->telephone   = '';
        $this->pays        = '';
        $this->zone_autre  = '';
        $this->est_admin   = false;
        $this->id_evenement = '';
        $this->resetErrorBag();
    }

    public function modifier(int $id): void
    {
        $cdd = ChefDelegation::findOrFail($id);
        $this->cdd_id      = $cdd->id;
        $this->nom         = $cdd->nom ?? '';
        $this->email       = $cdd->email ?? '';
        $this->telephone   = $cdd->telephone ?? '';
        $this->pays        = $cdd->pays_zone;
        $this->zone_autre  = $cdd->zone_personnalisee ?? '';
        $this->est_admin   = $cdd->est_admin;
        $this->id_evenement = $cdd->id_evenement ?? '';
        $this->isEditing   = true;
        $this->showModal   = true;
    }

    public function sauvegarder(): void
    {
        $regles = [
            'nom'  => 'required|string|max:255',
            'pays' => 'required|string',
        ];

        if ($this->pays === 'Autre') {
            $regles['zone_autre'] = 'required|string|max:255';
        }

        if (!$this->isEditing && !$this->est_admin) {
            $regles['email']    = 'required|email|unique:users,email';
            $regles['password'] = 'required|min:8|confirmed';
        }

        $this->validate($regles, [
            'zone_autre.required' => 'Veuillez préciser la zone couverte.',
        ]);

        if ($this->isEditing) {
            $cdd = ChefDelegation::findOrFail($this->cdd_id);
            $cdd->update([
                'nom'                => $this->nom,
                'pays_zone'          => $this->pays,
                'zone_personnalisee' => $this->pays === 'Autre' ? $this->zone_autre : null,
                'telephone'          => $this->telephone ?: null,
                'est_admin'          => $this->est_admin,
                'id_evenement'       => $this->id_evenement ?: null,
            ]);

            session()->flash('success', 'Chef de délégation modifié.');
            $this->closeModal();
            return;
        }

        if ($this->est_admin) {
            ChefDelegation::create([
                'id_user'            => auth()->id(),
                'nom'                => $this->nom,
                'email'              => auth()->user()->email,
                'telephone'          => $this->telephone ?: null,
                'pays_zone'          => $this->pays,
                'zone_personnalisee' => $this->pays === 'Autre' ? $this->zone_autre : null,
                'est_admin'          => true,
                'id_evenement'       => $this->id_evenement ?: null,
            ]);

            session()->flash('success', 'Vous êtes maintenant CDD pour cette zone.');
            $this->closeModal();
            return;
        }

        $user = User::create([
            'name'     => $this->nom,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
        ]);
        $user->assignRole('cdd');

        ChefDelegation::create([
            'id_user'            => $user->id,
            'nom'                => $this->nom,
            'email'              => $this->email,
            'telephone'          => $this->telephone ?: null,
            'pays_zone'          => $this->pays,
            'zone_personnalisee' => $this->pays === 'Autre' ? $this->zone_autre : null,
            'est_admin'          => false,
            'id_evenement'       => $this->id_evenement ?: null,
        ]);

        $this->identifiants = [
            'name'     => $this->nom,
            'email'    => $this->email,
            'password' => $this->password,
        ];

        $this->closeModal();
        $this->showIdentifiantsModal = true;
    }

    public function supprimer(int $id): void
    {
        $cdd = ChefDelegation::findOrFail($id);

        if ($cdd->id_user && !$cdd->est_admin) {
            User::find($cdd->id_user)?->delete();
        }

        $cdd->delete();
        session()->flash('success', 'Chef de délégation supprimé.');
    }

    public function render()
    {
        $layout = request()->is('superviseur/*') ? 'layouts.superviseur' : 'layouts.admin';

        return view('livewire.admin.gestion-chefs-delegation', [
            'cdds' => ChefDelegation::withCount('membres')
                ->with('evenement')
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('pays_zone', 'like', '%' . $this->search . '%')
                )
                ->latest()
                ->get(),
            'evenements' => Evenement::orderBy('nom')->get(),
        ])->layout($layout, ['title' => 'Chefs de Délégation']);
    }
}