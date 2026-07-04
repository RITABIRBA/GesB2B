<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\Evenement;
use App\Models\Inscription;
use App\Models\User;
use App\Mail\PreinscriptionRecue;
use App\Mail\NouvellePreinscriptionCdd;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class InscriptionParticipant extends Component
{
    public int $etape = 1;

    public string $type_inscrit      = 'particulier';
    public string $ifu               = '';
    public $entreprise_trouvee       = null;
    public string $erreur_ifu        = '';

    public string $nom               = '';
    public string $prenom            = '';
    public string $genre             = '';
    public string $email             = '';
    public string $telephone         = '';
    public string $fonction          = '';
    public string $fonction_autre    = '';
    public string $date_naissance    = '';
    public string $filiere           = '';
    public string $universite        = '';
    public string $pays              = 'Burkina Faso';
    public string $ville             = '';
    public string $ville_autre       = '';

    // Objectif de participation
    public array  $objectifs_participation = [];
    public string $objectif_autre          = '';

    // ✅ Profil B2B — étape 3
    public string $zone_geographique       = '';
    public array  $types_partenariat       = [];
    public string $type_partenariat_autre  = '';
    public array  $profils_partenaire      = [];
    public array  $secteurs_recherche      = [];
    public string $secteur_recherche_autre = '';
    public string $secteur_activite        = '';
    public string $sous_secteur            = '';
    public bool   $profilB2BRempli         = false; // true si rempli, false si passé

    public $id_evenement = '';
    public $id_cdd       = '';
    public bool $confirme = false;

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

    public array $secteurs = [
        'Agriculture et agro-alimentaire', 'Environnement', 'Industrie textile',
        'Biens de consommation', 'Energie', 'Formation', 'Tourisme', 'TIC',
        'Sous-traitance', 'Artisanat', 'Distribution', 'Prestation',
        'Industrie manufacturière', 'Enseignement', 'Services aux entreprises',
        'BTP', 'Activités médicales et pharmaceutiques', 'Autre',
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

    // ✅ Vérifie si l'événement sélectionné est avec B2B
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

    public function updatedPays(): void
    {
        $this->ville       = '';
        $this->ville_autre = '';
    }

    public function updatedVille(): void
    {
        if ($this->ville !== 'Autre') $this->ville_autre = '';
    }

    public function updatedIfu(): void
    {
        $this->entreprise_trouvee = null;
        $this->erreur_ifu         = '';

        if (strlen(trim($this->ifu)) >= 8) {
            $entreprise = Entreprise::where('ifu', strtoupper(trim($this->ifu)))->first();
            if ($entreprise) {
                $this->entreprise_trouvee = $entreprise;
            } else {
                $this->erreur_ifu = 'Aucune entreprise trouvée avec cet IFU.';
            }
        }
    }

    public function updatedObjectifsParticipation(): void
    {
        if (count($this->objectifs_participation) > 3) {
            $this->objectifs_participation = array_slice($this->objectifs_participation, 0, 3);
        }
        if (!in_array('Autre', $this->objectifs_participation)) {
            $this->objectif_autre = '';
        }
    }

    public function getEstEtudiantProperty(): bool
    {
        $fonctionActive = $this->fonction === 'Autre'
            ? mb_strtolower(trim($this->fonction_autre))
            : mb_strtolower(trim($this->fonction));
        return in_array($fonctionActive, ['étudiant', 'etudiant', 'étudiante', 'etudiante']);
    }

    // ✅ Toggles profil B2B
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

    // ✅ Passer l'étape B2B
    public function passerEtapeB2B(): void
    {
        $this->profilB2BRempli = false;
        $this->etape = $this->evenementEstB2B ? 4 : 3;
    }

    public function suivant(): void
    {
        if ($this->etape === 1) {
            if ($this->type_inscrit === 'membre_entreprise' && !$this->entreprise_trouvee) {
                $this->erreur_ifu = 'Veuillez saisir un IFU valide.';
                return;
            }
            $this->etape = 2;

        } elseif ($this->etape === 2) {
            $fonctionFinal = $this->fonction === 'Autre' ? $this->fonction_autre : $this->fonction;

            $regles = [
                'nom'            => 'required|string|max:255',
                'prenom'         => 'required|string|max:255',
                'genre'          => 'required|in:homme,femme',
                'telephone'      => 'required|string|max:20',
                'email'          => 'nullable|email|max:255',
                'date_naissance' => 'nullable|date|before:today',
                'pays'           => 'required|string',
            ];

            if ($this->ville === 'Autre') {
                $regles['ville_autre'] = 'required|string|max:255';
            } else {
                $regles['ville'] = 'required|string';
            }

            if ($this->estEtudiant) {
                $regles['filiere']    = 'required|string|max:255';
                $regles['universite'] = 'required|string|max:255';
            }

            if (in_array('Autre', $this->objectifs_participation)) {
                $regles['objectif_autre'] = 'required|string|max:255';
            }

            $this->validate($regles, [
                'nom.required'            => 'Le nom est obligatoire.',
                'prenom.required'         => 'Le prénom est obligatoire.',
                'genre.required'          => 'Le genre est obligatoire.',
                'telephone.required'      => 'Le téléphone est obligatoire.',
                'pays.required'           => 'Le pays est obligatoire.',
                'ville.required'          => 'La ville est obligatoire.',
                'ville_autre.required'    => 'Veuillez saisir votre ville.',
                'filiere.required'        => 'La filière est obligatoire pour un étudiant.',
                'universite.required'     => "L'université est obligatoire pour un étudiant.",
                'objectif_autre.required' => 'Veuillez préciser votre objectif.',
            ]);

            if ($this->fonction === 'Autre') $this->fonction = $fonctionFinal;

            // ✅ Si événement avec B2B → étape 3 = Profil B2B, sinon → étape 3 = Confirmation
            $this->etape = $this->evenementEstB2B ? 3 : 3;

        } elseif ($this->etape === 3 && $this->evenementEstB2B) {
            // ✅ Validation du profil B2B (si rempli)
            if (!empty($this->zone_geographique) || !empty($this->types_partenariat) || !empty($this->secteurs_recherche)) {
                if (empty($this->zone_geographique)) {
                    $this->addError('zone_geographique', 'La zone géographique est obligatoire si vous remplissez le profil B2B.');
                    return;
                }
            }
            if (!in_array('Autre', $this->types_partenariat)) {
                $this->type_partenariat_autre = '';
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
        $nom_sans_accent = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $this->nom);
        $code_acces = strtoupper(substr($nom_sans_accent, 0, 3)) . rand(1000, 9999);

        $villeFinal = $this->ville === 'Autre'
            ? (trim($this->ville_autre) ?: 'Autre')
            : $this->ville;

        $nomEvenement = 'Business Forum';
        if ($this->id_evenement) {
            $evenement = Evenement::find($this->id_evenement);
            if ($evenement) $nomEvenement = $evenement->nom;
        }

        $objectifsFinal = $this->objectifs_participation;
        if (($idx = array_search('Autre', $objectifsFinal)) !== false && $this->objectif_autre) {
            $objectifsFinal[$idx] = $this->objectif_autre;
        }

        $data = [
            'nom'                    => $this->nom,
            'prenom'                 => $this->prenom,
            'genre'                  => $this->genre,
            'email'                  => $this->email ?: null,
            'telephone'              => $this->telephone,
            'fonction'               => $this->fonction,
            'pays'                   => $this->pays,
            'ville'                  => $villeFinal,
            'date_naissance'         => $this->date_naissance ?: null,
            'filiere'                => $this->estEtudiant ? ($this->filiere ?: null) : null,
            'universite'             => $this->estEtudiant ? ($this->universite ?: null) : null,
            'code_acces'             => $code_acces,
            'role'                   => 'participant',
            'statut_historique'      => 'actif',
            'statut_preinscription'  => 'en_attente',
            'participation_rdv'      => true,
            'id_cdd'                 => $this->id_cdd ?: null,
            'objectif_participation' => !empty($objectifsFinal) ? json_encode($objectifsFinal) : null,
        ];

        // ✅ Profil B2B si rempli
        if ($this->profilB2BRempli && $this->evenementEstB2B) {
            $data['secteur_activite']        = $this->secteur_activite ?: null;
            $data['sous_secteur']            = $this->sous_secteur ?: null;
            $data['zone_geographique']       = $this->zone_geographique ?: null;
            $data['types_partenariat']       = !empty($this->types_partenariat) ? json_encode($this->types_partenariat) : null;
            $data['type_partenariat_autre']  = in_array('Autre', $this->types_partenariat) ? $this->type_partenariat_autre : null;
            $data['profils_partenaire']      = !empty($this->profils_partenaire) ? json_encode($this->profils_partenaire) : null;
            $data['secteurs_recherche']      = !empty($this->secteurs_recherche) ? json_encode($this->secteurs_recherche) : null;
            $data['secteur_recherche_autre'] = in_array('Autre', $this->secteurs_recherche) ? $this->secteur_recherche_autre : null;
        }

        if ($this->type_inscrit === 'membre_entreprise' && $this->entreprise_trouvee) {
            $data['id_entreprise'] = $this->entreprise_trouvee->id;
            $data['role']          = 'representant';
        }

        if ($this->id_evenement) {
            $data['id_evenement'] = $this->id_evenement;
        }

        $participant = Participant::create($data);

        if ($this->id_evenement) {
            $evenement = Evenement::find($this->id_evenement);
            if ($evenement && $evenement->inscriptionsOuvertes()) {
                $montant        = $evenement->montant_inscription ?? 0;
                $statutPaiement = 'en_attente';

                if ($evenement->type_paiement == 'gratuit') {
                    $montant        = 0;
                    $statutPaiement = 'paye';
                } elseif ($evenement->type_paiement == 'par_entreprise' && $participant->id_entreprise) {
                    $montant        = 0;
                    $statutPaiement = 'paye';
                }

                Inscription::create([
                    'id_participant'   => $participant->id,
                    'id_evenement'     => $this->id_evenement,
                    'date_inscription' => now()->toDateString(),
                    'montant_paye'     => $montant,
                    'statut_paiement'  => $statutPaiement,
                    'statut_presence'  => 'absent',
                ]);
            }
        }

        if ($participant->email) {
            try {
                Mail::to($participant->email)->send(new PreinscriptionRecue($participant, $nomEvenement));
            } catch (\Exception $e) {
                Log::error('Email participant échoué', ['erreur' => $e->getMessage()]);
            }
        }

        if ($this->id_cdd) {
            $cdd = User::find($this->id_cdd);
            if ($cdd && $cdd->email) {
                try {
                    Mail::to($cdd->email)->send(new NouvellePreinscriptionCdd($participant, $cdd, $nomEvenement));
                } catch (\Exception $e) {
                    Log::error('Email CDD échoué', ['erreur' => $e->getMessage()]);
                }
            }
        }

        $this->confirme = true;
    }

    public function render()
    {
        return view('livewire.auth.inscription-participant', [
            'evenements'        => Evenement::where('date_fin', '>=', now()->toDateString())
                ->where(function ($q) {
                    $q->whereNull('date_cloture_inscriptions')
                      ->orWhere('date_cloture_inscriptions', '>=', now()->toDateString());
                })
                ->orderBy('date_debut')
                ->get(),
            'villesDisponibles' => $this->getVillesDisponibles(),
            'estEtudiant'       => $this->estEtudiant,
            'cdds'              => User::role('cdd')->orderBy('name')->get(),
            'objectifsOptions'  => $this->objectifsOptions,
            'evenementEstB2B'   => $this->evenementEstB2B,
            'nbEtapes'          => $this->nbEtapes,
        ])->layout('layouts.guest');
    }
}