@extends('emails.layouts.email')

@section('content')
<h2>Votre préinscription a bien été reçue</h2>

<p>Bonjour <strong>{{ $participant->prenom }} {{ $participant->nom }}</strong>,</p>

<p>
    Nous avons bien reçu votre demande de préinscription pour l'événement
    <strong>{{ $nomEvenement }}</strong>.
</p>

<div class="info-box">
    <strong>Statut : En cours d'examen</strong>
    Notre équipe va examiner votre dossier dans les meilleurs délais.
    Vous recevrez un email dès qu'une décision aura été prise.
</div>

<p>Cordialement,<br><strong>L'équipe Business Forum</strong></p>
@endsection