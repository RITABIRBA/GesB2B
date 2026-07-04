@extends('emails.layouts.email')

@section('content')

<h2> Match Mutuel !</h2>

<p>Bonjour <strong>{{ $destinataire->prenom }} {{ $destinataire->nom }}</strong>,</p>

<div class="info-box">
    <strong>Vous et <strong>{{ $partenaire->prenom }} {{ $partenaire->nom }}</strong> vous êtes mutuellement sélectionnés !</strong>
    Un rendez-vous d'affaires sera automatiquement généré dans le planning.
</div>

{{-- Fiche partenaire --}}
<h3 style="color: #1e3a5f; font-size: 15px; margin-top: 24px;">Votre partenaire</h3>
<table class="details">
    <tr>
        <td>Nom</td>
        <td><strong>{{ $partenaire->prenom }} {{ $partenaire->nom }}</strong></td>
    </tr>
    @if($partenaire->fonction)
    <tr>
        <td>Fonction</td>
        <td>{{ $partenaire->fonction }}</td>
    </tr>
    @endif
    @if($partenaire->entreprise)
    <tr>
        <td>Entreprise</td>
        <td>{{ $partenaire->entreprise->nom }}</td>
    </tr>
    @endif
    @if($partenaire->email)
    <tr>
        <td>Email</td>
        <td>{{ $partenaire->email }}</td>
    </tr>
    @endif
    @if($partenaire->secteur_activite)
    <tr>
        <td>Secteur</td>
        <td>{{ $partenaire->secteur_activite }}</td>
    </tr>
    @endif
    @if($partenaire->zone_geographique)
    <tr>
        <td>Zone ciblée</td>
        <td>{{ $partenaire->zone_geographique }}</td>
    </tr>
    @endif
</table>

<div class="info-box" style="background: #fffbeb; border-color: #f59e0b;">
    <strong style="color: #92400e;">⏭ Prochaine étape</strong>
    Le planning des rendez-vous sera généré par les organisateurs.
    Vous recevrez un email avec votre planning complet dès qu'il sera disponible.
</div>

<p>Bonne chance pour vos échanges au <strong>{{ $nomEvenement }}</strong> !</p>

<p>Cordialement,<br><strong>L'équipe Business Forum — CCI-BF</strong></p>

@endsection