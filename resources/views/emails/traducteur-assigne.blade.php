@extends('emails.layouts.email')

@section('content')
<h2>Nouveau rendez-vous à interpréter</h2>

<p>Bonjour <strong>{{ $traducteur->nom }}</strong>,</p>

<p>
    Un rendez-vous vous a été assigné pour l'événement
    <strong>{{ $nomEvenement }}</strong>.
    Veuillez vous assurer d'être disponible aux horaires indiqués ci-dessous.
</p>

<div class="info-box">
    <strong>Détails du rendez-vous</strong>
    <table class="details" style="margin-top:12px;">
        <tr>
            <td>Date</td>
            <td><strong>{{ \Carbon\Carbon::parse($rendezVous->date)->format('d/m/Y') }}</strong></td>
        </tr>
        <tr>
            <td>Heure</td>
            <td>
                <strong>
                    {{ \Carbon\Carbon::parse($rendezVous->heure_debut)->format('H:i') }}
                    —
                    {{ \Carbon\Carbon::parse($rendezVous->heure_fin)->format('H:i') }}
                </strong>
            </td>
        </tr>
        @if($rendezVous->salle)
        <tr>
            <td>Salle</td>
            <td>{{ $rendezVous->salle }}</td>
        </tr>
        @endif
        @if($rendezVous->numero_table)
        <tr>
            <td>Table</td>
            <td><strong>N° {{ $rendezVous->numero_table }}</strong></td>
        </tr>
        @endif
        <tr>
            <td>Participant 1</td>
            <td>
                {{ $rendezVous->participant1?->prenom }}
                {{ $rendezVous->participant1?->nom }}
                @if($rendezVous->participant1?->entreprise)
                    <br>
                    <span style="font-size:12px;color:#666;">
                        {{ $rendezVous->participant1->entreprise->nom }}
                    </span>
                @endif
            </td>
        </tr>
        <tr>
            <td>Participant 2</td>
            <td>
                {{ $rendezVous->participant2?->prenom }}
                {{ $rendezVous->participant2?->nom }}
                @if($rendezVous->participant2?->entreprise)
                    <br>
                    <span style="font-size:12px;color:#666;">
                        {{ $rendezVous->participant2->entreprise->nom }}
                    </span>
                @endif
            </td>
        </tr>
    </table>
</div>

<p>
    Merci d'être présent(e) à l'heure indiquée.
    Pour toute question, contactez l'administration.
</p>

<p>Cordialement,<br><strong>L'équipe Business Forum</strong></p>
@endsection