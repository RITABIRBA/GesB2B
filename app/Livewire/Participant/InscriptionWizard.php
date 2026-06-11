<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Evenement;
use App\Models\Inscription;
use App\Models\Entreprise;

/**
 * Wizard d'inscription à un événement
 *
 * DEUX MODES selon si le participant appartient à une entreprise :
 *
 * MODE MEMBRE (appartient déjà à une entreprise) :
 * → Étape 1 : Présentation de l'événement
 * → Étape 2 : Infos personnelles (pré-remplies)
 * → Étape 3 : Profil partenaire recherché
 * → Étape 4 : Disponibilités
 * → Étape 5 : Confirmation
 *
 * MODE REPRÉSENTANT (inscrit son entreprise) :
 * → Étape 1 : Présentation de l'événement
 * → Étape 2 : Infos personnelles + entreprise (pré-remplies)
 * → Étape 3 : Profil partenaire recherché
 * → Étape 4 : Disponibilités
 * → Étape 5 : Confirmation
 */
class InscriptionWizard extends Component
{
    
    // NAVIGATION
    

    public int $etape = 0;

    
    // ÉVÉNEMENT
    

    public $id_evenement;
    public $evenement = null;

    
    // MODE : membre ou représentant
    

    /** true si le participant appartient déjà à une entreprise */
    public bool $estMembre = false;

    /** L'entreprise du participant (si membre) */
    public $entreprise = null;


    // INFOS PERSONNELLES (commun aux 2 modes)
    

    public string $nom           = '';
    public string $prenom        = '';
    public string $email         = '';
    public string $telephone     = '';
    public string $fonction      = '';
    public string $fonction_autre = '';
    public array  $disponibilites = [];

    
    // INFOS ENTREPRISE (uniquement pour le représentant)
    

    public string $pays                  = '';
    public string $ville                 = '';
    public string $secteur_activite      = '';
    public string $sous_secteur          = '';
    public string $description_activites = '';
    public string $principaux_produits   = '';
    public string $annee_creation        = '';
    public string $nombre_salaries       = '';
    public string $chiffre_affaires      = '';

    
    // PROFIL PARTENAIRE RECHERCHÉ (commun aux 2 modes)
    

    public string $secteur_recherche = '';
    public string $zone_geographique = '';
    public string $type_partenaire   = '';

    
    // LISTES DE RÉFÉRENCE
    

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
    

    public function getVillesDisponibles(): array
    {
        return $this->villes_par_pays[$this->pays] ?? ['Autre'];
    }

    public function updatedPays(): void
    {
        $this->ville = '';
    }

    /**
     * Retourne le dashboard selon le rôle.
     */
    private function getDashboardRoute(): string
    {
        $user = auth()->user();
        if ($user->hasRole('entreprise'))  return 'entreprise.dashboard';
        if ($user->hasRole('participant')) return 'participant.dashboard';
        if ($user->hasRole('cdd'))         return 'cdd.dashboard';
        return 'dashboard';
    }

    /**
     * Retourne le layout selon le rôle.
     */
    private function getLayout(): string
    {
        $user = auth()->user();
        if ($user->hasRole('entreprise'))  return 'layouts.entreprise';
        if ($user->hasRole('participant')) return 'layouts.participant';
        if ($user->hasRole('cdd'))         return 'layouts.cdd';
        return 'layouts.participant';
    }

    
    // MOUNT
    

