<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\Evenement;
use App\Models\User;
use App\Models\Notification;
use App\Models\Inscription;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Mail\PreinscriptionValidee;
use App\Mail\PreinscriptionRejetee;

class GestionParticipants extends Component
{
    public $participant_id;
    public $id_entreprise = '';
    public $id_evenement = '';
    public string $nom = '';
    public string $prenom = '';
    public string $genre = '';
    public string $fonction = '';
    public string $ifu = '';
    public string $email = '';
    public string $telephone = '';
    public string $pays = '';
    public string $ville = '';
    public string $role = 'representant';
    public string $statut_historique = 'actif';
    public bool $participation_rdv = true;

    // Infos optionnelles entreprise
    public string $secteur_activite = '';
    public string $secteur_activite_autre = '';
    public string $sous_secteur = '';
    public string $description_activites = '';
    public string $principaux_produits = '';
    public string $annee_creation = '';
    public string $nombre_salaries = '';
    public string $chiffre_affaires = '';

    // Préférences de jumelage / B2B
    public string $zone_geographique = '';
    public array $disponibilites = [];
    public array $types_partenariat = [];
    public string $type_partenariat_autre = '';
    public array $profils_partenaire = [];
    public array $secteurs_recherche = [];
    public string $secteur_recherche_autre = '';
    public string $objectif_participation = '';
    
    public $id_chef_delegation = '';

    // Modals & États
    public bool $showModal = false;
    public bool $isEditing = false;
    public string $search = '';
    public string $filtre_evenement = '';
    public string $filtre_preinscription = '';
    public string $entreprise_trouvee = '';

    public bool $showModalCompte = false;
    public string $compte_email = '';
    public string $compte_password = '';
    public string $compte_code_acces = '';
    public bool $compte_has_email = false;

    // ✅ NOUVEAU : validation/rejet de préinscription
    public bool $showModalPreinscription = false;
    public $preinscription_courante = null;
    public bool $showModalRejet = false;
    public string $motif_rejet = '';

    // --- OPTIONS DES LISTES DÉROULANTES ---
    
    public array $joursDisponibles = [
        'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'
    ];

    public array $roles = ['representant', 'membre'];

    public array $secteurs = [
        'Agriculture et agro-alimentaire', 'Environnement', 'Industrie textile',
        'Biens de consommation', 'Energie', 'Formation', 'Tourisme', 'TIC',
        'Sous-traitance', 'Artisanat', 'Distribution', 'Prestation',
        'Industrie manufacturière', 'Enseignement', 'Services aux entreprises',
        'BTP', 'Activités médicales et pharmaceutiques', 'Autre',
    ];

    public array $typesPartenariatOptions = [
        'Alliance commerciale', 'Alliance financière', 'Alliance industrielle', 'Autre',
    ];

    public array $profilsPartenariatOptions = [
        'Consultant', 'Distributeur', 'Exportateur', 'Fabricant / Producteur',
        'Investisseur', 'Importateur', 'Prestataire de service', 'Sous-traitant',
        'Innovation', 'R&D',
    ];

    public array $zonesGeographiques = [
        'Locale', 'Nationale', 'Régionale (CEDEAO)', 'Africaine', 'Internationale',
    ];

    public array $pays_liste = [
        'Bénin', 'Burkina Faso', 'Cap-Vert', 'Côte d\'Ivoire', 'Gambie', 'Ghana', 'Guinée', 'Guinée-Bissau', 
        'Liberia', 'Mali', 'Mauritanie', 'Niger', 'Nigeria', 'Sénégal', 'Sierra Leone', 'Togo', 'Angola', 
        'Cameroun', 'Congo', 'Gabon', 'Guinée équatoriale', 'République centrafricaine', 'République démocratique du Congo', 
        'Tchad', 'Burundi', 'Djibouti', 'Érythrée', 'Éthiopie', 'Kenya', 'Madagascar', 'Malawi', 'Maurice', 
        'Mozambique', 'Ouganda', 'Rwanda', 'Seychelles', 'Somalie', 'Soudan', 'Soudan du Sud', 'Tanzanie', 
        'Zambie', 'Zimbabwe', 'Algérie', 'Égypte', 'Libye', 'Maroc', 'Tunisie', 'Afrique du Sud', 'Botswana', 
        'Eswatini', 'Lesotho', 'Namibie', 'Allemagne', 'Autriche', 'Belgique', 'Danemark', 'Espagne', 
        'Finlande', 'France', 'Grèce', 'Irlande', 'Italie', 'Luxembourg', 'Norvège', 'Pays-Bas', 'Pologne', 
        'Portugal', 'Royaume-Uni', 'Russie', 'Suède', 'Suisse', 'Turquie', 'Ukraine', 'Argentine', 'Bolivie', 
        'Brésil', 'Canada', 'Chili', 'Colombie', 'Cuba', 'États-Unis', 'Mexique', 'Pérou', 'Venezuela', 
        'Arabie Saoudite', 'Bangladesh', 'Chine', 'Corée du Sud', 'Émirats arabes unis', 'Inde', 'Indonésie', 
        'Iran', 'Irak', 'Israël', 'Japon', 'Jordanie', 'Liban', 'Malaisie', 'Pakistan', 'Philippines', 
        'Qatar', 'Singapour', 'Thaïlande', 'Vietnam', 'Australie', 'Nouvelle-Zélande', 'Autre'
    ];

