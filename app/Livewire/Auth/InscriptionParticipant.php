<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\Evenement;
use App\Models\Inscription;

class InscriptionParticipant extends Component
{
    public int $etape = 1;

    public string $type_inscrit = 'particulier';

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

    public $id_evenement = '';
    public bool $confirme = false;

    public array $fonctions = [
        'Directeur Général', 'Directeur Commercial', 'PDG', 'Gérant',
        'Responsable Export', 'Responsable Partenariats',
        'Chargé de Développement', 'Représentant', 'Étudiant', 'Autre',
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

    public function getVillesDisponibles(): array
    {
        return $this->villes_par_pays[$this->pays] ?? ['Autre'];
    }

    public function updatedPays(): void
    {
        $this->ville       = '';
        $this->ville_autre = '';
    }

    public function updatedVille(): void
    {
        if ($this->ville !== 'Autre') {
            $this->ville_autre = '';
        }
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

    /**
     * ✅ mb_strtolower() gère correctement les accents
     * (É, è, etc.) contrairement à strtolower().
     */
    public function getEstEtudiantProperty(): bool
    {
        $fonctionActive = $this->fonction === 'Autre'
            ? mb_strtolower(trim($this->fonction_autre))
            : mb_strtolower(trim($this->fonction));

        return in_array($fonctionActive, ['étudiant', 'etudiant', 'étudiante', 'etudiante']);
    }

    public function getVilleFinalProperty(): string
    {
        if ($this->ville === 'Autre') {
            return trim($this->ville_autre) ?: 'Autre';
        }
        return $this->ville;
    }

    public function suivant(): void
    {
        if ($this->etape === 1) {
            if ($this->type_inscrit === 'membre_entreprise') {
                if (!$this->entreprise_trouvee) {
                    $this->erreur_ifu = 'Veuillez saisir un IFU valide.';
                    return;
                }
            }
            $this->etape = 2;

        } elseif ($this->etape === 2) {
            $fonctionFinal = $this->fonction === 'Autre'
                ? $this->fonction_autre
                : $this->fonction;

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

            $this->validate($regles, [
                'nom.required'        => 'Le nom est obligatoire.',
                'prenom.required'     => 'Le prénom est obligatoire.',
                'genre.required'      => 'Le genre est obligatoire.',
                'telephone.required'  => 'Le téléphone est obligatoire.',
                'pays.required'       => 'Le pays est obligatoire.',
                'ville.required'      => 'La ville est obligatoire.',
                'ville_autre.required'=> 'Veuillez saisir votre ville.',
                'filiere.required'    => 'La filière est obligatoire pour un étudiant.',
                'universite.required' => "L'université est obligatoire pour un étudiant.",
            ]);

            if ($this->fonction === 'Autre') {
                $this->fonction = $fonctionFinal;
            }

            $this->etape = 3;
        }
    }

    public function precedent(): void
    {
        if ($this->etape > 1) $this->etape--;
    }

    public function soumettre(): void
    {
        $code_acces = strtoupper(substr($this->nom, 0, 3) . rand(1000, 9999));

        $villeFinal = $this->ville === 'Autre'
            ? (trim($this->ville_autre) ?: 'Autre')
            : $this->ville;

        $data = [
            'nom'                   => $this->nom,
            'prenom'                => $this->prenom,
            'genre'                 => $this->genre,
            'email'                 => $this->email ?: null,
            'telephone'             => $this->telephone,
            'fonction'              => $this->fonction,
            'pays'                  => $this->pays,
            'ville'                 => $villeFinal,
            'date_naissance'        => $this->date_naissance ?: null,
            'filiere'               => $this->estEtudiant ? ($this->filiere ?: null) : null,
            'universite'            => $this->estEtudiant ? ($this->universite ?: null) : null,
            'code_acces'            => $code_acces,
            // ✅ CORRIGÉ : un particulier indépendant n'est pas un "representant"
            'role'                  => 'participant',
            'statut_historique'     => 'actif',
            'statut_preinscription' => 'en_attente',
            'participation_rdv'     => true,
        ];

        if ($this->type_inscrit === 'membre_entreprise' && $this->entreprise_trouvee) {
            $data['id_entreprise'] = $this->entreprise_trouvee->id;
            // Le visiteur qui s'inscrit en indiquant l'IFU de son entreprise
            // devient le représentant officiel de cette entreprise.
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
                } elseif ($evenement->type_paiement == 'par_entreprise'
                    && $participant->id_entreprise) {
                    $montant        = 0;
                    $statutPaiement = 'en_attente';
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
        ])->layout('layouts.guest');
    }
}