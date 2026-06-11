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
    
    // PROPRIÉTÉS
    

    /** ID du membre en cours de modification */
    public $participant_id;

    /** Formulaire d'ajout/modification de membre */
    public string $nom           = '';
    public string $prenom        = '';
    public string $genre         = '';
    public string $fonction      = '';
    public string $fonction_autre = ''; // ← Saisie libre si "Autre"
    public string $email         = '';
    public string $telephone     = '';

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
        'Autre',
    ];

    
    // HELPERS PRIVÉS
    

    /**
     * Récupère l'entreprise du représentant connecté.
     */
    private function getEntreprise(): ?Entreprise
    {
        return Entreprise::where('email_responsable', auth()->user()->email)->first();
    }

    /**
     * Génère un code d'accès unique pour un membre.
     * Format : 3 premières lettres du nom + 4 chiffres aléatoires
     * Exemple : DIA7823
     */
    private function genererCodeAcces(string $nom): string
    {
        do {
            $code = strtoupper(substr($nom, 0, 3) . rand(1000, 9999));
        } while (Participant::where('code_acces', $code)->exists());

        return $code;
    }


    // MODAL AJOUT / MODIFICATION
    

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
        $this->resetErrorBag();
    }

    
    // MODIFIER UN MEMBRE
    

    public function modifier(int $id): void
    {
        $membre = Participant::findOrFail($id);

        $this->participant_id = $membre->id;
        $this->nom            = $membre->nom;
        $this->prenom         = $membre->prenom;
        $this->genre          = $membre->genre ?? '';
        $this->email          = $membre->email ?? '';
        $this->telephone      = $membre->telephone ?? '';
        $this->fonction_autre = '';

        // ← Gère la fonction "Autre"
        if (in_array($membre->fonction, $this->fonctions)) {
            $this->fonction = $membre->fonction ?? '';
        } else {
            $this->fonction       = 'Autre';
            $this->fonction_autre = $membre->fonction ?? '';
        }

        $this->isEditing = true;
        $this->showModal = true;
    }

    
    // VALIDER / REJETER ADHÉSION
    

    /**
     * Valide la demande d'adhésion d'un membre.
     * Le membre peut maintenant accéder à la plateforme.
     */
    public function accepterAdhesion(int $id): void
    {
        Participant::findOrFail($id)->update([
            'statut_adhesion'   => 'accepte',
            'statut_historique' => 'actif',
        ]);
        session()->flash('success', 'Adhésion acceptée ! Le membre peut maintenant accéder à la plateforme.');
    }

    /**
     * Rejette la demande d'adhésion d'un membre.
     */
    public function rejeterAdhesion(int $id): void
    {
        Participant::findOrFail($id)->update([
            'statut_adhesion'   => 'rejete',
            'statut_historique' => 'inactif',
        ]);
        session()->flash('success', 'Demande d\'adhésion rejetée.');
    }

    
    // SAUVEGARDER (AJOUT OU MODIFICATION)
    

    public function sauvegarder(): void
    {
        // ← Si "Autre" on utilise la saisie libre
        if ($this->fonction === 'Autre' && $this->fonction_autre) {
            $this->fonction = $this->fonction_autre;
        }

        $this->validate([
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'fonction'  => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email'     => 'nullable|email|max:255',
        ], [
            'nom.required'       => 'Le nom est obligatoire.',
            'prenom.required'    => 'Le prénom est obligatoire.',
            'fonction.required'  => 'La fonction est obligatoire.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'email.email'        => 'L\'adresse email n\'est pas valide.',
        ]);

        $entreprise = $this->getEntreprise();

        if (!$entreprise) {
            session()->flash('error', 'Entreprise non trouvée.');
            return;
        }

        if ($this->isEditing) {
            // ← Modification d'un membre existant
            Participant::findOrFail($this->participant_id)->update([
                'nom'       => $this->nom,
                'prenom'    => $this->prenom,
                'genre'     => $this->genre ?: null,
                'fonction'  => $this->fonction,
                'email'     => $this->email ?: null,
                'telephone' => $this->telephone,
            ]);

            session()->flash('success', 'Membre modifié avec succès.');
            $this->closeModal();

        } else {
            // ← Ajout d'un nouveau membre

            // ← Génère un code d'accès unique
            $code_acces = $this->genererCodeAcces($this->nom);

            // ← Crée le membre
            $membre = Participant::create([
                'id_entreprise'     => $entreprise->id,
                'id_cdd'            => $entreprise->id_cdd,
                'nom'               => $this->nom,
                'prenom'            => $this->prenom,
                'genre'             => $this->genre ?: null,
                'fonction'          => $this->fonction,
                'email'             => $this->email ?: null,
                'telephone'         => $this->telephone,
                'code_acces'        => $code_acces,
                'role'              => 'membre',
                'participation_rdv' => true,
                'statut_historique' => 'actif',
                'statut_adhesion'   => 'accepte',
                // ← Le membre hérite du secteur de l'entreprise
                'secteur_activite'  => $entreprise->secteur_activite,
                'sous_secteur'      => $entreprise->sous_secteur,
                'pays'              => $entreprise->pays,
                'ville'             => $entreprise->ville,
            ]);

            // ← Crée un compte USER si email fourni
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

            // ← Stocke les infos du nouveau membre pour affichage
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

    
    // SUPPRIMER UN MEMBRE
    

    public function supprimer(int $id): void
    {
        $membre     = Participant::findOrFail($id);
        $entreprise = $this->getEntreprise();

        // ← Sécurité : ne peut supprimer que ses propres membres
        if ($membre->id_entreprise !== $entreprise?->id) {
            session()->flash('error', 'Action non autorisée.');
            return;
        }

        // ← Ne peut pas supprimer le représentant
        if ($membre->role === 'representant') {
            session()->flash('error', 'Vous ne pouvez pas supprimer le représentant.');
            return;
        }

        $membre->delete();
        session()->flash('success', 'Membre supprimé.');
    }

    
    // RENDER
    

    public function render()
    {
        $entreprise = $this->getEntreprise();

        // ← Membres actifs et acceptés
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

        // ← Demandes d'adhésion en attente
        $demandesEnAttente = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)
                ->where('statut_adhesion', 'en_attente')
                ->get()
            : collect();

        return view('livewire.entreprise.mes-participants', [
            'membres'           => $membres,
            'demandesEnAttente' => $demandesEnAttente,
            'entreprise'        => $entreprise,
        ])->layout('layouts.entreprise', ['title' => 'Mes Membres']);
    }
}