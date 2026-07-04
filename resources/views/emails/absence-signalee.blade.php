@extends('emails.layouts.email')

@section('content')

<h2>⚠️ Absence signalée</h2>

<p>Bonjour <strong>{{ $destinataire->prenom }} {{ $destinataire->nom }}</strong>,</p>

{{-- Alerte principale --}}
<div class="info-box" style="background: #fff1f2; border-color: #fecdd3;">
    <strong style="color: #9f1239;">
        Votre partenaire {{ $absent->prenom }} {{ $absent->nom }} a signalé son absence.
    </strong>
    @if($absent->entreprise)
    <p style="margin: 6px 0; color: #be185d; font-size: 13px;">
        🏢 {{ $absent->entreprise->nom }}
    </p>
    @endif
    <p style="margin: 6px 0 0; color: #9f1239; font-size: 13px;">
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

<h3 style="color: #1e3a5f; font-size: 15px; margin-top: 24px;">
    🔄 Participants compatibles proposés en remplacement
</h3>
<p style="font-size: 13px; color: #6b7280; margin: 0 0 15px 0;">
    Ces participants sont disponibles et ont un profil compatible avec le vôtre.
    Connectez-vous à l'application pour choisir un remplaçant.
</p>

<table class="details">
    <thead>
        <tr style="background: #1e3a5f;">
            <th style="padding: 10px 12px; color: white; font-size: 12px; text-align: left;">Participant</th>
            <th style="padding: 10px 12px; color: white; font-size: 12px; text-align: left;">Fonction</th>
            <th style="padding: 10px 12px; color: white; font-size: 12px; text-align: left;">Entreprise</th>
            <th style="padding: 10px 12px; color: white; font-size: 12px; text-align: left;">Compatibilité</th>
        </tr>
    </thead>
    <tbody>
        @foreach($remplacants->take(5) as $r)
        <tr>
            <td><strong>{{ $r->prenom }} {{ $r->nom }}</strong></td>
            <td>{{ $r->fonction ?: '-' }}</td>
            <td>
                @if($r->entreprise)
                {{ $r->entreprise->nom }}
                @else
                <em style="color: #9ca3af;">Indépendant</em>
                @endif
            </td>
            <td>{{ str_repeat('⭐', min($r->score_compatibilite, 5)) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@if($remplacants->count() > 5)
<p style="font-size: 13px; color: #6b7280; text-align: center; margin: 8px 0 0 0;">
    + {{ $remplacants->count() - 5 }} autre(s) remplaçant(s) disponibles dans l'application.
</p>
@endif

@else

<div class="info-box" style="background: #fefce8; border-color: #fde047;">
    <strong style="color: #713f12;">Aucun remplaçant trouvé</strong>
    Aucun remplaçant compatible n'a été trouvé automatiquement.
    Les organisateurs seront informés et vous contacteront si nécessaire.
</div>

@endif

<p style="margin-top: 20px;">
    Connectez-vous à l'application pour choisir un remplaçant ou gérer vos rendez-vous.
</p>

<p>Cordialement,<br><strong>L'équipe Business Forum — CCI-BF</strong></p>

@endsection