<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use App\Models\Participant;
use App\Models\Entreprise;
use App\Models\DemandeAide;

/**
 * Widget flottant "Besoin d'aide ?" affiché dans l'espace participant
 * et représentant d'entreprise. Permet d'envoyer une demande d'aide
 * vers le CDD assigné, ou vers l'admin/superviseur si pas de CDD.
 */
class DemandeAideWidget extends Component
{
    public bool $showModal     = false;
    public string $sujet       = 'autre';
    public string $message     = '';
    public string $alertSuccess = '';
    public string $alertError   = '';

    /**
     * Récupère le participant connecté, qu'il soit un participant
     * "membre" ou le représentant d'une entreprise.
     */
    private function getParticipant(): ?Participant
    {
        $participant = Participant::where('email', auth()->user()->email)->first();
        if ($participant) return $participant;

        $entreprise = Entreprise::where('email_responsable', auth()->user()->email)->first();
        if ($entreprise) {
            return Participant::where('id_entreprise', $entreprise->id)
                ->where('role', 'representant')
                ->first();
        }

        return null;
    }

    public function ouvrir(): void
    {
        $this->showModal    = true;
        $this->sujet        = 'autre';
        $this->message      = '';
        $this->alertSuccess = '';
        $this->alertError   = '';
        $this->resetErrorBag();
    }

    public function fermer(): void
    {
        $this->showModal = false;
    }

    /**
     * Envoie la demande d'aide.
     * Destinataire = CDD assigné (via entreprise->id_cdd), sinon admin/superviseur.
     */
    public function envoyer(): void
    {
        $this->validate([
            'sujet'   => 'required|in:inscription,rendez_vous,autre',
            'message' => 'required|min:5|max:1000',
        ], [
            'message.required' => 'Veuillez décrire votre besoin.',
            'message.min'      => 'Votre message est trop court.',
        ]);

        $participant = $this->getParticipant();

        if (!$participant) {
            $this->alertError = 'Profil introuvable.';
            return;
        }

        $idCdd = $participant->entreprise->id_cdd ?? $participant->id_cdd ?? null;
        $destinataireType = $idCdd ? 'cdd' : 'admin_superviseur';

        DemandeAide::create([
            'id_participant'     => $participant->id,
            'id_evenement'       => $participant->id_evenement,
            'id_cdd'             => $idCdd,
            'destinataire_type'  => $destinataireType,
            'sujet'              => $this->sujet,
            'message'            => $this->message,
            'statut'             => 'en_attente',
        ]);

        $this->message = '';
        $this->sujet   = 'autre';

        $this->alertSuccess = $destinataireType === 'cdd'
            ? '✅ Votre demande a été envoyée à votre Chef de Délégation.'
            : '✅ Votre demande a été envoyée à l\'administration.';
    }

    public function render()
    {
        $participant = $this->getParticipant();

        $mesDemandes = $participant
            ? DemandeAide::where('id_participant', $participant->id)
                ->latest()
                ->take(5)
                ->get()
            : collect();

        return view('livewire.shared.demande-aide-widget', [
            'mesDemandes' => $mesDemandes,
        ]);
    }
}