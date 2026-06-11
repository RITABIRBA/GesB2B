<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Entreprise;
use App\Models\Participant;
use App\Models\Notification;
use Illuminate\Validation\Rule;

/**
 * Gestion des Entreprises — Admin
 *
 * L'admin peut :
 * → Voir toutes les entreprises
 * → Valider ou rejeter une entreprise
 * → Modifier les infos d'une entreprise
 * → Supprimer une entreprise
 *
 * Quand l'admin valide une entreprise :
 * → Une notification est créée pour le représentant
 * → Le représentant voit un message sur son dashboard
 *   avec un bouton "Payer maintenant via LigdiCash"
 */
class GestionEntreprises extends Component
{
    // ============================================================
    // PROPRIÉTÉS
    // ============================================================

    public $entreprise_id;
    public string $nom               = '';
    public string $ifu               = '';
    public string $secteur_activite  = '';
    public string $sous_secteur      = '';
    public string $pays              = '';
    public string $ville             = '';
    public string $telephone         = '';
    public string $email             = '';
    public string $statut_validation = 'en_attente';
    public bool   $showModal         = false;
    public bool   $isEditing         = false;
    public string $search            = '';

    // ============================================================
    // LISTES DE RÉFÉRENCE
    // ============================================================

    public array $secteurs = [
        'Agriculture', 'Industrie', 'Commerce', 'Services',
        'Technologie', 'Transport', 'Construction', 'Tourisme',
        'Santé', 'Education', 'Finance', 'Energie', 'Mines',
        'Artisanat', 'Autre',
    ];

    public array $pays_liste = [
        'Burkina Faso', 'Côte d\'Ivoire', 'Mali', 'Sénégal',
        'Ghana', 'Togo', 'Bénin', 'Niger', 'Guinée', 'Cameroun',
        'Nigeria', 'France', 'Allemagne', 'États-Unis', 'Chine', 'Autre',
    ];

    public array $villes_par_pays = [
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

    // ============================================================
    // HELPERS
    // ============================================================

    public function getVillesDisponiblesProperty(): array
    {
        return $this->villes_par_pays[$this->pays] ?? ['Autre'];
    }

    public function updatedPays(): void
    {
        $this->ville = '';
    }

    // ============================================================
    // MODAL
    // ============================================================

    public function openModal(): void
    {
        $this->resetFields();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function resetFields(): void
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

    public function modifier(int $id): void
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

    // ============================================================
    // VALIDATION / REJET
    // ============================================================

    /**
     * Valide une entreprise et notifie le représentant.
     *
     * Une notification est créée pour le représentant.
     * Il verra sur son dashboard un message l'invitant
     * à payer via LigdiCash.
     */
    public function valider(int $id): void
    {
        $entreprise = Entreprise::findOrFail($id);
        $entreprise->update(['statut_validation' => 'valide']);

        // ← Récupère le représentant de l'entreprise
        $representant = Participant::where('id_entreprise', $entreprise->id)
            ->where('role', 'representant')
            ->first();

        // ← Crée une notification pour le représentant
        if ($representant) {
            Notification::create([
                'id_participant' => $representant->id,
                'contenu'        => 'Votre entreprise ' . $entreprise->nom . ' a été validée par l\'administration. Vous pouvez maintenant procéder au paiement de votre inscription via LigdiCash.',
                'date_envoie'    => now()->toDateString(),
                'type'           => 'systeme',
            ]);
        }

        session()->flash('success', 'Entreprise validée. Le représentant a été notifié.');
    }

    /**
     * Rejette une entreprise et notifie le représentant.
     */
    public function rejeter(int $id): void
    {
        $entreprise = Entreprise::findOrFail($id);
        $entreprise->update(['statut_validation' => 'rejete']);

        // ← Récupère le représentant
        $representant = Participant::where('id_entreprise', $entreprise->id)
            ->where('role', 'representant')
            ->first();

        // ← Notifie le représentant du rejet
        if ($representant) {
            Notification::create([
                'id_participant' => $representant->id,
                'contenu'        => 'Votre entreprise ' . $entreprise->nom . ' a été rejetée par l\'administration. Veuillez contacter l\'équipe CCI-BF pour plus d\'informations.',
                'date_envoie'    => now()->toDateString(),
                'type'           => 'systeme',
            ]);
        }

        session()->flash('success', 'Entreprise rejetée. Le représentant a été notifié.');
    }

    public function supprimer(int $id): void
    {
        Entreprise::findOrFail($id)->delete();
        session()->flash('success', 'Entreprise supprimée.');
    }

    // ============================================================
    // SAUVEGARDER
    // ============================================================

    public function sauvegarder(): void
    {
        $this->validate([
            'nom'              => 'required|string|max:255',
            'ifu'              => [
                'required',
                'string',
                'regex:/^\d{8}[A-Za-z]$/',
                $this->isEditing
                    ? Rule::unique('entreprises', 'ifu')->ignore($this->entreprise_id)
                    : Rule::unique('entreprises', 'ifu'),
            ],
            'secteur_activite' => 'required|string|max:255',
            'sous_secteur'     => 'required|string|max:255',
            'pays'             => 'required|string|max:255',
            'ville'            => 'required|string|max:255',
            'telephone'        => 'required|string|max:20',
        ], [
            'ifu.required'          => 'Le numéro IFU est obligatoire.',
            'ifu.regex'             => 'Format IFU invalide. Exemple : 12345678A',
            'ifu.unique'            => 'Ce numéro IFU est déjà utilisé.',
            'sous_secteur.required' => 'Le sous-secteur est obligatoire.',
        ]);

        $data = [
            'nom'               => $this->nom,
            'ifu'               => strtoupper($this->ifu),
            'secteur_activite'  => $this->secteur_activite,
            'sous_secteur'      => $this->sous_secteur,
            'pays'              => $this->pays,
            'ville'             => $this->ville,
            'contact'           => $this->telephone,
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

    // ============================================================
    // RENDER
    // ============================================================

    public function render()
    {
        return view('livewire.admin.gestion-entreprises', [
            'entreprises' => Entreprise::when($this->search, fn($q) =>
                $q->where('nom', 'like', '%' . $this->search . '%')
                  ->orWhere('pays', 'like', '%' . $this->search . '%')
                  ->orWhere('ville', 'like', '%' . $this->search . '%')
                  ->orWhere('ifu', 'like', '%' . $this->search . '%')
            )->latest()->get(),

            'villesDisponibles' => $this->villesDisponibles,
        ])->layout('layouts.admin', ['title' => 'Gestion des Entreprises']);
    }
}