    public function mount($evenement): void
    {
        $this->id_evenement = $evenement;
        $this->evenement    = Evenement::with('typeEvenement')->findOrFail($evenement);

        $participant = Participant::findForUser(auth()->user());

        if ($participant) {
            // ← Pré-remplit les infos personnelles
            $this->nom       = $participant->nom;
            $this->prenom    = $participant->prenom;
            $this->email     = $participant->email ?? '';
            $this->telephone = $participant->telephone ?? '';

            // ← Gestion fonction avec saisie libre
            if (in_array($participant->fonction, $this->fonctions)) {
                $this->fonction = $participant->fonction ?? '';
            } else {
                $this->fonction       = 'Autre';
                $this->fonction_autre = $participant->fonction ?? '';
            }

            // ← Pré-remplit le profil partenaire
            $this->secteur_recherche = $participant->secteur_recherche ?? '';
            $this->zone_geographique = $participant->zone_geographique ?? '';
            $this->type_partenaire   = $participant->type_partenaire ?? '';

            /// ← Vérifie si le participant appartient à une entreprise
if ($participant->id_entreprise) {
    $this->estMembre  = true;
    $entreprise = Entreprise::find($participant->id_entreprise);

    // ← Récupère l'annee_creation depuis le représentant
    $representant = Participant::where('id_entreprise', $participant->id_entreprise)
        ->where('role', 'representant')
        ->first();

    if ($entreprise) {
        $entreprise->annee_creation  = $representant->annee_creation ?? null;
        $entreprise->nombre_salaries = $representant->nombre_salaries ?? null;
        $entreprise->chiffre_affaires = $representant->chiffre_affaires ?? null;
    }

    $this->entreprise = $entreprise;
}
            else {
                // ← Représentant : pré-remplit les infos entreprise
                $this->estMembre             = false;
                $this->pays                  = $participant->pays ?? '';
                $this->ville                 = $participant->ville ?? '';
                $this->secteur_activite      = $participant->secteur_activite ?? '';
                $this->sous_secteur          = $participant->sous_secteur ?? '';
                $this->description_activites = $participant->description_activites ?? '';
                $this->principaux_produits   = $participant->principaux_produits ?? '';
                $this->annee_creation        = $participant->annee_creation ?? '';
                $this->nombre_salaries       = $participant->nombre_salaries ?? '';
                $this->chiffre_affaires      = $participant->chiffre_affaires ?? '';
            }
        }
    }

    
    // JOURS DE L'ÉVÉNEMENT
    

    public function getJoursEvenement(): array
    {
        if (!$this->evenement) return [];

        $jours = [];
        $debut = \Carbon\Carbon::parse($this->evenement->date_debut);
        $fin   = \Carbon\Carbon::parse($this->evenement->date_fin);

        while ($debut->lte($fin)) {
            $jours[] = $debut->format('Y-m-d');
            $debut->addDay();
        }

        return $jours;
    }

    public function getIsMultiJours(): bool
    {
        return count($this->getJoursEvenement()) > 1;
    }

    // NAVIGATION
    

    public function commencer(): void
    {
        $this->etape = 1;
    }

    public function suivant(): void
    {
        if ($this->etape == 1) {
            // ← Étape 1 → 2 : toujours
            $this->etape = 2;

        } elseif ($this->etape == 2) {

            // Si "Autre" on utilise la saisie libre
            if ($this->fonction == 'Autre') {
                $this->fonction = $this->fonction_autre;
            }

            if ($this->estMembre) {
                // ← MODE MEMBRE : valide uniquement les infos personnelles
                $this->validate([
                    'nom'       => 'required|string|max:255',
                    'prenom'    => 'required|string|max:255',
                    'telephone' => 'required|string|max:20',
                    'fonction'  => 'required|string|max:255',
                ], [
                    'nom.required'       => 'Le nom est obligatoire.',
                    'prenom.required'    => 'Le prénom est obligatoire.',
                    'telephone.required' => 'Le téléphone est obligatoire.',
                    'fonction.required'  => 'La fonction est obligatoire.',
                ]);
            } else {
                // ← MODE REPRÉSENTANT : valide infos personnelles + entreprise
                $this->validate([
                    'nom'                   => 'required|string|max:255',
                    'prenom'                => 'required|string|max:255',
                    'email'                 => 'required|email|max:255',
                    'telephone'             => 'required|string|max:20',
                    'fonction'              => 'required|string|max:255',
                    'pays'                  => 'required|string|max:255',
                    'ville'                 => 'required|string|max:255',
                    'secteur_activite'      => 'required|string|max:255',
                    'sous_secteur'          => 'required|string|max:255',
                    'description_activites' => 'required|string',
                    'annee_creation'        => 'required|integer|min:1900|max:' . date('Y'),
                    'nombre_salaries'       => 'required|integer|min:1',
                    'chiffre_affaires'      => 'required|numeric|min:0|max:100',
                ], [
                    'email.required'                 => 'L\'email est obligatoire.',
                    'telephone.required'             => 'Le téléphone est obligatoire.',
                    'fonction.required'              => 'La fonction est obligatoire.',
                    'pays.required'                  => 'Le pays est obligatoire.',
                    'ville.required'                 => 'La ville est obligatoire.',
                    'secteur_activite.required'      => 'Le secteur est obligatoire.',
                    'sous_secteur.required'          => 'Le sous-secteur est obligatoire.',
                    'description_activites.required' => 'La description est obligatoire.',
                    'annee_creation.required'        => 'L\'année de création est obligatoire.',
                    'nombre_salaries.required'       => 'Le nombre de salariés est obligatoire.',
                    'chiffre_affaires.required'      => 'Le chiffre d\'affaires est obligatoire.',
                    'chiffre_affaires.max'           => 'Le chiffre d\'affaires ne peut pas dépasser 100%.',
                ]);
            }

            $this->etape = 3;

        } elseif ($this->etape == 3) {
            // ← Étape 3 : profil partenaire
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

        } elseif ($this->etape == 4) {
            // ← Étape 4 : disponibilités (optionnel)
            if ($this->getIsMultiJours() && empty($this->disponibilites)) {
                $this->addError('disponibilites', 'Veuillez sélectionner au moins un jour.');
                return;
            }

            $this->etape = 5;
        }
    }

