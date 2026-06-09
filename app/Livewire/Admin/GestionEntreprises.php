<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Entreprise;
use Illuminate\Validation\Rule;

class GestionEntreprises extends Component
{
    public $entreprise_id;
    public $nom              = '';
    public $ifu              = '';
    public $secteur_activite = '';
    public $sous_secteur     = '';
    public $pays             = '';
    public $ville            = '';
    public $telephone        = '';
    public $email            = '';
    public $statut_validation = 'en_attente';
    public $showModal        = false;
    public $isEditing        = false;
    public $search           = '';

    public $secteurs = [
        'Agriculture', 'Industrie', 'Commerce', 'Services',
        'Technologie', 'Transport', 'Construction', 'Tourisme',
        'Santé', 'Education', 'Finance', 'Energie', 'Mines',
        'Artisanat', 'Autre',
    ];

    public $pays_liste = [
        'Burkina Faso', 'Côte d\'Ivoire', 'Mali', 'Sénégal',
        'Ghana', 'Togo', 'Bénin', 'Niger', 'Guinée', 'Cameroun',
        'Nigeria', 'France', 'Allemagne', 'États-Unis', 'Chine', 'Autre',
    ];

    // ← Villes par pays
    public $villes_par_pays = [
        'Burkina Faso'   => ['Ouagadougou', 'Bobo-Dioulasso', 'Koudougou', 'Banfora', 'Ouahigouya', 'Pouytenga', 'Kaya', 'Tenkodogo', 'Fada N\'Gourma', 'Dédougou', 'Autre'],
        'Côte d\'Ivoire' => ['Abidjan', 'Bouaké', 'Daloa', 'San-Pédro', 'Yamoussoukro', 'Korhogo', 'Man', 'Divo', 'Gagnoa', 'Abengourou', 'Autre'],
        'Mali'           => ['Bamako', 'Sikasso', 'Mopti', 'Koutiala', 'Kayes', 'Ségou', 'Gao', 'Tombouctou', 'Autre'],
        'Sénégal'        => ['Dakar', 'Thiès', 'Kaolack', 'Ziguinchor', 'Saint-Louis', 'Rufisque', 'Mbour', 'Louga', 'Autre'],
        'Ghana'          => ['Accra', 'Kumasi', 'Tamale', 'Sekondi-Takoradi', 'Cape Coast', 'Autre'],
        'Togo'           => ['Lomé', 'Sokodé', 'Kara', 'Atakpamé', 'Kpalimé', 'Autre'],
        'Bénin'          => ['Cotonou', 'Porto-Novo', 'Parakou', 'Abomey-Calavi', 'Autre'],
        'Niger'          => ['Niamey', 'Zinder', 'Maradi', 'Tahoua', 'Agadez', 'Autre'],
        'Guinée'         => ['Conakry', 'Nzérékoré', 'Kankan', 'Kindia', 'Autre'],
        'Cameroun'       => ['Yaoundé', 'Douala', 'Garoua', 'Bamenda', 'Autre'],
        'Nigeria'        => ['Lagos', 'Kano', 'Ibadan', 'Abuja', 'Port Harcourt', 'Autre'],
        'France'         => ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Autre'],
        'Allemagne'      => ['Berlin', 'Hambourg', 'Munich', 'Cologne', 'Francfort', 'Autre'],
        'États-Unis'     => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Washington', 'Autre'],
        'Chine'          => ['Pékin', 'Shanghai', 'Guangzhou', 'Shenzhen', 'Autre'],
        'Autre'          => ['Autre'],
    ];

    // ← Villes disponibles selon le pays
    public function getVillesDisponiblesProperty()
    {
        return $this->villes_par_pays[$this->pays] ?? ['Autre'];
    }

    // ← Reset ville quand pays change
    public function updatedPays()
    {
        $this->ville = '';
    }

    public function openModal()
    {
        $this->resetFields();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->entreprise_id     = null;
        $this->nom               = '';
        $this->ifu               = '';
        $this->secteur_activite  = '';
        $this->sous_secteur      = '';
        $this->pays              = '';
        $this->ville             = '';
        $this->telephone         = '';
        $this->email             = '';
        $this->statut_validation = 'en_attente';
        $this->resetErrorBag();
    }

    public function modifier($id)
    {
        $e = Entreprise::findOrFail($id);
        $this->entreprise_id     = $e->id;
        $this->nom               = $e->nom;
        $this->ifu               = $e->ifu ?? '';
        $this->secteur_activite  = $e->secteur_activite;
        $this->sous_secteur      = $e->sous_secteur ?? '';
        $this->pays              = $e->pays;
        $this->ville             = $e->ville;
        $this->telephone         = $e->contact;
        $this->statut_validation = $e->statut_validation;
        $this->isEditing         = true;
        $this->showModal         = true;
    }

    public function sauvegarder()
    {
        $this->validate([
            'nom'              => 'required|string|max:255',
            // ← Format IFU : 8 chiffres + 1 lettre
            'ifu'              => [
                'required',
                'string',
                'regex:/^\d{8}[A-Za-z]$/',
                $this->isEditing
                    ? Rule::unique('entreprises', 'ifu')->ignore($this->entreprise_id)
                    : Rule::unique('entreprises', 'ifu'),
            ],
            'secteur_activite' => 'required|string|max:255',
            'sous_secteur'     => 'required|string|max:255', // ← obligatoire
            'pays'             => 'required|string|max:255',
            'ville'            => 'required|string|max:255',
            'telephone'        => 'required|string|max:20',
            'email'            => 'nullable|email|max:255',
        ], [
            'ifu.required' => 'Le numéro IFU est obligatoire.',
            'ifu.regex'    => 'Format IFU invalide. Exemple correct : 12345678A',
            'ifu.unique'   => 'Ce numéro IFU est déjà utilisé.',
            'sous_secteur.required' => 'Le sous-secteur est obligatoire.',
        ]);

        $data = [
            'nom'               => $this->nom,
            'ifu'               => strtoupper($this->ifu), // ← majuscules
            'secteur_activite'  => $this->secteur_activite,
            'sous_secteur'      => $this->sous_secteur,
            'pays'              => $this->pays,
            'ville'             => $this->ville,
            'contact'           => $this->telephone . ($this->email ? ' / ' . $this->email : ''),
            'statut_validation' => $this->statut_validation,
        ];

        if ($this->isEditing) {
            Entreprise::findOrFail($this->entreprise_id)->update($data);
            session()->flash('success', 'Entreprise modifiée avec succès.');
        } else {
            $data['statut_validation'] = 'en_attente';
            Entreprise::create($data);
            session()->flash('success', 'Entreprise créée avec succès.');
        }

        $this->closeModal();
    }

    public function valider($id)
    {
        Entreprise::findOrFail($id)->update(['statut_validation' => 'valide']);
        session()->flash('success', 'Entreprise validée.');
    }

    public function rejeter($id)
    {
        Entreprise::findOrFail($id)->update(['statut_validation' => 'rejete']);
        session()->flash('success', 'Entreprise rejetée.');
    }

    public function supprimer($id)
    {
        Entreprise::findOrFail($id)->delete();
        session()->flash('success', 'Entreprise supprimée.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-entreprises', [
            'entreprises'        => Entreprise::when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('pays', 'like', '%'.$this->search.'%')
                      ->orWhere('ville', 'like', '%'.$this->search.'%')
                      ->orWhere('ifu', 'like', '%'.$this->search.'%')
                )->latest()->get(),
            'villesDisponibles'  => $this->villesDisponibles, // ← dynamique
        ])->layout('layouts.admin', ['title' => 'Gestion des Entreprises']);
    }
}