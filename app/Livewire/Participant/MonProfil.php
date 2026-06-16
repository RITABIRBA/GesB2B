<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;

class MonProfil extends Component
{
    public $participant_id;
    public $nom = '';
    public $prenom = '';
    public $genre = '';
    public $fonction = '';
    public $email = '';
    public $telephone = '';
    public $secteur_activite = '';
    public $participation_rdv = true;
    public $ifu = '';
    public $isEditing = false;
    public $statut_adhesion = null;

    // Entreprise
    public $entreprise_trouvee = null;
    public $entreprise_actuelle = null;

    // Recherche entreprise par nom
    public $recherche_entreprise = '';
    public $entreprises_trouvees = [];
    public $showDemandeModal = false;
    public $entreprise_choisie = null;

    
    // PROFIL PARTENAIRE RECHERCHÉ (max 3 chacun)
    

    public string $zone_geographique = '';

    public array  $secteurs_recherche      = [];
    public string $secteur_recherche_autre = '';

    public array  $types_partenariat       = [];
    public string $type_partenariat_autre  = '';

    public array  $profils_partenaire      = [];

    
    // LISTES OFFICIELLES
    

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
        'Locale',
        'Nationale',
        'Régionale (CEDEAO)',
        'Africaine',
        'Internationale',
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

