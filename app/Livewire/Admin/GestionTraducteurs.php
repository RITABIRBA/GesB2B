<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Traducteur;

/**
 * Composant Livewire — Gestion des Traducteurs
 * 
 * Ce composant permet à l'administrateur de gérer les traducteurs
 * de la plateforme GesB2B. Les traducteurs facilitent les rencontres
 * B2B entre entreprises qui parlent des langues différentes.
 * 
 * Fonctionnalités :
 * - Lister les traducteurs avec recherche en temps réel
 * - Créer un nouveau traducteur
 * - Modifier un traducteur existant
 * - Supprimer un traducteur
 */
class GestionTraducteurs extends Component
{
    // =========================================================
    // PROPRIÉTÉS DU FORMULAIRE
    // =========================================================

    /** @var int|null Identifiant du traducteur en cours de modification */
    public $traducteur_id;

    /** @var string Nom du traducteur */
    public $nom = '';

    /** @var string Prénom du traducteur */
    public $prenom = '';

    /** @var string Numéro de téléphone du traducteur */
    public $telephone = '';

    /** @var string|null Email du traducteur (optionnel) */
    public $email = '';

    /** @var string Langue maîtrisée par le traducteur */
    public $langue = '';

    // =========================================================
    // PROPRIÉTÉS DE L'INTERFACE
    // =========================================================

    /** @var bool Contrôle l'affichage du modal (ouvert/fermé) */
    public $showModal = false;

    /** @var bool Indique si on est en mode modification (true) ou création (false) */
    public $isEditing = false;

    /** @var string Texte de recherche pour filtrer les traducteurs */
    public $search = '';

    // =========================================================
    // DONNÉES STATIQUES
    // =========================================================

    /**
     * Liste des langues disponibles pour les traducteurs.
     * Inclut les langues internationales et les langues locales
     * du Burkina Faso (Dioula, Mooré, Fulfuldé).
     */
    public $langues = [
        'Français', 'Anglais', 'Arabe', 'Espagnol',
        'Portugais', 'Allemand', 'Chinois', 'Dioula',
        'Mooré', 'Fulfuldé',
    ];

    // =========================================================
    // GESTION DU MODAL
    // =========================================================

    /**
     * Ouvre le modal en mode création.
     * Réinitialise tous les champs avant d'ouvrir.
     */
    public function openModal()
    {
        $this->resetFields();
        $this->showModal = true;
        $this->isEditing = false;
    }

    /**
     * Ferme le modal et réinitialise les champs.
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
    }

    /**
     * Réinitialise tous les champs du formulaire
     * et efface les erreurs de validation.
     */
    public function resetFields()
    {
        $this->traducteur_id = null;
        $this->nom           = '';
        $this->prenom        = '';
        $this->telephone     = '';
        $this->email         = '';
        $this->langue        = '';
        $this->resetErrorBag(); // Efface les messages d'erreur
    }

    // =========================================================
    // ACTIONS CRUD
    // =========================================================

    /**
     * Charge les données d'un traducteur dans le formulaire
     * et ouvre le modal en mode modification.
     *
     * @param int $id Identifiant du traducteur à modifier
     */
    public function modifier($id)
    {
        // Récupère le traducteur (lève une erreur 404 si introuvable)
        $t = Traducteur::findOrFail($id);

        // Charge les données dans le formulaire
        $this->traducteur_id = $t->id;
        $this->nom           = $t->nom;
        $this->prenom        = $t->prenom;
        $this->telephone     = $t->telephone;
        $this->email         = $t->email;
        $this->langue        = $t->langue;

        // Active le mode modification et ouvre le modal
        $this->isEditing = true;
        $this->showModal = true;
    }

    /**
     * Valide et sauvegarde le traducteur (création ou modification).
     * 
     * En création : insère un nouveau traducteur en base.
     * En modification : met à jour le traducteur existant.
     */
    public function sauvegarder()
    {
        // Validation des champs du formulaire
        $this->validate([
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email'     => 'nullable|email|max:255', // Email optionnel mais doit être valide
            'langue'    => 'required|string|max:255',
        ]);

        // Préparation des données à sauvegarder
        $data = [
            'nom'       => $this->nom,
            'prenom'    => $this->prenom,
            'telephone' => $this->telephone,
            'email'     => $this->email ?: null, // Convertit chaîne vide en null
            'langue'    => $this->langue,
        ];

        if ($this->isEditing) {
            // Mode modification : mise à jour du traducteur existant
            Traducteur::findOrFail($this->traducteur_id)->update($data);
            session()->flash('success', 'Traducteur modifié avec succès.');
        } else {
            // Mode création : insertion d'un nouveau traducteur
            Traducteur::create($data);
            session()->flash('success', 'Traducteur créé avec succès.');
        }

        // Ferme le modal après sauvegarde
        $this->closeModal();
    }

    /**
     * Supprime un traducteur de la base de données.
     * La confirmation est gérée côté vue avec wire:confirm.
     *
     * @param int $id Identifiant du traducteur à supprimer
     */
    public function supprimer($id)
    {
        Traducteur::findOrFail($id)->delete();
        session()->flash('success', 'Traducteur supprimé.');
    }

    
    // RENDU DU COMPOSANT
    

    /**
     * Rendu du composant Livewire.
     * 
     * Charge les traducteurs avec filtre de recherche en temps réel.
     * Utilise le layout admin principal.
     */
    public function render()
    {
        return view('livewire.admin.gestion-traducteurs', [
            // Charge les traducteurs avec filtre de recherche
            'traducteurs' => Traducteur::when($this->search, fn($q) =>
                    // Recherche sur le nom, prénom ou la langue
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('prenom', 'like', '%'.$this->search.'%')
                      ->orWhere('langue', 'like', '%'.$this->search.'%')
                )
                ->latest() // Tri par date de création (plus récent en premier)
                ->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Traducteurs']);
    }
}