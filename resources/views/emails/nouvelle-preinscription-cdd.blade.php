@extends('emails.layouts.email')

@section('content')
<h2>Nouvelle préinscription à valider</h2>

<p>Bonjour <strong>{{ $cdd->name }}</strong>,</p>

<p>
    Un participant vient de soumettre une préinscription pour l'événement
    <strong>{{ $nomEvenement }}</strong> et vous a désigné comme CDD référent.
</p>

<div class="info-box">
    <strong>Informations du participant</strong>
    <table class="details" style="margin-top:12px;">
        <tr>
            <td>Nom complet</td>
            <td><strong>{{ $participant->prenom }} {{ $participant->nom }}</strong></td>
        </tr>
        <tr>
            <td>Téléphone</td>
            <td>{{ $participant->telephone }}</td>
        </tr>
        @if($participant->email)
        <tr>
            <td>Email</td>
            <td>{{ $participant->email }}</td>
        </tr>
        @endif
        @if($participant->fonction)
        <tr>
            <td>Fonction</td>
            <td>{{ $participant->fonction }}</td>
        </tr>
        @endif
        @if($participant->entreprise)
        <tr>
            <td>Entreprise</td>
            <td>{{ $participant->entreprise->nom }}</td>
        </tr>
        @endif
        <tr>
            <td>Pays / Ville</td>
            <td>{{ $participant->ville }}, {{ $participant->pays }}</td>
        </tr>
    </table>
</div>

<p>
    Connectez-vous à votre espace pour examiner et valider cette préinscription.
</p>

<p>Cordialement,<br><strong>L'équipe Business Forum</strong></p>
@endsection