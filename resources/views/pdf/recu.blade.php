<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; color: #333; font-size: 13px; }
        .header { text-align: center; border-bottom: 3px solid #C8102E; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { color: #007A3D; margin: 0; font-size: 22px; }
        .header p { color: #888; margin: 4px 0 0; font-size: 12px; }
        .recu-badge { display: inline-block; background: #007A3D; color: white; padding: 6px 16px; border-radius: 20px; font-weight: bold; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        td.label { color: #888; width: 40%; }
        td.value { font-weight: bold; color: #333; }
        .montant-box { background: #f0f9f4; border: 2px solid #007A3D; border-radius: 10px; padding: 20px; text-align: center; margin: 25px 0; }
        .montant-box .montant { font-size: 28px; font-weight: bold; color: #007A3D; }
        .footer { margin-top: 40px; text-align: center; color: #aaa; font-size: 11px; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Business Forum — CCI-BF</h1>
        <p>Chambre de Commerce et d'Industrie du Burkina Faso</p>
        <div class="recu-badge">REÇU N° REC-{{ str_pad($recu->id, 6, '0', STR_PAD_LEFT) }}</div>
    </div>

    <table>
        <tr>
            <td class="label">Participant</td>
            <td class="value">{{ $participant->nom }} {{ $participant->prenom }}</td>
        </tr>
        @if($participant->entreprise)
        <tr>
            <td class="label">Entreprise</td>
            <td class="value">{{ $participant->entreprise->nom }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">Événement</td>
            <td class="value">{{ $evenement->nom ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Date du paiement</td>
            <td class="value">{{ \Carbon\Carbon::parse($recu->date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Mode de paiement</td>
            <td class="value">{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</td>
        </tr>
        @if($paiement->numero_cheque)
        <tr>
            <td class="label">N° de chèque</td>
            <td class="value">{{ $paiement->numero_cheque }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">Statut</td>
            <td class="value" style="color: #007A3D;">Payé et validé</td>
        </tr>
    </table>

    <div class="montant-box">
        <p style="margin:0; color:#888; font-size: 13px;">Montant payé</p>
        <p class="montant">{{ number_format($recu->montant, 0, ',', ' ') }} FCFA</p>
    </div>

    <div class="footer">
        Ce reçu fait office de preuve de paiement pour le Business Forum CCI-BF.<br>
        © {{ date('Y') }} CCI-BF — Tous droits réservés
    </div>
</body>
</html>