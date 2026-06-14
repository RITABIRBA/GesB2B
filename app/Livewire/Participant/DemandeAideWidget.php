<?php

namespace App\Livewire\Participant;

use Livewire\Component;
use App\Models\Participant;
use App\Models\DemandeAide;

/**
 * Bouton flottant "Besoin d'aide ?" disponible sur toutes les pages
 * de l'espace participant. Crée une demande d'aide adressée au CDD
 * du participant, ou sans destinataire (pool admin/superviseur)
 * si le participant n'a pas de CDD assigné.
 */
class DemandeAideWidget extends Component
{
    public bool $showModal = false;
    public string $sujet = 'inscription';
    public string $message = '';
    public string $alertSuccess = '';
    public string $alertError = '';

    public function ouvrir(): void
    {
        $this->showModal    = true;
        $this->sujet        = 'inscription';
        $this->message      = '';
        $this->alertSuccess = '';
        $this->alertError   = '';
        $this->resetErrorBag();
    }

    public function fermer(): void
    {
        $this->showModal = false;
    }

    public function envoyer(): void
    {
        $this->validate([
            'sujet'   => 'required|in:inscription,rendez_vous,autre',
            'message' => 'required|min:5|max:500',
        ], [
            'message.required' => 'Veuillez décrire votre besoin.',
            'message.min'      => 'Votre message est trop court (5 caractères minimum).',
        ]);

        $participant = Participant::findForUser(auth()->user());

        if (!$participant) {
            $this->alertError = 'Participant non trouvé.';
            return;
        }

        DemandeAide::create([
            'id_participant' => $participant->id,
            'id_cdd'         => $participant->id_cdd,
            'sujet'          => $this->sujet,
            'message'        => $this->message,
            'statut'         => 'en_attente',
        ]);

        $this->message      = '';
        $this->alertError   = '';
        $this->alertSuccess = $participant->id_cdd
            ? 'Votre demande a été envoyée à votre Chef de Délégation.'
            : 'Votre demande a été envoyée à l\'administration.';
    }

    public function render()
    {
        $participant = Participant::findForUser(auth()->user());

        $mesDemandes = $participant
            ? DemandeAide::where('id_participant', $participant->id)
                ->latest()
                ->take(5)
                ->get()
            : collect();

        return view('livewire.participant.demande-aide-widget', [
            'mesDemandes' => $mesDemandes,
        ]);
    }
}