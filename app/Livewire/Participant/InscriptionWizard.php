<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Evenement;
use App\Models\Inscription;

class InscriptionWizard extends Component
{
    public $etape = 0;

    public $id_evenement;
    public $evenement = null;

    // ← Étape 2 — Infos personnelles complètes
    public $nom                  = '';
    public $prenom               = '';
    public $email                = '';
    public $telephone            = '';
    public $fonction             = '';
    public $fonction_autre       = ''; // ← nouvelle variable
    public $pays                 = '';
    public $ville                = '';
    public $secteur_activite     = '';
    public $sous_secteur         = '';
    public $description_activites = '';
    public $principaux_produits  = '';
    public $annee_creation       = '';
    public $nombre_salaries      = '';
    public $chiffre_affaires     = '';
    public $disponibilites       = [];

    // ← Étape 3 — Profil partenaire
    public $secteur_recherche = '';
    public $zone_geographique = '';
    public $type_partenaire   = '';

    public $secteurs = [
        'Agriculture', 'Industrie', 'Commerce', 'Services',
        'Technologie', 'Transport', 'Construction', 'Tourisme',
        'Santé', 'Education', 'Finance', 'Energie', 'Mines',
        'Artisanat', 'Autre',
    ];

    public $zones = [
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

    public $types_partenaires = [
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

    public $fonctions = [
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

    public $pays_liste = [
        'Burkina Faso', 'Côte d\'Ivoire', 'Mali', 'Sénégal',
        'Ghana', 'Togo', 'Bénin', 'Niger', 'Guinée', 'Cameroun',
        'Nigeria', 'France', 'Allemagne', 'États-Unis', 'Chine', 'Autre',
    ];

    public $villes_par_pays = [
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

    public function getVillesDisponibles(): array
    {
        return $this->villes_par_pays[$this->pays] ?? ['Autre'];
    }

    public function updatedPays()
    {
        $this->ville = '';
    }

    public function mount($evenement)
    {
        $this->id_evenement = $evenement;
        $this->evenement    = Evenement::with('typeEvenement')->findOrFail($evenement);

        $participant = Participant::findForUser(auth()->user());
        if ($participant) {
            $this->nom                   = $participant->nom;
            $this->prenom                = $participant->prenom;
            $this->email                 = $participant->email ?? '';
            $this->telephone             = $participant->telephone ?? '';
            $this->pays                  = $participant->pays ?? '';
            $this->ville                 = $participant->ville ?? '';
            $this->secteur_activite      = $participant->secteur_activite ?? '';
            $this->sous_secteur          = $participant->sous_secteur ?? '';
            $this->description_activites = $participant->description_activites ?? '';
            $this->principaux_produits   = $participant->principaux_produits ?? '';
            $this->annee_creation        = $participant->annee_creation ?? '';
            $this->nombre_salaries       = $participant->nombre_salaries ?? '';
            $this->chiffre_affaires      = $participant->chiffre_affaires ?? '';
            $this->zone_geographique     = $participant->zone_geographique ?? '';
            $this->type_partenaire       = $participant->type_partenaire ?? '';

            // ← Gestion fonction
            if (in_array($participant->fonction, $this->fonctions)) {
                $this->fonction = $participant->fonction ?? '';
            } else {
                $this->fonction       = 'Autre';
                $this->fonction_autre = $participant->fonction ?? '';
            }
        }
    }

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

    public function commencer()
    {
        $this->etape = 1;
    }

    public function suivant()
    {
        if ($this->etape == 1) {
            $this->etape = 2;

        } elseif ($this->etape == 2) {

            // ← Si Autre, utilise la saisie libre
            if ($this->fonction == 'Autre') {
                $this->fonction = $this->fonction_autre;
            }

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

            if ($this->getIsMultiJours() && empty($this->disponibilites)) {
                $this->addError('disponibilites', 'Veuillez sélectionner au moins un jour.');
                return;
            }

            $this->etape = 3;

        } elseif ($this->etape == 3) {
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
        }
    }

    public function precedent()
    {
        if ($this->etape > 1) {
            $this->etape--;
        } else {
            $this->etape = 0;
        }
    }

    public function confirmer()
    {
        $participant = Participant::findForUser(auth()->user());

        if (!$participant) {
            session()->flash('error', 'Participant non trouvé.');
            return;
        }

        $existe = Inscription::where('id_participant', $participant->id)
            ->where('id_evenement', $this->id_evenement)
            ->exists();

        if ($existe) {
            session()->flash('error', 'Vous êtes déjà inscrit à cet événement.');
            return redirect()->route('participant.dashboard');
        }

        $participant->update([
            'nom'                  => $this->nom,
            'prenom'               => $this->prenom,
            'email'                => $this->email,
            'telephone'            => $this->telephone,
            'fonction'             => $this->fonction,
            'pays'                 => $this->pays,
            'ville'                => $this->ville,
            'secteur_activite'     => $this->secteur_activite,
            'sous_secteur'         => $this->sous_secteur,
            'description_activites' => $this->description_activites,
            'principaux_produits'  => $this->principaux_produits,
            'annee_creation'       => $this->annee_creation,
            'nombre_salaries'      => $this->nombre_salaries,
            'chiffre_affaires'     => $this->chiffre_affaires,
            'zone_geographique'    => $this->zone_geographique,
            'type_partenaire'      => $this->type_partenaire,
            'disponibilites'       => !empty($this->disponibilites)
                ? json_encode($this->disponibilites)
                : null,
            'id_evenement'         => $this->id_evenement,
        ]);

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

        return redirect()->route('participant.dashboard');
    }

    public function render()
    {
        return view('livewire.participant.inscription-wizard', [
            'joursEvenement'    => $this->getJoursEvenement(),
            'isMultiJours'      => $this->getIsMultiJours(),
            'villesDisponibles' => $this->getVillesDisponibles(),
        ])->layout('layouts.participant', ['title' => 'Inscription — ' . ($this->evenement->nom ?? '')]);
    }
}