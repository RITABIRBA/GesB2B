<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Evenement;
use App\Models\TypeEvenement;
use App\Models\TypeStand;

class GestionEvenements extends Component
{
    public $evenement_id;
    public $id_type_evenement           = '';
    public $nouveau_type                = '';
    public $utiliser_nouveau_type       = '';
    public $nom                         = '';
    public $annee                       = '';

    public $type_evenement              = 'avec_b2b';

    public $date_debut                  = '';
    public $date_fin                    = '';
    public $date_ouverture_inscriptions = '';
    public $date_cloture_inscriptions   = '';

    public $date_limite_rdv             = '';

    public $heure_debut                 = '';
    public $heure_fin                   = '';
    public $ville                       = '';
    public $lieu                        = '';
    public $nom_salle                   = '';
    public $nombre_tables               = 10;
    public $montant_inscription         = 0;
    public $type_paiement               = 'gratuit';
    public $nombre_stands               = 0;
    public $prix_stand_standard         = 0;
    public $prix_stand_premium          = 0;
    public $prix_stand_vip              = 0;
    public $min_souhaits                = 5;
    public $max_souhaits                = 20;
    public $duree_rdv                   = 20;
    public $duree_pause                 = 0;
    public $showModal                   = false;
    public $isEditing                   = false;
    public $search                      = '';

    public array $types_stands          = [];
    public bool  $showTypesStands       = false;

    // ─── Modal Types de stands ─────────────────────────────
    public bool   $showTypeStandModal = false;
    public int    $typeStandIndex     = -1;
    public string $ts_standing        = '';
    // ✅ NOUVEAU : quantité par standing
    public int    $ts_quantite        = 1;
    public string $ts_superficie      = '';
    public bool   $ts_est_gratuit     = false;
    public float  $ts_montant         = 0;
    public array  $ts_composants      = [];
    public string $ts_composant_nom   = '';
    public int    $ts_composant_qte   = 1;

    // ─── Watchers ─────────────────────────────────────────

    public function updatedTypeEvenement(): void
    {
        if ($this->type_evenement === 'sans_b2b') {
            $this->min_souhaits    = 0;
            $this->max_souhaits    = 0;
            $this->duree_rdv       = 0;
            $this->date_limite_rdv = '';
        } else {
            $this->min_souhaits = 5;
            $this->max_souhaits = 20;
            $this->duree_rdv    = 20;
        }
    }

    // ─── Types de stands ──────────────────────────────────

    public function ouvrirAjoutTypeStand(): void
    {
        $this->typeStandIndex   = -1;
        $this->ts_standing      = '';
        $this->ts_quantite      = 1;
        $this->ts_superficie    = '';
        $this->ts_est_gratuit   = false;
        $this->ts_montant       = 0;
        $this->ts_composants    = [];
        $this->ts_composant_nom = '';
        $this->ts_composant_qte = 1;
        $this->showTypeStandModal = true;
    }

    public function ouvrirModifierTypeStand(int $index): void
    {
        $ts = $this->types_stands[$index];
        $this->typeStandIndex   = $index;
        $this->ts_standing      = $ts['standing'];
        $this->ts_quantite      = $ts['quantite'] ?? 1;
        $this->ts_superficie    = $ts['superficie'] ?? '';
        $this->ts_est_gratuit   = $ts['est_gratuit'] ?? false;
        $this->ts_montant       = $ts['montant'] ?? 0;
        $this->ts_composants    = $ts['composants'] ?? [];
        $this->ts_composant_nom = '';
        $this->ts_composant_qte = 1;
        $this->showTypeStandModal = true;
    }

    public function ajouterComposant(): void
    {
        if (!trim($this->ts_composant_nom)) return;

        $this->ts_composants[] = [
            'nom' => trim($this->ts_composant_nom),
            'qte' => (int) $this->ts_composant_qte,
        ];
        $this->ts_composant_nom = '';
        $this->ts_composant_qte = 1;
    }

