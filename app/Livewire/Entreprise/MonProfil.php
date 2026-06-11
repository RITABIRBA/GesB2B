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

    // ← Profil partenaire recherché
    public $secteur_recherche = '';
    public $zone_geographique = '';
    public $type_partenaire   = '';

    public bool $isEditing = false;

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
            $this->secteur_recherche = $representant->secteur_recherche ?? '';
            $this->zone_geographique = $representant->zone_geographique ?? '';
            $this->type_partenaire   = $representant->type_partenaire ?? '';
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
            'secteur_recherche'     => 'required|string|max:255',
            'zone_geographique'     => 'required|string|max:255',
            'type_partenaire'       => 'required|string|max:255',
        ]);

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
                'secteur_recherche'     => $this->secteur_recherche,
                'zone_geographique'     => $this->zone_geographique,
                'type_partenaire'       => $this->type_partenaire,
            ]);
        }

        $this->isEditing = false;
        session()->flash('success', 'Profil mis à jour avec succès.');
    }

    
    // RENDER
    

    public function render()
    {
        return view('livewire.entreprise.mon-profil', [
            'entreprise'        => $this->getEntreprise(),
            'villesDisponibles' => $this->getVillesDisponibles(),
        ])->layout('layouts.entreprise', ['title' => 'Mon Profil']);
    }
}