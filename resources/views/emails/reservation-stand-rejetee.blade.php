@extends('emails.layouts.email')

@section('content')
<h2>Concernant votre réservation de stand</h2>

<p>Bonjour <strong>{{ $nomDestinataire }}</strong>,</p>

<p>
    Nous avons examiné votre demande de réservation du stand
    <strong>N° {{ $stand->numero_stand }}</strong>
    pour l'événement <strong>{{ $nomEvenement }}</strong>.
</p>

<div class="info-box" style="border-left-color:#dc2626;background:#fef2f2;">
    <strong style="color:#991b1b;">Statut : Réservation non retenue</strong>
    @if($motif)
        <p style="margin:8px 0 0;"><strong>Motif :</strong> {{ $motif }}</p>
    @else
        <p style="margin:8px 0 0;">
            Votre demande de réservation n'a malheureusement pas pu être retenue.
        </p>
    @endif
</div>

<p>
    D'autres stands peuvent être disponibles. Connectez-vous à votre espace
    pour consulter les stands disponibles.
</p>

<p>Cordialement,<br><strong>L'équipe Business Forum</strong></p>
@endsection