@extends('emails.layouts.email')

@section('content')
<h2>Votre planning de rendez-vous</h2>

<p>Bonjour <strong>{{ $participant->prenom }} {{ $participant->nom }}</strong>,</p>

<p>
    Le planning des rendez-vous pour l'événement <strong>{{ $nomEvenement }}</strong>
    du <strong>{{ $dateEvenement }}</strong> est disponible.
</p>

@if($rendezVous->isEmpty())
    <div class="info-box">
        <strong>Aucun rendez-vous planifié pour cet événement.</strong>
    </div>
@else
    <table class="details">
        <thead>
            <tr style="background:#eef4fb;">
                <td><strong>Heure</strong></td>
                <td><strong>Partenaire</strong></td>
                <td><strong>Table</strong></td>
            </tr>
        </thead>
        <tbody>
        @foreach($rendezVous->sortBy('heure_debut') as $rdv)
            @php
                $partenaire = $rdv->id_participant1 === $participant->id
                    ? $rdv->participant2
                    : $rdv->participant1;
            @endphp
            <tr>
                <td>
                    {{ \Carbon\Carbon::parse($rdv->heure_debut)->format('H:i') }}
                    —
                    {{ \Carbon\Carbon::parse($rdv->heure_fin)->format('H:i') }}
                </td>
                <td>
                    {{ $partenaire?->prenom }} {{ $partenaire?->nom }}
                    @if($partenaire?->entreprise)
                        <br><span style="font-size:12px;color:#666;">{{ $partenaire->entreprise->nom }}</span>
                    @endif
                </td>
                <td>{{ $rdv->table_numero ?? '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

<p style="margin-top:20px;">Cordialement,<br><strong>L'équipe Business Forum</strong></p>
@endsection