    public function mount()
    {
        $participant = Participant::where('email', auth()->user()->email)
            ->with('entreprise')
            ->first();

        if ($participant) {
            $this->participant_id      = $participant->id;
            $this->nom                 = $participant->nom;
            $this->prenom              = $participant->prenom;
            $this->genre               = $participant->genre;
            $this->fonction            = $participant->fonction;
            $this->email               = $participant->email;
            $this->telephone           = $participant->telephone;
            $this->secteur_activite    = $participant->secteur_activite;
            $this->participation_rdv   = $participant->participation_rdv;
            $this->ifu                 = $participant->ifu ?? '';
            $this->statut_adhesion     = $participant->statut_adhesion;
            $this->entreprise_actuelle = $participant->entreprise;

            // ← Profil partenaire recherché
            $this->zone_geographique = $participant->zone_geographique ?? '';

            $this->secteurs_recherche = $this->chargerListeAvecAutre(
                $participant->secteurs_recherche,
                $this->secteurs,
                'secteur_recherche_autre'
            );

            $this->types_partenariat = $this->chargerListeAvecAutre(
                $participant->types_partenariat,
                $this->typesPartenariatOptions,
                'type_partenariat_autre'
            );

            $this->profils_partenaire = is_array($participant->profils_partenaire)
                ? $participant->profils_partenaire
                : (json_decode($participant->profils_partenaire ?? '[]', true) ?: []);
        }
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

    public function activer() { $this->isEditing = true; }

    public function annuler()
    {
        $this->isEditing = false;
        $this->entreprise_trouvee = null;
        $this->mount();
        $this->resetErrorBag();
    }

    // ← Quand IFU change → cherche l'entreprise
    public function updatedIfu($value)
    {
        if ($value && strlen($value) >= 3) {
            $this->entreprise_trouvee = Entreprise::where('ifu', $value)->first();
        } else {
            $this->entreprise_trouvee = null;
        }
    }

    // ← Recherche entreprise par nom
    public function updatedRechercheEntreprise($value)
    {
        if ($value && strlen($value) >= 2) {
            $this->entreprises_trouvees = Entreprise::where('nom', 'like', '%'.$value.'%')
                ->where('statut_validation', 'valide')
                ->limit(5)
                ->get()
                ->toArray();
        } else {
            $this->entreprises_trouvees = [];
        }
    }

    // ← Sélectionne une entreprise dans la liste
    public function ouvrirDemandeAdhesion($entreprise_id)
    {
        // ← toArray() pour éviter le problème d'accès objet/tableau
        $entreprise = Entreprise::find($entreprise_id);
        if ($entreprise) {
            $this->entreprise_choisie = $entreprise->toArray();
        }
    }

    public function fermerDemandeModal()
    {
        $this->showDemandeModal     = false;
        $this->entreprise_choisie   = null;
        $this->recherche_entreprise = '';
        $this->entreprises_trouvees = [];
    }

    // ← Envoie la demande d'adhésion
    public function envoyerDemande()
    {
        if (!$this->entreprise_choisie) return;

        // ← Sauvegarde le nom AVANT de fermer le modal
        $nom_entreprise = $this->entreprise_choisie['nom'];
        $id_entreprise  = $this->entreprise_choisie['id'];

        Participant::findOrFail($this->participant_id)->update([
            'id_entreprise'   => $id_entreprise,
            'statut_adhesion' => 'en_attente',
        ]);

        $this->statut_adhesion     = 'en_attente';
        $this->entreprise_actuelle = Entreprise::find($id_entreprise);

        $this->fermerDemandeModal();
        session()->flash('success', 'Demande envoyée à ' . $nom_entreprise . ' ! En attente de validation.');
    }

    // ← Annuler la demande
    public function annulerDemande()
    {
        Participant::findOrFail($this->participant_id)->update([
            'id_entreprise'   => null,
            'statut_adhesion' => null,
        ]);

        $this->statut_adhesion     = null;
        $this->entreprise_actuelle = null;
        session()->flash('success', 'Demande annulée.');
    }

    public function toggleParticipationRdv()
    {
        $participant = Participant::findOrFail($this->participant_id);
        $this->participation_rdv = !$this->participation_rdv;
        $participant->update(['participation_rdv' => $this->participation_rdv]);

        $message = $this->participation_rdv
            ? 'Vous participez maintenant aux rendez-vous d\'affaire !'
            : 'Vous ne participez plus aux rendez-vous d\'affaire.';

        session()->flash('success', $message);
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

    public function sauvegarder()
    {
        $this->validate([
            'nom'                => 'required|string|max:255',
            'prenom'             => 'required|string|max:255',
            'email'              => 'required|email|max:255',
            'ifu'                => 'nullable|string|max:255',
            'zone_geographique'  => 'nullable|string|max:255',
            'secteurs_recherche' => 'nullable|array|max:3',
            'types_partenariat'  => 'nullable|array|max:3',
            'profils_partenaire' => 'nullable|array|max:3',
        ]);

        if (in_array('Autre', $this->secteurs_recherche) && !$this->secteur_recherche_autre) {
            $this->addError('secteur_recherche_autre', 'Précisez le secteur recherché.');
            return;
        }

        if (in_array('Autre', $this->types_partenariat) && !$this->type_partenariat_autre) {
            $this->addError('type_partenariat_autre', 'Précisez le type de partenariat.');
            return;
        }

        $id_entreprise   = null;
        $statut_adhesion = null;

        if ($this->ifu) {
            $entreprise = Entreprise::where('ifu', $this->ifu)->first();
            if ($entreprise) {
                $id_entreprise             = $entreprise->id;
                $statut_adhesion           = 'accepte';
                $this->entreprise_actuelle = $entreprise;
            }
        }

        // ← Remplace "Autre" par la saisie libre dans les sélections
        $secteursRecherche = $this->secteurs_recherche;
        if (($idx = array_search('Autre', $secteursRecherche)) !== false && $this->secteur_recherche_autre) {
            $secteursRecherche[$idx] = $this->secteur_recherche_autre;
        }

        $typesPartenariat = $this->types_partenariat;
        if (($idx = array_search('Autre', $typesPartenariat)) !== false && $this->type_partenariat_autre) {
            $typesPartenariat[$idx] = $this->type_partenariat_autre;
        }

        Participant::findOrFail($this->participant_id)->update([
            'nom'                => $this->nom,
            'prenom'             => $this->prenom,
            'genre'              => $this->genre,
            'fonction'           => $this->fonction,
            'email'              => $this->email,
            'telephone'          => $this->telephone,
            'secteur_activite'   => $this->secteur_activite,
            'participation_rdv'  => $this->participation_rdv,
            'ifu'                => $this->ifu ?: null,
            'id_entreprise'      => $id_entreprise,
            'statut_adhesion'    => $statut_adhesion,
            'zone_geographique'  => $this->zone_geographique ?: null,
            'secteurs_recherche' => !empty($secteursRecherche) ? json_encode($secteursRecherche) : null,
            'types_partenariat'  => !empty($typesPartenariat) ? json_encode($typesPartenariat) : null,
            'profils_partenaire' => !empty($this->profils_partenaire) ? json_encode($this->profils_partenaire) : null,
        ]);

        auth()->user()->update(['email' => $this->email]);

        $this->isEditing          = false;
        $this->entreprise_trouvee = null;

        if ($id_entreprise) {
            session()->flash('success', 'Profil mis à jour ! Vous êtes lié à ' . $this->entreprise_actuelle->nom);
        } else {
            session()->flash('success', 'Profil mis à jour avec succès.');
        }
    }

    public function render()
    {
        return view('livewire.participant.mon-profil', [
            'secteurs'                  => $this->secteurs,
            'zonesGeographiques'        => $this->zonesGeographiques,
            'typesPartenariatOptions'   => $this->typesPartenariatOptions,
            'profilsPartenariatOptions' => $this->profilsPartenariatOptions,
        ])->layout('layouts.participant', ['title' => 'Mon Profil']);
    }
}