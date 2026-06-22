<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\Entreprise;
use App\Models\Participant;

class MonProfil extends Component
{
    
    // INFOS ENTREPRISE
    
    public $entreprise_id;
    public $nom                   = '';
    public $nom_responsable       = '';
    public $prenom_responsable    = '';
    public $fonction_responsable  = '';
    public $ifu                   = '';
    public $secteur_activite      = '';
    public $sous_secteur          = '';
    public $description_activites = '';
    public $principaux_produits   = '';
    public $pays                  = '';
    public $ville                 = '';
    public $contact               = '';
    public $email_responsable     = '';

    // ← Infos entreprise supplémentaires
    public $annee_creation    = '';
    public $nombre_salaries   = '';
    public $chiffre_affaires  = '';

    
    // PROFIL PARTENAIRE RECHERCHÉ (max 3 chacun)
    

    public string $zone_geographique = '';

    public array  $secteurs_recherche      = [];
    public string $secteur_recherche_autre = '';

    public array  $types_partenariat       = [];
    public string $type_partenariat_autre  = '';

    public array  $profils_partenaire      = [];

    public bool $isEditing = false;

    
    // LISTES DE RÉFÉRENCE
    

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
    // AFRIQUE — zones économiques
    'UEMOA (Afrique de l\'Ouest)',
    'CEMAC (Afrique Centrale)',
    'Afrique du Nord (Maghreb)',
    'Afrique de l\'Est (EAC)',
    'Afrique Australe (SADC)',
    'Afrique (toute la région)',

    // EUROPE
    'Union Européenne',
    'Europe de l\'Ouest',
    'Europe de l\'Est',
    'Europe (toute la région)',

    // AMÉRIQUES
    'Amérique du Nord',
    'Amérique Centrale et Caraïbes',
    'Amérique du Sud',
    'Amériques (toute la région)',

    // ASIE
    'Asie de l\'Est',
    'Asie du Sud-Est',
    'Asie du Sud',
    'Moyen-Orient',
    'Asie (toute la région)',

    // OCÉANIE
    'Océanie',

    // GLOBAL
    'Locale (mon pays uniquement)',
    'Internationale (toutes zones)',
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

    public array $pays_liste = [
        'Burkina Faso', 'Côte d\'Ivoire', 'Mali', 'Sénégal',
        'Ghana', 'Togo', 'Bénin', 'Niger', 'Guinée', 'Cameroun',
        'Nigeria', 'France', 'Allemagne', 'États-Unis', 'Chine', 'Autre',
    ];

    public array $villes_par_pays = [
        'Burkina Faso'   => ['Ouagadougou', 'Bobo-Dioulasso', 'Koudougou', 'Banfora', 'Ouahigouya', 'Kaya', 'Tenkodogo', 'Fada N\'Gourma', 'Dédougou', 'Autre'],
        'Côte d\'Ivoire' => ['Abidjan', 'Bouaké', 'Daloa', 'San-Pédro', 'Yamoussoukro', 'Korhogo', 'Man', 'Autre'],
        'Mali'           => ['Bamako', 'Sikasso', 'Mopti', 'Koutiala', 'Kayes', 'Ségou', 'Autre'],
        'Sénégal'        => ['Dakar', 'Thiès', 'Kaolack', 'Ziguinchor', 'Saint-Louis', 'Autre'],
        'Ghana'          => ['Accra', 'Kumasi', 'Tamale', 'Cape Coast', 'Autre'],
        'Togo'           => ['Lomé', 'Sokodé', 'Kara', 'Atakpamé', 'Autre'],
        'Bénin'          => ['Cotonou', 'Porto-Novo', 'Parakou', 'Abomey-Calavi', 'Autre'],
        'Niger'          => ['Niamey', 'Zinder', 'Maradi', 'Tahoua', 'Autre'],
        'Guinée'         => ['Conakry', 'Nzérékoré', 'Kankan', 'Autre'],
        'Cameroun'       => ['Yaoundé', 'Douala', 'Garoua', 'Bamenda', 'Autre'],
        'Nigeria'        => ['Lagos', 'Kano', 'Ibadan', 'Abuja', 'Autre'],
        'France'         => ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Autre'],
        'Allemagne'      => ['Berlin', 'Hambourg', 'Munich', 'Autre'],
        'États-Unis'     => ['New York', 'Los Angeles', 'Chicago', 'Washington', 'Autre'],
        'Chine'          => ['Pékin', 'Shanghai', 'Guangzhou', 'Autre'],
        'Autre'          => ['Autre'],
    ];

