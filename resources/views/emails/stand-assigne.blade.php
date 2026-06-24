@extends('emails.layouts.email')

@section('content')
<h2>Votre stand est prêt !</h2>

<p>Bonjour <strong>{{ $participant->prenom }} {{ $participant->nom }}</strong>,</p>

<p>
    Un stand vous a été assigné pour l'événement <strong>{{ $nomEvenement }}</strong>.
</p>

<div class="info-box">
    <strong>Caractéristiques de votre stand</strong>
    <table class="details" style="margin-top:12px;">
        <tr>
            <td>Numéro de stand</td>
            <td><strong style="font-size:18px;">{{ $stand->numero }}</strong></td>
        </tr>
        @if($stand->typeStand)
        <tr>
            <td>Type</td>
            <td>{{ $stand->typeStand->nom }}</td>
        </tr>
        <tr>
            <td>Superficie</td>
            <td>{{ $stand->typeStand->superficie ?? $stand->superficie }} m²</td>
        </tr>
        @if($stand->typeStand->composants)
        <tr>
            <td>Équipements inclus</td>
            <td>{{ $stand->typeStand->composants }}</td>
        </tr>
        @endif
        @endif
        <tr>
            <td>Tarif</td>
            <td>
                @if($stand->est_gratuit)
                    Gratuit
                @else
                    {{ number_format($stand->montant, 0, ',', ' ') }} FCFA
                @endif
            </td>
        </tr>
    </table>
</div>

<p>Cordialement,<br><strong>L'équipe Business Forum</strong></p>
@endsection