    public array $villes_par_pays = [
        'Burkina Faso'   => ['Ouagadougou', 'Bobo-Dioulasso', 'Koudougou', 'Banfora', 'Ouahigouya', 'Pouytenga', 'Kaya', 'Tenkodogo', 'Fada N\'Gourma', 'Dédougou', 'Ziniaré', 'Kongoussi', 'Autre'],
        'Bénin'          => ['Cotonou', 'Porto-Novo', 'Parakou', 'Abomey-Calavi', 'Djougou', 'Bohicon', 'Autre'],
        'Côte d\'Ivoire' => ['Abidjan', 'Bouaké', 'Daloa', 'San-Pédro', 'Yamoussoukro', 'Korhogo', 'Autre'],
        'Mali'           => ['Bamako', 'Sikasso', 'Mopti', 'Koutiala', 'Kayes', 'Ségou', 'Gao', 'Autre'],
        'Niger'          => ['Niamey', 'Zinder', 'Maradi', 'Tahoua', 'Agadez', 'Autre'],
        'Sénégal'        => ['Dakar', 'Thiès', 'Kaolack', 'Ziguinchor', 'Saint-Louis', 'Autre'],
        'Togo'           => ['Lomé', 'Sokodé', 'Kara', 'Atakpamé', 'Kpalimé', 'Autre'],
        'Ghana'          => ['Accra', 'Kumasi', 'Tamale', 'Sekondi-Takoradi', 'Autre'],
        'Nigeria'        => ['Lagos', 'Kano', 'Ibadan', 'Abuja', 'Port Harcourt', 'Autre'],
        'France'         => ['Paris', 'Marseille', 'Lyon', 'Toulouse', 'Nice', 'Autre'],
        'Autre'          => ['Autre'],
    ];

    public function getVillesDisponiblesProperty(): array
    {
        return $this->villes_par_pays[$this->pays] ?? ['Autre'];
    }

    public function updatedPays(): void
    {
        $this->ville = '';
    }

    // --- GESTION DES SELECTIONS MULTIPLES (TOGGLES) ---

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

    public function toggleDisponibilite(string $jour): void
    {
        if (in_array($jour, $this->disponibilites)) {
            $this->disponibilites = array_values(array_filter($this->disponibilites, fn($d) => $d !== $jour));
        } else {
            $this->disponibilites[] = $jour;
        }
    }

    // --- MODALS ACTIONS ---

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

    public function closeModalCompte(): void
    {
        $this->showModalCompte = false;
    }

    public function resetFields(): void
    {
        $this->participant_id = null;
        $this->id_entreprise = '';
        $this->id_evenement = '';
        $this->nom = '';
        $this->prenom = '';
        $this->genre = '';
        $this->fonction = '';
        $this->ifu = '';
        $this->email = '';
        $this->telephone = '';
        $this->pays = '';
        $this->ville = '';
        $this->role = 'representant';
        $this->statut_historique = 'actif';
        $this->participation_rdv = true;
        $this->secteur_activite = '';
        $this->secteur_activite_autre = '';
        $this->sous_secteur = '';
        $this->description_activites = '';
        $this->principaux_produits = '';
        $this->annee_creation = '';
        $this->nombre_salaries = '';
        $this->chiffre_affaires = '';
        $this->zone_geographique = '';
        $this->disponibilites = [];
        $this->types_partenariat = [];
        $this->type_partenariat_autre = '';
        $this->profils_partenaire = [];
        $this->secteurs_recherche = [];
        $this->secteur_recherche_autre = '';
        $this->objectif_participation = '';
        $this->id_chef_delegation = '';
        $this->entreprise_trouvee = '';
        $this->resetErrorBag();
    }

    // --- PERSISTENCE (CRUD OPERATORS) ---

