<?php

namespace App\Livewire\Entreprise;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\Evenement;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Recu;
use App\Models\Badge;
use App\Models\TypeBadge;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MesParticipants extends Component
{
    public $participant_id;
    public $id_evenement = '';
    public $nom = '';
    public $prenom = '';
    public $genre = '';
    public $fonction = '';
    public $email = '';
    public $telephone = '';
    public $role = 'exposant';
    public $showModal = false;
    public $isEditing = false;
    public $search = '';

    // Paiement groupé
    public $showPaiementGroupeModal = false;
    public $id_evenement_paiement   = '';
    public $mode_paiement           = 'orange_money';
    public $telephone_paiement      = '';
    public $otp_code                = '';
    public $otp_saisi               = '';
    public $etape_paiement          = 1;
    public $carte_numero            = '';
    public $carte_nom               = '';
    public $carte_expiration        = '';
    public $carte_cvv               = '';
    public $montant_total           = 0;
    public $participants_a_payer    = [];

    public $roles = ['exposant', 'participant'];

    private function getEntreprise()
    {
        return Entreprise::where('email_responsable', auth()->user()->email)->first()
            ?? Entreprise::where('nom', auth()->user()->name)->first();
    }

    // =========================================================
    // VALIDATION ADHÉSION
    // =========================================================

    public function accepterAdhesion($id)
    {
        Participant::findOrFail($id)->update([
            'statut_adhesion' => 'accepte',
        ]);
        session()->flash('success', 'Demande d\'adhésion acceptée !');
    }

    public function rejeterAdhesion($id)
    {
        Participant::findOrFail($id)->update([
            'statut_adhesion' => 'rejete',
            'id_entreprise'   => null,
        ]);
        session()->flash('success', 'Demande d\'adhésion rejetée.');
    }

    // =========================================================
    // MODAL PARTICIPANT
    // =========================================================

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
        $this->participant_id = null;
        $this->id_evenement   = '';
        $this->nom            = '';
        $this->prenom         = '';
        $this->genre          = '';
        $this->fonction       = '';
        $this->email          = '';
        $this->telephone      = '';
        $this->role           = 'exposant';
        $this->resetErrorBag();
    }

    // =========================================================
    // PAIEMENT GROUPÉ
    // =========================================================

    public function openPaiementGroupe($id_evenement)
    {
        $entreprise = $this->getEntreprise();
        if (!$entreprise) return;

        $evenement = Evenement::find($id_evenement);
        if (!$evenement) return;

        $this->participants_a_payer = Inscription::with('participant')
            ->whereHas('participant', fn($q) =>
                $q->where('id_entreprise', $entreprise->id)
                  ->where('id_evenement', $id_evenement)
            )
            ->where('statut_paiement', '!=', 'paye')
            ->get();

        $this->montant_total           = $evenement->montant_inscription;
        $this->id_evenement_paiement   = $id_evenement;
        $this->etape_paiement          = 1;
        $this->mode_paiement           = 'orange_money';
        $this->showPaiementGroupeModal = true;
    }

    public function closePaiementGroupe()
    {
        $this->showPaiementGroupeModal = false;
        $this->etape_paiement          = 1;
        $this->participants_a_payer    = [];
        $this->montant_total           = 0;
        $this->telephone_paiement      = '';
        $this->otp_saisi               = '';
        $this->otp_code                = '';
    }

    public function envoyerOtp()
    {
        $this->validate([
            'telephone_paiement' => 'required|string|min:8|max:15',
        ]);
        $this->otp_code       = rand(100000, 999999);
        $this->etape_paiement = 3;
    }

    public function confirmerOtp()
    {
        $this->validate(['otp_saisi' => 'required|string']);

        if ($this->otp_saisi != $this->otp_code) {
            $this->addError('otp_saisi', 'Code OTP incorrect.');
            return;
        }

        $this->enregistrerPaiementGroupe();
    }

    public function payerCarte()
    {
        $this->validate([
            'carte_numero'     => 'required|string|min:16|max:19',
            'carte_nom'        => 'required|string|max:255',
            'carte_expiration' => 'required|string',
            'carte_cvv'        => 'required|string|min:3|max:4',
        ]);

        $this->enregistrerPaiementGroupe();
    }

    private function enregistrerPaiementGroupe()
    {
        foreach ($this->participants_a_payer as $inscription) {
            $paiement = Paiement::create([
                'id_inscription' => $inscription->id,
                'montant'        => $this->montant_total,
                'date_paiement'  => now()->toDateString(),
                'mode_paiement'  => $this->mode_paiement,
                'statut'         => 'valide',
            ]);

            $inscription->update([
                'statut_paiement' => 'paye',
                'statut_presence' => 'present',
            ]);

            Recu::create([
                'id_paiement' => $paiement->id,
                'date'        => now()->toDateString(),
                'montant'     => $this->montant_total,
            ]);

            $participant = $inscription->participant;
            if ($participant) {
                $badgeExiste = Badge::where('id_participant', $participant->id)->exists();
                if (!$badgeExiste) {
                    $typeBadge = TypeBadge::firstOrCreate(
                        ['libelle' => ucfirst($participant->role)],
                        ['description' => 'Badge ' . ucfirst($participant->role)]
                    );

                    Badge::create([
                        'id_participant' => $participant->id,
                        'id_type_badge'  => $typeBadge->id,
                        'qr_code'        => strtoupper(
                            substr($participant->nom, 0, 2) .
                            substr($participant->prenom ?? 'XX', 0, 2) .
                            '-' . $this->id_evenement_paiement .
                            '-' . Str::random(6)
                        ),
                    ]);
                }
            }
        }

        $nb = count($this->participants_a_payer);
        $this->closePaiementGroupe();
        session()->flash('success', "Paiement groupé confirmé ! {$nb} participant(s) validés !");
    }

    // =========================================================
    // GESTION PARTICIPANTS
    // =========================================================

    public function modifier($id)
    {
        $p = Participant::findOrFail($id);
        $this->participant_id = $p->id;
        $this->id_evenement   = $p->id_evenement;
        $this->nom            = $p->nom;
        $this->prenom         = $p->prenom;
        $this->genre          = $p->genre;
        $this->fonction       = $p->fonction;
        $this->email          = $p->email;
        $this->telephone      = $p->telephone;
        $this->role           = $p->role;
        $this->isEditing      = true;
        $this->showModal      = true;
    }

    public function sauvegarder()
    {
        $this->validate([
            'id_evenement' => 'required',
            'nom'          => 'required|string|max:255',
            'prenom'       => 'required|string|max:255',
            'email'        => 'nullable|email|max:255',
            'telephone'    => 'required|string|max:20',
            'role'         => 'required',
        ]);

        $entreprise = $this->getEntreprise();

        if (!$entreprise) {
            session()->flash('error', 'Entreprise non trouvée.');
            return;
        }

        $code = strtoupper(substr($this->nom, 0, 3) . rand(1000, 9999));

        $data = [
            'id_entreprise'     => $entreprise->id,
            'id_cdd'            => $entreprise->id_cdd,
            'id_evenement'      => $this->id_evenement,
            'nom'               => $this->nom,
            'prenom'            => $this->prenom,
            'genre'             => $this->genre ?: null,
            'fonction'          => $this->fonction ?: null,
            'email'             => $this->email ?: null,
            'telephone'         => $this->telephone,
            'role'              => $this->role,
            'secteur_activite'  => $entreprise->secteur_activite,
            'statut_historique' => 'actif',
            'statut_adhesion'   => 'accepte',
        ];

        if ($this->isEditing) {
            Participant::findOrFail($this->participant_id)->update($data);
            session()->flash('success', 'Participant modifié.');
        } else {
            $data['code_acces'] = $code;
            $participant = Participant::create($data);

            // ← Crée le compte USER si email fourni
            if ($this->email) {
                $userExiste = User::where('email', $this->email)->exists();
                if (!$userExiste) {
                    $user = User::create([
                        'name'     => $this->nom . ' ' . $this->prenom,
                        'email'    => $this->email,
                        'password' => Hash::make($code),
                    ]);
                    $user->assignRole('participant');
                }
            }

            // ← Crée automatiquement l'inscription
            $evenement = Evenement::find($this->id_evenement);
            if ($evenement) {
                $inscriptionExiste = Inscription::where('id_participant', $participant->id)
                    ->where('id_evenement', $this->id_evenement)
                    ->exists();

                if (!$inscriptionExiste) {
                    // ← Détermine le montant et statut selon type paiement
                    $montant = $evenement->montant_inscription ?? 0;
                    $statut  = 'en_attente';

                    if ($evenement->type_paiement == 'gratuit') {
                        $montant = 0;
                        $statut  = 'paye';
                    } elseif ($evenement->type_paiement == 'par_entreprise') {
                        // ← L'entreprise paie → pas de paiement individuel
                        $montant = 0;
                        $statut  = 'en_attente'; // ← En attente du paiement groupé
                    }

                    Inscription::create([
                        'id_participant'   => $participant->id,
                        'id_evenement'     => $this->id_evenement,
                        'date_inscription' => now()->toDateString(),
                        'montant_paye'     => $montant,
                        'statut_paiement'  => $statut,
                        'statut_presence'  => 'absent',
                    ]);
                }
            }

            session()->flash('success', "Participant ajouté ! Code : {$code}");
        }

        $this->closeModal();
    }

    public function supprimer($id)
    {
        Participant::findOrFail($id)->delete();
        session()->flash('success', 'Participant supprimé.');
    }

    // =========================================================
    // RENDU
    // =========================================================

    public function render()
    {
        $entreprise = $this->getEntreprise();

        $evenements_avec_impayés = collect();
        if ($entreprise) {
            $evenements_avec_impayés = Evenement::whereHas('inscriptions', fn($q) =>
                $q->whereHas('participant', fn($q) =>
                    $q->where('id_entreprise', $entreprise->id)
                )
                ->where('statut_paiement', '!=', 'paye')
            )
            ->where('type_paiement', 'par_entreprise')
            ->get();
        }

        return view('livewire.entreprise.mes-participants', [
            'participants' => $entreprise
                ? Participant::where('id_entreprise', $entreprise->id)
                    ->when($this->search, fn($q) =>
                        $q->where('nom', 'like', '%'.$this->search.'%')
                          ->orWhere('prenom', 'like', '%'.$this->search.'%')
                    )
                    ->latest()
                    ->get()
                : collect(),

            'demandesEnAttente' => $entreprise
                ? Participant::where('id_entreprise', $entreprise->id)
                    ->where('statut_adhesion', 'en_attente')
                    ->get()
                : collect(),

            'evenements' => Evenement::where('date_fin', '>=', now()->toDateString())
                ->orderBy('nom')
                ->get(),

            'entreprise'              => $entreprise,
            'evenements_avec_impayés' => $evenements_avec_impayés,
        ])->layout('layouts.entreprise', ['title' => 'Mes Participants']);
    }
}