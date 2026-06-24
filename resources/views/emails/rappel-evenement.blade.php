@extends('emails.layouts.email')

@section('content')
<h2>Rappel — C'est bientôt !</h2>

<p>Bonjour <strong>{{ $participant->prenom }} {{ $participant->nom }}</strong>,</p>

<p>
    @if($joursRestants === 1)
        L'événement <strong>{{ $nomEvenement }}</strong> a lieu <strong>demain</strong>. Préparez-vous !
    @else
        L'événement <strong>{{ $nomEvenement }}</strong> a lieu dans
        <strong>{{ $joursRestants }} jours</strong>.
    @endif
</p>

<div class="info-box">
    <strong>Informations sur l'événement</strong>
    <table class="details" style="margin-top:12px;">
        <tr>
            <td>Événement</td>
            <td>{{ $nomEvenement }}</td>
        </tr>
        <tr>
            <td>Date</td>
            <td>{{ $dateEvenement }}</td>
        </tr>
        <tr>
            <td>Lieu</td>
            <td>{{ $lieuEvenement }}</td>
        </tr>
    </table>
</div>

<p>Cordialement,<br><strong>L'équipe Business Forum</strong></p>
@endsection