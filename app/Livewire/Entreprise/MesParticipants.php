<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Gestion des Membres de l'Entreprise
 *
 * Le représentant peut :
 * → Voir tous les membres de son entreprise
 * → Ajouter de nouveaux membres
 * → Valider ou rejeter les demandes d'adhésion
 * → Un code d'accès est généré pour chaque membre
 */
class MesParticipants extends Component
{
    // ────────────────────────────────────────────────────────
    // PROPRIÉTÉS
    // ────────────────────────────────────────────────────────

    /** ID du membre en cours de modification */
    public $participant_id;

    /** Formulaire d'ajout/modification de membre */
    public string $nom            = '';
    public string $prenom         = '';
    public string $genre          = '';
    public string $fonction       = '';
    public string $fonction_autre = '';
    public string $email          = '';
    public string $telephone      = '';

    // ✅ NOUVEAU
    public string $date_naissance = '';
    public string $filiere        = '';
    public string $universite     = '';

    /** Contrôle du modal */
    public bool $showModal  = false;
    public bool $isEditing  = false;

    /** Recherche */
    public string $search = '';

    /** Modal affichage du code d'accès généré */
    public bool  $showCodeModal = false;
    public array $nouveauMembre = [];

    /** Liste des fonctions disponibles */
    public array $fonctions = [
        'Directeur Général',
        'Directeur Commercial',
        'PDG',
        'Gérant',
        'Responsable Export',
        'Responsable Partenariats',
        'Chargé de Développement',
        'Commercial',
        'Technicien',
        'Représentant',
        // ✅ NOUVEAU
        'Étudiant',
        'Autre',
    ];

    // ────────────────────────────────────────────────────────
    // HELPERS PRIVÉS
    // ────────────────────────────────────────────────────────

    /**
     * Récupère l'entreprise du représentant connecté.
     */
    private function getEntreprise(): ?Entreprise
    {
        return Entreprise::where('email_responsable', auth()->user()->email)->first();
    }

    /**
     * Génère un code d'accès unique pour un membre.
     */
    private function genererCodeAcces(string $nom): string
    {
        $nomNettoye = $this->translitterer($nom);

        do {
            $code = strtoupper(mb_substr($nomNettoye, 0, 3) . rand(1000, 9999));
        } while (Participant::where('code_acces', $code)->exists());

        return $code;
    }

    /**
     * Convertit les caractères accentués en leur équivalent sans accent.
     */
    private function translitterer(string $texte): string
    {
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT', $texte);
        return $transliterated !== false ? $transliterated : $texte;
    }

    /**
     * ✅ CORRIGÉ : utilise mb_strtolower() pour gérer correctement
     * les accents (É, è, etc.)
     */
    public function getEstEtudiantProperty(): bool
    {
        $fonctionActive = $this->fonction === 'Autre'
            ? mb_strtolower(trim($this->fonction_autre))
            : mb_strtolower(trim($this->fonction));

        return in_array($fonctionActive, ['étudiant', 'etudiant', 'étudiante', 'etudiante']);
    }

    // ────────────────────────────────────────────────────────
    // MODAL AJOUT / MODIFICATION
    // ────────────────────────────────────────────────────────

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

    public function closeCodeModal(): void
    {
        $this->showCodeModal = false;
        $this->nouveauMembre = [];
    }

    public function resetFields(): void
    {
        $this->participant_id  = null;
        $this->nom             = '';
        $this->prenom          = '';
        $this->genre           = '';
        $this->fonction        = '';
        $this->fonction_autre  = '';
        $this->email           = '';
        $this->telephone       = '';
        $this->date_naissance  = '';
        $this->filiere         = '';
        $this->universite      = '';
        $this->resetErrorBag();
    }

    // ────────────────────────────────────────────────────────
    // MODIFIER UN MEMBRE
    // ────────────────────────────────────────────────────────

    public function modifier(int $id): void
    {
        $membre = Participant::findOrFail($id);

        $this->participant_id = $membre->id;
        $this->nom            = $membre->nom;
        $this->prenom         = $membre->prenom;
        $this->genre          = $membre->genre ?? '';
        $this->email          = $membre->email ?? '';
        $this->telephone      = $membre->telephone ?? '';
        $this->date_naissance = $membre->date_naissance
            ? $membre->date_naissance->format('Y-m-d') : '';
        $this->filiere        = $membre->filiere ?? '';
        $this->universite     = $membre->universite ?? '';
        $this->fonction_autre = '';

        if (in_array($membre->fonction, $this->fonctions)) {
            $this->fonction = $membre->fonction ?? '';
        } else {
            $this->fonction       = 'Autre';
            $this->fonction_autre = $membre->fonction ?? '';
        }

        $this->isEditing = true;
        $this->showModal = true;
    }

    // ────────────────────────────────────────────────────────
    // VALIDER / REJETER ADHÉSION
    // ────────────────────────────────────────────────────────

    public function accepterAdhesion(int $id): void
    {
        Participant::findOrFail($id)->update([
            'statut_adhesion'   => 'accepte',
            'statut_historique' => 'actif',
        ]);
        session()->flash('success', 'Adhésion acceptée ! Le membre peut maintenant accéder à la plateforme.');
    }

    public function rejeterAdhesion(int $id): void
    {
        Participant::findOrFail($id)->update([
            'statut_adhesion'   => 'rejete',
            'statut_historique' => 'inactif',
        ]);
        session()->flash('success', 'Demande d\'adhésion rejetée.');
    }

    // ────────────────────────────────────────────────────────
    // SAUVEGARDER (AJOUT OU MODIFICATION)
    // ────────────────────────────────────────────────────────

