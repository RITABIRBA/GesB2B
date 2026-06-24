@extends('emails.layouts.email')

@section('content')
<h2>Votre inscription est confirmée !</h2>

<p>Bonjour <strong>{{ $participant->prenom }} {{ $participant->nom }}</strong>,</p>

<p>
    Votre préinscription à <strong>Business Forum</strong> a été
    <strong>validée</strong> par notre équipe.
</p>

<div class="info-box">
    <strong>Vos identifiants de connexion</strong>
    <table class="details" style="margin-top:12px;">
        <tr>
            <td>Email</td>
            <td>{{ $participant->email }}</td>
        </tr>
        @if($motDePasse)
        <tr>
            <td>Mot de passe</td>
            <td>
                <strong style="font-family:monospace;font-size:16px;">
                    {{ $motDePasse }}
                </strong>
            </td>
        </tr>
        @endif
        <tr>
            <td>Code d'accès</td>
            <td>
                <strong style="font-family:monospace;font-size:16px;color:#1e3a5f;">
                    {{ $participant->code_acces }}
                </strong>
            </td>
        </tr>
    </table>
    <p style="margin:8px 0 0;font-size:12px;color:#666;">
        Ce mot de passe ne vous sera montré qu'une seule fois.
        Changez-le dès votre première connexion.
    </p>
</div>

<p>Cordialement,<br><strong>L'équipe Business Forum</strong></p>
@endsection