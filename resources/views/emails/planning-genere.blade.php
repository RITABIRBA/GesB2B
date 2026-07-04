@extends('emails.layouts.email')

@section('content')

<h2>📅 Votre planning B2B</h2>

<p>Bonjour <strong>{{ $destinataire->prenom }} {{ $destinataire->nom }}</strong>,</p>

<div class="info-box">
    <strong>Le planning de vos rendez-vous B2B a été généré !</strong>
    Voici le détail de vos rendez-vous pour le <strong>{{ $dateEvenement }}</strong>.
</div>

@if($rendezVous->isEmpty())

<div class="info-box" style="background: #fef9c3; border-color: #fde047;">
    <strong style="color: #713f12;">Aucun rendez-vous planifié</strong>
    Aucun rendez-vous n'a été planifié pour vous lors de cette génération.
    Contactez les organisateurs pour plus d'informations.
</div>

@else

<h3 style="color: #1e3a5f; font-size: 15px; margin-top: 24px;">
    Vos rendez-vous ({{ $rendezVous->count() }})
</h3>

<table class="details">
    <thead>
        <tr style="background: #1e3a5f;">
            <th style="padding: 10px 12px; color: white; font-size: 12px; text-align: left;">Horaire</th>
            <th style="padding: 10px 12px; color: white; font-size: 12px; text-align: left;">Table</th>
            <th style="padding: 10px 12px; color: white; font-size: 12px; text-align: left;">Partenaire</th>
            <th style="padding: 10px 12px; color: white; font-size: 12px; text-align: left;">Entreprise</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rendezVous->sortBy('heure_debut') as $rdv)
        @php
            $estP1      = $rdv->id_participant1 == $destinataire->id;
            $partenaire = $estP1 ? $rdv->participant2 : $rdv->participant1;
        @endphp
        <tr>
            <td>
                @if($rdv->date)
                <strong>{{ \Carbon\Carbon::parse($rdv->date)->format('d/m/Y') }}</strong><br>
                @endif
                @if($rdv->heure_debut && $rdv->heure_fin)
                {{ $rdv->heure_debut }} → {{ $rdv->heure_fin }}
                @else
                <em style="color: #9ca3af;">À planifier</em>
                @endif
            </td>
            <td>
                @if($rdv->salle)
                {{ $rdv->salle }}<br>
                <small style="color: #6b7280;">Table {{ $rdv->numero_table }}</small>
                @else
                <em style="color: #9ca3af;">Non assigné</em>
                @endif
            </td>
            <td>
                @if($partenaire)
                <strong>{{ $partenaire->prenom }} {{ $partenaire->nom }}</strong><br>
                @if($partenaire->fonction)
                <small style="color: #6b7280;">{{ $partenaire->fonction }}</small><br>
                @endif
                @if($partenaire->email)
                <small style="color: #007A3D;">{{ $partenaire->email }}</small>
                @endif
                @else
                —
                @endif
            </td>
            <td>
                @if($partenaire && $partenaire->entreprise)
                <strong>{{ $partenaire->entreprise->nom }}</strong><br>
                @if($partenaire->secteur_activite)
                <small style="color: #6b7280;">{{ $partenaire->secteur_activite }}</small>
                @endif
                @else
                <em style="color: #9ca3af;">Indépendant</em>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Résumé --}}
<div class="info-box" style="margin-top: 20px;">
    <strong>Récapitulatif</strong>
    <p style="margin: 8px 0 0;">
        Total : <strong>{{ $rendezVous->count() }} rendez-vous</strong>
    </p>
</div>

@endif

{{-- Conseils --}}
<div class="info-box" style="background: #eff6ff; border-color: #bfdbfe; margin-top: 20px;">
    <strong style="color: #1e40af;"> Conseils pour vos rendez-vous</strong>
    <ul style="margin: 8px 0 0; padding-left: 16px; font-size: 13px; color: #1e3a8a; line-height: 1.8;">
        <li>Préparez votre pitch de présentation (2-3 minutes max)</li>
        <li>Apportez des cartes de visite ou brochures</li>
        <li>Arrivez 5 minutes avant l'heure de votre premier RDV</li>
        <li>En cas d'absence imprévue, signalez-la via l'application</li>
    </ul>
</div>

<div class="info-box" style="background: #fffbeb; border-color: #fde68a; margin-top: 16px;">
    <strong style="color: #92400e;"> Important</strong>
    En cas d'absence ou de modification, connectez-vous à l'application
    et signalez votre absence depuis la section "Mes Rendez-vous".
</div>

<p>Bonne chance pour vos échanges au <strong>{{ $nomEvenement }}</strong> !</p>

<p>Cordialement,<br><strong>L'équipe Business Forum — CCI-BF</strong></p>

@endsection