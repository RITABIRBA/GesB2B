<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\Evenement;
use App\Models\Inscription;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;

class GestionParticipants extends Component
{
    public $participant_id;
    public $id_entreprise            = '';
    public $id_evenement             = '';
    public $nom                      = '';
    public $prenom                   = '';
    public $genre                    = '';
    public $fonction                 = '';
    public $ifu                      = '';
    public $email                    = '';
    public $telephone                = '';
    public $pays                     = '';
    public $ville                    = '';
    // ✅ CORRIGÉ : valeur par défaut était 'representant'
    public $role                     = 'participant';
    public $statut_historique        = 'actif';
    public $participation_rdv        = true;

    public $date_naissance           = '';
    public $filiere                  = '';
    public $universite               = '';
    public $statut_participant       = 'classique';

    // Infos activité
    public string $secteur_activite       = '';
    public string $secteur_activite_autre = '';
    public string $sous_secteur           = '';
    public string $description_activites  = '';
    public string $principaux_produits    = '';
    public string $annee_creation         = '';
    public string $nombre_salaries        = '';
    public string $chiffre_affaires       = '';

    public string $zone_geographique = '';
    public array $disponibilites = [];
    public array  $types_partenariat      = [];
    public string $type_partenariat_autre = '';
    public array $profils_partenaire = [];
    public array  $secteurs_recherche      = [];
    public string $secteur_recherche_autre = '';
    public string $objectif_participation = '';
    public $id_chef_delegation = '';

    // UI
    public bool   $showModal          = false;
    public bool   $isEditing          = false;
    public string $search             = '';
    public string $filtre_statut      = '';
    public string $filtre_preinscription = '';
    public string $entreprise_trouvee = '';

    // Modal compte
    public bool   $showModalCompte   = false;
    public string $compte_email      = '';
    public string $compte_password   = '';
    public string $compte_code_acces = '';
    public bool   $compte_has_email  = false;

    // Modal validation préinscription
    public bool $showModalPreinscription = false;
    public $preinscription_courante      = null;

    // Modal rejet
    public bool   $showModalRejet = false;
    public string $motif_rejet    = '';

    // ✅ CORRIGÉ : ajout du rôle 'participant'
    public array $roles = ['participant', 'representant', 'membre'];

    public array $secteurs = [
        'Agriculture et agro-alimentaire', 'Environnement', 'Industrie textile',
        'Biens de consommation', 'Energie', 'Formation', 'Tourisme', 'TIC',
        'Sous-traitance', 'Artisanat', 'Distribution', 'Prestation',
        'Industrie manufacturière', 'Enseignement', 'Services aux entreprises',
        'BTP', 'Activités médicales et pharmaceutiques', 'Autre',
    ];

    public array $typesPartenariatOptions = [
        'Alliance commerciale', 'Alliance financière', 'Alliance industrielle', 'Autre',
    ];

    public array $profilsPartenariatOptions = [
        'Consultant', 'Distributeur', 'Exportateur', 'Fabricant / Producteur',
        'Investisseur', 'Importateur', 'Prestataire de service', 'Sous-traitant',
        'Innovation', 'R&D',
    ];

    public array $zonesGeographiques = [
        'UEMOA (Afrique de l\'Ouest)',
        'CEMAC (Afrique Centrale)',
        'Afrique du Nord (Maghreb)',
        'Afrique de l\'Est (EAC)',
        'Afrique Australe (SADC)',
        'Afrique (toute la région)',

        'Union Européenne',
        'Europe de l\'Ouest',
        'Europe de l\'Est',
        'Europe (toute la région)',

        'Amérique du Nord',
        'Amérique Centrale et Caraïbes',
        'Amérique du Sud',
        'Amériques (toute la région)',

        'Asie de l\'Est',
        'Asie du Sud-Est',
        'Asie du Sud',
        'Moyen-Orient',
        'Asie (toute la région)',

        'Océanie',

        'Locale (mon pays uniquement)',
        'Internationale (toutes zones)',
    ];

