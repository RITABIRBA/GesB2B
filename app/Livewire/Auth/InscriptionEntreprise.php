<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\Evenement;
use App\Models\Inscription;
use App\Mail\PreinscriptionRecue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class InscriptionEntreprise extends Component
{
    public int $etape = 1;

    public string $nom              = '';
    public string $ifu              = '';
    public string $secteur_activite = '';
    public string $secteur_autre    = '';
    public string $sous_secteur     = '';
    public string $pays             = 'Burkina Faso';
    public string $ville            = '';
    public string $telephone        = '';
    public string $email            = '';

    public string $rep_nom             = '';
    public string $rep_prenom          = '';
    public string $rep_genre           = '';
    public string $rep_fonction        = '';
    public string $rep_fonction_autre  = '';
    public string $rep_email           = '';
    public string $rep_telephone       = '';
    public string $rep_date_naissance  = '';

    public array  $objectifs_participation = [];
    public string $objectif_autre          = '';

    // ✅ Profil B2B — étape 3
    public string $zone_geographique       = '';
    public array  $types_partenariat       = [];
    public string $type_partenariat_autre  = '';
    public array  $profils_partenaire      = [];
    public array  $secteurs_recherche      = [];
    public string $secteur_recherche_autre = '';
    public bool   $profilB2BRempli         = false;

    // ✅ Disponibilités (jours d'absence) — étape 3, cohérent avec le reste
    // de l'application (CompleterProfilB2B, InscriptionWizard participant,
    // InscriptionParticipant).
    public array $jours_absence = [];

    public $id_evenement = '';
    public bool $confirme = false;

    public array $secteurs = [
        'Agriculture et agro-alimentaire', 'Environnement',
        'Industrie textile', 'Biens de consommation', 'Energie',
        'Formation', 'Tourisme', 'TIC', 'Sous-traitance', 'Artisanat',
        'Distribution', 'Prestation', 'Industrie manufacturière',
        'Enseignement', 'Services aux entreprises', 'BTP',
        'Activités médicales et pharmaceutiques', 'Autre',
    ];

    public array $fonctions = [
        'Directeur Général', 'Directeur Commercial', 'PDG', 'Gérant',
        'Responsable Export', 'Responsable Partenariats',
        'Chargé de Développement', 'Représentant', 'Autre',
    ];

    public array $objectifsOptions = [
        'Trouver des fournisseurs',
        'Trouver des clients / acheteurs',
        'Rechercher des partenaires commerciaux',
        'Rechercher des investisseurs',
        'Explorer de nouveaux marchés',
        'Développer mon réseau professionnel',
        'Présenter mes produits / services',
        'Autre',
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

    public array $typesPartenariatOptions = [
        'Alliance commerciale', 'Alliance financière', 'Alliance industrielle', 'Autre',
    ];

    public array $profilsPartenariatOptions = [
        'Consultant', 'Distributeur', 'Exportateur', 'Fabricant / Producteur',
        'Investisseur', 'Importateur', 'Prestataire de service', 'Sous-traitant',
        'Innovation', 'R&D',
    ];

    public array $pays_liste = [
        'Burkina Faso', 'Côte d\'Ivoire', 'Mali', 'Sénégal',
        'Ghana', 'Togo', 'Bénin', 'Niger', 'Guinée', 'Cameroun',
        'Nigeria', 'France', 'Allemagne', 'États-Unis', 'Chine', 'Autre',
    ];

    public array $villes_par_pays = [
        'Burkina Faso'   => ['Ouagadougou', 'Bobo-Dioulasso', 'Koudougou', 'Banfora', 'Autre'],
        'Côte d\'Ivoire' => ['Abidjan', 'Bouaké', 'Yamoussoukro', 'Autre'],
        'Mali'           => ['Bamako', 'Sikasso', 'Autre'],
        'Sénégal'        => ['Dakar', 'Thiès', 'Autre'],
        'Autre'          => ['Autre'],
    ];

    public function getVillesDisponibles(): array
    {
        return $this->villes_par_pays[$this->pays] ?? ['Autre'];
    }

    public function getEvenementEstB2BProperty(): bool
    {
        if (!$this->id_evenement) return false;
        $ev = Evenement::find($this->id_evenement);
        return $ev && ($ev->type_evenement ?? 'avec_b2b') === 'avec_b2b';
    }

    public function getNbEtapesProperty(): int
    {
        return $this->evenementEstB2B ? 4 : 3;
    }

    // ✅ Liste des jours de l'événement sélectionné, pour les cases
    // "jours d'absence" de l'étape 3.
    public function getJoursEvenementProperty(): array
    {
        if (!$this->id_evenement) return [];
        $evenement = Evenement::find($this->id_evenement);
        if (!$evenement || !$evenement->date_debut) return [];

        $debut = \Carbon\Carbon::parse($evenement->date_debut);
        $fin   = \Carbon\Carbon::parse($evenement->date_fin ?? $evenement->date_debut);
        $jours = [];
        while ($debut->lte($fin)) {
            $jours[] = $debut->format('Y-m-d');
            $debut->addDay();
        }
        return $jours;
    }

    public function updatedPays(): void { $this->ville = ''; }

    public function updatedObjectifsParticipation(): void
    {
        if (count($this->objectifs_participation) > 3) {
            $this->objectifs_participation = array_slice($this->objectifs_participation, 0, 3);
        }
        if (!in_array('Autre', $this->objectifs_participation)) {
            $this->objectif_autre = '';
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

    public function passerEtapeB2B(): void
    {
        $this->profilB2BRempli = false;
        $this->etape = 4;
    }

    public function suivant(): void
    {
        if ($this->etape === 1) {
            $secteurFinal = $this->secteur_activite === 'Autre'
                ? $this->secteur_autre
                : $this->secteur_activite;

            $this->validate([
                'nom'              => 'required|string|max:255',
                'ifu'              => [
                    'required', 'string', 'regex:/^\d{8}[A-Za-z]$/',
                    Rule::unique('entreprises', 'ifu'),
                ],
                'secteur_activite' => 'required',
                'sous_secteur'     => 'required|string|max:255',
                'pays'             => 'required|string',
                'ville'            => 'required|string',
                'telephone'        => 'required|string|max:20',
            ], [
                'ifu.regex'  => 'Format IFU invalide. Exemple : 12345678A',
                'ifu.unique' => 'Ce numéro IFU est déjà enregistré.',
            ]);

            if ($this->secteur_activite === 'Autre') $this->secteur_activite = $secteurFinal;
            $this->etape = 2;

        } elseif ($this->etape === 2) {
            $regles = [
                'rep_nom'            => 'required|string|max:255',
                'rep_prenom'         => 'required|string|max:255',
                'rep_genre'          => 'required|in:homme,femme',
                'rep_telephone'      => 'required|string|max:20',
                'rep_email'          => 'nullable|email|max:255',
                'rep_date_naissance' => 'nullable|date|before:today',
            ];

            if (in_array('Autre', $this->objectifs_participation)) {
                $regles['objectif_autre'] = 'required|string|max:255';
            }

            $this->validate($regles, [
                'rep_nom.required'        => 'Le nom du représentant est obligatoire.',
                'rep_prenom.required'     => 'Le prénom du représentant est obligatoire.',
                'rep_genre.required'      => 'Le genre est obligatoire.',
                'rep_telephone.required'  => 'Le téléphone est obligatoire.',
                'objectif_autre.required' => 'Veuillez préciser votre objectif.',
            ]);

            if ($this->rep_fonction === 'Autre') {
                $this->rep_fonction = $this->rep_fonction_autre ?: 'Représentant';
            }

            // ✅ Si B2B → étape 3, sinon → étape 3 (confirmation)
            $this->etape = $this->evenementEstB2B ? 3 : 3;

        } elseif ($this->etape === 3 && $this->evenementEstB2B) {
            if (!empty($this->zone_geographique) || !empty($this->types_partenariat) || !empty($this->secteurs_recherche)) {
                if (empty($this->zone_geographique)) {
                    $this->addError('zone_geographique', 'La zone géographique est obligatoire si vous remplissez le profil B2B.');
                    return;
                }
            }

            // ✅ CORRECTION : si "Autre" est re-sélectionné pour le secteur
            // d'activité à cette étape, on exige et récupère la précision.
            if ($this->secteur_activite === 'Autre' && trim($this->secteur_autre) === '') {
                $this->addError('secteur_autre', 'Veuillez préciser votre secteur d\'activité.');
                return;
            }
            if ($this->secteur_activite === 'Autre') {
                $this->secteur_activite = $this->secteur_autre;
            }

            $this->profilB2BRempli = !empty($this->zone_geographique);
            $this->etape = 4;
        }
    }

    public function precedent(): void
    {
        if ($this->etape > 1) $this->etape--;
    }

    public function soumettre(): void
    {
        $entreprise = Entreprise::create([
            'nom'                => $this->nom,
            'ifu'                => strtoupper($this->ifu),
            'secteur_activite'   => $this->secteur_activite,
            'sous_secteur'       => $this->sous_secteur,
            'pays'               => $this->pays,
            'ville'              => $this->ville,
            'contact'            => $this->telephone,
            'statut_validation'  => 'en_attente',
            'nom_responsable'    => $this->rep_nom,
            'prenom_responsable' => $this->rep_prenom,
            'email_responsable'  => $this->rep_email ?: null,
        ]);

        $nom_sans_accent = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $this->rep_nom);
        $code_acces = strtoupper(substr($nom_sans_accent, 0, 3)) . rand(1000, 9999);

        $objectifsFinal = $this->objectifs_participation;
        if (($idx = array_search('Autre', $objectifsFinal)) !== false && $this->objectif_autre) {
            $objectifsFinal[$idx] = $this->objectif_autre;
        }

        $dataRep = [
            'id_entreprise'          => $entreprise->id,
            'nom'                    => $this->rep_nom,
            'prenom'                 => $this->rep_prenom,
            'genre'                  => $this->rep_genre,
            'email'                  => $this->rep_email ?: null,
            'telephone'              => $this->rep_telephone,
            'fonction'               => $this->rep_fonction ?: 'Représentant',
            'date_naissance'         => $this->rep_date_naissance ?: null,
            'pays'                   => $this->pays,
            'ville'                  => $this->ville,
            'code_acces'             => $code_acces,
            'role'                   => 'representant',
            'statut_historique'      => 'actif',
            'statut_preinscription'  => 'en_attente',
            'participation_rdv'      => true,
            'objectif_participation' => !empty($objectifsFinal) ? json_encode($objectifsFinal) : null,
        ];

        // ✅ Profil B2B si rempli
        if ($this->profilB2BRempli && $this->evenementEstB2B) {
            $dataRep['secteur_activite']        = $this->secteur_activite ?: null;
            $dataRep['sous_secteur']            = $this->sous_secteur ?: null;
            $dataRep['zone_geographique']       = $this->zone_geographique ?: null;
            $dataRep['types_partenariat']       = !empty($this->types_partenariat) ? json_encode($this->types_partenariat) : null;
            $dataRep['type_partenariat_autre']  = in_array('Autre', $this->types_partenariat) ? $this->type_partenariat_autre : null;
            $dataRep['profils_partenaire']      = !empty($this->profils_partenaire) ? json_encode($this->profils_partenaire) : null;
            $dataRep['secteurs_recherche']      = !empty($this->secteurs_recherche) ? json_encode($this->secteurs_recherche) : null;
            $dataRep['secteur_recherche_autre'] = in_array('Autre', $this->secteurs_recherche) ? $this->secteur_recherche_autre : null;

            // ✅ Conversion jours d'ABSENCE cochés → jours réellement
            // DISPONIBLES avant sauvegarde, pour garder `disponibilites`
            // cohérent avec le reste de l'application.
            $joursEvt = $this->joursEvenement;
            $dataRep['disponibilites'] = !empty($joursEvt)
                ? json_encode(array_values(array_diff($joursEvt, $this->jours_absence)))
                : null;
        }

        $representant = Participant::create($dataRep);

        if ($this->id_evenement) {
            $representant->update(['id_evenement' => $this->id_evenement]);
            $evenement = Evenement::find($this->id_evenement);
            if ($evenement && $evenement->inscriptionsOuvertes()) {
                Inscription::create([
                    'id_participant'   => $representant->id,
                    'id_evenement'     => $this->id_evenement,
                    'date_inscription' => now()->toDateString(),
                    'montant_paye'     => 0,
                    'statut_paiement'  => 'en_attente',
                    'statut_presence'  => 'absent',
                ]);
            }
        }

        $emailDestinataire = $this->rep_email ?: $this->email;
        if ($emailDestinataire) {
            try {
                $nomEvenement = 'Business Forum';
                if ($this->id_evenement) {
                    $ev = Evenement::find($this->id_evenement);
                    if ($ev) $nomEvenement = $ev->nom;
                }
                Mail::to($emailDestinataire)->send(new PreinscriptionRecue($representant, $nomEvenement));
            } catch (\Exception $e) {}
        }

        $this->confirme = true;
    }

    public function render()
    {
        return view('livewire.auth.inscription-entreprise', [
            'evenements'        => Evenement::where('date_fin', '>=', now()->toDateString())
                ->where(function ($q) {
                    $q->whereNull('date_cloture_inscriptions')
                      ->orWhere('date_cloture_inscriptions', '>=', now()->toDateString());
                })
                ->orderBy('date_debut')
                ->get(),
            'villesDisponibles' => $this->getVillesDisponibles(),
            'objectifsOptions'  => $this->objectifsOptions,
            'evenementEstB2B'   => $this->evenementEstB2B,
            'nbEtapes'          => $this->nbEtapes,
            'joursEvenement'    => $this->joursEvenement,
        ])->layout('layouts.guest');
    }
}