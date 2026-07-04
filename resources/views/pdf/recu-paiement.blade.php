<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: A5;
            margin: 12mm 14mm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 11px;
            width: 100%;
        }

        .header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #C8102E;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .header-logo-cell {
            display: table-cell;
            width: 70px;
            vertical-align: middle;
        }

        .header-logo {
            width: 65px;
            height: auto;
        }

        .header-text-cell {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            padding: 0 10px;
        }

        .header-text-cell h1 {
            color: #007A3D;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .header-text-cell p {
            color: #666;
            font-size: 9px;
            margin: 1px 0;
        }

        .header-right-cell {
            display: table-cell;
            width: 70px;
            vertical-align: middle;
        }

        .recu-badge-wrapper {
            text-align: center;
            margin: 10px 0;
        }

        .recu-badge {
            display: inline-block;
            background: #007A3D;
            color: white;
            padding: 4px 14px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 11px;
            letter-spacing: 1px;
        }

        .valide-band {
            background: #007A3D;
            color: white;
            text-align: center;
            padding: 5px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        td {
            padding: 7px 6px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        td.label {
            color: #888;
            width: 40%;
            font-size: 10px;
        }

        td.value {
            font-weight: bold;
            color: #333;
            font-size: 11px;
        }

        .montant-box {
            background: #f0f9f4;
            border: 2px solid #007A3D;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            margin: 14px 0;
        }

        .montant-box .label-montant {
            color: #888;
            font-size: 10px;
            margin-bottom: 4px;
        }

        .montant-box .montant {
            font-size: 22px;
            font-weight: bold;
            color: #007A3D;
        }

        .footer {
            margin-top: 16px;
            text-align: center;
            color: #aaa;
            font-size: 9px;
            border-top: 1px solid #eee;
            padding-top: 10px;
            line-height: 1.6;
        }

        .footer strong {
            color: #C8102E;
        }
    </style>
</head>
<body>

    {{-- EN-TÊTE --}}
    <div class="header">
        <div class="header-logo-cell">
            {{-- ✅ Chemin absolu pour DomPDF --}}
            <img class="header-logo"
                 src="{{ str_replace('\\', '/', public_path('images/logo-ccibf.png')) }}"
                 alt="Logo CCI-BF">
        </div>
        <div class="header-text-cell">
            <h1>Business Forum — CCI-BF</h1>
            <p>Chambre de Commerce et d'Industrie du Burkina Faso</p>
            <p>Direction Régionale de Bobo-Dioulasso</p>
        </div>
        <div class="header-right-cell"></div>
    </div>

    {{-- BADGE NUMÉRO REÇU --}}
    <div class="recu-badge-wrapper">
        <span class="recu-badge">
            REÇU N° REC-{{ str_pad($recu->id, 6, '0', STR_PAD_LEFT) }}
        </span>
    </div>

    {{-- BANDEAU VALIDÉ --}}
    <div class="valide-band"> PAIEMENT VALIDÉ</div>

    {{-- TABLEAU INFORMATIONS --}}
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
            <td class="label">Fonction</td>
            <td class="value">{{ $participant->fonction ?? '-' }}</td>
        </tr>
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
        @if($paiement->reference)
        <tr>
            <td class="label">Référence</td>
            <td class="value">{{ $paiement->reference }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">Statut</td>
            <td class="value" style="color: #007A3D;"> Payé et validé</td>
        </tr>
    </table>

    {{-- MONTANT --}}
    <div class="montant-box">
        <p class="label-montant">Montant payé</p>
        <p class="montant">{{ number_format($recu->montant, 0, ',', ' ') }} FCFA</p>
    </div>

    {{-- PIED DE PAGE --}}
    <div class="footer">
        Ce reçu fait office de preuve de paiement officielle pour le Business Forum CCI-BF.<br>
        Conservez ce document pour toute réclamation.<br><br>
        <strong>CCI-BF</strong> — Direction Régionale de Bobo-Dioulasso<br>
        © {{ date('Y') }} Chambre de Commerce et d'Industrie du Burkina Faso — Tous droits réservés
    </div>

</body>
</html>