    public function supprimerComposant(int $i): void
    {
        array_splice($this->ts_composants, $i, 1);
    }

    public function sauvegarderTypeStand(): void
    {
        $this->validate([
            'ts_standing'   => 'required|string|max:255',
            'ts_quantite'   => 'required|integer|min:1|max:200',
            'ts_superficie' => 'nullable|string|max:100',
            'ts_montant'    => 'nullable|numeric|min:0',
        ], [
            'ts_standing.required' => 'Le nom du standing est obligatoire.',
            'ts_quantite.required' => 'Le nombre de stands est obligatoire.',
            'ts_quantite.min'      => 'Le nombre de stands doit être au moins 1.',
        ]);

        $typeStand = [
            'standing'    => $this->ts_standing,
            'quantite'    => $this->ts_quantite,
            'superficie'  => $this->ts_superficie ?: null,
            'est_gratuit' => $this->ts_est_gratuit,
            'montant'     => $this->ts_est_gratuit ? 0 : $this->ts_montant,
            'composants'  => $this->ts_composants,
        ];

        if ($this->typeStandIndex === -1) {
            $this->types_stands[] = $typeStand;
        } else {
            $this->types_stands[$this->typeStandIndex] = $typeStand;
        }

        $this->recalculerNombreStandsTotal();

        $this->showTypeStandModal = false;
    }

    public function supprimerTypeStand(int $index): void
    {
        array_splice($this->types_stands, $index, 1);
        $this->recalculerNombreStandsTotal();
    }

    /**
     * ✅ Recalcule automatiquement le nombre total de stands
     * en additionnant la quantité de chaque standing.
     */
    private function recalculerNombreStandsTotal(): void
    {
        $this->nombre_stands = collect($this->types_stands)
            ->sum(fn($ts) => (int) ($ts['quantite'] ?? 0));
    }

