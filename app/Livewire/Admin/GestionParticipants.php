<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\Evenement;
use App\Models\Inscription;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GestionParticipants extends Component
{
    public $participant_id;
    public $id_entreprise      = '';
    public $id_evenement       = '';
    public $nom                = '';
    public $prenom             = '';
    public $genre              = '';
    public $fonction           = '';
    public $ifu                = '';
    public $secteur_activite   = '';
    public $nouveau_secteur    = '';
    public $utiliser_nouveau_secteur = '';
    public $email              = '';
    public $telephone          = '';
    public $role               = 'participant';
    public $statut_historique  = 'actif';
    public $participation_rdv  = true;
    public $showModal          = false;
    public $isEditing          = false;
    public $search             = '';
    public $filtre_statut      = '';

    // Modal info compte
    public $showModalCompte    = false;
    public $compte_email       = '';
    public $compte_password    = '';
    public $compte_code_acces  = '';
    public $compte_has_email   = false;

    public $roles = ['participant', 'exposant', 'organisateur'];

    public $secteurs = [
        'Agriculture', 'Industrie', 'Commerce', 'Services',
        'Technologie', 'Transport', 'Construction', 'Tourisme',
        'Santé', 'Education', 'Finance', 'Energie', 'Mines',
        'Artisanat', 'Autre',
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
        $this->participant_id           = null;
        $this->id_entreprise            = '';
        $this->id_evenement             = '';
        $this->nom                      = '';
        $this->prenom                   = '';
        $this->genre                    = '';
        $this->fonction                 = '';
        $this->ifu                      = '';
        $this->secteur_activite         = '';
        $this->nouveau_secteur          = '';
        $this->utiliser_nouveau_secteur = '';
        $this->email                    = '';
        $this->telephone                = '';
        $this->role                     = 'participant';
        $this->statut_historique        = 'actif';
        $this->participation_rdv        = true;
        $this->resetErrorBag();
    }

    public function modifier($id)
    {
        $p = Participant::findOrFail($id);
        $this->participant_id    = $p->id;
        $this->id_entreprise     = $p->id_entreprise;
        $this->id_evenement      = $p->id_evenement;
        $this->nom               = $p->nom;
        $this->prenom            = $p->prenom;
        $this->genre             = $p->genre;
        $this->fonction          = $p->fonction;
        $this->ifu               = $p->ifu;
        $this->secteur_activite  = $p->secteur_activite;
        $this->email             = $p->email;
        $this->telephone         = $p->telephone;
        $this->role              = $p->role;
        $this->statut_historique = $p->statut_historique;
        $this->participation_rdv = $p->participation_rdv;
        $this->isEditing         = true;
        $this->showModal         = true;
    }

    public function toggleStatut($id)
    {
        $p = Participant::findOrFail($id);
        $p->update([
            'statut_historique' => $p->statut_historique == 'actif' ? 'inactif' : 'actif'
        ]);
    }

    public function voirCompte($id)
    {
        $participant = Participant::findOrFail($id);
        $this->compte_email      = $participant->email ?? '';
        $this->compte_password   = '';
        $this->compte_code_acces = $participant->code_acces;
        $this->compte_has_email  = !empty($participant->email);
        $this->showModalCompte   = true;
    }

    public function sauvegarder()
    {
        $this->validate([
            'id_evenement' => 'required',
            'nom'          => 'required|string|max:255',
            'prenom'       => 'required|string|max:255',
            'telephone'    => 'required|string|max:20',
            'email'        => $this->isEditing
                ? 'nullable|email|max:255'
                : 'nullable|email|max:255|unique:users,email',
            'ifu'          => 'nullable|string|max:255',
            'role'         => 'required',
            'genre'        => 'nullable|in:homme,femme',
            'fonction'     => 'nullable|string|max:255',
        ]);

        $secteur = $this->utiliser_nouveau_secteur === '1'
            ? $this->nouveau_secteur
            : $this->secteur_activite;

        $code_acces = strtoupper(substr($this->nom, 0, 3) . rand(1000, 9999));

        $data = [
            'id_entreprise'     => $this->id_entreprise ?: null,
            'id_evenement'      => $this->id_evenement,
            'nom'               => $this->nom,
            'prenom'            => $this->prenom,
            'genre'             => $this->genre ?: null,
            'fonction'          => $this->fonction ?: null,
            'ifu'               => $this->ifu ?: null,
            'secteur_activite'  => $secteur,
            'email'             => $this->email ?: null,
            'telephone'         => $this->telephone,
            'role'              => $this->role,
            'statut_historique' => $this->statut_historique,
            'participation_rdv' => $this->participation_rdv,
        ];

        if ($this->isEditing) {
            Participant::findOrFail($this->participant_id)->update($data);
            session()->flash('success', 'Participant modifié avec succès.');
            $this->closeModal();
        } else {
            $data['code_acces'] = $code_acces;
            $participant = Participant::create($data);

            // ← Crée automatiquement l'inscription
            $evenement = Evenement::find($this->id_evenement);
            if ($evenement) {
                $inscriptionExiste = Inscription::where('id_participant', $participant->id)
                    ->where('id_evenement', $this->id_evenement)
                    ->exists();

                if (!$inscriptionExiste) {
                    // ← Détermine statut selon type paiement
                    $montant = $evenement->montant_inscription ?? 0;
                    $statut  = 'en_attente';

                    if ($evenement->type_paiement == 'gratuit') {
                        $montant = 0;
                        $statut  = 'paye'; // ← Gratuit = validé auto
                    } elseif ($evenement->type_paiement == 'par_entreprise'
                        && $participant->id_entreprise) {
                        $montant = 0;
                        $statut  = 'en_attente';
                    }

                    Inscription::create([
                        'id_participant'   => $participant->id,
                        'id_evenement'     => $this->id_evenement,
                        'date_inscription' => now()->toDateString(),
                        'montant_paye'     => $montant,
                        'statut_paiement'  => $statut,
                        'statut_presence'  => 'absent',
                    ]);
                }
            }

            $password_genere = null;

            // ← Crée compte USER si email fourni
            if ($this->email) {
                $password_genere = substr(str_shuffle(
                    'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
                ), 0, 8);

                $user = User::create([
                    'name'     => $this->nom . ' ' . $this->prenom,
                    'email'    => $this->email,
                    'password' => Hash::make($password_genere),
                ]);
                $user->assignRole('participant');
            }

            $this->compte_email      = $this->email;
            $this->compte_password   = $password_genere;
            $this->compte_code_acces = $code_acces;
            $this->compte_has_email  = !empty($this->email);
            $this->showModalCompte   = true;

            $this->closeModal();
        }
    }

    public function supprimer($id)
    {
        $participant = Participant::findOrFail($id);
        $user = User::where('email', $participant->email)->first();
        if ($user) $user->delete();
        $participant->delete();
        session()->flash('success', 'Participant supprimé.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-participants', [
            'participants' => Participant::with(['entreprise', 'evenement'])
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('prenom', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%')
                )
                ->when($this->filtre_statut, fn($q) =>
                    $q->where('statut_historique', $this->filtre_statut)
                )
                ->latest()->get(),
            'entreprises' => Entreprise::where('statut_validation', 'valide')->get(),
            'evenements'  => Evenement::orderBy('nom')->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Participants']);
    }
}