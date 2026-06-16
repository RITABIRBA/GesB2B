<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 10px; color: #333; }
    h1 { font-size: 16px; color: #C8102E; margin: 0 0 4px 0; }
    .meta { font-size: 10px; color: #666; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; }
    th { background-color: #f0f0f0; }
    tr.annule { background-color: #fdecea; }
</style>
</head>
<body>
    <h1>Planning des rendez-vous - GesB2B</h1>
    <p class="meta">
        @if($evenement)
            Evenement : {{ $evenement->nom }} ({{ $evenement->ville }}, {{ $evenement->date_debut }})
        @else
            Tous les evenements
        @endif
        &mdash; Genere le {{ now()->format('d/m/Y H:i') }}
        &mdash; {{ $rendezVous->count() }} rendez-vous
    </p>
    <table>
        <thead>
            <tr>
                <th>Participant 1</th>
                <th>Entreprise 1</th>
                <th>Participant 2</th>
                <th>Entreprise 2</th>
                <th>Date</th>
                <th>Horaire</th>
                <th>Salle / Table</th>
                <th>Traducteur</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rendezVous as $rdv)
            <tr class="{{ $rdv->statut == 'annule' ? 'annule' : '' }}">
                <td>{{ $rdv->participant1->nom ?? '-' }} {{ $rdv->participant1->prenom ?? '' }}</td>
                <td>{{ $rdv->participant1->entreprise->nom ?? 'Independant' }}</td>
                <td>{{ $rdv->participant2->nom ?? '-' }} {{ $rdv->participant2->prenom ?? '' }}</td>
                <td>{{ $rdv->participant2->entreprise->nom ?? 'Independant' }}</td>
                <td>{{ $rdv->date ?? '-' }}</td>
                <td>{{ $rdv->heure_debut ?? '-' }} - {{ $rdv->heure_fin ?? '-' }}</td>
                <td>{{ $rdv->salle ?? '-' }}{{ $rdv->numero_table ? ' / Table '.$rdv->numero_table : '' }}</td>
                <td>{{ $rdv->traducteur->nom ?? '-' }}</td>
                <td>{{ $libelleStatut($rdv->statut) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>