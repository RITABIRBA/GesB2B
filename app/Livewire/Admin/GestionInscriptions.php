<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Inscription;
use App\Models\Participant;
use App\Models\Evenement;
use App\Models\User;
use App\Models\Notification;
use App\Mail\PreinscriptionValidee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
        $this->onglet  = $onglet;
        $this->search  = '';
    }

    // ════════════════════════════════════════════════════════
    // PRÉINSCRIPTIONS
    // ════════════════════════════════════════════════════════

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

        $participant->update([
            'statut_preinscription' => 'valide',
        ]);

        $password_genere = null;
        $userExiste = $participant->email
            ? User::where('email', $participant->email)->exists()
            : false;

        if ($participant->email && !$userExiste) {
            $password_genere = substr(str_shuffle(
                'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
            ), 0, 8);

            $user = User::create([
                'name'     => $participant->nom . ' ' . $participant->prenom,
                'email'    => $participant->email,
                'password' => Hash::make($password_genere),
            ]);

            $user->assignRole($participant->id_entreprise ? 'entreprise' : 'participant');
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

        // ✅ ENVOI EMAIL AUTOMATIQUE
        if ($participant->email) {
            try {
                Mail::to($participant->email)->send(
                    new PreinscriptionValidee($participant, $password_genere)
                );
            } catch (\Exception $e) {
                // L'email a échoué mais on continue le process
                session()->flash('warning', 'Compte créé mais l\'email n\'a pas pu être envoyé.');
            }
        }

        $this->compte_email      = $participant->email ?? '';
        $this->compte_password   = $password_genere;
        $this->compte_code_acces = $participant->code_acces;
        $this->compte_has_email  = !empty($participant->email);

        $this->fermerValidationPreinscription();
        $this->showModalCompte = true;

        session()->flash('success', 'Préinscription validée ! Le compte a été créé et un email a été envoyé.');
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

        $participant->update([
            'statut_preinscription' => 'rejete',
        ]);

        Notification::create([
            'id_participant' => $participant->id,
            'contenu'        => '❌ Votre préinscription a été rejetée. Motif : '
                . $this->motif_rejet,
            'date_envoie'    => now()->toDateString(),
            'type'           => 'systeme',
        ]);

        $this->fermerRejetPreinscription();
        session()->flash('success', 'Préinscription rejetée. Le participant a été notifié.');
    }

    public function closeModalCompte(): void
    {
        $this->showModalCompte = false;
    }

    // ════════════════════════════════════════════════════════
    // INSCRIPTIONS (validées)
    // ════════════════════════════════════════════════════════

    public function validerPaiement($id)
    {
        $inscription = Inscription::findOrFail($id);
        $inscription->update(['statut_paiement' => 'paye']);
        session()->flash('success', 'Paiement validé avec succès.');
    }

    public function annuler($id)
    {
        Inscription::findOrFail($id)->update([
            'statut_paiement' => 'annule',
        ]);
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
        return view('livewire.admin.gestion-inscriptions', [
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
        ])->layout('layouts.admin', ['title' => 'Gestion des Inscriptions']);
    }
}