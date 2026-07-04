<?php

namespace App\Http\Controllers;

use App\Models\Recu;
use App\Models\Participant;
use App\Models\Entreprise;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RecuController extends Controller
{
    public function telecharger(int $id)
    {
        $recu = Recu::with(['paiement.inscription.participant.entreprise', 'paiement.inscription.evenement'])
            ->findOrFail($id);

        $user = Auth::user();
        $participantInscrit = $recu->paiement->inscription->participant;

        $estParticipantLuiMeme = Participant::findForUser($user)?->id === $participantInscrit->id;

        $estRepresentantEntreprise = false;
        if (!$estParticipantLuiMeme && $participantInscrit->id_entreprise) {
            $entreprise = Entreprise::find($participantInscrit->id_entreprise);
            $estRepresentantEntreprise = $entreprise
                && $entreprise->email_responsable === $user->email;
        }

        if (!$estParticipantLuiMeme && !$estRepresentantEntreprise) {
            abort(403, 'Accès non autorisé à ce reçu.');
        }

       $pdf = Pdf::loadView('pdf.recu-paiement', [ 
            'recu'        => $recu,
            'paiement'    => $recu->paiement,
            'inscription' => $recu->paiement->inscription,
            'participant' => $participantInscrit,
            'evenement'   => $recu->paiement->inscription->evenement,
        ]);

        return $pdf->download('recu-' . str_pad($recu->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }
}