    // ─── CRUD Événement ───────────────────────────────────

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
        $this->evenement_id                 = null;
        $this->id_type_evenement            = '';
        $this->nouveau_type                 = '';
        $this->utiliser_nouveau_type        = '';
        $this->nom                          = '';
        $this->annee                        = '';
        $this->type_evenement               = 'avec_b2b';
        $this->date_debut                   = '';
        $this->date_fin                     = '';
        $this->date_ouverture_inscriptions  = '';
        $this->date_cloture_inscriptions    = '';
        $this->date_limite_rdv              = '';
        $this->heure_debut                  = '';
        $this->heure_fin                    = '';
        $this->ville                        = '';
        $this->lieu                         = '';
        $this->nom_salle                    = '';
        $this->nombre_tables                = 10;
        $this->montant_inscription          = 0;
        $this->type_paiement                = 'gratuit';
        $this->nombre_stands                = 0;
        $this->prix_stand_standard          = 0;
        $this->prix_stand_premium           = 0;
        $this->prix_stand_vip               = 0;
        $this->min_souhaits                 = 5;
        $this->max_souhaits                 = 20;
        $this->duree_rdv                    = 20;
        $this->duree_pause                  = 0;
        $this->types_stands                 = [];
        $this->showTypesStands              = false;
        $this->showTypeStandModal           = false;
        $this->resetErrorBag();
    }

    public function modifier($id): void
    {
        $e = Evenement::with('typesStands')->findOrFail($id);

        $this->evenement_id                 = $e->id;
        $this->id_type_evenement            = $e->id_type_evenement;
        $this->nom                          = $e->nom;
        $this->annee                        = $e->annee;
        $this->type_evenement               = $e->type_evenement ?? 'avec_b2b';
        $this->date_debut                   = $e->date_debut;
        $this->date_fin                     = $e->date_fin;
        $this->date_ouverture_inscriptions  = $e->date_ouverture_inscriptions ?? '';
        $this->date_cloture_inscriptions    = $e->date_cloture_inscriptions ?? '';
        $this->date_limite_rdv              = $e->date_limite_rdv ?? '';
        $this->heure_debut                  = $e->heure_debut;
        $this->heure_fin                    = $e->heure_fin;
        $this->ville                        = $e->ville;
        $this->lieu                         = $e->lieu;
        $this->nom_salle                    = $e->nom_salle ?? '';
        $this->nombre_tables                = (int) ($e->nombre_tables ?? 10);
        $this->montant_inscription          = $e->montant_inscription;
        $this->type_paiement                = $e->type_paiement;
        $this->nombre_stands                = (int) ($e->nombre_stands ?? 0);
        $this->prix_stand_standard          = $e->prix_stand_standard ?? 0;
        $this->prix_stand_premium           = $e->prix_stand_premium ?? 0;
        $this->prix_stand_vip               = $e->prix_stand_vip ?? 0;
        $this->min_souhaits                 = $e->min_souhaits ?? 5;
        $this->max_souhaits                 = $e->max_souhaits ?? 20;
        $this->duree_rdv                    = $e->duree_rdv ?? 20;
        $this->duree_pause                  = $e->duree_pause ?? 0;

        $this->types_stands = $e->typesStands->map(fn($ts) => [
            'id'          => $ts->id,
            'standing'    => $ts->standing,
            'quantite'    => $ts->quantite ?? 1,
            'superficie'  => $ts->superficie,
            'est_gratuit' => $ts->est_gratuit,
            'montant'     => $ts->montant,
            'composants'  => $ts->composants ?? [],
        ])->toArray();

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function sauvegarder(): void
    {
        $regles = [
            'nom'           => 'required|string|max:255',
            'annee'         => 'required|integer|min:2000|max:2100',
            'type_evenement' => 'required|in:avec_b2b,sans_b2b',
            'date_debut'    => 'required|date',
            'date_fin'      => 'required|date|after_or_equal:date_debut',
            'heure_debut'   => 'required',
            'heure_fin'     => 'required',
            'ville'         => 'required|string|max:255',
            'lieu'          => 'required|string|max:255',
            'type_paiement' => 'required|in:gratuit,par_entreprise,payant',
            'date_ouverture_inscriptions' => 'nullable|date',
            'date_cloture_inscriptions'   => 'nullable|date|after_or_equal:date_ouverture_inscriptions',
            'date_limite_rdv'             => 'nullable|date|before_or_equal:date_fin',
            'nom_salle'                   => 'nullable|string|max:255',
            'nombre_tables'               => 'nullable|integer|min:1|max:500',
            'nombre_stands'               => 'nullable|integer|min:0|max:500',
            'prix_stand_standard'         => 'nullable|numeric|min:0',
            'prix_stand_premium'          => 'nullable|numeric|min:0',
            'prix_stand_vip'              => 'nullable|numeric|min:0',
        ];

        if ($this->type_evenement === 'avec_b2b') {
            $regles['min_souhaits'] = 'required|integer|min:1|max:50';
            $regles['max_souhaits'] = 'required|integer|min:1|max:100';
            $regles['duree_rdv']    = 'required|integer|min:5|max:120';
        }

        if ($this->type_paiement !== 'gratuit') {
            $regles['montant_inscription'] = 'required|numeric|min:1';
        }

        if ($this->utiliser_nouveau_type === '1') {
            $regles['nouveau_type'] = 'required|string|max:255';
            $this->validate($regles);
            $type    = TypeEvenement::create(['nom' => $this->nouveau_type]);
            $id_type = $type->id;
        } else {
            $regles['id_type_evenement'] = 'required';
            $this->validate($regles);
            $id_type = $this->id_type_evenement;
        }

        $data = [
            'id_type_evenement'           => $id_type,
            'nom'                         => $this->nom,
            'annee'                       => $this->annee,
            'type_evenement'              => $this->type_evenement,
            'date_debut'                  => $this->date_debut,
            'date_fin'                    => $this->date_fin,
            'date_ouverture_inscriptions' => $this->date_ouverture_inscriptions ?: null,
            'date_cloture_inscriptions'   => $this->date_cloture_inscriptions ?: null,
            'date_limite_rdv'             => $this->date_limite_rdv ?: null,
            'heure_debut'                 => $this->heure_debut,
            'heure_fin'                   => $this->heure_fin,
            'ville'                       => $this->ville,
            'lieu'                        => $this->lieu,
            'nom_salle'                   => $this->nom_salle ?: null,
            'nombre_tables'               => (int) ($this->nombre_tables ?: 10),
            'type_paiement'               => $this->type_paiement,
            'montant_inscription'         => $this->type_paiement === 'gratuit'
                ? 0 : $this->montant_inscription,
            // ✅ Calculé automatiquement à partir des types de stands
            'nombre_stands'               => collect($this->types_stands)
                ->sum(fn($ts) => (int) ($ts['quantite'] ?? 0)),
            'prix_stand_standard'         => $this->prix_stand_standard ?: 0,
            'prix_stand_premium'          => $this->prix_stand_premium ?: 0,
            'prix_stand_vip'              => $this->prix_stand_vip ?: 0,
            'min_souhaits'                => $this->type_evenement === 'avec_b2b'
                ? (int) $this->min_souhaits : 0,
            'max_souhaits'                => $this->type_evenement === 'avec_b2b'
                ? (int) $this->max_souhaits : 0,
            'duree_rdv'                   => $this->type_evenement === 'avec_b2b'
                ? (int) $this->duree_rdv : 0,
            'duree_pause'                 => 0,
        ];

        if ($this->isEditing) {
            $evenement = Evenement::findOrFail($this->evenement_id);
            $evenement->update($data);

            $this->sauvegarderTypesStands($evenement->id);

            session()->flash('success', 'Événement modifié avec succès.');
        } else {
            $evenement = Evenement::create($data);

            $this->sauvegarderTypesStands($evenement->id);

            session()->flash('success', 'Événement créé avec succès.');
        }

        $this->closeModal();
    }

    private function sauvegarderTypesStands(int $id_evenement): void
    {
        if (empty($this->types_stands)) return;

        // Supprime les types de stands qui ne sont plus dans la liste (édition)
        $idsConserves = collect($this->types_stands)
            ->pluck('id')
            ->filter()
            ->toArray();

        TypeStand::where('id_evenement', $id_evenement)
            ->when(!empty($idsConserves), fn($q) => $q->whereNotIn('id', $idsConserves))
            ->when(empty($idsConserves), fn($q) => $q)
            ->delete();

        foreach ($this->types_stands as $ts) {
            if (!empty($ts['id'])) {
                TypeStand::where('id', $ts['id'])
                    ->update([
                        'standing'    => $ts['standing'],
                        'quantite'    => $ts['quantite'] ?? 1,
                        'superficie'  => $ts['superficie'],
                        'est_gratuit' => $ts['est_gratuit'],
                        'montant'     => $ts['est_gratuit'] ? 0 : $ts['montant'],
                        'composants'  => $ts['composants'],
                    ]);
            } else {
                TypeStand::create([
                    'id_evenement' => $id_evenement,
                    'standing'     => $ts['standing'],
                    'quantite'     => $ts['quantite'] ?? 1,
                    'superficie'   => $ts['superficie'],
                    'est_gratuit'  => $ts['est_gratuit'],
                    'montant'      => $ts['est_gratuit'] ? 0 : $ts['montant'],
                    'composants'   => $ts['composants'],
                ]);
            }
        }
    }

    public function supprimer($id): void
    {
        Evenement::findOrFail($id)->delete();
        session()->flash('success', 'Événement supprimé avec succès.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-evenements', [
            'evenements' => Evenement::with('typeEvenement')
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('ville', 'like', '%' . $this->search . '%')
                )
                ->latest()
                ->get(),
            'typeEvenements' => TypeEvenement::orderBy('nom')->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Événements']);
    }
}