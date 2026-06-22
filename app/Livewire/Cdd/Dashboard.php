<?php

namespace App\Livewire\Cdd;

use Livewire\Component;
use App\Models\ChefDelegation;
use App\Models\Participant;
use App\Models\Evenement;
use App\Models\Inscription;
use App\Models\Notification;
use App\Models\Paiement;
use App\Models\Recu;
use App\Models\Souhait;

class Dashboard extends Component
{
    public $cdd = null;

    // ── Ajout de membre ──────────────────────────────
    public bool $showModalMembre = false;
    public $id_evenement       = '';
    public string $nom            = '';
    public string $prenom         = '';
    public string $genre          = '';
    public string $email          = '';
    public string $telephone      = '';
    public string $fonction       = '';
    public string $fonction_autre = '';
    public string $date_naissance = '';
    public string $filiere        = '';
    public string $universite     = '';
    public string $pays           = '';
    public string $ville          = '';

    public array $fonctions = [
        'Directeur Général', 'Directeur Commercial', 'PDG', 'Gérant',
        'Responsable Export', 'Responsable Partenariats',
        'Chargé de Développement', 'Représentant', 'Étudiant', 'Autre',
    ];

    public array $pays_liste = [
        'Burkina Faso', 'Côte d\'Ivoire', 'Mali', 'Sénégal',
        'Ghana', 'Togo', 'Bénin', 'Niger', 'Guinée', 'Cameroun',
        'Nigeria', 'France', 'Allemagne', 'États-Unis', 'Chine', 'Autre',
    ];

    // ── Inscrire un membre existant ──────────────────
    public bool $showModalInscrire = false;
    public $membre_a_inscrire_id   = null;
    public $evenement_a_inscrire   = '';

    // ── Paiement pour un membre ──────────────────────
    public bool   $showModalPaiement = false;
    public $inscription_paiement_id  = 0;
    public string $mode_paiement      = 'orange_money';
    public string $numero_cheque      = '';
    public float  $montant_paiement   = 0;
    public float  $montant_brut       = 0;
    public float  $pourcentage_remise = 0;

    // ── Souhait pour un membre ───────────────────────
    public bool $showModalSouhait = false;
    public $membre_souhait_id     = null;
    public string $rechercheCandidat = '';

    public string $alertSuccess = '';
    public string $alertError   = '';

    public function mount(): void
    {
        $this->cdd = ChefDelegation::where('id_user', auth()->id())->first();
    }

    /**
     * Vérifie si la fonction sélectionnée = Étudiant
     * (mb_strtolower pour gérer les accents).
     */
    public function getEstEtudiantProperty(): bool
    {
        $fonctionActive = $this->fonction === 'Autre'
            ? mb_strtolower(trim($this->fonction_autre))
            : mb_strtolower(trim($this->fonction));

        return in_array($fonctionActive, ['étudiant', 'etudiant', 'étudiante', 'etudiante']);
    }

    /**
     * Vérifie si l'événement choisi pour l'ajout est un événement B2B.
     */
    public function getEvenementSelectionneEstB2BProperty(): bool
    {
        if (!$this->id_evenement) return true;
        $evt = Evenement::find($this->id_evenement);
        return ($evt->type_evenement ?? 'avec_b2b') === 'avec_b2b';
    }

    // ════════════════════════════════════════════════
    // AJOUT D'UN NOUVEAU MEMBRE
    // ════════════════════════════════════════════════

    public function openModalMembre(): void
    {
        $this->resetFields();
        $this->showModalMembre = true;
    }

    public function closeModalMembre(): void
    {
        $this->showModalMembre = false;
        $this->resetFields();
    }

    public function resetFields(): void
    {
        $this->id_evenement   = $this->cdd->id_evenement ?? '';
        $this->nom            = '';
        $this->prenom         = '';
        $this->genre          = '';
        $this->email          = '';
        $this->telephone      = '';
        $this->fonction       = '';
        $this->fonction_autre = '';
        $this->date_naissance = '';
        $this->filiere        = '';
        $this->universite     = '';
        $this->pays           = $this->cdd && $this->cdd->pays_zone !== 'Autre' ? $this->cdd->pays_zone : '';
        $this->ville           = '';
        $this->resetErrorBag();
    }