    public function precedent(): void
    {
        if ($this->etape > 1) {
            $this->etape--;
        } else {
            $this->etape = 0;
        }
    }

    
    // CONFIRMATION FINALE
    

    public function confirmer()
    {
        $participant = Participant::findForUser(auth()->user());

        if (!$participant) {
            session()->flash('error', 'Participant non trouvé.');
            return;
        }

        // ← Vérifie si déjà inscrit
        $existe = Inscription::where('id_participant', $participant->id)
            ->where('id_evenement', $this->id_evenement)
            ->exists();

        if ($existe) {
            session()->flash('error', 'Vous êtes déjà inscrit à cet événement.');
            return redirect()->route($this->getDashboardRoute());
        }

        if ($this->estMembre) {
            // ← MODE MEMBRE : met à jour seulement les infos personnelles
            // Les infos entreprise restent inchangées
            $participant->update([
                'nom'              => $this->nom,
                'prenom'           => $this->prenom,
                'telephone'        => $this->telephone,
                'fonction'         => $this->fonction,
                'zone_geographique'=> $this->zone_geographique,
                'type_partenaire'  => $this->type_partenaire,
                'secteur_recherche'=> $this->secteur_recherche,
                'disponibilites'   => !empty($this->disponibilites)
                    ? json_encode($this->disponibilites)
                    : null,
                'id_evenement'     => $this->id_evenement,
            ]);
        } else {
            // ← MODE REPRÉSENTANT : met à jour toutes les infos
            $participant->update([
                'nom'                   => $this->nom,
                'prenom'                => $this->prenom,
                'email'                 => $this->email,
                'telephone'             => $this->telephone,
                'fonction'              => $this->fonction,
                'pays'                  => $this->pays,
                'ville'                 => $this->ville,
                'secteur_activite'      => $this->secteur_activite,
                'sous_secteur'          => $this->sous_secteur,
                'description_activites' => $this->description_activites,
                'principaux_produits'   => $this->principaux_produits,
                'annee_creation'        => $this->annee_creation,
                'nombre_salaries'       => $this->nombre_salaries,
                'chiffre_affaires'      => $this->chiffre_affaires,
                'zone_geographique'     => $this->zone_geographique,
                'type_partenaire'       => $this->type_partenaire,
                'secteur_recherche'     => $this->secteur_recherche,
                'disponibilites'        => !empty($this->disponibilites)
                    ? json_encode($this->disponibilites)
                    : null,
                'id_evenement'          => $this->id_evenement,
            ]);
        }

        // ← Détermine montant et statut paiement
        $montant = $this->evenement->montant_inscription ?? 0;
        $statut  = 'en_attente';

        if ($this->evenement->type_paiement == 'gratuit') {
            $montant = 0;
            $statut  = 'paye';
        } elseif ($this->evenement->type_paiement == 'par_entreprise'
            && $participant->id_entreprise) {
            $montant = 0;
            $statut  = 'en_attente';
        }

        // ← Crée l'inscription
        Inscription::create([
            'id_participant'    => $participant->id,
            'id_evenement'      => $this->id_evenement,
            'date_inscription'  => now()->toDateString(),
            'montant_paye'      => $montant,
            'statut_paiement'   => $statut,
            'statut_presence'   => 'absent',
            'secteur_recherche' => $this->secteur_recherche,
            'type_partenaire'   => $this->type_partenaire,
            'zone_geographique' => $this->zone_geographique,
        ]);

        if ($statut == 'paye') {
            session()->flash('success', 'Inscription confirmée ! Bienvenue à ' . $this->evenement->nom . ' !');
        } else {
            session()->flash('success', 'Préinscription envoyée ! En attente de validation.');
        }

        return redirect()->route($this->getDashboardRoute());
    }

    
    // RENDER
    

    public function render()
    {
        return view('livewire.participant.inscription-wizard', [
            'joursEvenement'    => $this->getJoursEvenement(),
            'isMultiJours'      => $this->getIsMultiJours(),
            'villesDisponibles' => $this->getVillesDisponibles(),
        ])->layout($this->getLayout(), [
            'title' => 'Inscription — ' . ($this->evenement->nom ?? '')
        ]);
    }
}