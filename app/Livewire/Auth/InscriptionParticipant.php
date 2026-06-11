<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Models\Participant;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Hash;

/**
 * Inscription d'un membre d'entreprise
 *
 * Le membre entre son IFU pour être lié à son entreprise.
 * Le représentant de l'entreprise reçoit une demande
 * d'adhésion qu'il peut valider ou rejeter.
 */
class InscriptionParticipant extends Component
{
    
    // ÉTAPE 1 — INFOS PERSONNELLES
    

    public string $nom      = '';
    public string $prenom   = '';
    public string $genre    = '';
    public string $fonction = '';
    public string $fonction_autre = '';

    
    // ÉTAPE 2 — ENTREPRISE (par IFU)
    

    public string $ifu = '';
    public $entreprise_trouvee = null;

    
    // COMPTE
    

    public string $telephone  = '';
    public string $email      = '';
    public string $password   = '';
    public string $password_confirmation = '';

    
    // RÉSULTAT
    

    public bool   $showSuccessModal  = false;
    public string $code_acces_genere = '';


    // LISTES
    

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

    // HELPERS
   

    /**
     * Cherche l'entreprise en temps réel quand l'IFU change.
     */
    public function updatedIfu(string $value): void
    {
        if (strlen($value) >= 9) {
            $this->entreprise_trouvee = Entreprise::where('ifu', strtoupper($value))->first();
        } else {
            $this->entreprise_trouvee = null;
        }
    }

    /**
     * Génère un code d'accès unique.
     * Format : 3 lettres du nom + 4 chiffres
     * Exemple : DIA7823
     */
    private function genererCodeAcces(string $nom): string
    {
        do {
            $code = strtoupper(substr($nom, 0, 3) . rand(1000, 9999));
        } while (Participant::where('code_acces', $code)->exists());

        return $code;
    }

    
    // INSCRIPTION
    
    public function sinscrire(): void
    {
        // ← Si "Autre" on utilise la saisie libre
        if ($this->fonction === 'Autre') {
            $this->fonction = $this->fonction_autre;
        }

        $this->validate([
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'genre'     => 'required|string',
            'fonction'  => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'ifu'       => 'required|string',
            'email'     => 'nullable|email|unique:users,email',
            'password'  => $this->email ? 'required|min:8|confirmed' : 'nullable',
        ], [
            'nom.required'       => 'Le nom est obligatoire.',
            'prenom.required'    => 'Le prénom est obligatoire.',
            'genre.required'     => 'Le genre est obligatoire.',
            'fonction.required'  => 'La fonction est obligatoire.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'ifu.required'       => 'Le numéro IFU est obligatoire pour rejoindre une entreprise.',
            'email.unique'       => 'Cet email est déjà utilisé.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.min'       => 'Minimum 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        // ← Vérifie que l'entreprise existe
        $entreprise = Entreprise::where('ifu', strtoupper($this->ifu))->first();

        if (!$entreprise) {
            $this->addError('ifu', 'Aucune entreprise trouvée avec ce numéro IFU. Vérifiez auprès de votre représentant.');
            return;
        }

        // ← Génère le code d'accès
        $code_acces = $this->genererCodeAcces($this->nom);

        // ← Crée le compte USER si email fourni
        if ($this->email) {
            $userExiste = User::where('email', $this->email)->exists();
            if (!$userExiste) {
                $user = User::create([
                    'name'     => $this->nom . ' ' . $this->prenom,
                    'email'    => $this->email,
                    'password' => Hash::make($this->password),
                ]);
                $user->assignRole('participant');
            }
        }

        // ← Crée le PARTICIPANT avec statut en_attente
        // Le représentant devra valider cette adhésion
        Participant::create([
            'id_entreprise'     => $entreprise->id,
            'id_cdd'            => $entreprise->id_cdd,
            'nom'               => $this->nom,
            'prenom'            => $this->prenom,
            'genre'             => $this->genre,
            'fonction'          => $this->fonction,
            'email'             => $this->email ?: null,
            'telephone'         => $this->telephone,
            'code_acces'        => $code_acces,
            'role'              => 'membre',
            'participation_rdv' => true,
            'statut_historique' => 'en_attente',
            'statut_adhesion'   => 'en_attente',
            // ← Hérite du secteur de l'entreprise
            'secteur_activite'  => $entreprise->secteur_activite,
            'sous_secteur'      => $entreprise->sous_secteur,
            'pays'              => $entreprise->pays,
            'ville'             => $entreprise->ville,
        ]);

        $this->code_acces_genere  = $code_acces;
        $this->entreprise_trouvee = $entreprise;
        $this->showSuccessModal   = true;
    }
    public function allerAuDashboard(): mixed
{
    return redirect()->route('participant.dashboard');
}

   
    // RENDER
    

    public function render()
    {
        return view('livewire.auth.inscription-participant')
            ->layout('layouts.guest');
    }
}