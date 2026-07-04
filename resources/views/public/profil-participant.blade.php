<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil — {{ $participant->nom }} {{ $participant->prenom }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }

        /* EN-TÊTE VERTE */
        .card-header {
            background: linear-gradient(135deg, #007A3D, #005a2d);
            padding: 28px 24px 20px;
            text-align: center;
            position: relative;
        }

        .card-header img.logo {
            height: 48px;
            width: auto;
            margin-bottom: 12px;
            filter: brightness(0) invert(1);
        }

        .avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            border: 3px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 28px;
            font-weight: bold;
            color: white;
        }

        .card-header h1 {
            color: white;
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .card-header .fonction {
            color: rgba(255,255,255,0.85);
            font-size: 13px;
        }

        .role-pill {
            display: inline-block;
            background: #C8102E;
            color: white;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        /* CORPS */
        .card-body {
            padding: 20px 24px;
        }

        /* SECTION */
        .section {
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #007A3D;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e5e7eb;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 8px;
        }

        .info-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #f0f9f4;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .info-content .info-label {
            font-size: 10px;
            color: #9ca3af;
            margin-bottom: 1px;
        }

        .info-content .info-value {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        /* ÉVÉNEMENT */
        .event-box {
            background: #f0f9f4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }

        .event-box .event-name {
            font-size: 14px;
            font-weight: 700;
            color: #007A3D;
            margin-bottom: 4px;
        }

        .event-box .event-dates {
            font-size: 12px;
            color: #6b7280;
        }

        /* CONTACT */
        .contact-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 12px 16px;
        }

        .contact-box .contact-item {
            font-size: 12px;
            color: #374151;
            margin-bottom: 4px;
        }

        .contact-box .contact-item:last-child {
            margin-bottom: 0;
        }

        /* FOOTER */
        .card-footer {
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 12px 24px;
            text-align: center;
        }

        .card-footer p {
            font-size: 10px;
            color: #9ca3af;
        }

        .card-footer strong {
            color: #C8102E;
        }
    </style>
</head>
<body>

<div class="card">

    {{-- EN-TÊTE --}}
    <div class="card-header">
        <img class="logo"
             src="{{ asset('images/logo-ccibf.png') }}"
             alt="CCI-BF">
        {{-- ⚠️ Remplace logo-ccibf.png par le vrai nom de ton logo --}}

        <div class="avatar">
            {{ strtoupper(substr($participant->prenom, 0, 1)) }}{{ strtoupper(substr($participant->nom, 0, 1)) }}
        </div>

        <h1>{{ $participant->prenom }} {{ strtoupper($participant->nom) }}</h1>

        @if($participant->fonction)
        <p class="fonction">{{ $participant->fonction }}</p>
        @endif

        <span class="role-pill">{{ ucfirst($participant->role) }}</span>
    </div>

    {{-- CORPS --}}
    <div class="card-body">

        {{-- ÉVÉNEMENT --}}
        <div class="event-box">
            <p class="event-name">🎪 {{ $evenement->nom ?? 'Business Forum CCI-BF' }}</p>
            <p class="event-dates">
                Du {{ \Carbon\Carbon::parse($evenement->date_debut)->format('d/m/Y') }}
                au {{ \Carbon\Carbon::parse($evenement->date_fin)->format('d/m/Y') }}
            </p>
        </div>

        {{-- INFORMATIONS PERSONNELLES --}}
        <div class="section">
            <p class="section-title">Informations personnelles</p>

            <div class="info-row">
                <div class="info-icon">👤</div>
                <div class="info-content">
                    <p class="info-label">Nom complet</p>
                    <p class="info-value">{{ $participant->prenom }} {{ $participant->nom }}</p>
                </div>
            </div>

            @if($participant->genre)
            <div class="info-row">
                <div class="info-icon">⚧</div>
                <div class="info-content">
                    <p class="info-label">Genre</p>
                    <p class="info-value">{{ $participant->genre === 'homme' ? 'Homme' : 'Femme' }}</p>
                </div>
            </div>
            @endif

            <div class="info-row">
                <div class="info-icon">📧</div>
                <div class="info-content">
                    <p class="info-label">Email</p>
                    <p class="info-value">{{ $participant->email }}</p>
                </div>
            </div>

            <div class="info-row">
                <div class="info-icon">📞</div>
                <div class="info-content">
                    <p class="info-label">Téléphone</p>
                    <p class="info-value">{{ $participant->telephone }}</p>
                </div>
            </div>
        </div>

        {{-- ENTREPRISE ou ÉTUDIANT --}}
        @if($participant->entreprise)
        <div class="section">
            <p class="section-title">Entreprise</p>

            <div class="info-row">
                <div class="info-icon">🏢</div>
                <div class="info-content">
                    <p class="info-label">Nom de l'entreprise</p>
                    <p class="info-value">{{ $participant->entreprise->nom }}</p>
                </div>
            </div>

            <div class="info-row">
                <div class="info-icon">📂</div>
                <div class="info-content">
                    <p class="info-label">Secteur d'activité</p>
                    <p class="info-value">{{ $participant->entreprise->secteur_activite }}</p>
                </div>
            </div>

            @if($participant->entreprise->sous_secteur)
            <div class="info-row">
                <div class="info-icon">🔖</div>
                <div class="info-content">
                    <p class="info-label">Sous-secteur</p>
                    <p class="info-value">{{ $participant->entreprise->sous_secteur }}</p>
                </div>
            </div>
            @endif

            <div class="info-row">
                <div class="info-icon">🌍</div>
                <div class="info-content">
                    <p class="info-label">Pays / Ville</p>
                    <p class="info-value">{{ $participant->entreprise->pays }}@if($participant->entreprise->ville), {{ $participant->entreprise->ville }}@endif</p>
                </div>
            </div>

            @if($participant->entreprise->contact)
            <div class="info-row">
                <div class="info-icon">📱</div>
                <div class="info-content">
                    <p class="info-label">Contact entreprise</p>
                    <p class="info-value">{{ $participant->entreprise->contact }}</p>
                </div>
            </div>
            @endif
        </div>

        @else
        {{-- Participant individuel / étudiant --}}
        @if($participant->secteur_activite)
        <div class="section">
            <p class="section-title">Filière / Domaine</p>
            <div class="info-row">
                <div class="info-icon">🎓</div>
                <div class="info-content">
                    <p class="info-label">Filière / Secteur</p>
                    <p class="info-value">{{ $participant->secteur_activite }}</p>
                </div>
            </div>
        </div>
        @endif
        @endif

        {{-- PARTICIPATION B2B --}}
        <div class="info-row">
            <div class="info-icon">🤝</div>
            <div class="info-content">
                <p class="info-label">Participation aux rencontres B2B</p>
                <p class="info-value" style="color: {{ $participant->participation_rdv ? '#007A3D' : '#6b7280' }}">
                    {{ $participant->participation_rdv ? '✔ Oui' : '✘ Non' }}
                </p>
            </div>
        </div>

    </div>

    {{-- FOOTER --}}
    <div class="card-footer">
        <p>Profil vérifié — <strong>Business Forum CCI-BF</strong></p>
        <p style="margin-top:4px;">© {{ date('Y') }} Chambre de Commerce et d'Industrie du Burkina Faso</p>
    </div>

</div>

</body>
</html>