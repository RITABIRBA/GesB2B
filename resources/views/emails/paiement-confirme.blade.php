@extends('emails.layouts.email')

@section('content')
<h2>Votre paiement a été confirmé</h2>

<p>Bonjour <strong>{{ $participant->prenom }} {{ $participant->nom }}</strong>,</p>

<p>
    Nous confirmons la réception de votre paiement pour l'événement
    <strong>{{ $nomEvenement }}</strong>.
</p>

<div class="info-box">
    <strong>Récapitulatif du paiement</strong>
    <table class="details" style="margin-top:12px;">
        <tr>
            <td>Montant</td>
            <td><strong>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</strong></td>
        </tr>
        <tr>
            <td>Mode de paiement</td>
            <td>
                @switch($paiement->mode_paiement)
                    @case('especes')  Espèces @break
                    @case('cheque')   Chèque @if($paiement->numero_cheque) n° {{ $paiement->numero_cheque }} @endif @break
                    @case('virement') Virement bancaire @break
                    @case('mobile')   Mobile money @break
                    @default          {{ $paiement->mode_paiement }}
                @endswitch
            </td>
        </tr>
        <tr>
            <td>Date</td>
            <td>{{ \Carbon\Carbon::parse($paiement->created_at)->format('d/m/Y à H:i') }}</td>
        </tr>
        <tr>
            <td>Référence</td>
            <td>{{ str_pad($paiement->id, 6, '0', STR_PAD_LEFT) }}</td>
        </tr>
    </table>
</div>

<p>Cordialement,<br><strong>L'équipe Business Forum</strong></p>
@endsection