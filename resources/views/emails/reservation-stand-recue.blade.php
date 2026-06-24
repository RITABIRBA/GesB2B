@extends('emails.layouts.email')

@section('content')
<h2>Demande de réservation reçue</h2>

<p>Bonjour <strong>{{ $nomDestinataire }}</strong>,</p>

<p>
    Nous avons bien reçu votre demande de réservation de stand
    pour l'événement <strong>{{ $nomEvenement }}</strong>.
</p>

<div class="info-box">
    <strong>Détails du stand demandé</strong>
    <table class="details" style="margin-top:12px;">
        <tr>
            <td>Numéro de stand</td>
            <td><strong>N° {{ $stand->numero_stand }}</strong></td>
        </tr>
        @if($stand->standing)
        <tr>
            <td>Standing</td>
            <td>{{ $stand->standing }}</td>
        </tr>
        @endif
        @if($stand->superficie)
        <tr>
            <td>Superficie</td>
            <td>{{ $stand->superficie }} m²</td>
        </tr>
        @endif
        <tr>
            <td>Statut</td>
            <td><strong style="color:#92400e;">En attente de validation</strong></td>
        </tr>
    </table>
    <p style="margin:8px 0 0;font-size:12px;color:#666;">
        Votre demande sera examinée par l'administration dans les meilleurs délais.
        Vous recevrez un email dès qu'une décision aura été prise.
    </p>
</div>

<p>Cordialement,<br><strong>L'équipe Business Forum</strong></p>
@endsection