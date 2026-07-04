<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: 105mm 74mm;
            margin: 0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            width: 105mm;
            height: 74mm;
            font-family: Helvetica, Arial, sans-serif;
            background: #ffffff;
            overflow: hidden;
        }

        /* ── BANDE HAUTE VERTE ── */
        .top-band {
            background: #007A3D;
            height: 18mm;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 4mm;
        }

        .top-band img.logo {
            height: 12mm;
            width: auto;
        }

        .top-band .event-name {
            color: white;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            flex: 1;
            padding: 0 3mm;
            line-height: 1.3;
        }

        .top-band .event-dates {
            color: rgba(255,255,255,0.85);
            font-size: 7px;
            text-align: right;
            line-height: 1.4;
        }

        /* ── CORPS DU BADGE ── */
        .body {
            display: flex;
            height: 46mm;
            padding: 3mm 4mm;
            gap: 3mm;
        }

        /* Colonne gauche : infos */
        .info-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 1.5mm;
        }

        .participant-nom {
            font-size: 14px;
            font-weight: bold;
            color: #111;
            line-height: 1.2;
        }

        .participant-prenom {
            font-size: 11px;
            color: #444;
            margin-top: 1px;
        }

        .separator {
            width: 20mm;
            height: 2px;
            background: #C8102E;
            margin: 2mm 0;
            border-radius: 2px;
        }

        .info-ligne {
            font-size: 8px;
            color: #555;
            line-height: 1.5;
        }

        .info-ligne strong {
            color: #007A3D;
            font-size: 8px;
        }

        .role-badge {
            display: inline-block;
            background: #C8102E;
            color: white;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 2mm;
        }

        /* Colonne droite : QR code */
        .qr-col {
            width: 24mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2mm;
        }

        .qr-col img.qr {
            width: 22mm;
            height: 22mm;
        }

        .qr-label {
            font-size: 6px;
            color: #999;
            text-align: center;
        }

        /* ── BANDE BAS ROUGE ── */
        .bottom-band {
            background: #C8102E;
            height: 10mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bottom-band p {
            color: white;
            font-size: 7px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    {{-- ── BANDE HAUTE ── --}}
    <div class="top-band">
        <img class="logo"
             src="{{ public_path('images/logo-ccibf.png') }}"
             alt="CCI-BF">
        {{-- ⚠️ Remplace logo-ccibf.png par le vrai nom de ton logo --}}

        <div class="event-name">
            {{ $evenement->nom ?? 'Business Forum CCI-BF' }}
        </div>

        <div class="event-dates">
            Du {{ \Carbon\Carbon::parse($evenement->date_debut)->format('d/m/Y') }}<br>
            au {{ \Carbon\Carbon::parse($evenement->date_fin)->format('d/m/Y') }}
        </div>
    </div>

    {{-- ── CORPS ── --}}
    <div class="body">

        {{-- INFOS PARTICIPANT --}}
        <div class="info-col">
            <div>
                <div class="participant-nom">{{ strtoupper($participant->nom) }}</div>
                <div class="participant-prenom">{{ $participant->prenom }}</div>
            </div>

            <div class="separator"></div>

            @if($participant->fonction)
            <div class="info-ligne">
                <strong>Fonction :</strong> {{ $participant->fonction }}
            </div>
            @endif

            @if($participant->entreprise)
            <div class="info-ligne">
                <strong>Entreprise :</strong> {{ $participant->entreprise->nom }}
            </div>
            <div class="info-ligne">
                <strong>Secteur :</strong> {{ $participant->entreprise->secteur_activite }}
            </div>
            <div class="info-ligne">
                <strong>Pays :</strong> {{ $participant->entreprise->pays }}
            </div>
            @else
            {{-- Participant sans entreprise : étudiant ou individuel --}}
            @if($participant->secteur_activite)
            <div class="info-ligne">
                <strong>Filière :</strong> {{ $participant->secteur_activite }}
            </div>
            @endif
            @endif

            <div>
                <span class="role-badge">{{ ucfirst($participant->role) }}</span>
            </div>
        </div>

        {{-- QR CODE --}}
        <div class="qr-col">
            <img class="qr"
                 src="data:image/png;base64,{{ $qrCode }}"
                 alt="QR Code">
            <p class="qr-label">Scanner pour<br>plus d'infos</p>
        </div>

    </div>

    {{-- ── BANDE BAS ── --}}
    <div class="bottom-band">
        <p>Chambre de Commerce et d'Industrie du Burkina Faso</p>
    </div>

</body>
</html>