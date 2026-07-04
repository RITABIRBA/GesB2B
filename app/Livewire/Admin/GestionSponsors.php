<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Sponsor;
use App\Models\Evenement;
use App\Models\Entreprise;

class GestionSponsors extends Component
{
    public string $search          = '';
    public string $filtre_niveau   = '';
    public string $filtre_evenement = '';

    public bool  $showModal  = false;
    public bool  $isEditing  = false;
    public $sponsor_id       = null;

    // Champs formulaire
    public string $type_entite        = 'entreprise';
    public string $nom                = '';
    public string $nom_contact        = '';
    public string $email              = '';
    public string $telephone          = '';
    public string $site_web           = '';
    public string $description        = '';
    public string $niveau             = 'partenaire';
    public int    $nb_stands_gratuits = 0;
    public int    $nb_badges_vip      = 0;
    public float  $remise_inscription = 0;
    public string $autres_avantages   = '';
    public string $id_evenement       = '';
    public string $id_entreprise      = '';

    // Modal détail
    public bool  $showModalDetail = false;
    public $sponsor_courant       = null;

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
        $this->sponsor_id        = null;
        $this->type_entite       = 'entreprise';
        $this->nom               = '';
        $this->nom_contact       = '';
        $this->email             = '';
        $this->telephone         = '';
        $this->site_web          = '';
        $this->description       = '';
        $this->niveau            = 'partenaire';
        $this->nb_stands_gratuits = 0;
        $this->nb_badges_vip     = 0;
        $this->remise_inscription = 0;
        $this->autres_avantages  = '';
        $this->id_evenement      = '';
        $this->id_entreprise     = '';
        $this->resetErrorBag();
    }

    public function modifier(int $id): void
    {
        $s = Sponsor::findOrFail($id);

        $this->sponsor_id         = $s->id;
        $this->type_entite        = $s->type_entite;
        $this->nom                = $s->nom;
        $this->nom_contact        = $s->nom_contact ?? '';
        $this->email              = $s->email ?? '';
        $this->telephone          = $s->telephone ?? '';
        $this->site_web           = $s->site_web ?? '';
        $this->description        = $s->description ?? '';
        $this->niveau             = $s->niveau;
        $this->nb_stands_gratuits = $s->nb_stands_gratuits;
        $this->nb_badges_vip      = $s->nb_badges_vip;
        $this->remise_inscription = $s->remise_inscription;
        $this->autres_avantages   = $s->autres_avantages ?? '';
        $this->id_evenement       = $s->id_evenement;
        $this->id_entreprise      = $s->id_entreprise ?? '';

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function voirDetail(int $id): void
    {
        $this->sponsor_courant  = Sponsor::with('evenement', 'entreprise')->findOrFail($id);
        $this->showModalDetail  = true;
    }

    public function fermerDetail(): void
    {
        $this->showModalDetail = false;
        $this->sponsor_courant = null;
    }

    public function sauvegarder(): void
    {
        $this->validate([
            'nom'         => 'required|string|max:255',
            'niveau'      => 'required|in:principal,associe,partenaire,supporter',
            'type_entite' => 'required|in:entreprise,personne',
            'id_evenement'=> 'required|exists:evenements,id',
            'email'       => 'nullable|email|max:255',
            'telephone'   => 'nullable|string|max:20',
            'nb_stands_gratuits' => 'integer|min:0',
            'nb_badges_vip'      => 'integer|min:0',
            'remise_inscription' => 'numeric|min:0|max:100',
        ], [
            'nom.required'          => 'Le nom est obligatoire.',
            'niveau.required'       => 'Le niveau est obligatoire.',
            'id_evenement.required' => 'L\'événement est obligatoire.',
        ]);

        $data = [
            'id_evenement'       => $this->id_evenement,
            'type_entite'        => $this->type_entite,
            'nom'                => $this->nom,
            'nom_contact'        => $this->nom_contact ?: null,
            'email'              => $this->email ?: null,
            'telephone'          => $this->telephone ?: null,
            'site_web'           => $this->site_web ?: null,
            'description'        => $this->description ?: null,
            'niveau'             => $this->niveau,
            'nb_stands_gratuits' => $this->nb_stands_gratuits,
            'nb_badges_vip'      => $this->nb_badges_vip,
            'remise_inscription' => $this->remise_inscription,
            'autres_avantages'   => $this->autres_avantages ?: null,
            'id_entreprise'      => $this->id_entreprise ?: null,
        ];

        if ($this->isEditing) {
            Sponsor::findOrFail($this->sponsor_id)->update($data);
            session()->flash('success', 'Sponsor modifié avec succès.');
        } else {
            Sponsor::create($data);
            session()->flash('success', 'Sponsor ajouté avec succès.');
        }

        $this->closeModal();
    }

    public function supprimer(int $id): void
    {
        Sponsor::findOrFail($id)->delete();
        session()->flash('success', 'Sponsor supprimé.');
    }

    public function render()
    {
        $sponsors = Sponsor::with('evenement', 'entreprise')
            ->when($this->search, fn($q) =>
                $q->where('nom', 'like', '%'.$this->search.'%')
                  ->orWhere('nom_contact', 'like', '%'.$this->search.'%')
            )
            ->when($this->filtre_niveau, fn($q) =>
                $q->where('niveau', $this->filtre_niveau)
            )
            ->when($this->filtre_evenement, fn($q) =>
                $q->where('id_evenement', $this->filtre_evenement)
            )
            ->orderByRaw("FIELD(niveau, 'principal', 'associe', 'partenaire', 'supporter')")
            ->get();

        return view('livewire.admin.gestion-sponsors', [
            'sponsors'    => $sponsors,
            'evenements'  => Evenement::orderBy('nom')->get(),
            'entreprises' => Entreprise::orderBy('nom')->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Sponsors']);
    }
}