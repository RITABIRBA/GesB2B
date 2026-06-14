<div>

    {{-- Messages --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    @if($alertSuccess)
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ $alertSuccess }}
    </div>
    @endif

    @if($alertError)
    <div class="bg-red-100 border border-red-300 text-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
        {{ $alertError }}
    </div>
    @endif

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Souhaits & Matchmaking</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #007A3D;">
                {{ $participants->count() }} participant(s)
            </span>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #C8102E;">
                {{ $souhaits->count() }} souhait(s)
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Souhait manuel
        </button>
    </div>

    {{-- Filtres --}}
    <div class="flex gap-4 mb-6">
        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher un participant ou une entreprise..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
        <select wire:model.live="filtre_evenement"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm text-gray-600">
            <option value="">Tous les événements</option>
            @foreach($evenements as $evenement)
            <option value="{{ $evenement->id }}">{{ $evenement->nom }}</option>
            @endforeach
        </select>
    </div>

    {{-- LISTE DES PARTICIPANTS avec leurs infos complètes --}}
    <div class="mb-8">
        <h4 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-users" style="color: #007A3D;"></i>
            Participants inscrits aux RDV
        </h4>

        @forelse($participants as $p)
        <div class="bg-white rounded-xl shadow mb-4 overflow-hidden">

            {{-- Bandeau statut souhaits --}}
            <div class="px-5 py-2 flex items-center justify-between
                {{ $p->nb_souhaits >= 5 ? 'bg-green-50 border-b border-green-200' : 'bg-orange-50 border-b border-orange-200' }}">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-medium {{ $p->nb_souhaits >= 5 ? 'text-green-700' : 'text-orange-600' }}">
                        <i class="fa-solid fa-heart mr-1"></i>
                        {{ $p->nb_souhaits }} souhait(s) émis
                    </span>
                    @if($p->nb_mutuels > 0)
                    <span class="text-xs font-medium text-red-600">
                        <i class="fa-solid fa-arrows-left-right mr-1"></i>
                        {{ $p->nb_mutuels }} mutuel(s)
                    </span>
                    @endif
                    @if($p->nb_souhaits < 5)
                    <span class="text-xs text-orange-500">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        Minimum non atteint
                    </span>
                    @endif
                </div>
                {{-- Bouton matchmaking --}}
                <button wire:click="ouvrirMatchmaking({{ $p->id }})"
                    class="px-4 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90 flex items-center gap-1.5"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    Faire un matchmaking
                </button>
            </div>

            <div class="p-5">
                <div class="flex items-start gap-4">

                    {{-- Avatar --}}
                    <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl font-bold flex-shrink-0"
                        style="background-color: {{ $p->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                        {{ strtoupper(substr($p->prenom ?? 'X', 0, 1)) }}
                    </div>

                    <div class="flex-1">
                        {{-- Identité --}}
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg">
                                    {{ $p->nom }} {{ $p->prenom }}
                                    @if($p->genre == 'femme')
                                    <span class="text-sm text-gray-400">(Mme)</span>
                                    @elseif($p->genre == 'homme')
                                    <span class="text-sm text-gray-400">(M.)</span>
                                    @endif
                                </h4>
                                @if($p->fonction)
                                <p class="text-sm text-gray-500">
                                    <i class="fa-solid fa-briefcase mr-1 text-gray-400"></i>
                                    {{ $p->fonction }}
                                </p>
                                @endif
                                @if($p->entreprise)
                                <p class="text-sm text-gray-500">
                                    <i class="fa-solid fa-building mr-1 text-gray-400"></i>
                                    <span class="font-medium">{{ $p->entreprise->nom }}</span>
                                    @if($p->pays) · {{ $p->pays }} @endif
                                </p>
                                @endif
                                @if($p->email)
                                <p class="text-xs text-gray-400">
                                    <i class="fa-solid fa-envelope mr-1"></i>{{ $p->email }}
                                </p>
                                @endif
                                @if($p->telephone)
                                <p class="text-xs text-gray-400">
                                    <i class="fa-solid fa-phone mr-1"></i>{{ $p->telephone }}
                                </p>
                                @endif
                            </div>
                            <span class="font-mono text-xs font-bold px-2 py-1 rounded-lg flex-shrink-0"
                                style="background-color: #fde8ec; color: #C8102E;">
                                {{ $p->code_acces }}
                            </span>
                        </div>

                        {{-- Données professionnelles --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">

                            @if($p->secteur_activite)
                            <div class="bg-gray-50 rounded-lg p-2">
                                <p class="text-xs text-gray-400">Secteur</p>
                                <p class="text-xs font-semibold text-gray-800">{{ $p->secteur_activite }}</p>
                                @if($p->sous_secteur)
                                <p class="text-xs text-gray-400">{{ $p->sous_secteur }}</p>
                                @endif
                            </div>
                            @endif

                            @if($p->zone_geographique)
                            <div class="bg-gray-50 rounded-lg p-2">
                                <p class="text-xs text-gray-400">Zone ciblée</p>
                                <p class="text-xs font-semibold text-gray-800">{{ $p->zone_geographique }}</p>
                            </div>
                            @endif

                            @if($p->type_partenaire)
                            <div class="bg-gray-50 rounded-lg p-2">
                                <p class="text-xs text-gray-400">Cherche</p>
                                <p class="text-xs font-semibold text-gray-800">{{ $p->type_partenaire }}</p>
                            </div>
                            @endif

                            @if($p->secteur_recherche)
                            <div class="bg-gray-50 rounded-lg p-2">
                                <p class="text-xs text-gray-400">Secteur recherché</p>
                                <p class="text-xs font-semibold text-gray-800">{{ $p->secteur_recherche }}</p>
                            </div>
                            @endif

                            @if($p->annee_creation)
                            <div class="bg-gray-50 rounded-lg p-2">
                                <p class="text-xs text-gray-400">Créée en</p>
                                <p class="text-xs font-semibold text-gray-800">{{ $p->annee_creation }}</p>
                            </div>
                            @endif

                            @if($p->nombre_salaries)
                            <div class="bg-gray-50 rounded-lg p-2">
                                <p class="text-xs text-gray-400">Salariés</p>
                                <p class="text-xs font-semibold text-gray-800">{{ $p->nombre_salaries }}</p>
                            </div>
                            @endif

                        </div>

                        @if($p->description_activites)
                        <p class="text-xs text-gray-500 bg-gray-50 rounded-lg p-2">
                            {{ Str::limit($p->description_activites, 150) }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @empty
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400">
            <i class="fa-solid fa-users text-5xl mb-3 block text-gray-300"></i>
            <p>Aucun participant avec participation aux RDV activée.</p>
        </div>
        @endforelse
    </div>

    {{-- LISTE DES SOUHAITS --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b" style="background-color: #f8f9fa;">
            <h4 class="font-bold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-list-ol" style="color: #C8102E;"></i>
                Tous les souhaits
                <span class="text-sm font-normal text-gray-400">({{ $souhaits->count() }})</span>
            </h4>
        </div>
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-4 py-3 text-gray-500 font-semibold text-sm">Priorité</th>
                    <th class="px-4 py-3 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-4 py-3 text-gray-500 font-semibold text-sm">Veut rencontrer</th>
                    <th class="px-4 py-3 text-gray-500 font-semibold text-sm">Statut</th>
                    <th class="px-4 py-3 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($souhaits as $souhait)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-white text-sm"
                            style="background-color: {{ $souhait->priorite <= 3 ? '#C8102E' : ($souhait->priorite <= 10 ? '#007A3D' : '#6b7280') }}">
                            {{ $souhait->priorite }}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800 text-sm">
                            {{ $souhait->participant->nom ?? '-' }}
                            {{ $souhait->participant->prenom ?? '' }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $souhait->participant->entreprise->nom ?? 'Indépendant' }}
                        </p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800 text-sm">
                            {{ $souhait->participantCible->nom ?? '-' }}
                            {{ $souhait->participantCible->prenom ?? '' }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $souhait->participantCible->entreprise->nom ?? 'Indépendant' }}
                        </p>
                    </td>
                    <td class="px-4 py-3">
                        @if($souhait->type == 'mutuel')
                        <span class="px-2 py-1 rounded-full text-xs text-white font-medium"
                            style="background-color: #C8102E;">
                            <i class="fa-solid fa-arrows-left-right mr-1"></i> Mutuel 🎉
                        </span>
                        @else
                        <span class="px-2 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                            <i class="fa-solid fa-arrow-right mr-1"></i> Envoyé
                        </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <button wire:click="modifier({{ $souhait->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="supprimer({{ $souhait->id }})"
                                wire:confirm="Supprimer ce souhait ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-gray-400">
                        <i class="fa-solid fa-heart text-4xl mb-2 block text-gray-300"></i>
                        <p>Aucun souhait enregistré.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL MATCHMAKING --}}
    @if($showModalMatch && $participantMatch)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col">

            {{-- Header --}}
            <div class="flex justify-between items-center px-8 py-5 border-b flex-shrink-0"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        Matchmaking — {{ $participantMatch->nom }} {{ $participantMatch->prenom }}
                    </h3>
                    <p class="text-green-200 text-xs mt-0.5">
                        {{ $participantMatch->entreprise->nom ?? 'Indépendant' }}
                        · {{ $candidatsMatch->count() }} candidat(s) disponible(s)
                    </p>
                </div>
                <button wire:click="fermerMatchmaking"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            {{-- Profil du participant --}}
            <div class="px-6 pt-4 flex-shrink-0">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs">
                    <p class="font-bold text-blue-700 mb-2">
                        <i class="fa-solid fa-user mr-1"></i>
                        Profil de {{ $participantMatch->nom }} :
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @if($participantMatch->secteur_recherche)
                        <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">
                            Cherche secteur : {{ $participantMatch->secteur_recherche }}
                        </span>
                        @endif
                        @if($participantMatch->zone_geographique)
                        <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">
                            Zone : {{ $participantMatch->zone_geographique }}
                        </span>
                        @endif
                        @if($participantMatch->type_partenaire)
                        <span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-medium">
                            Type : {{ $participantMatch->type_partenaire }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Recherche --}}
            <div class="px-6 pt-3 flex-shrink-0">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                    <input wire:model.live="search_cible" type="text"
                        placeholder="Rechercher un candidat..."
                        class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
                </div>
            </div>

            {{-- Liste des candidats --}}
            <div class="p-6 overflow-y-auto flex-1 space-y-3">

                @forelse($candidatsMatch as $c)
                @php $points = $c->score_compatibilite; @endphp

                <div class="border-2 rounded-xl p-4 {{ $c->souhait_emis ? 'border-gray-100 bg-gray-50 opacity-60' : 'border-gray-200 hover:border-green-300' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3 flex-1">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-lg font-bold flex-shrink-0"
                                style="background-color: {{ $c->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                                {{ strtoupper(substr($c->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800">
                                    {{ $c->nom }} {{ $c->prenom }}
                                    @if($c->genre == 'femme')
                                    <span class="text-xs text-gray-400">(Mme)</span>
                                    @elseif($c->genre == 'homme')
                                    <span class="text-xs text-gray-400">(M.)</span>
                                    @endif
                                </p>
                                @if($c->fonction)
                                <p class="text-xs text-gray-500">{{ $c->fonction }}</p>
                                @endif
                                <p class="text-xs text-gray-500">
                                    {{ $c->entreprise->nom ?? 'Indépendant' }}
                                    @if($c->pays) · {{ $c->pays }} @endif
                                </p>

                                <div class="grid grid-cols-3 gap-2 mt-2">
                                    @if($c->secteur_activite)
                                    <div class="bg-white rounded-lg p-1.5 border border-gray-100">
                                        <p class="text-xs text-gray-400">Secteur</p>
                                        <p class="text-xs font-semibold">{{ $c->secteur_activite }}</p>
                                    </div>
                                    @endif
                                    @if($c->zone_geographique)
                                    <div class="bg-white rounded-lg p-1.5 border border-gray-100">
                                        <p class="text-xs text-gray-400">Zone</p>
                                        <p class="text-xs font-semibold">{{ $c->zone_geographique }}</p>
                                    </div>
                                    @endif
                                    @if($c->type_partenaire)
                                    <div class="bg-white rounded-lg p-1.5 border border-gray-100">
                                        <p class="text-xs text-gray-400">Cherche</p>
                                        <p class="text-xs font-semibold">{{ $c->type_partenaire }}</p>
                                    </div>
                                    @endif
                                </div>

                                {{-- Indicateurs compatibilité --}}
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @if($participantMatch->secteur_recherche)
                                    <span class="text-xs px-1.5 py-0.5 rounded-full font-medium
                                        {{ $c->secteur_activite == $participantMatch->secteur_recherche ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-400' }}">
                                        @if($c->secteur_activite == $participantMatch->secteur_recherche)
                                        <i class="fa-solid fa-check mr-0.5"></i>
                                        @else
                                        <i class="fa-solid fa-xmark mr-0.5"></i>
                                        @endif
                                        {{ $participantMatch->secteur_recherche }}
                                    </span>
                                    @endif
                                    @if($participantMatch->zone_geographique)
                                    <span class="text-xs px-1.5 py-0.5 rounded-full font-medium
                                        {{ $c->zone_geographique == $participantMatch->zone_geographique ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-400' }}">
                                        @if($c->zone_geographique == $participantMatch->zone_geographique)
                                        <i class="fa-solid fa-check mr-0.5"></i>
                                        @else
                                        <i class="fa-solid fa-xmark mr-0.5"></i>
                                        @endif
                                        {{ $participantMatch->zone_geographique }}
                                    </span>
                                    @endif
                                    @if($participantMatch->type_partenaire)
                                    <span class="text-xs px-1.5 py-0.5 rounded-full font-medium
                                        {{ $c->type_partenaire == $participantMatch->type_partenaire ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-400' }}">
                                        @if($c->type_partenaire == $participantMatch->type_partenaire)
                                        <i class="fa-solid fa-check mr-0.5"></i>
                                        @else
                                        <i class="fa-solid fa-xmark mr-0.5"></i>
                                        @endif
                                        {{ $participantMatch->type_partenaire }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Badge compatibilité + bouton --}}
                        <div class="flex flex-col items-end gap-2 flex-shrink-0 ml-3">
                            @if($points == 3)
                            <span class="text-xs px-2 py-0.5 rounded-full font-bold text-white" style="background-color: #007A3D;">
                                ⭐⭐⭐ Très compatible
                            </span>
                            @elseif($points == 2)
                            <span class="text-xs px-2 py-0.5 rounded-full font-bold text-white bg-blue-600">
                                ⭐⭐ Compatible
                            </span>
                            @elseif($points == 1)
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium text-gray-600 bg-gray-100">
                                ⭐ Peu compatible
                            </span>
                            @else
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium text-gray-400 bg-gray-50">
                                Secteur différent
                            </span>
                            @endif

                            @if($c->souhait_emis)
                            <span class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-green-500"></i>
                                Souhait émis
                            </span>
                            @else
                            <button wire:click="matchmaker({{ $c->id }})"
                                wire:loading.attr="disabled"
                                wire:target="matchmaker({{ $c->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90 flex items-center gap-1"
                                style="background-color: #C8102E;">
                                <span wire:loading.remove wire:target="matchmaker({{ $c->id }})">
                                    <i class="fa-solid fa-heart mr-1"></i> Matcher
                                </span>
                                <span wire:loading wire:target="matchmaker({{ $c->id }})">
                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                </span>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fa-solid fa-users text-4xl mb-2 block text-gray-300"></i>
                    <p class="text-sm">Aucun candidat disponible pour ce participant.</p>
                </div>
                @endforelse
            </div>

            <div class="p-4 border-t flex-shrink-0 flex justify-end">
                <button wire:click="fermerMatchmaking"
                    class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                    <i class="fa-solid fa-xmark mr-1"></i> Fermer
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL SOUHAIT MANUEL --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-heart"></i>
                    {{ $isEditing ? 'Modifier le souhait' : 'Souhait manuel' }}
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8 space-y-5">

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">
                        Participant demandeur *
                    </label>
                    <select wire:model.live="id_participant"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                        <option value="">-- Choisir --</option>
                        @foreach($tousParticipants as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->nom }} {{ $p->prenom }}
                            {{ $p->entreprise ? ' ('.$p->entreprise->nom.')' : '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_participant')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">
                        Veut rencontrer *
                    </label>
                    @if(!$id_participant)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 text-xs text-gray-400 text-center">
                        Sélectionnez d'abord le participant demandeur
                    </div>
                    @elseif(count($participantsCibles) == 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-xs text-yellow-700">
                        Aucun participant disponible dans le même événement.
                    </div>
                    @else
                    <div class="border rounded-xl overflow-hidden max-h-48 overflow-y-auto">
                        @foreach($participantsCibles as $p)
                        <label class="flex items-center gap-3 p-3 cursor-pointer hover:bg-gray-50 transition border-b last:border-0
                            {{ $id_participant_cible == $p['id'] ? 'bg-green-50' : '' }}">
                            <input type="radio"
                                wire:model="id_participant_cible"
                                value="{{ $p['id'] }}"
                                class="text-green-600">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                style="background-color: {{ ($p['genre'] ?? '') == 'femme' ? '#C8102E' : '#007A3D' }}">
                                {{ strtoupper(substr($p['prenom'] ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{ $p['nom'] }} {{ $p['prenom'] }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $p['entreprise']['nom'] ?? 'Indépendant' }}
                                </p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endif
                    @error('id_participant_cible')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">
                        Priorité * (1 = plus importante)
                    </label>
                    <input wire:model="priorite" type="number" min="1" max="20"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                        placeholder="Entre 1 et 20">
                    @error('priorite')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="closeModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="sauvegarder"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #C8102E;">
                        <span wire:loading.remove>
                            <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-floppy-disk' }} mr-1"></i>
                            {{ $isEditing ? 'Modifier' : 'Enregistrer' }}
                        </span>
                        <span wire:loading>
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                            Enregistrement...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>