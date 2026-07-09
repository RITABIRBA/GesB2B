<div>

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h3 class="text-xl font-bold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-address-book" style="color: #007A3D;"></i>
                Catalogue des participants
            </h3>
            <p class="text-sm text-gray-400 mt-1">
                @if($evenement)
                    Participants inscrits à <strong>{{ $evenement->nom }}</strong>
                @else
                    Tous les participants
                @endif
            </p>
        </div>

        {{-- Sélecteur événement --}}
        <select wire:model.live="id_evenement"
            class="border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 text-sm">
            <option value="">Tous les événements</option>
            @foreach($evenements as $ev)
            <option value="{{ $ev->id }}">{{ $ev->nom }}</option>
            @endforeach
        </select>
    </div>

    @if(!$catalogueDisponible)
    {{-- Catalogue pas encore disponible --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-12 text-center">
        <i class="fa-solid fa-lock text-5xl text-yellow-400 mb-4 block"></i>
        <h3 class="text-lg font-bold text-gray-700 mb-2">Catalogue non disponible</h3>
        <p class="text-gray-500 text-sm">
            Le catalogue sera disponible à partir du
            <strong>{{ $evenement ? \Carbon\Carbon::parse($evenement->date_debut)->format('d/m/Y') : '-' }}</strong>
        </p>
    </div>

    @else

    {{-- FILTRES --}}
    <div class="bg-white rounded-2xl shadow p-4 mb-6 flex gap-3 flex-wrap">
        <div class="relative flex-1 min-w-48">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-sm"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher une entreprise, secteur..."
                class="w-full border rounded-xl pl-9 pr-4 py-2.5 focus:outline-none focus:ring-2 text-sm">
        </div>
        <select wire:model.live="secteur_filtre"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm min-w-48">
            <option value="">Tous les secteurs</option>
            @foreach($secteurs as $secteur)
            <option value="{{ $secteur }}">{{ $secteur }}</option>
            @endforeach
        </select>
        <select wire:model.live="pays_filtre"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm min-w-36">
            <option value="">Tous les pays</option>
            @foreach($pays as $p)
            <option value="{{ $p }}">{{ $p }}</option>
            @endforeach
        </select>
        @if($search || $secteur_filtre || $pays_filtre)
        <button wire:click="$set('search', ''); $set('secteur_filtre', ''); $set('pays_filtre', '')"
            class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 text-sm flex items-center gap-1.5">
            <i class="fa-solid fa-xmark"></i> Effacer
        </button>
        @endif
    </div>

    {{-- ══════════ ENTREPRISES ══════════ --}}
    @if($entreprises->isNotEmpty())
    <div class="mb-8">
        <h4 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-building" style="color: #007A3D;"></i>
            Entreprises
            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">
                {{ $entreprises->total() }}
            </span>
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($entreprises as $entreprise)
            @php
                $representant = $entreprise->participants->first();
            @endphp
            <div class="bg-white rounded-2xl shadow hover:shadow-lg transition border border-gray-100 overflow-hidden">
                {{-- Bande couleur --}}
                <div class="h-1.5 w-full" style="background: linear-gradient(90deg, #007A3D, #C8102E);"></div>

                <div class="p-5">
                    {{-- Header entreprise --}}
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0"
                            style="background-color: #007A3D;">
                            {{ strtoupper(substr($entreprise->nom, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h5 class="font-bold text-gray-800 text-sm truncate">{{ $entreprise->nom }}</h5>
                            @if($entreprise->secteur_activite)
                            <span class="text-xs px-2 py-0.5 rounded-full text-white mt-1 inline-block"
                                style="background-color: #007A3D;">
                                {{ $entreprise->secteur_activite }}
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Infos entreprise --}}
                    <div class="space-y-1.5 text-xs text-gray-500 mb-4">
                        @if($entreprise->pays || $entreprise->ville)
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-location-dot w-4" style="color: #C8102E;"></i>
                            {{ $entreprise->ville }}@if($entreprise->ville && $entreprise->pays), @endif{{ $entreprise->pays }}
                        </div>
                        @endif
                        @if($entreprise->ifu)
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-hashtag w-4 text-gray-400"></i>
                            IFU : {{ $entreprise->ifu }}
                        </div>
                        @endif
                    </div>

                    {{-- Représentant --}}
                    @if($representant)
                    <div class="border-t border-gray-100 pt-3">
                        <p class="text-xs text-gray-400 mb-2 font-medium uppercase tracking-wide">Représentant</p>
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                style="background-color: #C8102E;">
                                {{ strtoupper(substr($representant->prenom ?? 'R', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-700">
                                    {{ $representant->nom }} {{ $representant->prenom }}
                                </p>
                                @if($representant->fonction)
                                <p class="text-xs text-gray-400">{{ $representant->fonction }}</p>
                                @endif
                            </div>
                        </div>
                        @if($representant->email)
                        <div class="flex items-center gap-1.5 mt-2 text-xs text-gray-400">
                            <i class="fa-solid fa-envelope w-4"></i>
                            {{ $representant->email }}
                        </div>
                        @endif
                        @if($representant->telephone)
                        <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-400">
                            <i class="fa-solid fa-phone w-4"></i>
                            {{ $representant->telephone }}
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- Profil B2B --}}
                    @if($representant && $representant->secteur_activite)
                    <div class="mt-3 bg-blue-50 border border-blue-100 rounded-xl p-2.5">
                        <p class="text-xs font-semibold text-blue-700 mb-1">
                            <i class="fa-solid fa-handshake mr-1"></i> Profil B2B
                        </p>
                        @if($representant->zone_geographique)
                        <p class="text-xs text-blue-600">
                            <i class="fa-solid fa-globe mr-1"></i>
                            {{ $representant->zone_geographique }}
                        </p>
                        @endif
                        @if($representant->secteurs_recherche)
                        @php
                            $secteurs_recherche = is_array($representant->secteurs_recherche)
                                ? $representant->secteurs_recherche
                                : json_decode($representant->secteurs_recherche ?? '[]', true);
                        @endphp
                        @if(!empty($secteurs_recherche))
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach(array_slice($secteurs_recherche, 0, 2) as $s)
                            <span class="text-xs bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full">{{ $s }}</span>
                            @endforeach
                            @if(count($secteurs_recherche) > 2)
                            <span class="text-xs text-blue-400">+{{ count($secteurs_recherche) - 2 }}</span>
                            @endif
                        </div>
                        @endif
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $entreprises->links() }}</div>
    </div>
    @endif

    {{-- ══════════ PARTICIPANTS INDIVIDUELS ══════════ --}}
    @if($participantsIndividuels->isNotEmpty())
    <div>
        <h4 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-user" style="color: #C8102E;"></i>
            Participants individuels
            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-medium">
                {{ $participantsIndividuels->total() }}
            </span>
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($participantsIndividuels as $p)
            <div class="bg-white rounded-2xl shadow hover:shadow-lg transition border border-gray-100 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-xl font-bold flex-shrink-0"
                        style="background-color: #C8102E;">
                        {{ strtoupper(substr($p->prenom ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="font-bold text-gray-800 text-sm">{{ $p->nom }} {{ $p->prenom }}</h5>
                        @if($p->fonction)
                        <p class="text-xs text-gray-400">{{ $p->fonction }}</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-1.5 text-xs text-gray-500">
                    @if($p->secteur_activite)
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-industry w-4" style="color: #007A3D;"></i>
                        {{ $p->secteur_activite }}
                    </div>
                    @endif
                    @if($p->pays || $p->ville)
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-location-dot w-4" style="color: #C8102E;"></i>
                        {{ $p->ville }}@if($p->ville && $p->pays), @endif{{ $p->pays }}
                    </div>
                    @endif
                    @if($p->email)
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-envelope w-4 text-gray-400"></i>
                        {{ $p->email }}
                    </div>
                    @endif
                    @if($p->telephone)
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-phone w-4 text-gray-400"></i>
                        {{ $p->telephone }}
                    </div>
                    @endif
                </div>

                @if($p->zone_geographique)
                <div class="mt-3 bg-green-50 border border-green-100 rounded-xl p-2.5">
                    <p class="text-xs font-semibold text-green-700 mb-1">
                        <i class="fa-solid fa-handshake mr-1"></i> Profil B2B
                    </p>
                    <p class="text-xs text-green-600">
                        <i class="fa-solid fa-globe mr-1"></i>
                        {{ $p->zone_geographique }}
                    </p>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $participantsIndividuels->links() }}</div>
    </div>
    @endif

    {{-- Aucun résultat --}}
    @if($entreprises->isEmpty() && $participantsIndividuels->isEmpty())
    <div class="bg-white rounded-2xl shadow p-12 text-center text-gray-400">
        <i class="fa-solid fa-address-book text-5xl mb-3 block text-gray-300"></i>
        <p class="text-lg font-medium">Aucun participant trouvé</p>
        <p class="text-sm text-gray-300 mt-1">
            @if($search || $secteur_filtre || $pays_filtre)
                Essayez de modifier vos critères de recherche
            @else
                Aucune entreprise ou participant inscrit pour cet événement
            @endif
        </p>
    </div>
    @endif

    @endif

</div>