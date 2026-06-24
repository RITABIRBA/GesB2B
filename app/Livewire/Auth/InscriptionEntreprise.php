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

    // Infos entreprise
    public string $nom              = '';
    public string $ifu              = '';
    public string $secteur_activite = '';
    public string $secteur_autre    = '';
    public string $sous_secteur     = '';
    public string $pays             = 'Burkina Faso';
    public string $ville            = '';
    public string $telephone        = '';
    public string $email            = '';

    // Infos représentant
    public string $rep_nom             = '';
    public string $rep_prenom          = '';
    public string $rep_genre           = '';
    public string $rep_fonction        = '';
    public string $rep_fonction_autre  = ''; // ✅ NOUVEAU
    public string $rep_email           = '';
    public string $rep_telephone       = '';
    public string $rep_date_naissance  = '';

    // Événement
    public $id_evenement = '';

    // Confirmation
    public bool $confirme = false;

    public array $secteurs = [
        'Agriculture et agro-alimentaire', 'Environnement',
        'Industrie textile', 'Biens de consommation', 'Energie',
        'Formation', 'Tourisme', 'TIC', 'Sous-traitance', 'Artisanat',
        'Distribution', 'Prestation', 'Industrie manufacturière',
        'Enseignement', 'Services aux entreprises', 'BTP',
        'Activités médicales et pharmaceutiques', 'Autre',
    ];

    // ✅ NOUVEAU : liste des fonctions
    public array $fonctions = [
        'Directeur Général', 'Directeur Commercial', 'PDG', 'Gérant',
        'Responsable Export', 'Responsable Partenariats',
        'Chargé de Développement', 'Représentant', 'Autre',
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

    public function updatedPays(): void { $this->ville = ''; }

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

            if ($this->secteur_activite === 'Autre') {
                $this->secteur_activite = $secteurFinal;
            }

            $this->etape = 2;

        } elseif ($this->etape === 2) {
            $this->validate([
                'rep_nom'            => 'required|string|max:255',
                'rep_prenom'         => 'required|string|max:255',
                'rep_genre'          => 'required|in:homme,femme',
                'rep_telephone'      => 'required|string|max:20',
                'rep_email'          => 'nullable|email|max:255',
                'rep_date_naissance' => 'nullable|date|before:today',
            ], [
                'rep_nom.required'       => 'Le nom du représentant est obligatoire.',
                'rep_prenom.required'    => 'Le prénom du représentant est obligatoire.',
                'rep_genre.required'     => 'Le genre est obligatoire.',
                'rep_telephone.required' => 'Le téléphone est obligatoire.',
            ]);

            // ✅ Si fonction = Autre, on prend la valeur saisie
            if ($this->rep_fonction === 'Autre') {
                $this->rep_fonction = $this->rep_fonction_autre ?: 'Représentant';
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
        $secteurFinal = $this->secteur_activite;

        // Créer l'entreprise
        $entreprise = Entreprise::create([
            'nom'                => $this->nom,
            'ifu'                => strtoupper($this->ifu),
            'secteur_activite'   => $secteurFinal,
            'sous_secteur'       => $this->sous_secteur,
            'pays'               => $this->pays,
            'ville'              => $this->ville,
            'contact'            => $this->telephone,
            'statut_validation'  => 'en_attente',
            'nom_responsable'    => $this->rep_nom,
            'prenom_responsable' => $this->rep_prenom,
            'email_responsable'  => $this->rep_email ?: null,
        ]);

        // ✅ CORRECTION : iconv supprime les accents avant d'extraire les 3 premières lettres
        $nom_sans_accent = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $this->rep_nom);
        $code_acces = strtoupper(substr($nom_sans_accent, 0, 3)) . rand(1000, 9999);

        $representant = Participant::create([
            'id_entreprise'         => $entreprise->id,
            'nom'                   => $this->rep_nom,
            'prenom'                => $this->rep_prenom,
            'genre'                 => $this->rep_genre,
            'email'                 => $this->rep_email ?: null,
            'telephone'             => $this->rep_telephone,
            'fonction'              => $this->rep_fonction ?: 'Représentant',
            'date_naissance'        => $this->rep_date_naissance ?: null,
            'pays'                  => $this->pays,
            'ville'                 => $this->ville,
            'code_acces'            => $code_acces,
            'role'                  => 'representant',
            'statut_historique'     => 'actif',
            'statut_preinscription' => 'en_attente',
            'participation_rdv'     => true,
        ]);

        // Si événement sélectionné
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

        // ✅ ENVOI EMAIL — préinscription reçue au représentant
        $emailDestinataire = $this->rep_email ?: $this->email;

        if ($emailDestinataire) {
            try {
                $nomEvenement = 'Business Forum';
                if ($this->id_evenement) {
                    $ev = Evenement::find($this->id_evenement);
                    if ($ev) $nomEvenement = $ev->nom;
                }
                Mail::to($emailDestinataire)->send(
                    new PreinscriptionRecue($representant, $nomEvenement)
                );
            } catch (\Exception $e) {
                // L'email a échoué mais on continue
            }
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
        ])->layout('layouts.guest');
    }
}