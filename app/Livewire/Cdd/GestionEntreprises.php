<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\Evenement;
use App\Models\Inscription;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class GestionEntreprises extends Component
{
    public string $search    = '';
    public bool   $showModal = false;
    public bool   $isEditing = false;
    public $entreprise_id    = null;

    // Infos entreprise
    public string $nom                    = '';
    public string $ifu                    = '';
    public string $secteur_activite       = '';
    public string $secteur_activite_autre = '';
    public string $sous_secteur           = '';
    public string $pays                   = '';
    public string $ville                  = '';
    public string $telephone              = '';
    public string $email                  = '';
    public string $statut_validation      = 'en_attente';

    // Représentant
    public string $rep_nom        = '';
    public string $rep_prenom     = '';
    public string $rep_genre      = '';
    public string $rep_fonction   = '';
    public string $rep_email      = '';
    public string $rep_telephone  = '';
    public string $rep_pays       = '';
    public string $rep_ville      = '';
    public $rep_id_evenement      = '';

    // Profil partenariat représentant
    public string $zone_geographique       = '';
    public array  $types_partenariat       = [];
    public string $type_partenariat_autre  = '';
    public array  $profils_partenaire      = [];
    public array  $secteurs_recherche      = [];
    public string $secteur_recherche_autre = '';
    public string $objectif_participation  = '';
    public array  $disponibilites          = [];

    // Modal compte
    public bool   $showModalCompte   = false;
    public string $compte_email      = '';
    public string $compte_password   = '';
    public string $compte_code_acces = '';
    public bool   $compte_has_email  = false;

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
        'Investisseur', 'Importateur', 'Prestataire de service',
        'Sous-traitant', 'Innovation', 'R&D',
    ];

    public array $zonesGeographiques = [
        'Locale', 'Nationale', 'Régionale (CEDEAO)', 'Africaine', 'Internationale',
    ];

    public array $joursDisponibles = [
        'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche',
    ];

    public array $pays_liste = [
        // AFRIQUE DE L'OUEST
        'Bénin', 'Burkina Faso', 'Cap-Vert', 'Côte d\'Ivoire', 'Gambie',
        'Ghana', 'Guinée', 'Guinée-Bissau', 'Liberia', 'Mali', 'Mauritanie',
        'Niger', 'Nigeria', 'Sénégal', 'Sierra Leone', 'Togo',
        // AFRIQUE CENTRALE
        'Angola', 'Cameroun', 'Congo', 'Gabon', 'Guinée équatoriale',
        'République centrafricaine', 'République démocratique du Congo', 'Tchad',
        // AFRIQUE DE L'EST
        'Burundi', 'Djibouti', 'Érythrée', 'Éthiopie', 'Kenya', 'Madagascar',
        'Malawi', 'Maurice', 'Mozambique', 'Ouganda', 'Rwanda', 'Seychelles',
        'Somalie', 'Soudan', 'Soudan du Sud', 'Tanzanie', 'Zambie', 'Zimbabwe',
        // AFRIQUE DU NORD
        'Algérie', 'Égypte', 'Libye', 'Maroc', 'Tunisie',
        // AFRIQUE AUSTRALE
        'Afrique du Sud', 'Botswana', 'Eswatini', 'Lesotho', 'Namibie',
        // EUROPE
        'Allemagne', 'Autriche', 'Belgique', 'Danemark', 'Espagne',
        'Finlande', 'France', 'Grèce', 'Irlande', 'Italie', 'Luxembourg',
        'Norvège', 'Pays-Bas', 'Pologne', 'Portugal', 'Royaume-Uni',
        'Russie', 'Suède', 'Suisse', 'Turquie', 'Ukraine',
        // AMERIQUE
        'Argentine', 'Bolivie', 'Brésil', 'Canada', 'Chili', 'Colombie',
        'Cuba', 'États-Unis', 'Mexique', 'Pérou', 'Venezuela',
        // ASIE
        'Arabie Saoudite', 'Bangladesh', 'Chine', 'Corée du Sud',
        'Émirats arabes unis', 'Inde', 'Indonésie', 'Iran', 'Irak',
        'Israël', 'Japon', 'Jordanie', 'Liban', 'Malaisie', 'Pakistan',
        'Philippines', 'Qatar', 'Singapour', 'Thaïlande', 'Vietnam',
        // OCEANIE
        'Australie', 'Nouvelle-Zélande',
        // AUTRE
        'Autre',
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

    public function updatedPays(): void
    {
        $this->ville = '';
    }

    public function toggleTypePartenariat(string $type): void
    {
        if (in_array($type, $this->types_partenariat)) {
            $this->types_partenariat = array_values(
                array_filter($this->types_partenariat, fn($t) => $t !== $type)
            );
        } elseif (count($this->types_partenariat) < 3) {
            $this->types_partenariat[] = $type;
        }
    }

    public function toggleProfilPartenaire(string $profil): void
    {
        if (in_array($profil, $this->profils_partenaire)) {
            $this->profils_partenaire = array_values(
                array_filter($this->profils_partenaire, fn($p) => $p !== $profil)
            );
        } elseif (count($this->profils_partenaire) < 3) {
            $this->profils_partenaire[] = $profil;
        }
    }

    public function toggleSecteurRecherche(string $secteur): void
    {
        if (in_array($secteur, $this->secteurs_recherche)) {
            $this->secteurs_recherche = array_values(
                array_filter($this->secteurs_recherche, fn($s) => $s !== $secteur)
            );
        } elseif (count($this->secteurs_recherche) < 3) {
            $this->secteurs_recherche[] = $secteur;
        }
    }

    public function toggleDisponibilite(string $jour): void
    {
        if (in_array($jour, $this->disponibilites)) {
            $this->disponibilites = array_values(
                array_filter($this->disponibilites, fn($d) => $d !== $jour)
            );
        } else {
            $this->disponibilites[] = $jour;
        }
    }

    public function openModal($id = null): void
    {
        $this->resetFields();
        if ($id) {
            $e = Entreprise::findOrFail($id);
            $this->entreprise_id     = $e->id;
            $this->nom               = $e->nom;
            $this->ifu               = $e->ifu ?? '';
            $this->secteur_activite  = $e->secteur_activite;
            $this->sous_secteur      = $e->sous_secteur ?? '';
            $this->pays              = $e->pays;
            $this->ville             = $e->ville;
            $this->telephone         = $e->contact;
            $this->statut_validation = $e->statut_validation;

            $rep = Participant::where('id_entreprise', $e->id)
                ->where('role', 'representant')->first();

            if ($rep) {
                $this->rep_nom                 = $rep->nom;
                $this->rep_prenom              = $rep->prenom;
                $this->rep_genre               = $rep->genre ?? '';
                $this->rep_fonction            = $rep->fonction ?? '';
                $this->rep_email               = $rep->email ?? '';
                $this->rep_telephone           = $rep->telephone ?? '';
                $this->rep_pays                = $rep->pays ?? '';
                $this->rep_ville               = $rep->ville ?? '';
                $this->rep_id_evenement        = $rep->id_evenement ?? '';
                $this->zone_geographique       = $rep->zone_geographique ?? '';
                $this->types_partenariat       = $rep->types_partenariat ?? [];
                $this->type_partenariat_autre  = $rep->type_partenariat_autre ?? '';
                $this->profils_partenaire      = $rep->profils_partenaire ?? [];
                $this->secteurs_recherche      = $rep->secteurs_recherche ?? [];
                $this->secteur_recherche_autre = $rep->secteur_recherche_autre ?? '';
                $this->objectif_participation  = $rep->objectif_participation ?? '';
                $this->disponibilites          = $rep->disponibilites ?? [];
            }

            $this->isEditing = true;
        }
        $this->showModal = true;
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
        $this->entreprise_id           = null;
        $this->nom                     = '';
        $this->ifu                     = '';
        $this->secteur_activite        = '';
        $this->secteur_activite_autre  = '';
        $this->sous_secteur            = '';
        $this->pays                    = '';
        $this->ville                   = '';
        $this->telephone               = '';
        $this->email                   = '';
        $this->statut_validation       = 'en_attente';
        $this->rep_nom                 = '';
        $this->rep_prenom              = '';
        $this->rep_genre               = '';
        $this->rep_fonction            = '';
        $this->rep_email               = '';
        $this->rep_telephone           = '';
        $this->rep_pays                = '';
        $this->rep_ville               = '';
        $this->rep_id_evenement        = '';
        $this->zone_geographique       = '';
        $this->types_partenariat       = [];
        $this->type_partenariat_autre  = '';
        $this->profils_partenaire      = [];
        $this->secteurs_recherche      = [];
        $this->secteur_recherche_autre = '';
        $this->objectif_participation  = '';
        $this->disponibilites          = [];
        $this->isEditing               = false;
        $this->resetErrorBag();
    }
public function updatedRepPays(): void
{
    $this->rep_ville = '';
}
    public function sauvegarder(): void
    {
        $this->validate([
            'nom'          => 'required|string|max:255',
            'ifu'          => [
                'required', 'string', 'regex:/^\d{8}[A-Za-z]$/',
                $this->isEditing
                    ? Rule::unique('entreprises', 'ifu')->ignore($this->entreprise_id)
                    : Rule::unique('entreprises', 'ifu'),
            ],
            'secteur_activite' => 'required|string|max:255',
            'sous_secteur' => 'required|string|max:255',
            'pays'         => 'required|string|max:255',
            'ville'        => 'required|string|max:255',
            'telephone'    => 'required|string|max:20',
            'rep_nom'      => 'required|string|max:255',
            'rep_prenom'   => 'required|string|max:255',
            'rep_telephone' => 'required|string|max:20',
            'rep_email'    => $this->isEditing
                ? 'nullable|email|max:255'
                : 'nullable|email|max:255|unique:users,email',
            'objectif_participation' => 'nullable|string|max:200',
        ], [
            'ifu.regex'  => 'Format IFU invalide. Exemple : 12345678A',
            'ifu.unique' => 'Ce numéro IFU est déjà utilisé.',
        ]);

        $secteurFinal = $this->secteur_activite === 'Autre'
            ? $this->secteur_activite_autre
            : $this->secteur_activite;

        $dataEntreprise = [
            'nom'               => $this->nom,
            'ifu'               => strtoupper($this->ifu),
            'secteur_activite'  => $secteurFinal,
            'sous_secteur'      => $this->sous_secteur,
            'pays'              => $this->pays,
            'ville'             => $this->ville,
            'contact'           => $this->telephone,
            'id_cdd'            => auth()->id(),
            'statut_validation' => $this->statut_validation,
        ];

        if ($this->isEditing) {
            $entreprise = Entreprise::findOrFail($this->entreprise_id);
            $entreprise->update($dataEntreprise);

            $rep = Participant::where('id_entreprise', $entreprise->id)
                ->where('role', 'representant')->first();
            if ($rep) {
                $rep->update($this->getDataRepresentant($entreprise->id));
            }

            session()->flash('success', 'Entreprise modifiée avec succès.');
            $this->closeModal();
        } else {
            $dataEntreprise['statut_validation']  = 'en_attente';
            $dataEntreprise['email_responsable']  = $this->rep_email ?: null;
            $dataEntreprise['nom_responsable']    = $this->rep_nom;
            $dataEntreprise['prenom_responsable'] = $this->rep_prenom;

            $entreprise  = Entreprise::create($dataEntreprise);
            $code_acces  = strtoupper(substr($this->rep_nom, 0, 3) . rand(1000, 9999));
            $repData     = $this->getDataRepresentant($entreprise->id);
            $repData['code_acces'] = $code_acces;
            $repData['role']       = 'representant';

            $representant = Participant::create($repData);

            if ($this->rep_id_evenement) {
                $evenement = Evenement::find($this->rep_id_evenement);
                if ($evenement) {
                    $montant = $evenement->montant_inscription ?? 0;
                    $statut  = $evenement->type_paiement == 'gratuit' ? 'paye' : 'en_attente';
                    Inscription::create([
                        'id_participant'   => $representant->id,
                        'id_evenement'     => $this->rep_id_evenement,
                        'date_inscription' => now()->toDateString(),
                        'montant_paye'     => $montant,
                        'statut_paiement'  => $statut,
                        'statut_presence'  => 'absent',
                    ]);
                }
            }

            $password_genere = null;
            if ($this->rep_email) {
                $password_genere = substr(str_shuffle(
                    'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
                ), 0, 8);
                $user = User::create([
                    'name'     => $this->rep_nom . ' ' . $this->rep_prenom,
                    'email'    => $this->rep_email,
                    'password' => Hash::make($password_genere),
                ]);
                $user->assignRole('entreprise');
            }

            $this->compte_email      = $this->rep_email;
            $this->compte_password   = $password_genere;
            $this->compte_code_acces = $code_acces;
            $this->compte_has_email  = !empty($this->rep_email);
            $this->showModalCompte   = true;

            session()->flash('success', 'Entreprise et représentant créés avec succès.');
            $this->closeModal();
        }
    }

    private function getDataRepresentant(int $id_entreprise): array
    {
        return [
            'id_entreprise'          => $id_entreprise,
            'id_evenement'           => $this->rep_id_evenement ?: null,
            'id_cdd'                 => auth()->id(),
            'nom'                    => $this->rep_nom,
            'prenom'                 => $this->rep_prenom,
            'genre'                  => $this->rep_genre ?: null,
            'fonction'               => $this->rep_fonction ?: null,
            'email'                  => $this->rep_email ?: null,
            'telephone'              => $this->rep_telephone,
            'pays'                   => $this->rep_pays ?: null,
            'ville'                  => $this->rep_ville ?: null,
            'participation_rdv'      => true,
            'zone_geographique'      => $this->zone_geographique ?: null,
            'types_partenariat'      => $this->types_partenariat ?: null,
            'type_partenariat_autre' => in_array('Autre', $this->types_partenariat)
                ? $this->type_partenariat_autre : null,
            'profils_partenaire'     => $this->profils_partenaire ?: null,
            'secteurs_recherche'     => $this->secteurs_recherche ?: null,
            'secteur_recherche_autre' => in_array('Autre', $this->secteurs_recherche)
                ? $this->secteur_recherche_autre : null,
            'objectif_participation' => $this->objectif_participation ?: null,
            'disponibilites'         => $this->disponibilites ?: null,
        ];
    }

    public function valider(int $id): void
    {
        Entreprise::findOrFail($id)->update(['statut_validation' => 'valide']);
        session()->flash('success', 'Entreprise validée.');
    }

    public function rejeter(int $id): void
    {
        Entreprise::findOrFail($id)->update(['statut_validation' => 'rejete']);
        session()->flash('success', 'Entreprise rejetée.');
    }

    public function render()
    {
        return view('livewire.cdd.gestion-entreprises', [
            'entreprises'       => Entreprise::with(['participants'])
                ->where('id_cdd', auth()->id())
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%' . $this->search . '%')
                )
                ->latest()->get(),
            'villesDisponibles' => $this->villesDisponibles,
            'evenements'        => Evenement::orderBy('nom')->get(),
        ])->layout('layouts.cdd', ['title' => 'Mes Entreprises']);
    }
}