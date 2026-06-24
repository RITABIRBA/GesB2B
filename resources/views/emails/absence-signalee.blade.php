@component('emails.layouts.email')

{{-- EN-TÊTE --}}
<div style="background: linear-gradient(135deg, #C8102E, #a00d25); padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
    <div style="font-size: 48px; margin-bottom: 10px;">⚠️</div>
    <h1 style="color: white; font-size: 24px; font-weight: 800; margin: 0 0 8px 0;">
        Absence signalée
    </h1>
    <p style="color: rgba(255,255,255,0.8); font-size: 14px; margin: 0;">
        {{ $nomEvenement }}
    </p>
</div>

{{-- CORPS --}}
<div style="padding: 35px 30px;">

    <p style="font-size: 16px; color: #374151; margin: 0 0 20px 0;">
        Bonjour <strong>{{ $destinataire->prenom }} {{ $destinataire->nom }}</strong>,
    </p>

    {{-- Alerte principale --}}
    <div style="background: #fff1f2; border: 2px solid #fecdd3; border-radius: 12px; padding: 18px 20px; margin-bottom: 25px;">
        <p style="font-size: 15px; color: #9f1239; font-weight: 700; margin: 0 0 8px 0;">
            Votre partenaire <strong>{{ $absent->prenom }} {{ $absent->nom }}</strong> a signalé son absence.
        </p>
        @if($absent->entreprise)
        <p style="font-size: 13px; color: #be185d; margin: 0 0 6px 0;">
            🏢 {{ $absent->entreprise->nom }}
        </p>
        @endif
        <p style="font-size: 13px; color: #9f1239; margin: 0;">
            Le rendez-vous prévu
            @if($heureDebut)
                le <strong>{{ \Carbon\Carbon::parse($dateRdv)->format('d/m/Y') }}</strong>
                de <strong>{{ $heureDebut }}</strong> à <strong>{{ $heureFin }}</strong>
                @if($salle) — {{ $salle }}@if($table), Table {{ $table }}@endif @endif
            @else
                pour la journée du <strong>{{ \Carbon\Carbon::parse($dateRdv)->format('d/m/Y') }}</strong>
            @endif
            a été <strong>annulé</strong>.
        </p>
    </div>

    {{-- Remplaçants proposés --}}
    @if($remplacants->isNotEmpty())
    <div style="margin-bottom: 25px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 12px 0;">
            🔄 Participants compatibles proposés en remplacement
        </h3>
        <p style="font-size: 13px; color: #6b7280; margin: 0 0 15px 0;">
            Ces participants sont disponibles et ont un profil compatible avec le vôtre.
            Connectez-vous à l'application pour choisir un remplaçant.
        </p>

        @foreach($remplacants->take(5) as $r)
        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px 16px; margin-bottom: 10px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #007A3D; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 16px; flex-shrink: 0;">
                    {{ strtoupper(substr($r->prenom ?? 'P', 0, 1)) }}
                </div>
                <div style="flex: 1;">
                    <p style="font-size: 14px; font-weight: 700; color: #111827; margin: 0 0 3px 0;">
                        {{ $r->prenom }} {{ $r->nom }}
                    </p>
                    @if($r->fonction)
                    <p style="font-size: 12px; color: #6b7280; margin: 0 0 2px 0;">{{ $r->fonction }}</p>
                    @endif
                    @if($r->entreprise)
                    <p style="font-size: 12px; color: #007A3D; font-weight: 600; margin: 0;">
                        🏢 {{ $r->entreprise->nom }}
                    </p>
                    @endif
                    @if($r->secteur_activite)
                    <p style="font-size: 12px; color: #9ca3af; margin: 2px 0 0 0;">
                        📂 {{ $r->secteur_activite }}
                    </p>
                    @endif
                </div>
                <div style="text-align: center; flex-shrink: 0;">
                    <div style="font-size: 12px; font-weight: 700; color: {{ $r->score_compatibilite >= 4 ? '#007A3D' : ($r->score_compatibilite >= 2 ? '#2563eb' : '#9ca3af') }};">
                        {{ str_repeat('⭐', min($r->score_compatibilite, 5)) }}
                    </div>
                    <div style="font-size: 11px; color: #9ca3af; margin-top: 2px;">compatibilité</div>
                </div>
            </div>
        </div>
        @endforeach

        @if($remplacants->count() > 5)
        <p style="font-size: 13px; color: #6b7280; text-align: center; margin: 8px 0 0 0;">
            + {{ $remplacants->count() - 5 }} autre(s) remplaçant(s) disponibles dans l'application.
        </p>
        @endif
    </div>
    @else
    <div style="background: #fefce8; border: 1px solid #fde047; border-radius: 10px; padding: 14px 16px; margin-bottom: 25px;">
        <p style="font-size: 13px; color: #713f12; margin: 0;">
            ℹ️ Aucun remplaçant compatible n'a été trouvé automatiquement.
            Les organisateurs seront informés et vous contacteront si nécessaire.
        </p>
    </div>
    @endif

    {{-- CTA --}}
    <div style="text-align: center; margin-bottom: 25px;">
        <p style="font-size: 14px; color: #374151; margin: 0 0 12px 0;">
            Connectez-vous à l'application pour choisir un remplaçant ou gérer vos rendez-vous.
        </p>
        <a href="{{ config('app.url') }}"
            style="display: inline-block; background: #007A3D; color: white; padding: 12px 28px;
                   border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px;">
            Accéder à mes rendez-vous
        </a>
    </div>

    <div style="background: #f3f4f6; border-radius: 8px; padding: 12px 16px;">
        <p style="font-size: 12px; color: #6b7280; margin: 0; text-align: center;">
            Cet email a été envoyé automatiquement par la plateforme <strong>{{ $nomEvenement }}</strong>.
        </p>
    </div>

</div>

@endcomponent