    public array $joursDisponibles = [
        'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche',
    ];

    public array $pays_liste = [
        'Bénin', 'Burkina Faso', 'Cap-Vert', 'Côte d\'Ivoire', 'Gambie',
        'Ghana', 'Guinée', 'Guinée-Bissau', 'Liberia', 'Mali', 'Mauritanie',
        'Niger', 'Nigeria', 'Sénégal', 'Sierra Leone', 'Togo',
        'Angola', 'Cameroun', 'Congo', 'Gabon', 'Guinée équatoriale',
        'République centrafricaine', 'République démocratique du Congo', 'Tchad',
        'Burundi', 'Djibouti', 'Érythrée', 'Éthiopie', 'Kenya', 'Madagascar',
        'Malawi', 'Maurice', 'Mozambique', 'Ouganda', 'Rwanda', 'Seychelles',
        'Somalie', 'Soudan', 'Soudan du Sud', 'Tanzanie', 'Zambie', 'Zimbabwe',
        'Algérie', 'Égypte', 'Libye', 'Maroc', 'Tunisie',
        'Afrique du Sud', 'Botswana', 'Eswatini', 'Lesotho', 'Namibie',
        'Allemagne', 'Autriche', 'Belgique', 'Danemark', 'Espagne',
        'Finlande', 'France', 'Grèce', 'Irlande', 'Italie', 'Luxembourg',
        'Norvège', 'Pays-Bas', 'Pologne', 'Portugal', 'Royaume-Uni',
        'Russie', 'Suède', 'Suisse', 'Turquie', 'Ukraine',
        'Argentine', 'Bolivie', 'Brésil', 'Canada', 'Chili', 'Colombie',
        'Cuba', 'États-Unis', 'Mexique', 'Pérou', 'Venezuela',
        'Arabie Saoudite', 'Bangladesh', 'Chine', 'Corée du Sud',
        'Émirats arabes unis', 'Inde', 'Indonésie', 'Iran', 'Irak',
        'Israël', 'Japon', 'Jordanie', 'Liban', 'Malaisie', 'Pakistan',
        'Philippines', 'Qatar', 'Singapour', 'Thaïlande', 'Vietnam',
        'Australie', 'Nouvelle-Zélande', 'Autre',
    ];

