<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Badge — {{ $badge->participant->nom }} {{ $badge->participant->prenom }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #004d27 0%, #007A3D 50%, #005a2d 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 24px;
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
        }

        .card-header {
            background: linear-gradient(135deg, #007A3D, #005a2d);
            padding: 32px;
            text-align: center;
            position: relative;
        }

        .card-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            border: 4px solid #007A3D;
        }

        .logo {
            width: 64px;
            height: 64px;
            object-fit: contain;
            margin: 0 auto 12px;
            display: block;
        }

        .badge-type {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: rgba(200,16,46,0.2);
            color: #ff8a9a;
            border: 1px solid rgba(200,16,46,0.3);
            margin-bottom: 8px;
        }

        .card-body {
            padding: 40px 32px 32px;
        }

        .participant-name {
            text-align: center;
            margin-bottom: 24px;
        }

        .participant-name h1 {
            font-size: 26px;
            font-weight: 800;
            color: #111827;
            line-height: 1.2;
        }

        .participant-name p {
            font-size: 14px;
            color: #6b7280;
            margin-top: 4px;
        }

        .info-grid {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 24px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            background: #f9fafb;
            border-radius: 14px;
            border: 1px solid #f3f4f6;
        }

        .info-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 15px;
        }

        .info-content p:first-child {
            font-size: 11px;
            color: #9ca3af;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-content p:last-child {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin-top: 2px;
        }

        .divider {
            height: 1px;
            background: #f3f4f6;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            padding-top: 4px;
        }

        .footer span {
            color: #C8102E;
            font-weight: 600;
        }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: #e6f4ed;
            border: 1px solid #007A3D;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            color: #007A3D;
            margin: 0 auto 20px;
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>

    <div class="card">

        {{-- Header --}}
        <div class="card-header">
            <img src="{{ asset('images/logo-ccibf.png') }}"
                alt="CCI-BF" class="logo">
            <div class="badge-type">
                {{ $badge->typeBadge->libelle ?? 'Participant' }}
            </div>
            <p style="color: rgba(255,255,255,0.7); font-size: 13px; margin-top: 4px;">
                Chambre de Commerce et d'Industrie du Burkina Faso
            </p>
        </div>

        {{-- Body --}}
        <div class="card-body">

            {{-- Vérifié --}}
            <div class="verified-badge">
                <i class="fa-solid fa-circle-check"></i>
                Badge officiel vérifié
            </div>

            {{-- Nom --}}
            <div class="participant-name">
                <h1>
                    {{ $badge->participant->nom }}
                    {{ $badge->participant->prenom }}
                </h1>
                @if($badge->participant->fonction)
                <p>
                    <i class="fa-solid fa-briefcase" style="font-size: 11px; margin-right: 4px;"></i>
                    {{ $badge->participant->fonction }}
                </p>
                @endif
            </div>

            {{-- Infos --}}
            <div class="info-grid">

                {{-- Code d'accès --}}
                <div class="info-item">
                    <div class="info-icon" style="background: #fef2f2;">
                        <i class="fa-solid fa-key" style="color: #C8102E;"></i>
                    </div>
                    <div class="info-content">
                        <p>Numéro de badge</p>
                        <p style="font-family: monospace; letter-spacing: 2px;">
                            {{ $badge->participant->code_acces }}
                        </p>
                    </div>
                </div>

                {{-- Entreprise --}}
                <div class="info-item">
                    <div class="info-icon" style="background: #e6f4ed;">
                        <i class="fa-solid fa-building" style="color: #007A3D;"></i>
                    </div>
                    <div class="info-content">
                        <p>Entreprise</p>
                        <p>{{ $badge->participant->entreprise->nom ?? 'Indépendant' }}</p>
                    </div>
                </div>

                {{-- IFU entreprise --}}
                @if($badge->participant->entreprise?->ifu)
                <div class="info-item">
                    <div class="info-icon" style="background: #eff6ff;">
                        <i class="fa-solid fa-file-invoice" style="color: #3b82f6;"></i>
                    </div>
                    <div class="info-content">
                        <p>Numéro IFU</p>
                        <p style="font-family: monospace;">
                            {{ $badge->participant->entreprise->ifu }}
                        </p>
                    </div>
                </div>
                @endif

                {{-- Contact entreprise --}}
                @if($badge->participant->entreprise?->contact)
                <div class="info-item">
                    <div class="info-icon" style="background: #fdf4ff;">
                        <i class="fa-solid fa-phone" style="color: #a855f7;"></i>
                    </div>
                    <div class="info-content">
                        <p>Contact entreprise</p>
                        <p>{{ $badge->participant->entreprise->contact }}</p>
                    </div>
                </div>
                @endif

                {{-- Secteur --}}
                @if($badge->participant->secteur_activite)
                <div class="info-item">
                    <div class="info-icon" style="background: #fff7ed;">
                        <i class="fa-solid fa-tag" style="color: #f97316;"></i>
                    </div>
                    <div class="info-content">
                        <p>Secteur d'activité</p>
                        <p>{{ $badge->participant->secteur_activite }}</p>
                    </div>
                </div>
                @endif

            </div>

            <div class="divider"></div>

            <div class="footer">
                <p>
                    <i class="fa-solid fa-shield-halved" style="margin-right: 4px;"></i>
                    Badge officiel <span>GesB2B</span> — CCI-BF
                </p>
                <p style="margin-top: 4px;">
                    © {{ date('Y') }} Chambre de Commerce et d'Industrie du Burkina Faso
                </p>
            </div>

        </div>
    </div>

</body>
</html>