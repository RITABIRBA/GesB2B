@component('emails.layouts.email')

{{-- EN-TÊTE --}}
<div style="background: linear-gradient(135deg, #007A3D, #005a2d); padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
    <div style="font-size: 48px; margin-bottom: 10px;"></div>
    <h1 style="color: white; font-size: 26px; font-weight: 800; margin: 0 0 8px 0;">
        Votre planning B2B
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

    <div style="background: #f0fdf4; border: 2px solid #86efac; border-radius: 12px; padding: 16px 20px; margin-bottom: 25px;">
        <p style="font-size: 15px; color: #166534; margin: 0; font-weight: 600;">
             Le planning de vos rendez-vous B2B a été généré !
        </p>
        <p style="font-size: 13px; color: #15803d; margin: 8px 0 0 0;">
            Voici le détail de vos rendez-vous pour le <strong>{{ $dateEvenement }}</strong>.
        </p>
    </div>

    @if($rendezVous->isEmpty())
    <div style="background: #fef9c3; border: 1px solid #fde047; border-radius: 10px; padding: 16px 20px; margin-bottom: 25px;">
        <p style="font-size: 14px; color: #713f12; margin: 0;">
             Aucun rendez-vous n'a été planifié pour vous lors de cette génération.
            Contactez les organisateurs pour plus d'informations.
        </p>
    </div>
    @else

    {{-- TABLEAU DES RDV --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px;">
        <thead>
            <tr style="background: #007A3D;">
                <th style="padding: 10px 12px; color: white; font-size: 12px; text-align: left; border-radius: 8px 0 0 0;">Horaire</th>
                <th style="padding: 10px 12px; color: white; font-size: 12px; text-align: left;">Salle / Table</th>
                <th style="padding: 10px 12px; color: white; font-size: 12px; text-align: left;">Votre partenaire</th>
                <th style="padding: 10px 12px; color: white; font-size: 12px; text-align: left; border-radius: 0 8px 0 0;">Entreprise</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rendezVous->sortBy('heure_debut') as $i => $rdv)
            @php
                $estP1      = $rdv->id_participant1 == $destinataire->id;
                $partenaire = $estP1 ? $rdv->participant2 : $rdv->participant1;
                $bg         = $i % 2 === 0 ? '#f9fafb' : '#ffffff';
            @endphp
            <tr style="background: {{ $bg }};">
                <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px; color: #111827;">
                    @if($rdv->date)
                    <div style="font-weight: 700; color: #007A3D;">
                        {{ \Carbon\Carbon::parse($rdv->date)->format('d/m/Y') }}
                    </div>
                    @endif
                    @if($rdv->heure_debut && $rdv->heure_fin)
                    <div style="color: #374151; margin-top: 2px;">
                        {{ $rdv->heure_debut }} → {{ $rdv->heure_fin }}
                    </div>
                    @else
                    <div style="color: #9ca3af; font-style: italic;">À planifier</div>
                    @endif
                </td>
                <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px; color: #374151;">
                    @if($rdv->salle)
                    <div style="font-weight: 600;">{{ $rdv->salle }}</div>
                    <div style="color: #6b7280; margin-top: 2px;">Table {{ $rdv->numero_table }}</div>
                    @else
                    <span style="color: #9ca3af; font-style: italic;">Non assigné</span>
                    @endif
                </td>
                <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px;">
                    @if($partenaire)
                    <div style="font-weight: 700; color: #111827;">
                        {{ $partenaire->prenom }} {{ $partenaire->nom }}
                    </div>
                    @if($partenaire->fonction)
                    <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">{{ $partenaire->fonction }}</div>
                    @endif
                    @if($partenaire->email)
                    <div style="font-size: 12px; color: #007A3D; margin-top: 2px;">✉️ {{ $partenaire->email }}</div>
                    @endif
                    @if($partenaire->telephone)
                    <div style="font-size: 12px; color: #6b7280;"> {{ $partenaire->telephone }}</div>
                    @endif
                    @else
                    <span style="color: #9ca3af;">—</span>
                    @endif
                </td>
                <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px; color: #374151;">
                    @if($partenaire && $partenaire->entreprise)
                    <div style="font-weight: 600;">{{ $partenaire->entreprise->nom }}</div>
                    @if($partenaire->secteur_activite)
                    <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">{{ $partenaire->secteur_activite }}</div>
                    @endif
                    @else
                    <span style="color: #9ca3af;">Indépendant</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- RÉSUMÉ --}}
    <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px 20px; margin-bottom: 25px;">
        <p style="font-size: 14px; color: #374151; margin: 0 0 8px 0; font-weight: 700;">
            Récapitulatif
        </p>
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div style="text-align: center; background: white; border-radius: 8px; padding: 12px 20px; border: 1px solid #e5e7eb;">
                <div style="font-size: 24px; font-weight: 800; color: #007A3D;">{{ $rendezVous->count() }}</div>
                <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">Rendez-vous</div>
            </div>
            <div style="text-align: center; background: white; border-radius: 8px; padding: 12px 20px; border: 1px solid #e5e7eb;">
                <div style="font-size: 24px; font-weight: 800; color: #C8102E;">
                    {{ $rendezVous->where('statut', 'planifie')->count() + $rendezVous->where('statut', 'confirme')->count() }}
                </div>
                <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">Actifs</div>
            </div>
        </div>
    </div>
    @endif

    {{-- CONSEILS --}}
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 16px 20px; margin-bottom: 25px;">
        <p style="font-size: 13px; color: #1e40af; margin: 0 0 8px 0; font-weight: 700;">
             Conseils pour vos rendez-vous
        </p>
        <ul style="margin: 0; padding-left: 16px; font-size: 13px; color: #1e3a8a; line-height: 1.8;">
            <li>Préparez votre pitch de présentation (2-3 minutes max)</li>
            <li>Apportez des cartes de visite ou brochures</li>
            <li>Arrivez 5 minutes avant l'heure de votre premier RDV</li>
            <li>En cas d'absence imprévue, signalez-la via l'application</li>
        </ul>
    </div>

    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 18px; margin-bottom: 25px;">
        <p style="font-size: 13px; color: #92400e; margin: 0;">
            <strong> Important :</strong> En cas d'absence ou de modification, connectez-vous à l'application
            et signalez votre absence depuis la section "Mes Rendez-vous" pour que vos partenaires
            soient automatiquement notifiés.
        </p>
    </div>

    <p style="font-size: 14px; color: #6b7280; text-align: center; margin: 0;">
        Bonne chance pour vos échanges au <strong>{{ $nomEvenement }}</strong> !
    </p>
</div>

@endcomponent