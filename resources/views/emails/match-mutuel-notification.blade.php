@extends('emails.layouts.email')

@section('content')
<h2>Vous avez un souhait mutuel !</h2>

<p>Bonjour <strong>{{ $destinataire->prenom }} {{ $destinataire->nom }}</strong>,</p>

<p>
    Bonne nouvelle ! Votre souhait de rencontre avec
    <strong>{{ $partenaire->prenom }} {{ $partenaire->nom }}</strong>
    @if($partenaire->entreprise)
        de <strong>{{ $partenaire->entreprise->nom }}</strong>
    @endif
    est <strong>mutuel</strong>.
</p>

<div class="info-box">
    <strong>Que se passe-t-il maintenant ?</strong>
    <p style="margin:8px 0 0;">
        Un rendez-vous sera automatiquement planifié entre vous deux
        lors de la génération du planning de l'événement
        <strong>{{ $nomEvenement }}</strong>.
        Vous recevrez votre planning complet par email dès qu'il sera disponible.
    </p>
</div>

<p>Cordialement,<br><strong>L'équipe Business Forum</strong></p>
@endsection