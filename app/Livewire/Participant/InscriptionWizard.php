<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Evenement;
use App\Models\Inscription;
use App\Models\Entreprise;
use App\Models\Stand;
use App\Models\User;

class InscriptionWizard extends Component
{
    // Navigation
    public int $etape = 0;

    // Événement
    public $id_evenement;
    public $evenement = null;

    // Mode
    public bool $estMembre       = false;
    public bool $estRepresentant = false;
    public $entreprise           = null;

    // Étape 2 : Infos personnelles
    public string $nom            = '';
    public string $prenom         = '';
    public string $genre          = '';
    public string $email          = '';
    public string $telephone      = '';
    public string $fonction       = '';
    public string $fonction_autre = '';
    public string $pays           = '';
    public string $ville          = '';

    // Étape 3 : Activité professionnelle
    public string $secteur_activite        = '';
    public string $secteur_activite_autre  = '';
    public string $sous_secteur            = '';
    public string $description_activites   = '';
    public string $principaux_produits     = '';
    public string $annee_creation          = '';
    public string $nombre_salaries         = '';
    public string $chiffre_affaires        = '';
    public string $objectif_participation  = '';

    // Étape 4 : Recherche de partenariat
    public string $zone_geographique       = '';
    public array  $types_partenariat       = [];
    public string $type_partenariat_autre  = '';
    public array  $profils_partenaire      = [];
    public array  $secteurs_recherche      = [];
    public string $secteur_recherche_autre = '';

    // Étape 5 : Disponibilités + CDD + Stand
    public array $disponibilites    = [];
    public $id_chef_delegation      = '';
    public $id_stand_choisi         = ''; // ← Réservation de stand (représentant)

    // Listes
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
        'Services financiers',
        'BTP',
        'Activités médicales et pharmaceutiques',
        'Autre',
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

    public array $zonesGeographiques = [
        'Locale',
        'Nationale',
        'Régionale (CEDEAO)',
        'Africaine',
        'Internationale',
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
        'Burkina Faso'   => ['Ouagadougou', 'Bobo-Dioulasso', 'Koudougou', 'Banfora', 'Ouahigouya', 'Autre'],
        'Côte d\'Ivoire' => ['Abidjan', 'Bouaké', 'Daloa', 'Yamoussoukro', 'Autre'],
        'Mali'           => ['Bamako', 'Sikasso', 'Mopti', 'Kayes', 'Autre'],
        'Sénégal'        => ['Dakar', 'Thiès', 'Kaolack', 'Saint-Louis', 'Autre'],
        'Ghana'          => ['Accra', 'Kumasi', 'Tamale', 'Autre'],
        'Togo'           => ['Lomé', 'Sokodé', 'Kara', 'Autre'],
        'Bénin'          => ['Cotonou', 'Porto-Novo', 'Parakou', 'Autre'],
        'Niger'          => ['Niamey', 'Zinder', 'Maradi', 'Autre'],
        'Guinée'         => ['Conakry', 'Nzérékoré', 'Kankan', 'Autre'],
        'Cameroun'       => ['Yaoundé', 'Douala', 'Garoua', 'Autre'],
        'Nigeria'        => ['Lagos', 'Kano', 'Abuja', 'Autre'],
        'France'         => ['Paris', 'Lyon', 'Marseille', 'Autre'],
        'Allemagne'      => ['Berlin', 'Hambourg', 'Munich', 'Autre'],
        'États-Unis'     => ['New York', 'Los Angeles', 'Chicago', 'Autre'],
        'Chine'          => ['Pékin', 'Shanghai', 'Guangzhou', 'Autre'],
        'Autre'          => ['Autre'],
    ];

    public function getVillesDisponibles(): array
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

    private function getDashboardRoute(): string
    {
        $user = auth()->user();
        if ($user->hasRole('entreprise'))  return 'entreprise.dashboard';
        if ($user->hasRole('participant')) return 'participant.dashboard';
        if ($user->hasRole('cdd'))         return 'cdd.dashboard';
        return 'dashboard';
    }

    private function getLayout(): string
    {
        $user = auth()->user();
        if ($user->hasRole('entreprise'))  return 'layouts.entreprise';
        if ($user->hasRole('participant')) return 'layouts.participant';
        if ($user->hasRole('cdd'))         return 'layouts.cdd';
        return 'layouts.participant';
    }

