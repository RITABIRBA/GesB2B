@extends('emails.layouts.email')

@section('content')
<h2>Résultat de votre préinscription</h2>

<p>Bonjour <strong>{{ $participant->prenom }} {{ $participant->nom }}</strong>,</p>

<p>
    Nous avons examiné votre dossier de préinscription pour l'événement
    <strong>{{ $nomEvenement }}</strong>.
</p>

<div class="info-box" style="border-left-color:#dc2626;background:#fef2f2;">
    <strong style="color:#991b1b;">Statut : Dossier non retenu</strong>
    @if($motif)
        <p style="margin:8px 0 0;"><strong>Motif :</strong> {{ $motif }}</p>
    @else
        <p style="margin:8px 0 0;">Votre dossier n'a malheureusement pas pu être retenu pour cette édition.</p>
    @endif
</div>

<p>
    Nous vous remercions de l'intérêt que vous portez à notre événement
    et espérons vous accueillir lors d'une prochaine édition.
</p>

<p>Cordialement,<br><strong>L'équipe Business Forum</strong></p>
@endsection