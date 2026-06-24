@component('emails.layouts.email')

{{-- EN-TÊTE --}}
<div style="background: linear-gradient(135deg, #007A3D, #005a2d); padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
    <div style="font-size: 48px; margin-bottom: 10px;"></div>
    <h1 style="color: white; font-size: 26px; font-weight: 800; margin: 0 0 8px 0;">
        Match Mutuel !
    </h1>
    <p style="color: rgba(255,255,255,0.8); font-size: 15px; margin: 0;">
        {{ $nomEvenement }}
    </p>
</div>

{{-- CORPS --}}
<div style="padding: 35px 30px;">

    <p style="font-size: 16px; color: #374151; margin: 0 0 20px 0;">
        Bonjour <strong>{{ $destinataire->prenom }} {{ $destinataire->nom }}</strong>,
    </p>

    <div style="background: #f0fdf4; border: 2px solid #86efac; border-radius: 12px; padding: 20px; margin-bottom: 25px; text-align: center;">
        <p style="font-size: 18px; font-weight: 700; color: #166534; margin: 0 0 8px 0;">
             Vous et <strong>{{ $partenaire->prenom }} {{ $partenaire->nom }}</strong> vous êtes mutuellement sélectionnés !
        </p>
        <p style="font-size: 14px; color: #15803d; margin: 0;">
            Un rendez-vous d'affaires sera automatiquement généré dans le planning.
        </p>
    </div>

    {{-- Fiche partenaire --}}
    <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
        <h3 style="font-size: 14px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 15px 0;">
            Votre partenaire
        </h3>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="width: 50px; height: 50px; border-radius: 50%; background: #007A3D; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 20px; flex-shrink: 0;">
                {{ strtoupper(substr($partenaire->prenom ?? 'P', 0, 1)) }}
            </div>
            <div>
                <p style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 4px 0;">
                    {{ $partenaire->prenom }} {{ $partenaire->nom }}
                </p>
                @if($partenaire->fonction)
                <p style="font-size: 13px; color: #6b7280; margin: 0 0 2px 0;">
                    {{ $partenaire->fonction }}
                </p>
                @endif
                @if($partenaire->entreprise)
                <p style="font-size: 13px; color: #007A3D; font-weight: 600; margin: 0;">
                    🏢 {{ $partenaire->entreprise->nom }}
                </p>
                @endif
                @if($partenaire->email)
                <p style="font-size: 12px; color: #9ca3af; margin: 4px 0 0 0;">
                    ✉️ {{ $partenaire->email }}
                </p>
                @endif
            </div>
        </div>

        @if($partenaire->secteur_activite)
        <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e5e7eb;">
            <p style="font-size: 12px; color: #9ca3af; margin: 0 0 4px 0;">Secteur d'activité</p>
            <p style="font-size: 14px; color: #374151; font-weight: 600; margin: 0;">{{ $partenaire->secteur_activite }}</p>
        </div>
        @endif

        @if($partenaire->zone_geographique)
        <div style="margin-top: 8px;">
            <p style="font-size: 12px; color: #9ca3af; margin: 0 0 4px 0;">Zone géographique</p>
            <p style="font-size: 14px; color: #374151; font-weight: 600; margin: 0;">{{ $partenaire->zone_geographique }}</p>
        </div>
        @endif
    </div>

    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 15px; margin-bottom: 25px;">
        <p style="font-size: 13px; color: #92400e; margin: 0;">
            <strong> Prochaine étape :</strong> Le planning des rendez-vous sera généré par les organisateurs.
            Vous recevrez un email avec votre planning complet dès qu'il sera disponible.
        </p>
    </div>

    <p style="font-size: 14px; color: #6b7280; text-align: center; margin: 0;">
        Bonne chance pour vos échanges au <strong>{{ $nomEvenement }}</strong> !
    </p>
</div>

@endcomponent