    // HELPERS
    

    private function getEntreprise(): ?Entreprise
    {
        return Entreprise::where('email_responsable', auth()->user()->email)->first();
    }

    public function getVillesDisponibles(): array
    {
        return $this->villes_par_pays[$this->pays] ?? ['Autre'];
    }

    public function updatedPays(): void
    {
        $this->ville = '';
    }

    /**
     * Décode une liste JSON et remplace toute valeur hors-liste
     * par "Autre" + remplit la propriété "_autre" correspondante.
     */
    private function chargerListeAvecAutre($valeurBrute, array $listeOfficielle, string $proprieteAutre): array
    {
        $valeurs = is_array($valeurBrute)
            ? $valeurBrute
            : (json_decode($valeurBrute ?? '[]', true) ?: []);

        foreach ($valeurs as $i => $valeur) {
            if (!in_array($valeur, $listeOfficielle)) {
                $this->{$proprieteAutre} = $valeur;
                $valeurs[$i] = 'Autre';
            }
        }

        return $valeurs;
    }


    // MOUNT
    

    public function mount(): void
    {
        $entreprise  = $this->getEntreprise();
        $representant = $entreprise
            ? Participant::where('id_entreprise', $entreprise->id)
                ->where('role', 'representant')
                ->first()
            : null;

        if ($entreprise) {
            $this->entreprise_id          = $entreprise->id;
            $this->nom                    = $entreprise->nom;
            $this->nom_responsable        = $entreprise->nom_responsable ?? '';
            $this->prenom_responsable     = $entreprise->prenom_responsable ?? '';
            $this->fonction_responsable   = $entreprise->fonction_responsable ?? '';
            $this->ifu                    = $entreprise->ifu ?? '';
            $this->secteur_activite       = $entreprise->secteur_activite ?? '';
            $this->sous_secteur           = $entreprise->sous_secteur ?? '';
            $this->description_activites  = $entreprise->description_activites ?? '';
            $this->principaux_produits    = $entreprise->principaux_produits ?? '';
            $this->pays                   = $entreprise->pays ?? '';
            $this->ville                  = $entreprise->ville ?? '';
            $this->contact                = $entreprise->contact ?? '';
            $this->email_responsable      = $entreprise->email_responsable ?? '';
        }

        // ← Récupère les infos du représentant
        if ($representant) {
            $this->annee_creation   = $representant->annee_creation ?? '';
            $this->nombre_salaries  = $representant->nombre_salaries ?? '';
            $this->chiffre_affaires = $representant->chiffre_affaires ?? '';

            // ← Profil partenaire recherché
            $this->zone_geographique = $representant->zone_geographique ?? '';

            $this->secteurs_recherche = $this->chargerListeAvecAutre(
                $representant->secteurs_recherche,
                $this->secteurs,
                'secteur_recherche_autre'
            );

            $this->types_partenariat = $this->chargerListeAvecAutre(
                $representant->types_partenariat,
                $this->typesPartenariatOptions,
                'type_partenariat_autre'
            );

            $this->profils_partenaire = is_array($representant->profils_partenaire)
                ? $representant->profils_partenaire
                : (json_decode($representant->profils_partenaire ?? '[]', true) ?: []);
        }
    }

    public function activer(): void
    {
        $this->isEditing = true;
    }

