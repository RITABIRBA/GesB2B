<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Models\Entreprise;
use App\Models\Participant;
use Illuminate\Support\Facades\Hash;

/**
 * Inscription Représentant d'Entreprise
 *
 * Processus en 5 étapes :
 * Étape 1 : Infos personnelles du représentant
 * Étape 2 : Infos de l'entreprise
 * Étape 3 : Profil partenaire recherché
 * Étape 4 : Disponibilités (optionnel)
 * Étape 5 : Confirmation
 */
class InscriptionEntreprise extends Component
{
    
    // NAVIGATION
   
    /** Étape courante (1 à 6, 6 = succès) */
    public int $etape = 1;

    
    // ÉTAPE 1 — INFOS PERSONNELLES DU REPRÉSENTANT
    
    public string $nom_responsable       = '';
    public string $prenom_responsable    = '';
    public string $fonction_responsable  = '';
    public string $fonction_autre        = ''; // ← Saisie libre si "Autre"
    public string $email                 = '';
    public string $contact               = '';
    public string $ifu                   = '';
    public string $id_cdd                = '';
    public string $password              = '';
    public string $password_confirmation = '';

    
    // ÉTAPE 2 — INFOS ENTREPRISE
    
    public string $nom                   = '';
    public string $secteur_activite      = '';
    public string $sous_secteur          = '';
    public string $description_activites = '';
    public string $principaux_produits   = '';
    public string $pays                  = '';
    public string $ville                 = '';
    public string $annee_creation        = '';
    public string $nombre_salaries       = '';
    public string $chiffre_affaires      = '';

   
    // ÉTAPE 3 — PROFIL PARTENAIRE RECHERCHÉ
    
    public string $secteur_recherche = '';
    public string $zone_geographique = '';
    public string $type_partenaire   = '';

    
    // ÉTAPE 4 — DISPONIBILITÉS
    
    public array $disponibilites = [];

    
    // LISTES DE RÉFÉRENCE
    

    public array $fonctions = [
        'Directeur Général',
        'Directeur Commercial',
        'PDG',
        'Gérant',
        'Responsable Export',
        'Responsable Partenariats',
        'Chargé de Développement',
        'Représentant',
        'Autre',
    ];

    public array $secteurs = [
        'Agriculture', 'Industrie', 'Commerce', 'Services',
        'Technologie', 'Transport', 'Construction', 'Tourisme',
        'Santé', 'Education', 'Finance', 'Energie', 'Mines',
        'Artisanat', 'Autre',
    ];

    public array $zones = [
        'Burkina Faso',
        'Afrique de l\'Ouest',
        'Afrique Centrale',
        'Afrique de l\'Est',
        'Afrique du Nord',
        'Afrique Australe',
        'Europe',
        'Amérique',
        'Asie',
        'Monde entier',
    ];

    public array $types_partenaires = [
        'Fournisseur',
        'Client',
        'Distributeur',
        'Partenaire financier',
        'Investisseur',
        'Sous-traitant',
        'Revendeur',
        'Partenaire technique',
        'Autre',
    ];

    public array $pays_liste = [
        'Burkina Faso', 'Côte d\'Ivoire', 'Mali', 'Sénégal',
        'Ghana', 'Togo', 'Bénin', 'Niger', 'Guinée', 'Cameroun',
        'Nigeria', 'France', 'Allemagne', 'États-Unis', 'Chine', 'Autre',
    ];

    public array $villes_par_pays = [
        'Burkina Faso'   => ['Ouagadougou', 'Bobo-Dioulasso', 'Koudougou', 'Banfora', 'Ouahigouya', 'Pouytenga', 'Kaya', 'Tenkodogo', 'Fada N\'Gourma', 'Dédougou', 'Autre'],
        'Côte d\'Ivoire' => ['Abidjan', 'Bouaké', 'Daloa', 'San-Pédro', 'Yamoussoukro', 'Korhogo', 'Man', 'Divo', 'Gagnoa', 'Abengourou', 'Autre'],
        'Mali'           => ['Bamako', 'Sikasso', 'Mopti', 'Koutiala', 'Kayes', 'Ségou', 'Gao', 'Tombouctou', 'Kidal', 'Autre'],
        'Sénégal'        => ['Dakar', 'Thiès', 'Kaolack', 'Ziguinchor', 'Saint-Louis', 'Rufisque', 'Mbour', 'Diourbel', 'Louga', 'Tambacounda', 'Autre'],
        'Ghana'          => ['Accra', 'Kumasi', 'Tamale', 'Sekondi-Takoradi', 'Ashaiman', 'Sunyani', 'Cape Coast', 'Obuasi', 'Autre'],
        'Togo'           => ['Lomé', 'Sokodé', 'Kara', 'Atakpamé', 'Kpalimé', 'Bassar', 'Tsévié', 'Aného', 'Autre'],
        'Bénin'          => ['Cotonou', 'Porto-Novo', 'Parakou', 'Abomey-Calavi', 'Bohicon', 'Kandi', 'Natitingou', 'Ouidah', 'Autre'],
        'Niger'          => ['Niamey', 'Zinder', 'Maradi', 'Tahoua', 'Agadez', 'Dosso', 'Diffa', 'Tillabéri', 'Autre'],
        'Guinée'         => ['Conakry', 'Nzérékoré', 'Kankan', 'Kindia', 'Labé', 'Gueckedou', 'Siguiri', 'Mamou', 'Autre'],
        'Cameroun'       => ['Yaoundé', 'Douala', 'Garoua', 'Bamenda', 'Maroua', 'Bafoussam', 'Ngaoundéré', 'Bertoua', 'Autre'],
        'Nigeria'        => ['Lagos', 'Kano', 'Ibadan', 'Abuja', 'Port Harcourt', 'Benin City', 'Maiduguri', 'Kaduna', 'Autre'],
        'France'         => ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg', 'Bordeaux', 'Autre'],
        'Allemagne'      => ['Berlin', 'Hambourg', 'Munich', 'Cologne', 'Francfort', 'Stuttgart', 'Düsseldorf', 'Autre'],
        'États-Unis'     => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Washington', 'Boston', 'Autre'],
        'Chine'          => ['Pékin', 'Shanghai', 'Guangzhou', 'Shenzhen', 'Chengdu', 'Tianjin', 'Wuhan', 'Autre'],
        'Autre'          => ['Autre'],
    ];

   
    // HELPERS
    