    public function ajouterMembre(): void
    {
        $fonctionFinal = $this->fonction === 'Autre'
            ? $this->fonction_autre
            : $this->fonction;

        $regles = [
            'id_evenement'   => 'required',
            'nom'            => 'required|string|max:255',
            'prenom'         => 'required|string|max:255',
            'telephone'      => 'required|string|max:20',
            'email'          => 'nullable|email|max:255',
            'genre'          => 'nullable|in:homme,femme',
            'date_naissance' => 'nullable|date|before:today',
        ];

        if ($this->estEtudiant) {
            $regles['filiere']    = 'required|string|max:255';
            $regles['universite'] = 'required|string|max:255';
        }

        $this->validate($regles, [
            'filiere.required'    => 'La filière est obligatoire pour un étudiant.',
            'universite.required' => "L'université est obligatoire pour un étudiant.",
        ]);

        if ($this->fonction === 'Autre') {
            $this->fonction = $fonctionFinal;
        }

        $code_acces = strtoupper(substr($this->nom, 0, 3) . rand(1000, 9999));

        $participant = Participant::create([
            'nom'                   => $this->nom,
            'prenom'                => $this->prenom,
            'genre'                 => $this->genre ?: null,
            'email'                 => $this->email ?: null,
            'telephone'             => $this->telephone,
            'fonction'              => $this->fonction ?: null,
            'date_naissance'        => $this->date_naissance ?: null,
            'filiere'               => $this->estEtudiant ? ($this->filiere ?: null) : null,
            'universite'            => $this->estEtudiant ? ($this->universite ?: null) : null,
            'pays'                  => $this->pays ?: null,
            'ville'                 => $this->ville ?: null,
            'code_acces'            => $code_acces,
            'role'                  => 'participant',
            'statut_historique'     => 'actif',
            'statut_preinscription' => 'valide',
            'participation_rdv'     => true,
            'id_evenement'          => $this->id_evenement,
            'chef_delegation_id'    => $this->cdd->id,
        ]);

        $this->creerInscriptionPourEvenement($participant, $this->id_evenement);

        $this->closeModalMembre();
        $this->alertSuccess = "Membre {$this->nom} {$this->prenom} ajouté à votre délégation. Code d'accès : {$code_acces}";
    }

    private function creerInscriptionPourEvenement(Participant $participant, int $idEvenement): void
    {
        $evenement = Evenement::find($idEvenement);
        if (!$evenement) return;

        $montant = $evenement->montant_inscription ?? 0;
        $statut  = 'en_attente';

        if ($evenement->type_paiement == 'gratuit') {
            $montant = 0;
            $statut  = 'paye';
        }

        Inscription::create([
            'id_participant'   => $participant->id,
            'id_evenement'     => $idEvenement,
            'date_inscription' => now()->toDateString(),
            'montant_paye'     => $montant,
            'statut_paiement'  => $statut,
            'statut_presence'  => 'absent',
        ]);
    }

    // ════════════════════════════════════════════════
    // INSCRIRE UN MEMBRE EXISTANT À UN ÉVÉNEMENT
    // ════════════════════════════════════════════════

    public function openModalInscrire(int $participantId): void
    {
        $this->membre_a_inscrire_id = $participantId;
        $this->evenement_a_inscrire = $this->cdd->id_evenement ?? '';
        $this->showModalInscrire    = true;
        $this->resetErrorBag();
    }

    public function closeModalInscrire(): void
    {
        $this->showModalInscrire    = false;
        $this->membre_a_inscrire_id = null;
        $this->evenement_a_inscrire = '';
    }

