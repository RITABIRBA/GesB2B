<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Hash;

class InscriptionEntreprise extends Component
{
    // Infos responsable
    public $nom_responsable       = '';
    public $prenom_responsable    = '';
    public $fonction_responsable  = '';

    // Infos entreprise
    public $nom                   = '';
    public $ifu                   = '';
    public $secteur_activite      = '';
    public $sous_secteur          = '';
    public $description_activites = '';
    public $principaux_produits   = '';
    public $pays                  = '';
    public $ville                 = '';
    public $contact               = '';

    // Compte
    public $email                 = '';
    public $password              = '';
    public $password_confirmation = '';
    public $id_cdd                = '';

    public $showSuccessModal = false;

    public $secteurs = [
        'Agriculture', 'Industrie', 'Commerce', 'Services',
        'Technologie', 'Transport', 'Construction', 'Tourisme',
        'Santé', 'Education', 'Finance', 'Energie', 'Mines',
        'Artisanat', 'Autre',
    ];

    public $pays_liste = [
        'Burkina Faso', 'Côte d\'Ivoire', 'Mali', 'Sénégal',
        'Ghana', 'Togo', 'Bénin', 'Niger', 'Guinée', 'Cameroun',
        'Nigeria', 'France', 'Allemagne', 'États-Unis', 'Chine', 'Autre',
    ];

    // ← Villes par pays
    public $villes_par_pays = [
        'Burkina Faso'    => ['Ouagadougou', 'Bobo-Dioulasso', 'Koudougou', 'Banfora', 'Ouahigouya', 'Pouytenga', 'Kaya', 'Tenkodogo', 'Fada N\'Gourma', 'Dédougou', 'Autre'],
        'Côte d\'Ivoire'  => ['Abidjan', 'Bouaké', 'Daloa', 'San-Pédro', 'Yamoussoukro', 'Korhogo', 'Man', 'Divo', 'Gagnoa', 'Abengourou', 'Autre'],
        'Mali'            => ['Bamako', 'Sikasso', 'Mopti', 'Koutiala', 'Kayes', 'Ségou', 'Gao', 'Tombouctou', 'Kidal', 'Autre'],
        'Sénégal'         => ['Dakar', 'Thiès', 'Kaolack', 'Ziguinchor', 'Saint-Louis', 'Rufisque', 'Mbour', 'Diourbel', 'Louga', 'Tambacounda', 'Autre'],
        'Ghana'           => ['Accra', 'Kumasi', 'Tamale', 'Sekondi-Takoradi', 'Ashaiman', 'Sunyani', 'Cape Coast', 'Obuasi', 'Autre'],
        'Togo'            => ['Lomé', 'Sokodé', 'Kara', 'Atakpamé', 'Kpalimé', 'Bassar', 'Tsévié', 'Aného', 'Autre'],
        'Bénin'           => ['Cotonou', 'Porto-Novo', 'Parakou', 'Abomey-Calavi', 'Bohicon', 'Kandi', 'Natitingou', 'Ouidah', 'Autre'],
        'Niger'           => ['Niamey', 'Zinder', 'Maradi', 'Tahoua', 'Agadez', 'Dosso', 'Diffa', 'Tillabéri', 'Autre'],
        'Guinée'          => ['Conakry', 'Nzérékoré', 'Kankan', 'Kindia', 'Labé', 'Gueckedou', 'Siguiri', 'Mamou', 'Autre'],
        'Cameroun'        => ['Yaoundé', 'Douala', 'Garoua', 'Bamenda', 'Maroua', 'Bafoussam', 'Ngaoundéré', 'Bertoua', 'Autre'],
        'Nigeria'         => ['Lagos', 'Kano', 'Ibadan', 'Abuja', 'Port Harcourt', 'Benin City', 'Maiduguri', 'Kaduna', 'Autre'],
        'France'          => ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg', 'Bordeaux', 'Autre'],
        'Allemagne'       => ['Berlin', 'Hambourg', 'Munich', 'Cologne', 'Francfort', 'Stuttgart', 'Düsseldorf', 'Autre'],
        'États-Unis'      => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Washington', 'Boston', 'Autre'],
        'Chine'           => ['Pékin', 'Shanghai', 'Guangzhou', 'Shenzhen', 'Chengdu', 'Tianjin', 'Wuhan', 'Autre'],
        'Autre'           => ['Autre'],
    ];

    // ← Villes disponibles selon le pays sélectionné
    public function getVillesDisponiblesProperty()
    {
        return $this->villes_par_pays[$this->pays] ?? ['Autre'];
    }

    // ← Reset ville quand pays change
    public function updatedPays()
    {
        $this->ville = '';
    }

    public function sinscrire()
    {
        $this->validate([
            'nom_responsable'       => 'required|string|max:255',
            'prenom_responsable'    => 'required|string|max:255',
            'fonction_responsable'  => 'required|string|max:255', // ← obligatoire
            'nom'                   => 'required|string|max:255',
            // ← Format IFU : 8 chiffres + 1 lettre
            'ifu'                   => [
                'required',
                'string',
                'regex:/^\d{8}[A-Za-z]$/',
                'unique:entreprises,ifu',
            ],
            'secteur_activite'      => 'required|string|max:255', // ← obligatoire
            'sous_secteur'          => 'required|string|max:255', // ← obligatoire
            'description_activites' => 'required|string',
            'pays'                  => 'required|string|max:255',
            'ville'                 => 'required|string|max:255',
            'contact'               => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:8|confirmed',
            'id_cdd'                => 'nullable',
        ], [
            // ← Messages d'erreur personnalisés
            'ifu.required'      => 'Le numéro IFU est obligatoire.',
            'ifu.regex'         => 'Le format IFU doit être : 8 chiffres suivis d\'une lettre (ex: 12345678A).',
            'ifu.unique'        => 'Ce numéro IFU est déjà utilisé.',
            'secteur_activite.required' => 'Le secteur d\'activité est obligatoire.',
            'sous_secteur.required'     => 'Le sous-secteur est obligatoire.',
            'fonction_responsable.required' => 'La fonction du responsable est obligatoire.',
        ]);

        // Crée le compte USER
        $user = User::create([
            'name'     => $this->nom_responsable . ' ' . $this->prenom_responsable,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
        ]);
        $user->assignRole('entreprise');

        // Crée l'ENTREPRISE
        Entreprise::create([
            'id_cdd'                => $this->id_cdd ?: null,
            'nom'                   => $this->nom,
            'nom_responsable'       => $this->nom_responsable,
            'prenom_responsable'    => $this->prenom_responsable,
            'fonction_responsable'  => $this->fonction_responsable,
            'email_responsable'     => $this->email,
            'ifu'                   => strtoupper($this->ifu), // ← majuscules
            'secteur_activite'      => $this->secteur_activite,
            'sous_secteur'          => $this->sous_secteur,
            'description_activites' => $this->description_activites,
            'principaux_produits'   => $this->principaux_produits,
            'pays'                  => $this->pays,
            'ville'                 => $this->ville,
            'contact'               => $this->contact,
            'statut_validation'     => 'en_attente',
        ]);

        $this->showSuccessModal = true;
    }

    public function render()
    {
        return view('livewire.auth.inscription-entreprise', [
            'secteurs'           => $this->secteurs,
            'pays_liste'         => $this->pays_liste,
            'villesDisponibles'  => $this->villesDisponibles, // ← villes dynamiques
            'cdds'               => User::role('cdd')->orderBy('name')->get(),
        ])->layout('layouts.guest');
    }
}