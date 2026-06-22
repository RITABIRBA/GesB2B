@extends('emails.layout')

@section('content')
<h2 style="color: #007A3D; margin-bottom: 16px;">Votre préinscription est validée !</h2>

<p>Bonjour {{ $participant->nom }} {{ $participant->prenom }},</p>

<p>
    Nous avons le plaisir de vous informer que votre préinscription au
    Business Forum CCI-BF a été <strong>validée</strong> par notre équipe.
</p>

<div style="background-color: #f0f9f4; border: 1px solid #007A3D; border-radius: 12px; padding: 20px; margin: 20px 0;">
    <p style="font-weight: bold; color: #007A3D; margin-bottom: 12px;">
        Vos identifiants de connexion :
    </p>

    @if($participant->email && $password)
    <p style="margin: 6px 0;">
        <strong>Email :</strong> {{ $participant->email }}
    </p>
    <p style="margin: 6px 0;">
        <strong>Mot de passe temporaire :</strong>
        <span style="font-family: monospace; background: #fff; padding: 4px 10px; border-radius: 6px; font-weight: bold; color: #C8102E;">
            {{ $password }}
        </span>
    </p>
    <p style="font-size: 13px; color: #666; margin-top: 10px;">
        Nous vous recommandons de modifier ce mot de passe après votre première connexion.
    </p>
    @endif

    <p style="margin: 12px 0 6px 0;">
        <strong>Code d'accès (alternative) :</strong>
        <span style="font-family: monospace; background: #fff; padding: 4px 10px; border-radius: 6px; font-weight: bold; color: #C8102E;">
            {{ $participant->code_acces }}
        </span>
    </p>
    <p style="font-size: 13px; color: #666;">
        Vous pouvez aussi vous connecter uniquement avec ce code, sans mot de passe.
    </p>
</div>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{ route('login') }}"
        style="background-color: #C8102E; color: white; padding: 14px 32px; border-radius: 10px; text-decoration: none; font-weight: bold;">
        Se connecter maintenant
    </a>
</p>

@if($participant->evenement)
<div style="background-color: #fff8e6; border: 1px solid #f59e0b; border-radius: 12px; padding: 16px; margin: 20px 0;">
    <p style="font-weight: bold; color: #92400e; margin-bottom: 6px;">
        Prochaine étape : {{ $participant->evenement->nom }}
    </p>
    @if($participant->evenement->type_paiement !== 'gratuit')
    <p style="font-size: 14px; color: #92400e;">
        Cet événement nécessite un paiement. Connectez-vous à votre espace
        pour finaliser votre inscription via Mobile Money, carte bancaire ou chèque.
    </p>
    @else
    <p style="font-size: 14px; color: #92400e;">
        Cet événement est gratuit — votre inscription est déjà complète !
    </p>
    @endif
</div>
@endif

<p>
    Pour toute question, n'hésitez pas à contacter l'équipe CCI-BF.
</p>

<p>Cordialement,<br>L'équipe Business Forum CCI-BF</p>
@endsection