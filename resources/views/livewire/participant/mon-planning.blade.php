<div>

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6 no-print">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Mon Planning de Rendez-vous</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $rendezVous->count() }} RDV
            </span>
        </div>
        <button onclick="window.print()"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow no-print"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-print"></i>
            Imprimer mon planning
        </button>
    </div>

    {{-- En-tête imprimable --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white text-2xl font-bold"
                    style="background-color: #C8102E;">B</div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">GesB2B — CCI-BF</h1>
                    <p class="text-gray-500">Planning individuel de rendez-vous</p>
                </div>
            </div>
            <div class="text-right">
                <p class="font-bold text-gray-800 text-lg">
                    {{ $participant->nom ?? '-' }} {{ $participant->prenom ?? '' }}
                </p>
                <p class="text-gray-500 text-sm">
                    {{ $participant->entreprise->nom ?? 'Indépendant' }}
                </p>
                <p class="text-gray-400 text-xs mt-1">
                    Imprimé le {{ now()->format('d/m/Y à H:i') }}
                </p>
            </div>
        </div>
    </div>

    @if($rendezVous->isEmpty())
    <div class="bg-white rounded-xl shadow p-16 text-center text-gray-400 no-print">
        <i class="fa-solid fa-calendar-xmark text-5xl mb-3 block text-gray-300"></i>
        <p class="text-lg font-medium">Aucun rendez-vous planifié</p>
        <p class="text-sm text-gray-400 mt-1">
            Émettez des souhaits pour obtenir des rendez-vous
        </p>
    </div>
    @else

    @php $rdvParDate = $rendezVous->groupBy('date'); @endphp

    @foreach($rdvParDate as $date => $rdvs)
    <div class="mb-6">

        {{-- Titre journée --}}
        <div class="flex items-center gap-3 mb-3">
            <div class="h-px flex-1" style="background-color: #007A3D;"></div>
            <span class="text-sm font-bold px-4 py-1.5 rounded-full text-white"
                style="background-color: #007A3D;">
                <i class="fa-solid fa-calendar mr-1"></i>
                {{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}
            </span>
            <div class="h-px flex-1" style="background-color: #007A3D;"></div>
        </div>

        {{-- RDV de la journée --}}
        <div class="space-y-3">
            @foreach($rdvs as $index => $rdv)
            @php
                $estParticipant1 = $rdv->id_participant1 == $participant->id;
                $partenaire = $estParticipant1 ? $rdv->participant2 : $rdv->participant1;
            @endphp
            <div class="bg-white rounded-xl shadow p-5 flex items-center gap-5 hover:shadow-lg transition">

                {{-- Numéro --}}
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                    style="background-color: #C8102E;">
                    {{ $index + 1 }}
                </div>

                {{-- Horaire --}}
                <div class="text-center flex-shrink-0 w-24">
                    <p class="text-lg font-bold text-gray-800">{{ $rdv->heure_debut }}</p>
                    <p class="text-xs text-gray-400">↓</p>
                    <p class="text-lg font-bold text-gray-800">{{ $rdv->heure_fin }}</p>
                </div>

                <div class="w-px h-12 bg-gray-200 flex-shrink-0"></div>

                {{-- ← Salle & Table --}}
                <div class="text-center flex-shrink-0 w-28">
                    @if($rdv->salle)
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg mx-auto"
                        style="background-color: #2d5a8e;">
                        {{ $rdv->numero_table }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1 font-medium">{{ $rdv->salle }}</p>
                    <p class="text-xs text-gray-400">Table {{ $rdv->numero_table }}</p>
                    @else
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-gray-200 mx-auto">
                        <i class="fa-solid fa-question text-gray-400"></i>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Non assigné</p>
                    @endif
                </div>

                <div class="w-px h-12 bg-gray-200 flex-shrink-0"></div>

                {{-- Partenaire --}}
                <div class="flex-1">
                    <p class="text-xs text-gray-400 mb-1">Rendez-vous avec</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                            style="background-color: #2d5a8e;">
                            {{ strtoupper(substr($partenaire->prenom ?? 'X', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">
                                {{ $partenaire->nom ?? '-' }} {{ $partenaire->prenom ?? '' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $partenaire->entreprise->nom ?? 'Indépendant' }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ ucfirst($partenaire->role ?? '') }}
                                @if($partenaire->fonction)
                                — {{ $partenaire->fonction }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Traducteur --}}
                @if($rdv->traducteur)
                <div class="text-right flex-shrink-0">
                    <p class="text-xs text-gray-400 mb-1">Traducteur</p>
                    <span class="text-xs px-2 py-1 rounded-full text-white font-medium"
                        style="background-color: #8b5cf6;">
                        <i class="fa-solid fa-language mr-1"></i>
                        {{ $rdv->traducteur->nom }}
                    </span>
                    <p class="text-xs text-gray-400 mt-1">{{ $rdv->traducteur->langue }}</p>
                </div>
                @endif

                {{-- Statut --}}
                <div class="flex-shrink-0">
                    @if($rdv->statut == 'confirme')
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium"
                            style="background-color: #007A3D;">
                            <i class="fa-solid fa-circle-check mr-1"></i> Confirmé
                        </span>
                    @elseif($rdv->statut == 'annule')
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-red-600">
                            <i class="fa-solid fa-circle-xmark mr-1"></i> Annulé
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                            <i class="fa-solid fa-calendar-check mr-1"></i> Planifié
                        </span>
                    @endif
                </div>

            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    {{-- Footer --}}
    <div class="bg-white rounded-xl shadow p-4 mt-6 text-center text-sm text-gray-500">
        <i class="fa-solid fa-shield-halved mr-1"></i>
        Planning officiel CCI-BF — GesB2B Platform —
        {{ now()->format('d/m/Y') }}
    </div>

    @endif
</div>