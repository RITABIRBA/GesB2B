<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\Evenement;
use App\Models\Inscription;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GestionParticipants extends Component
{
    public string $search = '';

    public $participant_id;
    public $id_evenement            = '';
    public string $nom              = '';
    public string $prenom           = '';
    public string $genre            = '';
    public string $fonction         = '';
    public string $ifu              = '';
    public string $email            = '';
    public string $telephone        = '';
    public string $pays             = '';
    public string $ville            = '';
    public string $role             = 'representant';
    public bool   $participation_rdv = true;

    public string $secteur_activite        = '';
    public string $secteur_activite_autre  = '';
    public string $sous_secteur            = '';
    public string $description_activites   = '';
    public string $principaux_produits     = '';
    public string $annee_creation          = '';
    public string $nombre_salaries         = '';
    public string $chiffre_affaires        = '';
    public string $objectif_participation  = '';

    public string $zone_geographique       = '';
    public array  $types_partenariat       = [];
    public string $type_partenariat_autre  = '';
    public array  $profils_partenaire      = [];
    public array  $secteurs_recherche      = [];
    public string $secteur_recherche_autre = '';
    public array  $disponibilites          = [];

    public string $entreprise_trouvee = '';
    public $id_entreprise             = '';

    public bool $showModal       = false;
    public bool $isEditing       = false;
    public bool $showModalCompte = false;

    public string $compte_email      = '';
    public string $compte_password   = '';
    public string $compte_code_acces = '';
    public bool   $compte_has_email  = false;

    public array $roles = ['representant', 'membre'];

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
        'UEMOA (Afrique de l\'Ouest)', 'CEMAC (Afrique Centrale)',
        'Afrique du Nord (Maghreb)', 'Afrique de l\'Est (EAC)',
        'Afrique Australe (SADC)', 'Afrique (toute la région)',
        'Union Européenne', 'Europe de l\'Ouest', 'Europe de l\'Est',
        'Europe (toute la région)', 'Amérique du Nord',
        'Amérique Centrale et Caraïbes', 'Amérique du Sud',
        'Amériques (toute la région)', 'Asie de l\'Est', 'Asie du Sud-Est',
        'Asie du Sud', 'Moyen-Orient', 'Asie (toute la région)',
        'Océanie', 'Locale (mon pays uniquement)', 'Internationale (toutes zones)',
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
        'Burkina Faso'    => ['Ouagadougou', 'Bobo-Dioulasso', 'Koudougou', 'Banfora', 'Ouahigouya', 'Pouytenga', 'Kaya', 'Tenkodogo', 'Fada N\'Gourma', 'Dédougou', 'Ziniaré', 'Kongoussi', 'Autre'],
        'Côte d\'Ivoire'  => ['Abidjan', 'Bouaké', 'Daloa', 'San-Pédro', 'Yamoussoukro', 'Korhogo', 'Man', 'Divo', 'Gagnoa', 'Abengourou', 'Soubré', 'Bondoukou', 'Autre'],
        'Mali'            => ['Bamako', 'Sikasso', 'Mopti', 'Koutiala', 'Kayes', 'Ségou', 'Gao', 'Tombouctou', 'Kidal', 'Autre'],
        'Sénégal'         => ['Dakar', 'Thiès', 'Kaolack', 'Ziguinchor', 'Saint-Louis', 'Rufisque', 'Mbour', 'Louga', 'Diourbel', 'Tambacounda', 'Autre'],
        'Togo'            => ['Lomé', 'Sokodé', 'Kara', 'Atakpamé', 'Kpalimé', 'Dapaong', 'Tsévié', 'Autre'],
        'Ghana'           => ['Accra', 'Kumasi', 'Tamale', 'Sekondi-Takoradi', 'Cape Coast', 'Tema', 'Autre'],
        'Nigeria'         => ['Lagos', 'Kano', 'Ibadan', 'Abuja', 'Port Harcourt', 'Benin City', 'Maiduguri', 'Zaria', 'Aba', 'Enugu', 'Kaduna', 'Ilorin', 'Autre'],
        'France'          => ['Paris', 'Marseille', 'Lyon', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg', 'Bordeaux', 'Lille', 'Montpellier', 'Autre'],
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

    public function closeModalCompte(): void { $this->showModalCompte = false; }

    public function resetFields(): void
    {
        $this->participant_id          = null;
        $this->id_evenement            = '';
        $this->id_entreprise           = '';
        $this->entreprise_trouvee      = '';
        $this->nom                     = '';
        $this->prenom                  = '';
        $this->genre                   = '';
        $this->fonction                = '';
        $this->ifu                     = '';
        $this->email                   = '';
        $this->telephone               = '';
        $this->pays                    = '';
        $this->ville                   = '';
        $this->role                    = 'representant';
        $this->participation_rdv       = true;
        $this->secteur_activite        = '';
        $this->secteur_activite_autre  = '';
        $this->sous_secteur            = '';
        $this->description_activites   = '';
        $this->principaux_produits     = '';
        $this->annee_creation          = '';
        $this->nombre_salaries         = '';
        $this->chiffre_affaires        = '';
        $this->objectif_participation  = '';
        $this->zone_geographique       = '';
        $this->types_partenariat       = [];
        $this->type_partenariat_autre  = '';
        $this->profils_partenaire      = [];
        $this->secteurs_recherche      = [];
        $this->secteur_recherche_autre = '';
        $this->disponibilites          = [];
        $this->resetErrorBag();
    }

    public function modifier($id): void
    {
        $p = Participant::findOrFail($id);
        $this->participant_id          = $p->id;
        $this->id_evenement            = $p->id_evenement;
        $this->id_entreprise           = $p->id_entreprise;
        $this->nom                     = $p->nom;
        $this->prenom                  = $p->prenom;
        $this->genre                   = $p->genre ?? '';
        $this->fonction                = $p->fonction ?? '';
        $this->ifu                     = $p->ifu ?? '';
        $this->email                   = $p->email ?? '';
        $this->telephone               = $p->telephone ?? '';
        $this->pays                    = $p->pays ?? '';
        $this->ville                   = $p->ville ?? '';
        $this->role                    = $p->role;
        $this->participation_rdv       = $p->participation_rdv;
        $this->sous_secteur            = $p->sous_secteur ?? '';
        $this->description_activites   = $p->description_activites ?? '';
        $this->principaux_produits     = $p->principaux_produits ?? '';
        $this->annee_creation          = $p->annee_creation ?? '';
        $this->nombre_salaries         = $p->nombre_salaries ?? '';
        $this->chiffre_affaires        = $p->chiffre_affaires ?? '';
        $this->objectif_participation  = $p->objectif_participation ?? '';
        $this->zone_geographique       = $p->zone_geographique ?? '';
        $this->types_partenariat       = $p->types_partenariat ?? [];
        $this->type_partenariat_autre  = $p->type_partenariat_autre ?? '';
        $this->profils_partenaire      = $p->profils_partenaire ?? [];
        $this->secteurs_recherche      = $p->secteurs_recherche ?? [];
        $this->secteur_recherche_autre = $p->secteur_recherche_autre ?? '';
        $this->disponibilites          = $p->disponibilites ?? [];

        if (in_array($p->secteur_activite, $this->secteurs)) {
            $this->secteur_activite       = $p->secteur_activite ?? '';
            $this->secteur_activite_autre = '';
        } else {
            $this->secteur_activite       = 'Autre';
            $this->secteur_activite_autre = $p->secteur_activite ?? '';
        }

        if ($p->id_entreprise) {
            $this->entreprise_trouvee = Entreprise::find($p->id_entreprise)?->nom ?? '';
        }

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function sauvegarder(): void
    {
        $this->validate([
            'id_evenement'           => 'required',
            'nom'                    => 'required|string|max:255',
            'prenom'                 => 'required|string|max:255',
            'telephone'              => 'required|string|max:20',
            'email'                  => $this->isEditing
                ? 'nullable|email|max:255'
                : 'nullable|email|max:255|unique:users,email',
            'role'                   => 'required',
            'genre'                  => 'nullable|in:homme,femme',
            'objectif_participation' => 'nullable|string|max:200',
        ]);

        if ($this->ifu) {
            $ent = Entreprise::where('ifu', $this->ifu)->first();
            if ($ent) $this->id_entreprise = $ent->id;
        }

        $secteurFinal = $this->secteur_activite === 'Autre'
            ? $this->secteur_activite_autre
            : $this->secteur_activite;

        // ✅ CORRECTION : supprime les accents avant d'extraire les 3 premières lettres
        $nom_sans_accent = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $this->nom);
        $code_acces = strtoupper(substr($nom_sans_accent, 0, 3)) . rand(1000, 9999);

        $data = [
            'id_cdd'                 => auth()->id(),
            'id_entreprise'          => $this->id_entreprise ?: null,
            'id_evenement'           => $this->id_evenement,
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
            'participation_rdv'      => $this->participation_rdv,
            'statut_historique'      => 'actif',
            'secteur_activite'       => $secteurFinal ?: null,
            'sous_secteur'           => $this->sous_secteur ?: null,
            'description_activites'  => $this->description_activites ?: null,
            'principaux_produits'    => $this->principaux_produits ?: null,
            'annee_creation'         => $this->annee_creation ?: null,
            'nombre_salaries'        => $this->nombre_salaries ?: null,
            'chiffre_affaires'       => $this->chiffre_affaires ?: null,
            'objectif_participation' => $this->objectif_participation ?: null,
            'zone_geographique'      => $this->zone_geographique ?: null,
            'types_partenariat'      => $this->types_partenariat ?: null,
            'type_partenariat_autre' => in_array('Autre', $this->types_partenariat) ? $this->type_partenariat_autre : null,
            'profils_partenaire'     => $this->profils_partenaire ?: null,
            'secteurs_recherche'     => $this->secteurs_recherche ?: null,
            'secteur_recherche_autre' => in_array('Autre', $this->secteurs_recherche) ? $this->secteur_recherche_autre : null,
            'disponibilites'         => $this->disponibilites ?: null,
        ];

        if ($this->isEditing) {
            Participant::findOrFail($this->participant_id)->update($data);
            session()->flash('success', 'Participant modifié avec succès.');
            $this->closeModal();
        } else {
            $data['code_acces'] = $code_acces;
            $participant        = Participant::create($data);

            $evenement = Evenement::find($this->id_evenement);
            if ($evenement) {
                $montant = $evenement->montant_inscription ?? 0;
                $statut  = 'en_attente';
                if ($evenement->type_paiement == 'gratuit') {
                    $montant = 0;
                    $statut  = 'paye';
                } elseif ($evenement->type_paiement == 'par_entreprise' && $participant->id_entreprise) {
                    $montant = 0;
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

            $password_genere = null;
            if ($this->email) {
                try {
                    $password_genere = substr(str_shuffle(
                        'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
                    ), 0, 8);
                    $user = User::create([
                        'name'     => $this->nom . ' ' . $this->prenom,
                        'email'    => $this->email,
                        'password' => Hash::make($password_genere),
                    ]);
                    $user->assignRole('participant');
                } catch (\Exception $e) {
                    $password_genere = null;
                }
            }

            $this->compte_email      = $this->email;
            $this->compte_password   = $password_genere;
            $this->compte_code_acces = $code_acces;
            $this->compte_has_email  = !empty($this->email);
            $this->showModalCompte   = true;

            $this->closeModal();
        }
    }

    public function render()
    {
        return view('livewire.cdd.gestion-participants', [
            'participants'      => Participant::with(['entreprise', 'evenement'])
                ->where('id_cdd', auth()->id())
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('prenom', 'like', '%' . $this->search . '%')
                )
                ->latest()
                ->get(),
            'evenements'        => Evenement::orderBy('nom')->get(),
            'villesDisponibles' => $this->villesDisponibles,
        ])->layout('layouts.cdd', ['title' => 'Mes Participants']);
    }
}