    public array $villes_par_pays = [
        'Bénin'           => ['Cotonou', 'Porto-Novo', 'Parakou', 'Abomey-Calavi', 'Djougou', 'Bohicon', 'Kandi', 'Natitingou', 'Autre'],
        'Burkina Faso'    => ['Ouagadougou', 'Bobo-Dioulasso', 'Koudougou', 'Banfora', 'Ouahigouya', 'Pouytenga', 'Kaya', 'Tenkodogo', 'Fada N\'Gourma', 'Dédougou', 'Ziniaré', 'Kongoussi', 'Autre'],
        'Cap-Vert'        => ['Praia', 'Mindelo', 'Santa Maria', 'Espargos', 'Autre'],
        'Côte d\'Ivoire'  => ['Abidjan', 'Bouaké', 'Daloa', 'San-Pédro', 'Yamoussoukro', 'Korhogo', 'Man', 'Divo', 'Gagnoa', 'Abengourou', 'Soubré', 'Bondoukou', 'Autre'],
        'Gambie'          => ['Banjul', 'Serekunda', 'Brikama', 'Bakau', 'Farafenni', 'Autre'],
        'Ghana'           => ['Accra', 'Kumasi', 'Tamale', 'Sekondi-Takoradi', 'Cape Coast', 'Tema', 'Autre'],
        'Guinée'          => ['Conakry', 'Nzérékoré', 'Kankan', 'Kindia', 'Labé', 'Siguiri', 'Mamou', 'Autre'],
        'Guinée-Bissau'   => ['Bissau', 'Bafatá', 'Gabú', 'Bissorã', 'Autre'],
        'Liberia'         => ['Monrovia', 'Gbarnga', 'Buchanan', 'Voinjama', 'Autre'],
        'Mali'            => ['Bamako', 'Sikasso', 'Mopti', 'Koutiala', 'Kayes', 'Ségou', 'Gao', 'Tombouctou', 'Kidal', 'Autre'],
        'Mauritanie'      => ['Nouakchott', 'Nouadhibou', 'Rosso', 'Kaédi', 'Zouerate', 'Kiffa', 'Autre'],
        'Niger'           => ['Niamey', 'Zinder', 'Maradi', 'Tahoua', 'Agadez', 'Dosso', 'Arlit', 'Diffa', 'Autre'],
        'Nigeria'         => ['Lagos', 'Kano', 'Ibadan', 'Abuja', 'Port Harcourt', 'Benin City', 'Maiduguri', 'Zaria', 'Aba', 'Enugu', 'Kaduna', 'Ilorin', 'Autre'],
        'Sénégal'         => ['Dakar', 'Thiès', 'Kaolack', 'Ziguinchor', 'Saint-Louis', 'Rufisque', 'Mbour', 'Louga', 'Diourbel', 'Tambacounda', 'Autre'],
        'Sierra Leone'    => ['Freetown', 'Bo', 'Kenema', 'Makeni', 'Koidu', 'Autre'],
        'Togo'            => ['Lomé', 'Sokodé', 'Kara', 'Atakpamé', 'Kpalimé', 'Dapaong', 'Tsévié', 'Autre'],
        'Angola'          => ['Luanda', 'Huambo', 'Lobito', 'Benguela', 'Kuito', 'Lubango', 'Autre'],
        'Cameroun'        => ['Yaoundé', 'Douala', 'Garoua', 'Bamenda', 'Maroua', 'Bafoussam', 'Ngaoundéré', 'Bertoua', 'Autre'],
        'Congo'           => ['Brazzaville', 'Pointe-Noire', 'Dolisie', 'Nkayi', 'Autre'],
        'Gabon'           => ['Libreville', 'Port-Gentil', 'Franceville', 'Oyem', 'Autre'],
        'République démocratique du Congo' => ['Kinshasa', 'Lubumbashi', 'Mbuji-Mayi', 'Kananga', 'Kisangani', 'Bukavu', 'Goma', 'Autre'],
        'Tchad'           => ['N\'Djamena', 'Moundou', 'Sarh', 'Abéché', 'Kélo', 'Autre'],
        'Éthiopie'        => ['Addis-Abeba', 'Dire Dawa', 'Mekele', 'Gondar', 'Awasa', 'Bahir Dar', 'Autre'],
        'Kenya'           => ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika', 'Autre'],
        'Madagascar'      => ['Antananarivo', 'Toamasina', 'Antsirabe', 'Fianarantsoa', 'Mahajanga', 'Toliara', 'Autre'],
        'Mozambique'      => ['Maputo', 'Matola', 'Nampula', 'Beira', 'Chimoio', 'Autre'],
        'Ouganda'         => ['Kampala', 'Gulu', 'Lira', 'Mbarara', 'Jinja', 'Autre'],
        'Rwanda'          => ['Kigali', 'Butare', 'Gisenyi', 'Ruhengeri', 'Gitarama', 'Autre'],
        'Tanzanie'        => ['Dar es Salaam', 'Mwanza', 'Arusha', 'Dodoma', 'Mbeya', 'Zanzibar', 'Autre'],
        'Zimbabwe'        => ['Harare', 'Bulawayo', 'Mutare', 'Gweru', 'Kwekwe', 'Autre'],
        'Algérie'         => ['Alger', 'Oran', 'Constantine', 'Annaba', 'Blida', 'Batna', 'Sétif', 'Tlemcen', 'Autre'],
        'Égypte'          => ['Le Caire', 'Alexandrie', 'Gizeh', 'Charm el-Cheikh', 'Louxor', 'Assouan', 'Autre'],
        'Libye'           => ['Tripoli', 'Benghazi', 'Misrata', 'Al-Bayda', 'Sebha', 'Autre'],
        'Maroc'           => ['Casablanca', 'Rabat', 'Fès', 'Marrakech', 'Agadir', 'Tanger', 'Meknès', 'Oujda', 'Autre'],
        'Tunisie'         => ['Tunis', 'Sfax', 'Sousse', 'Gabès', 'Bizerte', 'Kairouan', 'Autre'],
        'Afrique du Sud'  => ['Johannesburg', 'Le Cap', 'Durban', 'Pretoria', 'Port Elizabeth', 'Bloemfontein', 'Autre'],
        'Botswana'        => ['Gaborone', 'Francistown', 'Molepolole', 'Serowe', 'Autre'],
        'Namibie'         => ['Windhoek', 'Rundu', 'Walvis Bay', 'Swakopmund', 'Autre'],
        'Allemagne'       => ['Berlin', 'Hambourg', 'Munich', 'Cologne', 'Francfort', 'Stuttgart', 'Düsseldorf', 'Leipzig', 'Autre'],
        'Belgique'        => ['Bruxelles', 'Anvers', 'Gand', 'Liège', 'Bruges', 'Namur', 'Autre'],
        'Espagne'         => ['Madrid', 'Barcelone', 'Valence', 'Séville', 'Bilbao', 'Málaga', 'Autre'],
        'France'          => ['Paris', 'Marseille', 'Lyon', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg', 'Bordeaux', 'Lille', 'Montpellier', 'Autre'],
        'Italie'          => ['Rome', 'Milan', 'Naples', 'Turin', 'Palerme', 'Gênes', 'Bologne', 'Florence', 'Autre'],
        'Pays-Bas'        => ['Amsterdam', 'Rotterdam', 'La Haye', 'Utrecht', 'Eindhoven', 'Autre'],
        'Portugal'        => ['Lisbonne', 'Porto', 'Braga', 'Amadora', 'Funchal', 'Autre'],
        'Royaume-Uni'     => ['Londres', 'Birmingham', 'Manchester', 'Glasgow', 'Liverpool', 'Leeds', 'Sheffield', 'Autre'],
        'Russie'          => ['Moscou', 'Saint-Pétersbourg', 'Novossibirsk', 'Ekaterinbourg', 'Kazan', 'Autre'],
        'Suisse'          => ['Zurich', 'Genève', 'Bâle', 'Berne', 'Lausanne', 'Autre'],
        'Turquie'         => ['Istanbul', 'Ankara', 'Izmir', 'Bursa', 'Adana', 'Gaziantep', 'Autre'],
        'Argentine'       => ['Buenos Aires', 'Córdoba', 'Rosario', 'Mendoza', 'La Plata', 'Autre'],
        'Brésil'          => ['São Paulo', 'Rio de Janeiro', 'Brasília', 'Salvador', 'Fortaleza', 'Manaus', 'Curitiba', 'Recife', 'Autre'],
        'Canada'          => ['Toronto', 'Montréal', 'Vancouver', 'Calgary', 'Ottawa', 'Edmonton', 'Québec', 'Autre'],
        'Colombie'        => ['Bogotá', 'Medellín', 'Cali', 'Barranquilla', 'Cartagena', 'Autre'],
        'États-Unis'      => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphie', 'San Antonio', 'San Diego', 'Dallas', 'Washington', 'Miami', 'Atlanta', 'Boston', 'Autre'],
        'Mexique'         => ['Mexico', 'Guadalajara', 'Monterrey', 'Puebla', 'Tijuana', 'Autre'],
        'Pérou'           => ['Lima', 'Arequipa', 'Trujillo', 'Chiclayo', 'Cusco', 'Autre'],
        'Arabie Saoudite' => ['Riyad', 'Djeddah', 'La Mecque', 'Médine', 'Dammam', 'Autre'],
        'Chine'           => ['Pékin', 'Shanghai', 'Guangzhou', 'Shenzhen', 'Chengdu', 'Tianjin', 'Wuhan', 'Xian', 'Hangzhou', 'Autre'],
        'Émirats arabes unis' => ['Dubaï', 'Abou Dabi', 'Charjah', 'Al Ain', 'Ajman', 'Autre'],
        'Inde'            => ['Mumbai', 'Delhi', 'Bangalore', 'Hyderabad', 'Chennai', 'Kolkata', 'Pune', 'Ahmedabad', 'Autre'],
        'Indonésie'       => ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Makassar', 'Bali', 'Autre'],
        'Japon'           => ['Tokyo', 'Osaka', 'Nagoya', 'Sapporo', 'Fukuoka', 'Yokohama', 'Kyoto', 'Kobe', 'Autre'],
        'Malaisie'        => ['Kuala Lumpur', 'George Town', 'Ipoh', 'Johor Bahru', 'Kota Kinabalu', 'Autre'],
        'Pakistan'        => ['Karachi', 'Lahore', 'Islamabad', 'Faisalabad', 'Rawalpindi', 'Autre'],
        'Qatar'           => ['Doha', 'Al-Wakrah', 'Al-Khor', 'Al-Rayyan', 'Autre'],
        'Singapour'       => ['Singapour', 'Autre'],
        'Thaïlande'       => ['Bangkok', 'Chiang Mai', 'Phuket', 'Pattaya', 'Khon Kaen', 'Autre'],
        'Vietnam'         => ['Hô Chi Minh-Ville', 'Hanoï', 'Đà Nẵng', 'Haïphong', 'Cần Thơ', 'Autre'],
        'Australie'       => ['Sydney', 'Melbourne', 'Brisbane', 'Perth', 'Adélaïde', 'Canberra', 'Autre'],
        'Nouvelle-Zélande'=> ['Auckland', 'Wellington', 'Christchurch', 'Hamilton', 'Autre'],
        'Autre'           => ['Autre'],
    ];

    public function getVillesDisponiblesProperty(): array
    {
        return $this->villes_par_pays[$this->pays] ?? ['Autre'];
    }

    public function updatedPays(): void { $this->ville = ''; }

    public function updatedIfu(): void
    {
        $this->entreprise_trouvee = '';
        $this->id_entreprise      = '';

        if (strlen($this->ifu) >= 8) {
            $entreprise = Entreprise::where('ifu', $this->ifu)->first();
            if ($entreprise) {
                $this->id_entreprise      = $entreprise->id;
                $this->entreprise_trouvee = $entreprise->nom;
            }
        }
    }

    public function toggleTypePartenariat(string $type): void
    {
        if (in_array($type, $this->types_partenariat)) {
            $this->types_partenariat = array_values(array_filter($this->types_partenariat, fn($t) => $t !== $type));
        } elseif (count($this->types_partenariat) < 3) {
            $this->types_partenariat[] = $type;
        }
    }

    public function toggleProfilPartenaire(string $profil): void
    {
        if (in_array($profil, $this->profils_partenaire)) {
            $this->profils_partenaire = array_values(array_filter($this->profils_partenaire, fn($p) => $p !== $profil));
        } elseif (count($this->profils_partenaire) < 3) {
            $this->profils_partenaire[] = $profil;
        }
    }

    public function toggleSecteurRecherche(string $secteur): void
    {
        if (in_array($secteur, $this->secteurs_recherche)) {
            $this->secteurs_recherche = array_values(array_filter($this->secteurs_recherche, fn($s) => $s !== $secteur));
        } elseif (count($this->secteurs_recherche) < 3) {
            $this->secteurs_recherche[] = $secteur;
        }
    }

    public function toggleDisponibilite(string $jour): void
    {
        if (in_array($jour, $this->disponibilites)) {
            $this->disponibilites = array_values(array_filter($this->disponibilites, fn($d) => $d !== $jour));
        } else {
            $this->disponibilites[] = $jour;
        }
    }

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

    public function closeModalCompte(): void
    {
        $this->showModalCompte = false;
    }

    public function resetFields(): void
    {
        $this->participant_id          = null;
        $this->id_entreprise           = '';
        $this->id_evenement            = '';
        $this->nom                     = '';
        $this->prenom                  = '';
        $this->genre                   = '';
        $this->fonction                = '';
        $this->ifu                     = '';
        $this->email                   = '';
        $this->telephone               = '';
        $this->pays                    = '';
        $this->ville                   = '';
        // ✅ CORRIGÉ
        $this->role                    = 'participant';
        $this->statut_historique       = 'actif';
        $this->participation_rdv       = true;
        $this->date_naissance          = '';
        $this->filiere                 = '';
        $this->universite              = '';
        $this->statut_participant      = 'classique';
        $this->secteur_activite        = '';
        $this->secteur_activite_autre  = '';
        $this->sous_secteur            = '';
        $this->description_activites   = '';
        $this->principaux_produits     = '';
        $this->annee_creation          = '';
        $this->nombre_salaries         = '';
        $this->chiffre_affaires        = '';
        $this->zone_geographique       = '';
        $this->disponibilites          = [];
        $this->types_partenariat       = [];
        $this->type_partenariat_autre  = '';
        $this->profils_partenaire      = [];
        $this->secteurs_recherche      = [];
        $this->secteur_recherche_autre = '';
        $this->objectif_participation  = '';
        $this->id_chef_delegation      = '';
        $this->entreprise_trouvee      = '';
        $this->resetErrorBag();
    }

    public function modifier($id): void
    {
        $p = Participant::findOrFail($id);
        $this->participant_id          = $p->id;
        $this->id_entreprise           = $p->id_entreprise;
        $this->id_evenement            = $p->id_evenement;
        $this->nom                     = $p->nom;
        $this->prenom                  = $p->prenom;
        $this->genre                   = $p->genre;
        $this->fonction                = $p->fonction;
        $this->ifu                     = $p->ifu;
        $this->email                   = $p->email;
        $this->telephone               = $p->telephone;
        $this->pays                    = $p->pays ?? '';
        $this->ville                   = $p->ville ?? '';
        $this->role                    = $p->role;
        $this->statut_historique       = $p->statut_historique;
        $this->participation_rdv       = $p->participation_rdv;
        $this->date_naissance          = $p->date_naissance
            ? $p->date_naissance->format('Y-m-d') : '';
        $this->filiere                 = $p->filiere ?? '';
        $this->universite              = $p->universite ?? '';
        $this->statut_participant      = $p->statut_participant ?? 'classique';
        $this->sous_secteur            = $p->sous_secteur ?? '';
        $this->description_activites   = $p->description_activites ?? '';
        $this->principaux_produits     = $p->principaux_produits ?? '';
        $this->annee_creation          = $p->annee_creation ?? '';
        $this->nombre_salaries         = $p->nombre_salaries ?? '';
        $this->chiffre_affaires        = $p->chiffre_affaires ?? '';
        $this->zone_geographique       = $p->zone_geographique ?? '';
        $this->disponibilites          = $p->disponibilites ?? [];
        $this->types_partenariat       = $p->types_partenariat ?? [];
        $this->type_partenariat_autre  = $p->type_partenariat_autre ?? '';
        $this->profils_partenaire      = $p->profils_partenaire ?? [];
        $this->secteurs_recherche      = $p->secteurs_recherche ?? [];
        $this->secteur_recherche_autre = $p->secteur_recherche_autre ?? '';
        $this->objectif_participation  = $p->objectif_participation ?? '';
        $this->id_chef_delegation      = $p->id_chef_delegation ?? '';

        if (in_array($p->secteur_activite, $this->secteurs)) {
            $this->secteur_activite       = $p->secteur_activite ?? '';
            $this->secteur_activite_autre = '';
        } else {
            $this->secteur_activite       = 'Autre';
            $this->secteur_activite_autre = $p->secteur_activite ?? '';
        }

        if ($p->id_entreprise) {
            $entreprise = Entreprise::find($p->id_entreprise);
            $this->entreprise_trouvee = $entreprise?->nom ?? '';
        }

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function voirCompte($id): void
    {
        $participant = Participant::findOrFail($id);
        $this->compte_email      = $participant->email ?? '';
        $this->compte_password   = '';
        $this->compte_code_acces = $participant->code_acces;
        $this->compte_has_email  = !empty($participant->email);
        $this->showModalCompte   = true;
    }

    // ════════════════════════════════════════════════════════
    // VALIDATION DE PRÉINSCRIPTION
    // ════════════════════════════════════════════════════════

    public function ouvrirValidationPreinscription(int $id): void
    {
        $this->preinscription_courante = Participant::with('entreprise', 'evenement')->findOrFail($id);
        $this->showModalPreinscription = true;
    }

    public function fermerValidationPreinscription(): void
    {
        $this->showModalPreinscription = false;
        $this->preinscription_courante = null;
    }

    public function validerPreinscription(): void
    {
        if (!$this->preinscription_courante) return;

        $participant = Participant::findOrFail($this->preinscription_courante->id);

        $participant->update([
            'statut_preinscription' => 'valide',
        ]);

        $password_genere = null;
        $userExiste = $participant->email
            ? User::where('email', $participant->email)->exists()
            : false;

        if ($participant->email && !$userExiste) {
            $password_genere = substr(str_shuffle(
                'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
            ), 0, 8);

            $user = User::create([
                'name'     => $participant->nom . ' ' . $participant->prenom,
                'email'    => $participant->email,
                'password' => Hash::make($password_genere),
            ]);

            $user->assignRole($participant->id_entreprise ? 'entreprise' : 'participant');
        }

        Notification::create([
            'id_participant' => $participant->id,
            'contenu'        => '✅ Votre préinscription a été validée ! '
                . 'Vous pouvez vous connecter avec votre code d\'accès : '
                . $participant->code_acces
                . ($participant->email ? ' ou via votre email.' : '.'),
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        $this->compte_email      = $participant->email ?? '';
        $this->compte_password   = $password_genere;
        $this->compte_code_acces = $participant->code_acces;
        $this->compte_has_email  = !empty($participant->email);

        $this->fermerValidationPreinscription();
        $this->showModalCompte = true;

        session()->flash('success', 'Préinscription validée ! Le compte a été créé.');
    }

    public function ouvrirRejetPreinscription(int $id): void
    {
        $this->preinscription_courante = Participant::findOrFail($id);
        $this->motif_rejet             = '';
        $this->showModalRejet          = true;
    }

    public function fermerRejetPreinscription(): void
    {
        $this->showModalRejet          = false;
        $this->preinscription_courante = null;
        $this->motif_rejet             = '';
    }

    public function rejeterPreinscription(): void
    {
        $this->validate([
            'motif_rejet' => 'required|min:5',
        ], [
            'motif_rejet.required' => 'Veuillez indiquer le motif du rejet.',
            'motif_rejet.min'      => 'Le motif est trop court.',
        ]);

        $participant = Participant::findOrFail($this->preinscription_courante->id);

        $participant->update([
            'statut_preinscription' => 'rejete',
        ]);

        Notification::create([
            'id_participant' => $participant->id,
            'contenu'        => '❌ Votre préinscription a été rejetée. Motif : '
                . $this->motif_rejet,
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        $this->fermerRejetPreinscription();
        session()->flash('success', 'Préinscription rejetée. Le participant a été notifié.');
    }

    // ════════════════════════════════════════════════════════

    public function sauvegarder(): void
    {
        $this->validate([
            'id_evenement'  => 'required',
            'nom'           => 'required|string|max:255',
            'prenom'        => 'required|string|max:255',
            'telephone'     => 'required|string|max:20',
            'email'         => $this->isEditing
                ? 'nullable|email|max:255'
                : 'nullable|email|max:255|unique:users,email',
            'ifu'           => 'nullable|string|max:255',
            'role'          => 'required',
            'genre'         => 'nullable|in:homme,femme',
            'date_naissance' => 'nullable|date',
            'objectif_participation' => 'nullable|string|max:200',
        ]);

        if ($this->ifu) {
            $entrepriseParIfu = Entreprise::where('ifu', $this->ifu)->first();
            if ($entrepriseParIfu) {
                $this->id_entreprise = $entrepriseParIfu->id;
            }
        }

        $secteurFinal = $this->secteur_activite === 'Autre'
            ? $this->secteur_activite_autre
            : $this->secteur_activite;

        $code_acces = strtoupper(substr($this->nom, 0, 3) . rand(1000, 9999));

        $data = [
            'id_entreprise'          => $this->id_entreprise ?: null,
            'id_evenement'           => $this->id_evenement,
            'id_chef_delegation'     => $this->id_chef_delegation ?: null,
            'nom'                    => $this->nom,
            'prenom'                 => $this->prenom,
            'genre'                  => $this->genre ?: null,
            'fonction'               => $this->fonction ?: null,
            'ifu'                    => $this->ifu ?: null,
            'email'                  => $this->email ?: null,
            'telephone'              => $this->telephone,
            'pays'                   => $this->pays ?: null,
            'ville'                  => $this->ville ?: null,
            'role'                   => $this->role,
            'statut_historique'      => $this->statut_historique,
            'participation_rdv'      => $this->participation_rdv,
            'date_naissance'         => $this->date_naissance ?: null,
            'filiere'                => $this->filiere ?: null,
            'universite'             => $this->universite ?: null,
            'statut_participant'     => $this->statut_participant,
            'secteur_activite'       => $secteurFinal ?: null,
            'sous_secteur'           => $this->sous_secteur ?: null,
            'description_activites'  => $this->description_activites ?: null,
            'principaux_produits'    => $this->principaux_produits ?: null,
            'annee_creation'         => $this->annee_creation ?: null,
            'nombre_salaries'        => $this->nombre_salaries ?: null,
            'chiffre_affaires'       => $this->chiffre_affaires ?: null,
            'zone_geographique'      => $this->zone_geographique ?: null,
            'disponibilites'         => $this->disponibilites ?: null,
            'types_partenariat'      => $this->types_partenariat ?: null,
            'type_partenariat_autre' => in_array('Autre', $this->types_partenariat)
                ? $this->type_partenariat_autre : null,
            'profils_partenaire'     => $this->profils_partenaire ?: null,
            'secteurs_recherche'     => $this->secteurs_recherche ?: null,
            'secteur_recherche_autre' => in_array('Autre', $this->secteurs_recherche)
                ? $this->secteur_recherche_autre : null,
            'objectif_participation' => $this->objectif_participation ?: null,
        ];

        if ($this->isEditing) {
            Participant::findOrFail($this->participant_id)->update($data);
            session()->flash('success', 'Participant modifié avec succès.');
            $this->closeModal();
        } else {
            $data['code_acces']            = $code_acces;
            $data['statut_preinscription'] = 'valide';
            $participant                   = Participant::create($data);

            $evenement = Evenement::find($this->id_evenement);
            if ($evenement) {
                $inscriptionExiste = Inscription::where('id_participant', $participant->id)
                    ->where('id_evenement', $this->id_evenement)
                    ->exists();

                if (!$inscriptionExiste) {
                    $montant = $evenement->montant_inscription ?? 0;
                    $statut  = 'en_attente';

                    if ($evenement->type_paiement == 'gratuit') {
                        $montant = 0;
                        $statut  = 'paye';
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

    public function supprimer($id): void
    {
        $participant = Participant::findOrFail($id);
        $user        = User::where('email', $participant->email)->first();
        if ($user) $user->delete();
        $participant->delete();
        session()->flash('success', 'Participant supprimé.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-participants', [
            'participants'      => Participant::with(['entreprise', 'evenement'])
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('prenom', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                )
                ->when($this->filtre_preinscription, fn($q) =>
                    $q->where('statut_preinscription', $this->filtre_preinscription)
                )
                ->latest()
                ->get(),
            'evenements'        => Evenement::orderBy('nom')->get(),
            'villesDisponibles' => $this->villesDisponibles,
            'chefsDelegation'   => Participant::where('role', 'chef_delegation')
                ->orderBy('nom')
                ->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Participants']);
    }
}