    public function modifier(int $id): void
    {
        $p = Participant::findOrFail($id);
        $this->participant_id = $p->id;
        $this->id_entreprise  = $p->id_entreprise;
        $this->id_evenement   = $p->id_evenement;
        $this->nom            = $p->nom;
        $this->prenom         = $p->prenom;
        $this->genre          = $p->genre ?? '';
        $this->fonction       = $p->fonction ?? '';
        $this->email          = $p->email ?? '';
        $this->telephone      = $p->telephone;
        $this->pays           = $p->pays ?? '';
        $this->ville          = $p->ville ?? '';
        $this->role           = $p->role;
        $this->statut_historique = $p->statut_historique;
        $this->participation_rdv = (bool)$p->participation_rdv;
        
        $this->zone_geographique       = $p->zone_geographique ?? '';
        $this->disponibilites          = $p->disponibilites ?? [];
        $this->types_partenariat       = $p->types_partenariat ?? [];
        $this->type_partenariat_autre  = $p->type_partenariat_autre ?? '';
        $this->profils_partenaire      = $p->profils_partenaire ?? [];
        $this->secteurs_recherche      = $p->secteurs_recherche ?? [];
        $this->secteur_recherche_autre = $p->secteur_recherche_autre ?? '';
        $this->objectif_participation  = $p->objectif_participation ?? '';
        $this->id_chef_delegation      = $p->id_chef_delegation ?? '';

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function supprimer(int $id): void
    {
        Participant::findOrFail($id)->delete();
        session()->flash('success', 'Participant supprimé avec succès.');
    }

    // ✅ NOUVEAU : validation / rejet de préinscription (sans restriction)

    public function ouvrirValidationPreinscription(int $id): void
    {
        $this->preinscription_courante = Participant::with('entreprise', 'evenement')->findOrFail($id);
        $this->showModalPreinscription = true;
    }

    public function fermerValidationPreinscription(): void
    {
        $this->showModalPreinscription = false;
        $this->preinscription_courante = null;
    }

    public function validerPreinscription(): void
    {
        if (!$this->preinscription_courante) return;

        $participant = Participant::findOrFail($this->preinscription_courante->id);

        $participant->update(['statut_preinscription' => 'valide']);

        $password_genere = null;

        if ($participant->email) {
            $userExiste = User::where('email', $participant->email)->exists();

            if (!$userExiste) {
                try {
                    $password_genere = substr(str_shuffle(
                        'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
                    ), 0, 8);

                    $user = User::create([
                        'name'     => $participant->nom . ' ' . $participant->prenom,
                        'email'    => $participant->email,
                        'password' => Hash::make($password_genere),
                    ]);

                    $user->assignRole($participant->id_entreprise ? 'entreprise' : 'participant');
                } catch (\Exception $e) {
                    $password_genere = null;
                    Log::error('Création compte échouée', ['erreur' => $e->getMessage()]);
                }
            }
        }

        Notification::create([
            'id_participant' => $participant->id,
            'contenu'        => '✅ Votre préinscription a été validée ! '
                . 'Vous pouvez vous connecter avec votre code d\'accès : '
                . $participant->code_acces
                . ($participant->email ? ' ou via votre email.' : '.'),
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        if ($participant->email) {
            try {
                Mail::to($participant->email)->send(
                    new PreinscriptionValidee($participant, $password_genere)
                );
            } catch (\Exception $e) {
                Log::error('Email validation échoué', [
                    'participant_id' => $participant->id,
                    'erreur'         => $e->getMessage(),
                ]);
            }
        }

        $this->compte_email      = $participant->email ?? '';
        $this->compte_password   = $password_genere;
        $this->compte_code_acces = $participant->code_acces;
        $this->compte_has_email  = !empty($participant->email);

        $this->fermerValidationPreinscription();
        $this->showModalCompte = true;

        session()->flash('success', 'Préinscription validée ! Le compte a été créé.');
    }

    public function ouvrirRejetPreinscription(int $id): void
    {
        $this->preinscription_courante = Participant::findOrFail($id);
        $this->motif_rejet             = '';
        $this->showModalRejet          = true;
    }

    public function fermerRejetPreinscription(): void
    {
        $this->showModalRejet          = false;
        $this->preinscription_courante = null;
        $this->motif_rejet             = '';
    }

    public function rejeterPreinscription(): void
    {
        $this->validate([
            'motif_rejet' => 'required|min:5',
        ], [
            'motif_rejet.required' => 'Veuillez indiquer le motif du rejet.',
            'motif_rejet.min'      => 'Le motif est trop court.',
        ]);

        $participant = Participant::findOrFail($this->preinscription_courante->id);

        $participant->update(['statut_preinscription' => 'rejete']);

        Notification::create([
            'id_participant' => $participant->id,
            'contenu'        => '❌ Votre préinscription a été rejetée. Motif : ' . $this->motif_rejet,
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        if ($participant->email) {
            try {
                Mail::to($participant->email)->send(
                    new PreinscriptionRejetee($participant, 'Business Forum', $this->motif_rejet)
                );
            } catch (\Exception $e) {
                Log::error('Email rejet échoué', ['erreur' => $e->getMessage()]);
            }
        }

        $this->fermerRejetPreinscription();
        session()->flash('success', 'Préinscription rejetée. Le participant a été notifié.');
    }

    public function render()
    {
        return view('livewire.superviseur.gestion-participants', [
            'participants' => Participant::with(['entreprise', 'evenement'])->when($this->search, function($q) {
                $q->where('nom', 'like', '%' . $this->search . '%')
                  ->orWhere('prenom', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->filtre_evenement, fn($q) => $q->where('id_evenement', $this->filtre_evenement))
            ->when($this->filtre_preinscription, fn($q) => $q->where('statut_preinscription', $this->filtre_preinscription))
            ->latest()->get(),
            'entreprises'       => Entreprise::orderBy('nom')->get(),
            'evenements'        => Evenement::orderBy('nom')->get(),
            'chefsDelegation'   => Participant::where('role', 'chef_delegation')->orderBy('nom')->get(),
            'villesDisponibles' => $this->villesDisponibles,
        ])->layout('layouts.superviseur', ['title' => 'Gestion des Participants']);
    }
}