    public function confirmerInscription(): void
    {
        $this->validate([
            'evenement_a_inscrire' => 'required',
        ], [
            'evenement_a_inscrire.required' => 'Choisissez un événement.',
        ]);

        $participant = Participant::find($this->membre_a_inscrire_id);
        if (!$participant) {
            $this->alertError = 'Membre introuvable.';
            return;
        }

        $dejaInscrit = Inscription::where('id_participant', $participant->id)
            ->where('id_evenement', $this->evenement_a_inscrire)
            ->exists();

        if ($dejaInscrit) {
            $this->alertError = 'Ce membre est déjà inscrit à cet événement.';
            $this->closeModalInscrire();
            return;
        }

        $this->creerInscriptionPourEvenement($participant, $this->evenement_a_inscrire);

        $this->closeModalInscrire();
        $this->alertSuccess = "{$participant->nom} {$participant->prenom} a été inscrit avec succès.";
    }

    // ════════════════════════════════════════════════
    // PAYER POUR UN MEMBRE
    // ════════════════════════════════════════════════

    public function openModalPaiement(int $inscriptionId): void
    {
        $inscription = Inscription::with(['evenement', 'participant'])->find($inscriptionId);
        if (!$inscription) return;

        $this->inscription_paiement_id = $inscription->id;
        $montantBrut = $inscription->evenement->montant_inscription ?? 0;

        $details = $inscription->participant->montantApresRemise($montantBrut);

        $this->montant_brut       = $details['montant_brut'];
        $this->pourcentage_remise = $details['pourcentage'];
        $this->montant_paiement   = $details['montant_net'];
        $this->mode_paiement      = 'orange_money';
        $this->numero_cheque      = '';
        $this->showModalPaiement  = true;
        $this->resetErrorBag();
    }

    public function closeModalPaiement(): void
    {
        $this->showModalPaiement       = false;
        $this->inscription_paiement_id = 0;
    }

    public function confirmerPaiement(): void
    {
        if ($this->mode_paiement === 'cheque') {
            $this->validate([
                'numero_cheque' => 'required|string|min:3|max:50',
            ], [
                'numero_cheque.required' => 'Le numéro de chèque est obligatoire.',
            ]);
        }

        $paiement = Paiement::create([
            'id_inscription' => $this->inscription_paiement_id,
            'montant'        => $this->montant_paiement,
            'date_paiement'  => now()->toDateString(),
            'mode_paiement'  => $this->mode_paiement === 'cheque' ? 'cheque' : 'ligdicash_' . $this->mode_paiement,
            'numero_cheque'  => $this->mode_paiement === 'cheque' ? $this->numero_cheque : null,
            'type_paiement'  => 'individuel',
            'statut'         => 'en_attente',
        ]);

        if ($this->mode_paiement !== 'cheque') {
            Recu::create([
                'id_paiement' => $paiement->id,
                'montant'     => $this->montant_paiement,
                'date'        => now()->toDateString(),
            ]);
        }

        Inscription::find($this->inscription_paiement_id)?->update([
            'montant_paye' => $this->montant_paiement,
        ]);

        $this->closeModalPaiement();
        $this->alertSuccess = 'Paiement soumis pour le membre. En attente de validation par l\'administration.';
    }

    // ════════════════════════════════════════════════
    // ÉMETTRE UN SOUHAIT POUR UN MEMBRE
    // ════════════════════════════════════════════════

    public function openModalSouhait(int $participantId): void
    {
        $this->membre_souhait_id = $participantId;
        $this->rechercheCandidat = '';
        $this->showModalSouhait  = true;
    }

    public function closeModalSouhait(): void
    {
        $this->showModalSouhait  = false;
        $this->membre_souhait_id = null;
    }