    /**
     * Retourne les villes disponibles selon le pays sélectionné.
     */
    public function getVillesDisponibles(): array
    {
        return $this->villes_par_pays[$this->pays] ?? ['Autre'];
    }

    /**
     * Remet la ville à vide quand le pays change.
     */
    public function updatedPays(): void
    {
        $this->ville = '';
    }

    /**
     * Remet la saisie libre quand on change la fonction.
     */
    public function updatedFonctionResponsable(): void
    {
        if ($this->fonction_responsable !== 'Autre') {
            $this->fonction_autre = '';
        }
    }

    
    // NAVIGATION ENTRE ÉTAPES
    

    /**
     * Passe à l'étape suivante après validation.
     */
    public function suivant(): void
    {
        if ($this->etape === 1) {

            // Si "Autre" on utilise la saisie libre
            if ($this->fonction_responsable === 'Autre') {
                $this->fonction_responsable = $this->fonction_autre;
            }

            $this->validate([
                'nom_responsable'      => 'required|string|max:255',
                'prenom_responsable'   => 'required|string|max:255',
                'fonction_responsable' => 'required|string|max:255',
                'email'                => 'required|email|unique:users,email',
                'contact'              => 'required|string|max:255',
                'ifu'                  => [
                    'required',
                    'string',
                    'regex:/^\d{8}[A-Za-z]$/',
                    'unique:entreprises,ifu',
                ],
                'password'             => 'required|min:8|confirmed',
            ], [
                'nom_responsable.required'      => 'Le nom est obligatoire.',
                'prenom_responsable.required'   => 'Le prénom est obligatoire.',
                'fonction_responsable.required' => 'La fonction est obligatoire.',
                'email.required'                => 'L\'email est obligatoire.',
                'email.unique'                  => 'Cet email est déjà utilisé.',
                'contact.required'              => 'Le téléphone est obligatoire.',
                'ifu.required'                  => 'Le numéro IFU est obligatoire.',
                'ifu.regex'                     => 'Format IFU invalide. Exemple : 12345678A',
                'ifu.unique'                    => 'Ce numéro IFU est déjà utilisé.',
                'password.required'             => 'Le mot de passe est obligatoire.',
                'password.min'                  => 'Minimum 8 caractères.',
                'password.confirmed'            => 'Les mots de passe ne correspondent pas.',
            ]);

            $this->etape = 2;

        } elseif ($this->etape === 2) {

            $this->validate([
                'nom'                   => 'required|string|max:255',
                'secteur_activite'      => 'required|string|max:255',
                'sous_secteur'          => 'required|string|max:255',
                'description_activites' => 'required|string',
                'pays'                  => 'required|string|max:255',
                'ville'                 => 'required|string|max:255',
                'annee_creation'        => 'required|integer|min:1900|max:' . date('Y'),
                'nombre_salaries'       => 'required|integer|min:1',
                'chiffre_affaires'      => 'required|numeric|min:0|max:100',
            ], [
                'nom.required'                   => 'Le nom de l\'entreprise est obligatoire.',
                'secteur_activite.required'      => 'Le secteur d\'activité est obligatoire.',
                'sous_secteur.required'          => 'Le sous-secteur est obligatoire.',
                'description_activites.required' => 'La description est obligatoire.',
                'pays.required'                  => 'Le pays est obligatoire.',
                'ville.required'                 => 'La ville est obligatoire.',
                'annee_creation.required'        => 'L\'année de création est obligatoire.',
                'nombre_salaries.required'       => 'Le nombre de salariés est obligatoire.',
                'chiffre_affaires.required'      => 'Le chiffre d\'affaires est obligatoire.',
                'chiffre_affaires.max'           => 'Le CA export ne peut pas dépasser 100%.',
            ]);

            $this->etape = 3;

        } elseif ($this->etape === 3) {

            $this->validate([
                'secteur_recherche' => 'required|string|max:255',
                'zone_geographique' => 'required|string|max:255',
                'type_partenaire'   => 'required|string|max:255',
            ], [
                'secteur_recherche.required' => 'Le secteur recherché est obligatoire.',
                'zone_geographique.required' => 'La zone géographique est obligatoire.',
                'type_partenaire.required'   => 'Le type de partenaire est obligatoire.',
            ]);

            $this->etape = 4;

        } elseif ($this->etape === 4) {
            // Disponibilités optionnelles → pas de validation
            $this->etape = 5;
        }
    }