    public function annuler(): void
    {
        $this->isEditing = false;
        $this->mount();
        $this->resetErrorBag();
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

    
    // SAUVEGARDER


    public function sauvegarder(): void
    {
        $this->validate([
            'nom'                   => 'required|string|max:255',
            'secteur_activite'      => 'required|string|max:255',
            'sous_secteur'          => 'required|string|max:255',
            'description_activites' => 'required|string',
            'pays'                  => 'required|string|max:255',
            'ville'                 => 'required|string|max:255',
            'contact'               => 'required|string|max:255',
            'annee_creation'        => 'required|integer|min:1900|max:' . date('Y'),
            'nombre_salaries'       => 'required|integer|min:1',
            'chiffre_affaires'      => 'required|numeric|min:0|max:100',
            'zone_geographique'     => 'required|string|max:255',
            'secteurs_recherche'    => 'required|array|min:1|max:3',
            'types_partenariat'     => 'required|array|min:1|max:3',
            'profils_partenaire'    => 'nullable|array|max:3',
        ], [
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

        // ← Met à jour l'entreprise
        Entreprise::findOrFail($this->entreprise_id)->update([
            'nom'                   => $this->nom,
            'nom_responsable'       => $this->nom_responsable,
            'prenom_responsable'    => $this->prenom_responsable,
            'fonction_responsable'  => $this->fonction_responsable,
            'ifu'                   => $this->ifu ?: null,
            'secteur_activite'      => $this->secteur_activite,
            'sous_secteur'          => $this->sous_secteur,
            'description_activites' => $this->description_activites,
            'principaux_produits'   => $this->principaux_produits,
            'pays'                  => $this->pays,
            'ville'                 => $this->ville,
            'contact'               => $this->contact,
        ]);

        // ← Remplace "Autre" par la saisie libre dans les sélections
        $secteursRecherche = $this->secteurs_recherche;
        if (($idx = array_search('Autre', $secteursRecherche)) !== false && $this->secteur_recherche_autre) {
            $secteursRecherche[$idx] = $this->secteur_recherche_autre;
        }

        $typesPartenariat = $this->types_partenariat;
        if (($idx = array_search('Autre', $typesPartenariat)) !== false && $this->type_partenariat_autre) {
            $typesPartenariat[$idx] = $this->type_partenariat_autre;
        }

        // ← Met à jour le représentant
        $representant = Participant::where('id_entreprise', $this->entreprise_id)
            ->where('role', 'representant')
            ->first();

        if ($representant) {
            $representant->update([
                'secteur_activite'      => $this->secteur_activite,
                'sous_secteur'          => $this->sous_secteur,
                'description_activites' => $this->description_activites,
                'principaux_produits'   => $this->principaux_produits,
                'pays'                  => $this->pays,
                'ville'                 => $this->ville,
                'annee_creation'        => $this->annee_creation,
                'nombre_salaries'       => $this->nombre_salaries,
                'chiffre_affaires'      => $this->chiffre_affaires,
                'zone_geographique'     => $this->zone_geographique,
                'secteurs_recherche'    => json_encode($secteursRecherche),
                'types_partenariat'     => json_encode($typesPartenariat),
                'profils_partenaire'    => !empty($this->profils_partenaire) ? json_encode($this->profils_partenaire) : null,
            ]);
        }

        $this->isEditing = false;
        session()->flash('success', 'Profil mis à jour avec succès.');
    }

    
    // RENDER
    

    public function render()
    {
        return view('livewire.entreprise.mon-profil', [
            'entreprise'                => $this->getEntreprise(),
            'villesDisponibles'         => $this->getVillesDisponibles(),
            'zonesGeographiques'        => $this->zonesGeographiques,
            'typesPartenariatOptions'   => $this->typesPartenariatOptions,
            'profilsPartenariatOptions' => $this->profilsPartenariatOptions,
        ])->layout('layouts.entreprise', ['title' => 'Mon Profil']);
    }
}