    public function mount($evenement): void
    {
        $this->id_evenement = $evenement;
        $this->evenement    = Evenement::with('typeEvenement')->findOrFail($evenement);

        $participant = Participant::findForUser(auth()->user());

        if ($participant) {
            $this->nom       = $participant->nom;
            $this->prenom    = $participant->prenom;
            $this->email     = $participant->email ?? '';
            $this->telephone = $participant->telephone ?? '';
            $this->genre     = $participant->genre ?? '';
            $this->pays      = $participant->pays ?? '';
            $this->ville     = $participant->ville ?? '';

            if (in_array($participant->fonction, $this->fonctions)) {
                $this->fonction = $participant->fonction ?? '';
            } else {
                $this->fonction       = 'Autre';
                $this->fonction_autre = $participant->fonction ?? '';
            }

            // Secteur activité
            if (in_array($participant->secteur_activite, $this->secteurs)) {
                $this->secteur_activite = $participant->secteur_activite ?? '';
            } else {
                $this->secteur_activite       = 'Autre';
                $this->secteur_activite_autre = $participant->secteur_activite ?? '';
            }

            $this->sous_secteur            = $participant->sous_secteur ?? '';
            $this->description_activites   = $participant->description_activites ?? '';
            $this->principaux_produits     = $participant->principaux_produits ?? '';
            $this->annee_creation          = $participant->annee_creation ?? '';
            $this->nombre_salaries         = $participant->nombre_salaries ?? '';
            $this->chiffre_affaires        = $participant->chiffre_affaires ?? '';
            $this->objectif_participation  = $participant->objectif_participation ?? '';
            $this->zone_geographique       = $participant->zone_geographique ?? '';
            $this->types_partenariat       = $participant->types_partenariat ?? [];
            $this->type_partenariat_autre  = $participant->type_partenariat_autre ?? '';
            $this->profils_partenaire      = $participant->profils_partenaire ?? [];
            $this->secteurs_recherche      = $participant->secteurs_recherche ?? [];
            $this->secteur_recherche_autre = $participant->secteur_recherche_autre ?? '';
            $this->disponibilites          = $participant->disponibilites ?? [];
            $this->id_chef_delegation      = $participant->id_chef_delegation ?? '';

            if ($participant->id_entreprise) {
                $this->estMembre       = true;
                $this->estRepresentant = ($participant->role === 'representant');
                $this->entreprise      = Entreprise::find($participant->id_entreprise);
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

    /**
     * Stands disponibles pour cet événement (uniquement pour le représentant).
     */
    public function getStandsDisponiblesEvenement()
    {
        if (!$this->estRepresentant || !$this->id_evenement) {
            return collect();
        }

        return Stand::with('evenement')
            ->where('id_evenement', $this->id_evenement)
            ->whereNull('id_entreprise')
            ->orderBy('numero_stand')
            ->get();
    }

    public function commencer(): void
    {
        $this->etape = 1;
    }

    public function suivant(): void
    {
        if ($this->etape == 1) {
            $this->etape = 2;

        } elseif ($this->etape == 2) {
            // Si "Autre" → utilise la saisie libre
            if ($this->fonction === 'Autre') {
                $this->fonction = $this->fonction_autre;
            }

            $regles = [
                'nom'       => 'required|string|max:255',
                'prenom'    => 'required|string|max:255',
                'telephone' => 'required|string|max:20',
                'fonction'  => 'required|string|max:255',
                'genre'     => 'required|in:homme,femme',
            ];

            if (!$this->estMembre) {
                $regles['pays']  = 'required|string|max:255';
                $regles['ville'] = 'required|string|max:255';
            }

            $this->validate($regles);
            $this->etape = 3;

        } elseif ($this->etape == 3) {
            // Si "Autre" → utilise la saisie libre
            $secteurFinal = $this->secteur_activite === 'Autre'
                ? $this->secteur_activite_autre
                : $this->secteur_activite;

            if (!$this->estMembre) {
                $this->validate([
                    'secteur_activite'      => 'required',
                    'description_activites' => 'required|string',
                    'annee_creation'        => 'required|integer|min:1900|max:' . date('Y'),
                    'nombre_salaries'       => 'required|integer|min:1',
                    'chiffre_affaires'      => 'required|numeric|min:0|max:100',
                    'objectif_participation' => 'nullable|string|max:200',
                ]);
            } else {
                $this->validate([
                    'objectif_participation' => 'nullable|string|max:200',
                ]);
            }

            // Sauvegarde le secteur final
            if ($this->secteur_activite === 'Autre') {
                $this->secteur_activite = $secteurFinal;
            }

            $this->etape = 4;

        } elseif ($this->etape == 4) {
            $this->validate([
                'zone_geographique' => 'required|string|max:255',
            ], [
                'zone_geographique.required' => 'La zone géographique est obligatoire.',
            ]);

            if (empty($this->types_partenariat)) {
                $this->addError('types_partenariat', 'Choisissez au moins un type de partenariat.');
                return;
            }
            if (empty($this->profils_partenaire)) {
                $this->addError('profils_partenaire', 'Choisissez au moins un profil de partenaire.');
                return;
            }
            if (empty($this->secteurs_recherche)) {
                $this->addError('secteurs_recherche', 'Choisissez au moins un secteur recherché.');
                return;
            }

            $this->etape = 5;

        } elseif ($this->etape == 5) {
            if ($this->getIsMultiJours() && empty($this->disponibilites)) {
                $this->addError('disponibilites', 'Veuillez sélectionner au moins un jour.');
                return;
            }
            $this->etape = 6;
        }
    }

    public function precedent(): void
    {
        if ($this->etape > 1) $this->etape--;
        else $this->etape = 0;
    }

    /**
     * Normalise une valeur de tableau avant sauvegarde : si une chaîne JSON
     * est reçue par erreur (au lieu d'un tableau PHP), on la décode pour
     * éviter un double encodage en base.
     */
    private function normalizeArrayField($value): ?array
    {
        if (is_array($value)) {
            return !empty($value) ? $value : null;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return !empty($decoded) ? $decoded : null;
            }
        }

        return null;
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
            return redirect()->route($this->getDashboardRoute());
        }

        $participant->update([
            'nom'                    => $this->nom,
            'prenom'                 => $this->prenom,
            'genre'                  => $this->genre,
            'telephone'              => $this->telephone,
            'fonction'               => $this->fonction,
            'pays'                   => $this->pays ?: $participant->pays,
            'ville'                  => $this->ville ?: $participant->ville,
            'secteur_activite'       => $this->secteur_activite ?: $participant->secteur_activite,
            'sous_secteur'           => $this->sous_secteur ?: null,
            'description_activites'  => $this->description_activites ?: null,
            'principaux_produits'    => $this->principaux_produits ?: null,
            'annee_creation'         => $this->annee_creation ?: null,
            'nombre_salaries'        => $this->nombre_salaries ?: null,
            'chiffre_affaires'       => $this->chiffre_affaires ?: null,
            'objectif_participation' => $this->objectif_participation ?: null,
            'zone_geographique'      => $this->zone_geographique,
            'types_partenariat'      => $this->normalizeArrayField($this->types_partenariat),
            'type_partenariat_autre' => in_array('Autre', $this->types_partenariat)
                ? $this->type_partenariat_autre : null,
            'profils_partenaire'     => $this->normalizeArrayField($this->profils_partenaire),
            'secteurs_recherche'     => $this->normalizeArrayField($this->secteurs_recherche),
            'secteur_recherche_autre' => in_array('Autre', $this->secteurs_recherche)
                ? $this->secteur_recherche_autre : null,
            'disponibilites'         => $this->normalizeArrayField($this->disponibilites),
            'id_chef_delegation'     => $this->id_chef_delegation ?: null,
            'id_evenement'           => $this->id_evenement,
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
            'id_participant'   => $participant->id,
            'id_evenement'     => $this->id_evenement,
            'date_inscription' => now()->toDateString(),
            'montant_paye'     => $montant,
            'statut_paiement'  => $statut,
            'statut_presence'  => 'absent',
        ]);

        // ← Réservation d'un stand (uniquement représentant)
        if ($this->estRepresentant && $this->id_stand_choisi) {
            $stand = Stand::find($this->id_stand_choisi);
            if ($stand && !$stand->id_entreprise) {
                $stand->update([
                    'id_entreprise'         => $participant->id_entreprise,
                    'statut_reservation'    => 'en_attente',
                    'statut_paiement_stand' => null,
                ]);
            }
        }

        // Notification au CDD si choisi
        if ($this->id_chef_delegation) {
            $cddParticipant = Participant::where('email',
                User::find($this->id_chef_delegation)?->email
            )->first();

            if ($cddParticipant) {
                \App\Models\Notification::create([
                    'id_participant' => $cddParticipant->id,
                    'contenu'        => $participant->nom . ' ' . $participant->prenom
                        . ' vous a choisi comme Chef de Délégation. Veuillez valider sa préinscription.',
                    'date_envoie'    => now()->toDateString(),
                    'type'           => 'systeme',
                ]);
            }
        }

        if ($statut == 'paye') {
            session()->flash('success', 'Inscription confirmée ! Bienvenue à ' . $this->evenement->nom . ' !');
        } else {
            session()->flash('success', 'Préinscription envoyée ! En attente de validation.');
        }

        return redirect()->route($this->getDashboardRoute());
    }

    public function render()
    {
        return view('livewire.participant.inscription-wizard', [
            'joursEvenement'             => $this->getJoursEvenement(),
            'isMultiJours'               => $this->getIsMultiJours(),
            'villesDisponibles'          => $this->getVillesDisponibles(),
            'standsDisponiblesEvenement' => $this->getStandsDisponiblesEvenement(),
            'chefsDelegation'            => User::whereHas('roles', fn($q) =>
                $q->where('name', 'cdd')
            )->orderBy('name')->get(),
        ])->layout($this->getLayout(), [
            'title' => 'Inscription — ' . ($this->evenement->nom ?? '')
        ]);
    }
}