    /**
     * Revient à l'étape précédente.
     */
    public function precedent(): void
    {
        if ($this->etape > 1) {
            $this->etape--;
        }
    }

    
    // SOUMISSION FINALE
    

    /**
     * Crée le compte utilisateur, l'entreprise et le représentant.
     *
     * Logique du statut :
     * → Avec CDD    : en_attente (le CDD doit valider)
     * → Sans CDD    : actif (validation directe par l'admin)
     */
    public function sinscrire(): void
    {
        // ← Double vérification email et IFU
        if (User::where('email', $this->email)->exists()) {
            $this->etape = 1;
            $this->addError('email', 'Cet email est déjà utilisé.');
            return;
        }

        if (Entreprise::where('ifu', strtoupper($this->ifu))->exists()) {
            $this->etape = 1;
            $this->addError('ifu', 'Ce numéro IFU est déjà utilisé.');
            return;
        }

        // ← Détermine le statut selon si CDD choisi ou non
        $statutParticipant = $this->id_cdd ? 'en_attente' : 'actif';

        // ← Crée le compte USER
        $user = User::create([
            'name'     => $this->nom_responsable . ' ' . $this->prenom_responsable,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
        ]);
        $user->assignRole('entreprise');

        // ← Crée l'ENTREPRISE
        // L'entreprise est toujours en_attente de validation admin
        $entreprise = Entreprise::create([
            'id_cdd'                => $this->id_cdd ?: null,
            'nom'                   => $this->nom,
            'nom_responsable'       => $this->nom_responsable,
            'prenom_responsable'    => $this->prenom_responsable,
            'fonction_responsable'  => $this->fonction_responsable,
            'email_responsable'     => $this->email,
            'ifu'                   => strtoupper($this->ifu),
            'secteur_activite'      => $this->secteur_activite,
            'sous_secteur'          => $this->sous_secteur,
            'description_activites' => $this->description_activites,
            'principaux_produits'   => $this->principaux_produits,
            'pays'                  => $this->pays,
            'ville'                 => $this->ville,
            'contact'               => $this->contact,
            'statut_validation'     => 'en_attente',
        ]);

        // ← Génère un code d'accès unique pour le représentant
        do {
            $code_acces = strtoupper(
                substr($this->nom_responsable, 0, 3) . rand(1000, 9999)
            );
        } while (Participant::where('code_acces', $code_acces)->exists());

        // ← Crée le PARTICIPANT représentant
        Participant::create([
            'id_entreprise'         => $entreprise->id,
            'id_cdd'                => $this->id_cdd ?: null,
            'nom'                   => $this->nom_responsable,
            'prenom'                => $this->prenom_responsable,
            'fonction'              => $this->fonction_responsable,
            'email'                 => $this->email,
            'telephone'             => $this->contact,
            'pays'                  => $this->pays,
            'ville'                 => $this->ville,
            'secteur_activite'      => $this->secteur_activite,
            'sous_secteur'          => $this->sous_secteur,
            'description_activites' => $this->description_activites,
            'principaux_produits'   => $this->principaux_produits,
            'annee_creation'        => $this->annee_creation,
            'nombre_salaries'       => $this->nombre_salaries,
            'chiffre_affaires'      => $this->chiffre_affaires,
            'secteur_recherche'     => $this->secteur_recherche,
            'zone_geographique'     => $this->zone_geographique,
            'type_partenaire'       => $this->type_partenaire,
            'disponibilites'        => !empty($this->disponibilites)
                ? json_encode($this->disponibilites)
                : null,
            'code_acces'            => $code_acces,
            'role'                  => 'representant',
            'participation_rdv'     => true,
            // ← Avec CDD : en_attente | Sans CDD : actif
            'statut_historique'     => $statutParticipant,
            'statut_adhesion'       => 'accepte',
        ]);

        // ← Passe à l'étape succès
        $this->etape = 6;
    }

    
    // RENDER
    
    public function render()
    {
        return view('livewire.auth.inscription-entreprise', [
            'secteurs'          => $this->secteurs,
            'zones'             => $this->zones,
            'types_partenaires' => $this->types_partenaires,
            'pays_liste'        => $this->pays_liste,
            'fonctions'         => $this->fonctions,
            'villesDisponibles' => $this->getVillesDisponibles(),
            'cdds'              => User::role('cdd')->orderBy('name')->get(),
        ])->layout('layouts.guest');
    }
}