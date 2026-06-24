@extends('emails.layouts.email')

@section('content')
<h2>Votre réservation de stand est validée !</h2>

<p>Bonjour <strong>{{ $nomDestinataire }}</strong>,</p>

<p>
    Nous avons le plaisir de vous informer que votre réservation de stand
    pour l'événement <strong>{{ $nomEvenement }}</strong> a été
    <strong>validée</strong> par l'administration.
</p>

<div class="info-box">
    <strong>Détails de votre stand</strong>
    <table class="details" style="margin-top:12px;">
        <tr>
            <td>Numéro de stand</td>
            <td><strong style="font-size:18px;">N° {{ $stand->numero_stand }}</strong></td>
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
            <td>Tarif</td>
            <td>
                @if($stand->est_gratuit)
                    <span style="color:#065f46;font-weight:600;">Gratuit</span>
                @else
                    <strong>{{ number_format($stand->prix_calcule, 0, ',', ' ') }} FCFA</strong>
                @endif
            </td>
        </tr>
    </table>

    @if(!$stand->est_gratuit && $stand->prix_calcule > 0)
    <p style="margin:12px 0 0;font-size:13px;color:#1e3a5f;font-weight:600;">
        Vous pouvez maintenant procéder au paiement de votre stand
        depuis votre espace personnel.
    </p>
    @endif
</div>

<p>Cordialement,<br><strong>L'équipe Business Forum</strong></p>
@endsection