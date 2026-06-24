@extends('emails.layouts.email')

@section('content')
<h2>Vous avez un rendez-vous !</h2>

<p>Bonjour <strong>{{ $destinataire->prenom }} {{ $destinataire->nom }}</strong>,</p>

<p>
    Votre souhait de rencontre avec
    <strong>{{ $partenaire->prenom }} {{ $partenaire->nom }}</strong>
    est mutuel. Un rendez-vous a été planifié pour vous.
</p>

<div class="info-box">
    <strong>Détails du rendez-vous</strong>
    <table class="details" style="margin-top:12px;">
        <tr>
            <td>Événement</td>
            <td>{{ $nomEvenement }}</td>
        </tr>
        <tr>
            <td>Partenaire</td>
            <td>
                {{ $partenaire->prenom }} {{ $partenaire->nom }}
                @if($partenaire->entreprise)
                    <br><span style="font-size:12px;color:#666;">{{ $partenaire->entreprise->nom }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td>Date</td>
            <td>{{ \Carbon\Carbon::parse($rendezVous->date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>Heure</td>
            <td>
                {{ \Carbon\Carbon::parse($rendezVous->heure_debut)->format('H:i') }}
                —
                {{ \Carbon\Carbon::parse($rendezVous->heure_fin)->format('H:i') }}
            </td>
        </tr>
        @if($rendezVous->table_numero)
        <tr>
            <td>Table</td>
            <td><strong>N° {{ $rendezVous->table_numero }}</strong></td>
        </tr>
        @endif
    </table>
</div>

<p>Cordialement,<br><strong>L'équipe Business Forum</strong></p>
@endsection