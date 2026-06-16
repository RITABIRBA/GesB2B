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

    
    // PROFIL PARTENAIRE RECHERCHÉ (max 3 chacun)
    

    public string $zone_geographique = '';

    public array  $secteurs_recherche      = [];
    public string $secteur_recherche_autre = '';

    public array  $types_partenariat       = [];
    public string $type_partenariat_autre  = '';

    public array  $profils_partenaire      = [];

    
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

    public array $secteurs = [
        'Agriculture et agro-alimentaire',
        'Environnement',
        'Industrie textile',
        'Biens de consommation',
        'Energie',
        'Formation',
        'Tourisme',
        'TIC',
        'Sous-traitance',
        'Artisanat',
        'Distribution',
        'Prestation',
        'Industrie manufacturière',
        'Enseignement',
        'Services aux entreprises',
        'BTP',
        'Activités médicales et pharmaceutiques',
        'Autre',
    ];

    public array $zonesGeographiques = [
        'Locale',
        'Nationale',
        'Régionale (CEDEAO)',
        'Africaine',
        'Internationale',
    ];

    public array $typesPartenariatOptions = [
        'Alliance commerciale',
        'Alliance financière',
        'Alliance industrielle',
        'Autre',
    ];

    public array $profilsPartenariatOptions = [
        'Consultant',
        'Distributeur',
        'Exportateur',
        'Fabricant / Producteur',
        'Investisseur',
        'Importateur',
        'Prestataire de service',
        'Sous-traitant',
        'Innovation',
        'R&D',
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

    
    // TOGGLES — PROFIL PARTENAIRE (max 3 chacun)
    

    public function toggleSecteurRecherche(string $option): void
    {
        if (in_array($option, $this->secteurs_recherche)) {
            $this->secteurs_recherche = array_values(array_diff($this->secteurs_recherche, [$option]));
        } elseif (count($this->secteurs_recherche) < 3) {
            $this->secteurs_recherche[] = $option;
        }

        if (!in_array('Autre', $this->secteurs_recherche)) {
            $this->secteur_recherche_autre = '';
        }
    }

    public function toggleTypePartenariat(string $option): void
    {
        if (in_array($option, $this->types_partenariat)) {
            $this->types_partenariat = array_values(array_diff($this->types_partenariat, [$option]));
        } elseif (count($this->types_partenariat) < 3) {
            $this->types_partenariat[] = $option;
        }

        if (!in_array('Autre', $this->types_partenariat)) {
            $this->type_partenariat_autre = '';
        }
    }

    public function toggleProfilPartenaire(string $option): void
    {
        if (in_array($option, $this->profils_partenaire)) {
            $this->profils_partenaire = array_values(array_diff($this->profils_partenaire, [$option]));
        } elseif (count($this->profils_partenaire) < 3) {
            $this->profils_partenaire[] = $option;
        }
    }

    
    // INSCRIPTION
    
    public function sinscrire(): void
    {
        // ← Si "Autre" on utilise la saisie libre
        if ($this->fonction === 'Autre') {
            $this->fonction = $this->fonction_autre;
        }

        $this->validate([
            'nom'                => 'required|string|max:255',
            'prenom'             => 'required|string|max:255',
            'genre'              => 'required|string',
            'fonction'           => 'required|string|max:255',
            'telephone'          => 'required|string|max:20',
            'ifu'                => 'required|string',
            'email'              => 'nullable|email|unique:users,email',
            'password'           => $this->email ? 'required|min:8|confirmed' : 'nullable',
            'zone_geographique'  => 'required|string|max:255',
            'secteurs_recherche' => 'required|array|min:1|max:3',
            'types_partenariat'  => 'required|array|min:1|max:3',
            'profils_partenaire' => 'nullable|array|max:3',
        ], [
            'nom.required'                => 'Le nom est obligatoire.',
            'prenom.required'             => 'Le prénom est obligatoire.',
            'genre.required'              => 'Le genre est obligatoire.',
            'fonction.required'           => 'La fonction est obligatoire.',
            'telephone.required'          => 'Le téléphone est obligatoire.',
            'ifu.required'                => 'Le numéro IFU est obligatoire pour rejoindre une entreprise.',
            'email.unique'                => 'Cet email est déjà utilisé.',
            'password.required'           => 'Le mot de passe est obligatoire.',
            'password.min'                => 'Minimum 8 caractères.',
            'password.confirmed'          => 'Les mots de passe ne correspondent pas.',
            'zone_geographique.required'  => 'La zone géographique est obligatoire.',
            'secteurs_recherche.required' => 'Sélectionnez au moins un secteur recherché.',
            'secteurs_recherche.min'      => 'Sélectionnez au moins un secteur recherché.',
            'types_partenariat.required'  => 'Sélectionnez au moins un type de partenariat.',
            'types_partenariat.min'       => 'Sélectionnez au moins un type de partenariat.',
        ]);

        if (in_array('Autre', $this->secteurs_recherche) && !$this->secteur_recherche_autre) {
            $this->addError('secteur_recherche_autre', 'Précisez le secteur recherché.');
            return;
        }

        if (in_array('Autre', $this->types_partenariat) && !$this->type_partenariat_autre) {
            $this->addError('type_partenariat_autre', 'Précisez le type de partenariat.');
            return;
        }

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

        // ← Remplace "Autre" par la saisie libre dans les sélections
        $secteursRecherche = $this->secteurs_recherche;
        if (($idx = array_search('Autre', $secteursRecherche)) !== false && $this->secteur_recherche_autre) {
            $secteursRecherche[$idx] = $this->secteur_recherche_autre;
        }

        $typesPartenariat = $this->types_partenariat;
        if (($idx = array_search('Autre', $typesPartenariat)) !== false && $this->type_partenariat_autre) {
            $typesPartenariat[$idx] = $this->type_partenariat_autre;
        }

        // ← Crée le PARTICIPANT avec statut en_attente
        // Le représentant devra valider cette adhésion
        Participant::create([
            'id_entreprise'      => $entreprise->id,
            'id_cdd'             => $entreprise->id_cdd,
            'nom'                => $this->nom,
            'prenom'             => $this->prenom,
            'genre'              => $this->genre,
            'fonction'           => $this->fonction,
            'email'              => $this->email ?: null,
            'telephone'          => $this->telephone,
            'code_acces'         => $code_acces,
            'role'               => 'membre',
            'participation_rdv'  => true,
            'statut_historique'  => 'en_attente',
            'statut_adhesion'    => 'en_attente',
            // ← Hérite du secteur de l'entreprise
            'secteur_activite'   => $entreprise->secteur_activite,
            'sous_secteur'       => $entreprise->sous_secteur,
            'pays'               => $entreprise->pays,
            'ville'              => $entreprise->ville,
            // ← Profil partenaire propre au membre
            'zone_geographique'  => $this->zone_geographique,
            'secteurs_recherche' => json_encode($secteursRecherche),
            'types_partenariat'  => json_encode($typesPartenariat),
            'profils_partenaire' => json_encode($this->profils_partenaire),
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
        return view('livewire.auth.inscription-participant', [
            'secteurs'                  => $this->secteurs,
            'zonesGeographiques'        => $this->zonesGeographiques,
            'typesPartenariatOptions'   => $this->typesPartenariatOptions,
            'profilsPartenariatOptions' => $this->profilsPartenariatOptions,
        ])->layout('layouts.guest');
    }
}