    public function sauvegarder(): void
    {
        if ($this->fonction === 'Autre' && $this->fonction_autre) {
            $this->fonction = $this->fonction_autre;
        }

        $regles = [
            'nom'            => 'required|string|max:255',
            'prenom'         => 'required|string|max:255',
            'fonction'       => 'required|string|max:255',
            'telephone'      => 'required|string|max:20',
            'email'          => 'nullable|email|max:255',
            'date_naissance' => 'nullable|date|before:today',
        ];

        // ✅ Filière + université si étudiant
        if ($this->estEtudiant) {
            $regles['filiere']    = 'required|string|max:255';
            $regles['universite'] = 'required|string|max:255';
        }

        $this->validate($regles, [
            'nom.required'        => 'Le nom est obligatoire.',
            'prenom.required'     => 'Le prénom est obligatoire.',
            'fonction.required'   => 'La fonction est obligatoire.',
            'telephone.required'  => 'Le téléphone est obligatoire.',
            'email.email'         => 'L\'adresse email n\'est pas valide.',
            'filiere.required'    => 'La filière est obligatoire pour un étudiant.',
            'universite.required' => "L'université est obligatoire pour un étudiant.",
        ]);

        $entreprise = $this->getEntreprise();

        if (!$entreprise) {
            session()->flash('error', 'Entreprise non trouvée.');
            return;
        }

        if ($this->isEditing) {
            Participant::findOrFail($this->participant_id)->update([
                'nom'            => $this->nom,
                'prenom'         => $this->prenom,
                'genre'          => $this->genre ?: null,
                'fonction'       => $this->fonction,
                'email'          => $this->email ?: null,
                'telephone'      => $this->telephone,
                'date_naissance' => $this->date_naissance ?: null,
                'filiere'        => $this->estEtudiant ? ($this->filiere ?: null) : null,
                'universite'     => $this->estEtudiant ? ($this->universite ?: null) : null,
            ]);

            session()->flash('success', 'Membre modifié avec succès.');
            $this->closeModal();

        } else {
            $code_acces = $this->genererCodeAcces($this->nom);

            $membre = Participant::create([
                'id_entreprise'         => $entreprise->id,
                'id_cdd'                => $entreprise->id_cdd,
                'nom'                   => $this->nom,
                'prenom'                => $this->prenom,
                'genre'                 => $this->genre ?: null,
                'fonction'              => $this->fonction,
                'email'                 => $this->email ?: null,
                'telephone'             => $this->telephone,
                'date_naissance'        => $this->date_naissance ?: null,
                'filiere'               => $this->estEtudiant ? ($this->filiere ?: null) : null,
                'universite'            => $this->estEtudiant ? ($this->universite ?: null) : null,
                'code_acces'            => $code_acces,
                'role'                  => 'membre',
                'participation_rdv'     => true,
                'statut_historique'     => 'actif',
                'statut_adhesion'       => 'accepte',
                // ✅ Le membre hérite du secteur de l'entreprise
                'secteur_activite'      => $entreprise->secteur_activite,
                'sous_secteur'          => $entreprise->sous_secteur,
                'pays'                  => $entreprise->pays,
                'ville'                 => $entreprise->ville,
                // ✅ Statut préinscription : déjà validé
                // (ajouté par le représentant, pas via le formulaire public)
                'statut_preinscription' => 'valide',
            ]);

            if ($this->email) {
                $userExiste = User::where('email', $this->email)->exists();
                if (!$userExiste) {
                    $user = User::create([
                        'name'     => $this->nom . ' ' . $this->prenom,
                        'email'    => $this->email,
                        'password' => Hash::make($code_acces),
                    ]);
                    $user->assignRole('participant');
                }
            }

            $this->nouveauMembre = [
                'nom'        => $this->nom,
                'prenom'     => $this->prenom,
                'fonction'   => $this->fonction,
                'email'      => $this->email,
                'telephone'  => $this->telephone,
                'code_acces' => $code_acces,
            ];

            $this->closeModal();
            $this->showCodeModal = true;
        }
    }

    // ────────────────────────────────────────────────────────
    // SUPPRIMER UN MEMBRE
    // ────────────────────────────────────────────────────────

    public function supprimer(int $id): void
    {
        $membre     = Participant::findOrFail($id);
        $entreprise = $this->getEntreprise();

        if ($membre->id_entreprise !== $entreprise?->id) {
            session()->flash('error', 'Action non autorisée.');
            return;
        }

        if ($membre->role === 'representant') {
            session()->flash('error', 'Vous ne pouvez pas supprimer le représentant.');
            return;
        }

        $membre->delete();
        session()->flash('success', 'Membre supprimé.');
    }

    // ────────────────────────────────────────────────────────
    // RENDER
    // ────────────────────────────────────────────────────────

    public function render()
    {
        $entreprise = $this->getEntreprise();

        $membres = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)
                ->whereIn('statut_adhesion', ['accepte', null])
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('prenom', 'like', '%' . $this->search . '%')
                      ->orWhere('fonction', 'like', '%' . $this->search . '%')
                )
                ->orderBy('nom')
                ->get()
            : collect();

        $demandesEnAttente = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)
                ->where('statut_adhesion', 'en_attente')
                ->get()
            : collect();

        return view('livewire.entreprise.mes-participants', [
            'membres'           => $membres,
            'demandesEnAttente' => $demandesEnAttente,
            'entreprise'        => $entreprise,
            'estEtudiant'       => $this->estEtudiant,
        ])->layout('layouts.entreprise', ['title' => 'Mes Membres']);
    }
}