<?php

namespace App\Livewire\Superviseur;

use Livewire\Component;
use App\Models\Inscription;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\Evenement;
use App\Models\User;
use App\Models\Notification;
use App\Models\Badge;
use App\Models\TypeBadge;
use App\Models\Recu;
use App\Mail\PreinscriptionValidee;
use App\Mail\PreinscriptionRejetee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GestionInscriptions extends Component
{
    public string $onglet = 'preinscrits';

    public $search           = '';
    public $filtre_statut    = '';
    public $filtre_evenement = '';

    public bool $showModalPreinscription = false;
    public $preinscription_courante      = null;

    public bool   $showModalRejet = false;
    public string $motif_rejet    = '';

    public bool   $showModalCompte   = false;
    public string $compte_email      = '';
    public string $compte_password   = '';
    public string $compte_code_acces = '';
    public bool   $compte_has_email  = false;

    public function changerOnglet(string $onglet): void
    {
        $this->onglet = $onglet;
        $this->search = '';
    }

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

        $participant = Participant::with('evenement')->findOrFail($this->preinscription_courante->id);

        // ✅ 1 — Marquer la préinscription comme validée
        $participant->update(['statut_preinscription' => 'valide']);

        // ✅ 2 — Si représentant d'entreprise → valider aussi l'entreprise
        if ($participant->id_entreprise) {
            Entreprise::where('id', $participant->id_entreprise)
                ->update(['statut_validation' => 'valide']);
        }

        // ✅ 3 — Créer ou mettre à jour l'inscription avec statut_presence = 'present'
        if ($participant->id_evenement) {
            $evenement = $participant->evenement;

            $montant        = $evenement->montant_inscription ?? 0;
            $statutPaiement = 'en_attente';

            if ($evenement->type_paiement === 'gratuit') {
                $montant        = 0;
                $statutPaiement = 'paye';
            } elseif ($evenement->type_paiement === 'par_entreprise' && $participant->id_entreprise) {
                $montant        = 0;
                $statutPaiement = 'paye';
            }

            $inscription = Inscription::firstOrCreate(
                [
                    'id_participant' => $participant->id,
                    'id_evenement'   => $participant->id_evenement,
                ],
                [
                    'date_inscription' => now()->toDateString(),
                    'montant_paye'     => $montant,
                    'statut_paiement'  => $statutPaiement,
                    'statut_presence'  => 'present',
                ]
            );

            if (!$inscription->wasRecentlyCreated) {
                $inscription->update([
                    'statut_presence' => 'present',
                    'statut_paiement' => $statutPaiement,
                    'montant_paye'    => $montant,
                ]);
            }

            // ✅ Si gratuit → générer badge directement
            if ($statutPaiement === 'paye') {
                $this->genererBadge($inscription->fresh(['participant', 'evenement']));
            }
        }

        // ✅ 4 — Créer le compte User
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
                    Log::error('Création compte User échouée', ['erreur' => $e->getMessage()]);
                }
            }
        }

        // ✅ 5 — Notification
        Notification::create([
            'id_participant' => $participant->id,
            'contenu'        => 'Votre préinscription a été validée ! Code d\'accès : ' . $participant->code_acces,
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        // ✅ 6 — Email
        if ($participant->email) {
            try {
                Mail::to($participant->email)->send(new PreinscriptionValidee($participant, $password_genere));
            } catch (\Exception $e) {
                Log::error('Email validation échoué', ['erreur' => $e->getMessage()]);
            }
        }

        $this->compte_email      = $participant->email ?? '';
        $this->compte_password   = $password_genere;
        $this->compte_code_acces = $participant->code_acces;
        $this->compte_has_email  = !empty($participant->email);

        $this->fermerValidationPreinscription();
        $this->showModalCompte = true;

        session()->flash('success', 'Préinscription validée !');
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
            'contenu'        => 'Votre préinscription a été rejetée. Motif : ' . $this->motif_rejet,
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        if ($participant->email) {
            try {
                Mail::to($participant->email)->send(new PreinscriptionRejetee($participant, 'Business Forum', $this->motif_rejet));
            } catch (\Exception $e) {}
        }

        $this->fermerRejetPreinscription();
        session()->flash('success', 'Préinscription rejetée.');
    }

    public function closeModalCompte(): void
    {
        $this->showModalCompte = false;
    }

    public function validerPaiement($id)
    {
        $inscription = Inscription::with(['paiement', 'participant', 'evenement'])->findOrFail($id);

        // ✅ 1 — Valider l'inscription
        $inscription->update(['statut_paiement' => 'paye']);

        // ✅ 2 — Valider le paiement soumis
        if ($inscription->paiement) {
            $inscription->paiement->update(['statut' => 'valide']);

            // ✅ 3 — Générer le reçu si pas encore fait
            if (!$inscription->paiement->recu) {
                Recu::create([
                    'id_paiement' => $inscription->paiement->id,
                    'date'        => now()->toDateString(),
                    'montant'     => $inscription->paiement->montant,
                ]);
            }
        }

        // ✅ 4 — Générer le badge
        $this->genererBadge($inscription);

        session()->flash('success', 'Paiement validé ! Badge généré automatiquement.');
    }

    private function genererBadge(Inscription $inscription): void
    {
        $participant = $inscription->participant;
        if (!$participant) return;

        if (Badge::where('id_participant', $participant->id)->exists()) return;

        $libelle = match($participant->role) {
            'vip'          => 'VIP',
            'organisateur' => 'Organisateur',
            'exposant'     => 'Exposant',
            'representant' => 'Représentant',
            default        => 'Visiteur',
        };

        $typeBadge = TypeBadge::firstOrCreate(
            ['libelle' => $libelle],
            ['description' => 'Badge ' . $libelle]
        );

        $qrCode = strtoupper(
            substr($participant->nom, 0, 2) .
            substr($participant->prenom ?? 'XX', 0, 2) .
            '-' . $inscription->id_evenement .
            '-' . Str::random(6)
        );

        Badge::create([
            'id_participant' => $participant->id,
            'id_type_badge'  => $typeBadge->id,
            'qr_code'        => $qrCode,
        ]);
    }

    public function annuler($id)
    {
        Inscription::findOrFail($id)->update(['statut_paiement' => 'annule']);
        session()->flash('success', 'Inscription annulée.');
    }

    public function marquerPresent($id)
    {
        Inscription::findOrFail($id)->update(['statut_presence' => 'present']);
        session()->flash('success', 'Présence marquée.');
    }

    public function marquerAbsent($id)
    {
        Inscription::findOrFail($id)->update(['statut_presence' => 'absent']);
        session()->flash('success', 'Absence marquée.');
    }

    public function render()
    {
        return view('livewire.superviseur.gestion-inscriptions', [
            'preinscrits' => Participant::with(['entreprise', 'evenement'])
                ->where('statut_preinscription', 'en_attente')
                ->when($this->search, fn($q) =>
                    $q->where('nom', 'like', '%'.$this->search.'%')
                      ->orWhere('prenom', 'like', '%'.$this->search.'%')
                )
                ->when($this->filtre_evenement, fn($q) =>
                    $q->where('id_evenement', $this->filtre_evenement)
                )
                ->latest()
                ->get(),

            'inscriptions' => Inscription::with(['participant', 'participant.entreprise', 'evenement', 'paiement'])
                ->whereHas('participant', fn($q) =>
                    $q->where('statut_preinscription', 'valide')
                )
                ->when($this->filtre_statut, fn($q) =>
                    $q->where('statut_paiement', $this->filtre_statut)
                )
                ->when($this->filtre_evenement, fn($q) =>
                    $q->where('id_evenement', $this->filtre_evenement)
                )
                ->when($this->search, fn($q) =>
                    $q->whereHas('participant', fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )
                )
                ->latest()
                ->get(),

            'evenements' => Evenement::orderBy('nom')->get(),

            'nbPreinscrits' => Participant::where('statut_preinscription', 'en_attente')->count(),
            'nbInscrits'    => Inscription::whereHas('participant', fn($q) =>
                $q->where('statut_preinscription', 'valide')
            )->count(),
        ])->layout('layouts.superviseur', ['title' => 'Gestion des Inscriptions']);
    }
}