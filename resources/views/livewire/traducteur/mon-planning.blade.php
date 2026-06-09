<div>

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Mon Planning de Traduction</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $rendezVous->count() }} RDV
            </span>
        </div>
        <button onclick="window.print()"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-print"></i>
            Imprimer
        </button>
    </div>

    {{-- Filtres --}}
    <div class="flex gap-4 mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher un participant..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
        <select wire:model.live="filtre_statut"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm text-gray-600">
            <option value="">Tous les statuts</option>
            <option value="planifie">Planifié</option>
            <option value="confirme">Confirmé</option>
            <option value="annule">Annulé</option>
            <option value="termine">Terminé</option>
        </select>
    </div>

    {{-- En-tête imprimable --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white text-2xl font-bold"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-language"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">GesB2B — CCI-BF</h1>
                    <p class="text-gray-500">Planning de traduction</p>
                </div>
            </div>
            @if($traducteur)
            <div class="text-right">
                <p class="font-bold text-gray-800 text-lg">
                    {{ $traducteur->nom }} {{ $traducteur->prenom }}
                </p>
                <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-language mr-1"></i>
                    {{ $traducteur->langue }}
                </span>
                <p class="text-gray-400 text-xs mt-1">
                    Imprimé le {{ now()->format('d/m/Y à H:i') }}
                </p>
            </div>
            @endif
        </div>
    </div>

    @if($rendezVous->isEmpty())
    <div class="bg-white rounded-xl shadow p-16 text-center text-gray-400">
        <i class="fa-solid fa-calendar-xmark text-5xl mb-3 block text-gray-300"></i>
        <p class="text-lg font-medium">Aucun rendez-vous assigné</p>
        <p class="text-sm text-gray-400 mt-1">
            L'administrateur vous assignera des rendez-vous
        </p>
    </div>
    @else

    {{-- Groupé par date --}}
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
            <div class="bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
                <div class="flex items-center gap-5">

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
                    <div class="text-center flex-shrink-0 w-32">
                        @if($rdv->salle)
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-xl mx-auto"
                            style="background-color: #2d5a8e;">
                            {{ $rdv->numero_table }}
                        </div>
                        <p class="text-xs font-medium text-gray-700 mt-1">{{ $rdv->salle }}</p>
                        <p class="text-xs text-gray-400">Table {{ $rdv->numero_table }}</p>
                        @else
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-gray-200 mx-auto">
                            <i class="fa-solid fa-question text-gray-400"></i>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Non assigné</p>
                        @endif
                    </div>

                    <div class="w-px h-12 bg-gray-200 flex-shrink-0"></div>

                    {{-- Participants --}}
                    <div class="flex-1">
                        <p class="text-xs text-gray-400 mb-2">Participants à traduire</p>
                        <div class="flex items-center gap-4">

                            {{-- Participant 1 --}}
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                    style="background-color: #C8102E;">
                                    {{ strtoupper(substr($rdv->participant1->prenom ?? 'X', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">
                                        {{ $rdv->participant1->nom ?? '-' }}
                                        {{ $rdv->participant1->prenom ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $rdv->participant1->entreprise->nom ?? 'Indépendant' }}
                                    </p>
                                    @if($rdv->participant1->fonction)
                                    <p class="text-xs text-gray-400">
                                        {{ $rdv->participant1->fonction }}
                                    </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Séparateur --}}
                            <div class="flex flex-col items-center gap-1 flex-shrink-0">
                                <i class="fa-solid fa-arrows-left-right text-gray-400 text-lg"></i>
                                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">
                                    RDV
                                </span>
                            </div>

                            {{-- Participant 2 --}}
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                    style="background-color: #007A3D;">
                                    {{ strtoupper(substr($rdv->participant2->prenom ?? 'X', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">
                                        {{ $rdv->participant2->nom ?? '-' }}
                                        {{ $rdv->participant2->prenom ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $rdv->participant2->entreprise->nom ?? 'Indépendant' }}
                                    </p>
                                    @if($rdv->participant2->fonction)
                                    <p class="text-xs text-gray-400">
                                        {{ $rdv->participant2->fonction }}
                                    </p>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

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
                        @elseif($rdv->statut == 'termine')
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-gray-500">
                            <i class="fa-solid fa-flag-checkered mr-1"></i> Terminé
                        </span>
                        @else
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                            <i class="fa-solid fa-calendar-check mr-1"></i> Planifié
                        </span>
                        @endif
                    </div>

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