    public function emettreSouhaitPourMembre(int $cibleId): void
    {
        $membre = Participant::find($this->membre_souhait_id);
        if (!$membre) return;

        $dejaEmis = Souhait::where('id_participant', $membre->id)
            ->where('id_participant_cible', $cibleId)
            ->exists();

        if ($dejaEmis) {
            $this->alertError = 'Un souhait existe déjà entre ces deux participants.';
            return;
        }

        $dernierePriorite = Souhait::where('id_participant', $membre->id)->max('priorite') ?? 0;

        $souhaitRetour = Souhait::where('id_participant', $cibleId)
            ->where('id_participant_cible', $membre->id)
            ->first();

        $estMutuel = (bool) $souhaitRetour;

        Souhait::create([
            'id_participant'       => $membre->id,
            'id_participant_cible' => $cibleId,
            'id_evenement'         => $membre->id_evenement,
            'priorite'             => $dernierePriorite + 1,
            'type'                 => $estMutuel ? 'mutuel' : 'envoye',
            'statut'               => $estMutuel ? 'accepte' : 'en_attente',
        ]);

        if ($estMutuel) {
            $souhaitRetour->update(['type' => 'mutuel', 'statut' => 'accepte']);

            $cible = Participant::find($cibleId);
            Notification::create([
                'id_participant' => $membre->id,
                'contenu'        => "🎉 Souhait mutuel avec {$cible->nom} {$cible->prenom} ! (émis par votre CDD)",
                'date_envoie'    => now()->toDateString(),
                'type'           => 'systeme',
            ]);
            Notification::create([
                'id_participant' => $cibleId,
                'contenu'        => "🎉 Souhait mutuel avec {$membre->nom} {$membre->prenom} !",
                'date_envoie'    => now()->toDateString(),
                'type'           => 'systeme',
            ]);
        }

        $this->alertSuccess = 'Souhait émis pour le compte du membre.'
            . ($estMutuel ? ' 🎉 Mutuel !' : '');
    }

    public function retirerMembre(int $id): void
    {
        $participant = Participant::where('chef_delegation_id', $this->cdd->id)
            ->where('id', $id)
            ->first();

        if ($participant) {
            $participant->update(['chef_delegation_id' => null]);
            $this->alertSuccess = 'Membre retiré de votre délégation.';
        }
    }

    public function render()
    {
        if (!$this->cdd) {
            return view('livewire.cdd.dashboard', [
                'membres'    => collect(),
                'evenements' => collect(),
                'candidatsSouhait' => collect(),
            ])->layout('layouts.cdd', ['title' => 'Dashboard CDD']);
        }

        $evenements = $this->cdd->id_evenement
            ? Evenement::where('id', $this->cdd->id_evenement)->get()
            : Evenement::where('date_fin', '>=', now()->toDateString())
                ->orderBy('date_debut')
                ->get();

        $membres = Participant::with(['evenement', 'inscriptions.evenement'])
            ->where('chef_delegation_id', $this->cdd->id)
            ->latest()
            ->get();

        $candidatsSouhait = collect();
        if ($this->membre_souhait_id) {
            $membre = Participant::find($this->membre_souhait_id);
            if ($membre && $membre->id_evenement) {
                $idsExistants = Souhait::where('id_participant', $membre->id)
                    ->pluck('id_participant_cible')->toArray();

                $candidatsSouhait = Participant::with('entreprise')
                    ->where('id_evenement', $membre->id_evenement)
                    ->where('id', '!=', $membre->id)
                    ->where('participation_rdv', true)
                    ->when($this->rechercheCandidat, fn($q) =>
                        $q->where(function ($q) {
                            $q->where('nom', 'like', '%' . $this->rechercheCandidat . '%')
                              ->orWhere('prenom', 'like', '%' . $this->rechercheCandidat . '%');
                        })
                    )
                    ->get()
                    ->map(function ($c) use ($idsExistants) {
                        $c->souhait_emis = in_array($c->id, $idsExistants);
                        return $c;
                    });
            }
        }

        return view('livewire.cdd.dashboard', [
            'membres'          => $membres,
            'evenements'       => $evenements,
            'candidatsSouhait' => $candidatsSouhait,
        ])->layout('layouts.cdd', ['title' => 'Dashboard CDD']);
    }
}