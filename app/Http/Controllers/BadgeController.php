<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Evenement;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BadgeController extends Controller
{
    /**
     * Télécharger le badge PDF d'un participant.
     * Accessible par le participant lui-même, l'admin ou le superviseur.
     */
    public function telecharger(int $id)
    {
        $participant = Participant::with('entreprise')->findOrFail($id);
        $evenement   = Evenement::findOrFail($participant->id_evenement);

        // Générer le QR code pointant vers la page publique du profil
        $url     = route('participant.profil-public', $participant->id);
        $qrCode  = base64_encode(QrCode::format('png')->size(200)->generate($url));

        $pdf = Pdf::loadView('pdf.badge', compact('participant', 'evenement', 'qrCode'))
                  ->setPaper([0, 0, 297.638, 209.764], 'landscape'); // 105mm x 74mm en points

        return $pdf->download("badge-{$participant->nom}-{$participant->prenom}.pdf");
    }

    /**
     * Afficher la page publique du profil (page vers laquelle pointe le QR code).
     */
    public function profilPublic(int $id)
    {
        $participant = Participant::with('entreprise')->findOrFail($id);
        $evenement   = Evenement::findOrFail($participant->id_evenement);

        return view('public.profil-participant', compact('participant', 'evenement'));
    }

    /**
     * Générer tous les badges d'un événement (pour l'admin).
     */
    public function tousLesBadges(int $idEvenement)
    {
        $evenement    = Evenement::findOrFail($idEvenement);
        $participants = Participant::with('entreprise')
            ->where('id_evenement', $idEvenement)
            ->where('statut_historique', 'actif')
            ->get();

        // Générer les QR codes pour chaque participant
        $participantsAvecQr = $participants->map(function ($p) {
            $url       = route('participant.profil-public', $p->id);
            $p->qrCode = base64_encode(QrCode::format('png')->size(200)->generate($url));
            return $p;
        });

        $pdf = Pdf::loadView('pdf.badges-tous', compact('participantsAvecQr', 'evenement'))
                  ->setPaper([0, 0, 297.638, 209.764], 'landscape');

        return $pdf->download("badges-{$evenement->nom}.pdf");
    }
}