<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\Evenement;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CompleterProfilB2B extends Component
{
    public string $zone_geographique       = '';
    public array  $types_partenariat       = [];
    public string $type_partenariat_autre  = '';
    public array  $profils_partenaire      = [];
    public array  $secteurs_recherche      = [];
    public string $secteur_recherche_autre = '';
    public array  $disponibilites          = [];
    public string $secteur_activite        = ''; // ✅ NOUVEAU
    public string $sous_secteur            = ''; // ✅ NOUVEAU

    // Indique si le secteur est auto (depuis l'entreprise) ou manuel
    public bool $secteurAutoEntreprise = false;
    public string $secteurNomEntreprise = '';

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

    public function mount(): void
    {
        $participant = Participant::findForUser(auth()->user());
        if (!$participant) return;

        $this->zone_geographique       = $participant->zone_geographique ?? '';
        $this->types_partenariat       = $participant->types_partenariat ?? [];
        $this->type_partenariat_autre  = $participant->type_partenariat_autre ?? '';
        $this->profils_partenaire      = $participant->profils_partenaire ?? [];
        $this->secteurs_recherche      = $participant->secteurs_recherche ?? [];
        $this->secteur_recherche_autre = $participant->secteur_recherche_autre ?? '';
        $this->disponibilites          = $participant->disponibilites ?? [];
        $this->secteur_activite        = $participant->secteur_activite ?? '';
        $this->sous_secteur            = $participant->sous_secteur ?? '';

        // ✅ Si membre d'une entreprise → secteur auto depuis l'entreprise
        if ($participant->id_entreprise) {
            $entreprise = Entreprise::find($participant->id_entreprise);
            if ($entreprise && $entreprise->secteur_activite) {
                $this->secteur_activite      = $entreprise->secteur_activite;
                $this->sous_secteur          = $entreprise->sous_secteur ?? '';
                $this->secteurAutoEntreprise = true;
                $this->secteurNomEntreprise  = $entreprise->nom;
            }
        }
    }

    public function getJoursEvenementProperty(): array
    {
        $participant = Participant::findForUser(auth()->user());
        if (!$participant || !$participant->id_evenement) return [];

        $evenement = Evenement::find($participant->id_evenement);
        if (!$evenement || !$evenement->date_debut) return [];

        $debut  = Carbon::parse($evenement->date_debut);
        $fin    = Carbon::parse($evenement->date_fin ?? $evenement->date_debut);
        $jours  = [];
        foreach (CarbonPeriod::create($debut, $fin) as $date) {
            $jours[] = $date->toDateString();
        }
        return $jours;
    }

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

    public function enregistrer()
    {
        $rules = ['zone_geographique' => 'required|string'];

        // Secteur obligatoire seulement pour les non-membres
        if (!$this->secteurAutoEntreprise) {
            $rules['secteur_activite'] = 'required|string';
        }

        $this->validate($rules, [
            'zone_geographique.required' => 'La zone géographique est obligatoire.',
            'secteur_activite.required'  => 'Votre secteur d\'activité est obligatoire.',
        ]);

        if (empty($this->types_partenariat)) {
            $this->addError('types_partenariat', 'Choisissez au moins un type de partenariat.');
            return;
        }
        if (empty($this->secteurs_recherche)) {
            $this->addError('secteurs_recherche', 'Choisissez au moins un secteur recherché.');
            return;
        }

        $participant = Participant::findForUser(auth()->user());

        $participant->update([
            'secteur_activite'       => $this->secteur_activite,
            'sous_secteur'           => $this->sous_secteur ?: null,
            'zone_geographique'      => $this->zone_geographique,
            'types_partenariat'      => $this->types_partenariat,
            'type_partenariat_autre' => in_array('Autre', $this->types_partenariat) ? $this->type_partenariat_autre : null,
            'profils_partenaire'     => $this->profils_partenaire,
            'secteurs_recherche'     => $this->secteurs_recherche,
            'secteur_recherche_autre'=> in_array('Autre', $this->secteurs_recherche) ? $this->secteur_recherche_autre : null,
            'disponibilites'         => $this->disponibilites,
        ]);

        session()->flash('success', 'Votre profil B2B a été complété ! Vous pouvez maintenant émettre des souhaits.');

        return redirect()->route('participant.souhaits');
    }

    public function render()
    {
        return view('livewire.participant.completer-profil-b2-b', [
            'joursEvenement' => $this->joursEvenement,
        ])->layout('layouts.participant', ['title' => 'Compléter mon profil B2B']);
    }
}