<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-300 text-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- Blocage participation_rdv --}}
    @if(!$participant || !$participant->participation_rdv)
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-8 text-center">
        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-orange-100">
            <i class="fa-solid fa-ban text-3xl text-orange-500"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-800 mb-2">Participation aux RDV désactivée</h3>
        <p class="text-gray-500 text-sm mb-4">
            Vous n'avez pas activé la participation aux rendez-vous d'affaires.
        </p>
        <a href="{{ route('participant.profil') }}"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-white font-medium transition hover:opacity-90"
            style="background-color: #007A3D;">
            <i class="fa-solid fa-user-gear"></i> Aller à mon profil
        </a>
    </div>

    {{-- Blocage inscription non validée --}}
    @elseif(!$inscriptionValide)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-yellow-100">
            <i class="fa-solid fa-clock text-3xl text-yellow-500"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-800 mb-2">Inscription non validée</h3>
        <p class="text-gray-500 text-sm mb-4">
            Vous devez avoir une inscription validée pour émettre des souhaits de RDV.
        </p>
        <a href="{{ route('participant.inscription') }}"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-white font-medium transition hover:opacity-90"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-clipboard-list"></i> Voir mes inscriptions
        </a>
    </div>

    @else

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Mes Souhaits de RDV</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: {{ $objectifAtteint ? '#007A3D' : '#f59e0b' }}">
                {{ $nbSouhaits }}/{{ $minSouhaits }} minimum
            </span>
        </div>
        @if(!$maxAtteint)
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i> Nouveau souhait
        </button>
        @else
        <span class="px-5 py-2.5 rounded-xl text-white font-medium bg-gray-400 flex items-center gap-2 cursor-not-allowed">
            <i class="fa-solid fa-lock"></i> Maximum atteint
        </span>
        @endif
    </div>

    {{-- Barre de progression --}}
    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <div class="flex justify-between text-sm mb-2">
            <span class="text-gray-600 font-medium">Progression des souhaits</span>
            <span class="font-bold {{ $objectifAtteint ? 'text-green-600' : 'text-orange-500' }}">
                {{ $nbSouhaits }} / {{ $maxSouhaits }}
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3 relative">
            <div class="h-3 rounded-full transition-all duration-500"
                style="width: {{ min(($nbSouhaits / $maxSouhaits) * 100, 100) }}%;
                       background-color: {{ $objectifAtteint ? '#007A3D' : '#f59e0b' }}">
            </div>
            <div class="absolute top-0 h-3 w-0.5 bg-red-500"
                style="left: {{ ($minSouhaits / $maxSouhaits) * 100 }}%">
            </div>
        </div>
        <div class="flex justify-between text-xs text-gray-400 mt-1">
            <span>0</span>
            <span class="text-red-500 font-medium">min: {{ $minSouhaits }}</span>
            <span>max: {{ $maxSouhaits }}</span>
        </div>
        @if($maxAtteint)
        <p class="text-xs text-blue-600 mt-2 flex items-center gap-1">
            <i class="fa-solid fa-circle-check"></i>
            Maximum atteint ! Vous avez émis {{ $nbSouhaits }} souhaits.
        </p>
        @elseif($objectifAtteint)
        <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
            <i class="fa-solid fa-circle-check"></i>
            Objectif atteint ! Vous pouvez encore ajouter {{ $maxSouhaits - $nbSouhaits }} souhait(s).
        </p>
        @else
        <p class="text-xs text-orange-500 mt-2 flex items-center gap-1">
            <i class="fa-solid fa-triangle-exclamation"></i>
            Il vous manque encore {{ $minSouhaits - $nbSouhaits }} souhait(s) pour atteindre le minimum.
        </p>
        @endif
    </div>

    {{-- Tableau des souhaits --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Priorité</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Entreprise</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Secteur</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Type</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($souhaits as $souhait)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white text-sm flex-shrink-0"
                                style="background-color: {{ $souhait->priorite <= 3 ? '#C8102E' : ($souhait->priorite <= 10 ? '#007A3D' : '#6b7280') }}">
                                {{ $souhait->priorite }}
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <button wire:click="monterPriorite({{ $souhait->id }})"
                                    {{ $souhait->priorite <= 1 ? 'disabled' : '' }}
                                    class="w-6 h-6 rounded-lg flex items-center justify-center transition text-xs
                                        {{ $souhait->priorite <= 1 ? 'bg-gray-100 text-gray-300 cursor-not-allowed' : 'bg-green-100 text-green-600 hover:bg-green-200' }}">
                                    <i class="fa-solid fa-chevron-up"></i>
                                </button>
                                <button wire:click="descendrePriorite({{ $souhait->id }})"
                                    {{ $souhait->priorite >= $souhaits->count() ? 'disabled' : '' }}
                                    class="w-6 h-6 rounded-lg flex items-center justify-center transition text-xs
                                        {{ $souhait->priorite >= $souhaits->count() ? 'bg-gray-100 text-gray-300 cursor-not-allowed' : 'bg-orange-100 text-orange-600 hover:bg-orange-200' }}">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                style="background-color: {{ $souhait->participantCible?->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                                {{ strtoupper(substr($souhait->participantCible->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{ $souhait->participantCible->nom ?? '-' }}
                                    {{ $souhait->participantCible->prenom ?? '' }}
                                </p>
                                @if($souhait->participantCible?->fonction)
                                <p class="text-xs text-gray-400">
                                    <i class="fa-solid fa-briefcase mr-1"></i>
                                    {{ $souhait->participantCible->fonction }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                            {{ $souhait->participantCible->entreprise->nom ?? 'Indépendant' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($souhait->participantCible?->secteur_activite)
                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                            {{ $souhait->participantCible->secteur_activite }}
                        </span>
                        @if($souhait->participantCible?->sous_secteur)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $souhait->participantCible->sous_secteur }}</p>
                        @endif
                        @else
                        <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($souhait->type == 'mutuel')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium" style="background-color: #C8102E;">
                                <i class="fa-solid fa-arrows-left-right mr-1"></i> Mutuel
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                                <i class="fa-solid fa-arrow-right mr-1"></i> Envoyé
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <button wire:click="supprimer({{ $souhait->id }})"
                            wire:confirm="Supprimer ce souhait ?"
                            class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-heart text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun souhait émis</p>
                        <p class="text-sm text-gray-400 mt-1">
                            Émettez au moins {{ $minSouhaits }} souhaits pour participer au match-making
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL ENRICHI --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col">

            {{-- Header --}}
            <div class="flex justify-between items-center px-8 py-5 border-b flex-shrink-0"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-heart"></i>
                    Choisir un participant à rencontrer
                    <span class="text-sm font-normal text-green-200">
                        ({{ $nbSouhaits }}/{{ $maxSouhaits }})
                    </span>
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-6 overflow-y-auto flex-1">

                {{-- Info priorité automatique --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-5 text-xs text-blue-700 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i>
                    La priorité sera attribuée automatiquement.
                    Vous pourrez la modifier avec les flèches ↑↓ après l'ajout.
                </div>

                {{-- Mon profil partenaire --}}
                @if($participant->secteur_recherche || $participant->type_partenaire)
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-5">
                    <p class="text-xs font-bold text-gray-600 mb-2">
                        <i class="fa-solid fa-user mr-1"></i>
                        Ce que vous recherchez :
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @if($participant->secteur_recherche)
                        <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                            <i class="fa-solid fa-tag mr-1"></i>{{ $participant->secteur_recherche }}
                        </span>
                        @endif
                        @if($participant->zone_geographique)
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                            <i class="fa-solid fa-location-dot mr-1"></i>{{ $participant->zone_geographique }}
                        </span>
                        @endif
                        @if($participant->type_partenaire)
                        <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">
                            <i class="fa-solid fa-handshake mr-1"></i>{{ $participant->type_partenaire }}
                        </span>
                        @endif
                    </div>
                </div>
                @endif

                @error('id_participant_cible')
                <p class="text-red-500 text-xs mb-3">{{ $message }}</p>
                @enderror

                {{-- FICHES DÉTAILLÉES --}}
                <div class="space-y-4">
                    @forelse($autresParticipants as $p)
                    @php
                        // Calcul compatibilite simple
                        $points = 0;
                        if($participant->secteur_recherche && $p->secteur_activite == $participant->secteur_recherche) $points++;
                        if($participant->zone_geographique && $p->zone_geographique == $participant->zone_geographique) $points++;
                        if($participant->type_partenaire && $p->type_partenaire == $participant->type_partenaire) $points++;
                        $compatible = $points >= 2;
                    @endphp

                    <label class="block cursor-pointer">
                        <input type="radio"
                            wire:model="id_participant_cible"
                            value="{{ $p->id }}"
                            class="hidden peer">

                        <div class="border-2 rounded-xl p-4 transition
                            {{ $id_participant_cible == $p->id
                                ? 'border-green-400 bg-green-50'
                                : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">

                            {{-- En-tête fiche --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-lg font-bold flex-shrink-0"
                                        style="background-color: {{ $p->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                                        {{ strtoupper(substr($p->prenom ?? 'X', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">
                                            {{ $p->nom }} {{ $p->prenom }}
                                            @if($p->genre == 'femme')
                                            <span class="text-xs text-gray-400">(Mme)</span>
                                            @elseif($p->genre == 'homme')
                                            <span class="text-xs text-gray-400">(M.)</span>
                                            @endif
                                        </p>
                                        @if($p->fonction)
                                        <p class="text-xs text-gray-500">
                                            <i class="fa-solid fa-briefcase mr-1"></i>{{ $p->fonction }}
                                        </p>
                                        @endif
                                        <p class="text-xs text-gray-500">
                                            <i class="fa-solid fa-building mr-1"></i>
                                            {{ $p->entreprise->nom ?? 'Indépendant' }}
                                            @if($p->pays)
                                            — <i class="fa-solid fa-flag mr-1"></i>{{ $p->pays }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- Badge compatibilite --}}
                                <div class="flex-shrink-0">
                                    @if($points == 3)
                                    <span class="px-3 py-1 rounded-full text-xs font-bold text-white"
                                        style="background-color: #007A3D;">
                                        <i class="fa-solid fa-star mr-1"></i> Très compatible
                                    </span>
                                    @elseif($points == 2)
                                    <span class="px-3 py-1 rounded-full text-xs font-bold text-white bg-blue-600">
                                        <i class="fa-solid fa-circle-check mr-1"></i> Compatible
                                    </span>
                                    @elseif($points == 1)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium text-gray-600 bg-gray-100">
                                        Peu compatible
                                    </span>
                                    @else
                                    <span class="px-3 py-1 rounded-full text-xs font-medium text-gray-400 bg-gray-50">
                                        Secteur différent
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Infos entreprise --}}
                            <div class="grid grid-cols-2 gap-3 mb-3">

                                @if($p->secteur_activite)
                                <div class="bg-white rounded-xl p-3 border border-gray-100">
                                    <p class="text-xs text-gray-400 mb-1">
                                        <i class="fa-solid fa-tag mr-1"></i> Secteur
                                    </p>
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ $p->secteur_activite }}
                                        @if($p->sous_secteur)
                                        <span class="text-xs text-gray-400">/ {{ $p->sous_secteur }}</span>
                                        @endif
                                    </p>
                                </div>
                                @endif

                                @if($p->zone_geographique)
                                <div class="bg-white rounded-xl p-3 border border-gray-100">
                                    <p class="text-xs text-gray-400 mb-1">
                                        <i class="fa-solid fa-location-dot mr-1"></i> Zone géographique
                                    </p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $p->zone_geographique }}</p>
                                </div>
                                @endif

                                @if($p->annee_creation)
                                <div class="bg-white rounded-xl p-3 border border-gray-100">
                                    <p class="text-xs text-gray-400 mb-1">
                                        <i class="fa-solid fa-calendar mr-1"></i> Créée en
                                    </p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $p->annee_creation }}</p>
                                </div>
                                @endif

                                @if($p->nombre_salaries)
                                <div class="bg-white rounded-xl p-3 border border-gray-100">
                                    <p class="text-xs text-gray-400 mb-1">
                                        <i class="fa-solid fa-users mr-1"></i> Salariés
                                    </p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $p->nombre_salaries }}</p>
                                </div>
                                @endif

                                @if($p->chiffre_affaires)
                                <div class="bg-white rounded-xl p-3 border border-gray-100">
                                    <p class="text-xs text-gray-400 mb-1">
                                        <i class="fa-solid fa-chart-line mr-1"></i> CA export
                                    </p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $p->chiffre_affaires }}%</p>
                                </div>
                                @endif

                                @if($p->type_partenaire)
                                <div class="bg-white rounded-xl p-3 border border-gray-100">
                                    <p class="text-xs text-gray-400 mb-1">
                                        <i class="fa-solid fa-handshake mr-1"></i> Cherche
                                    </p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $p->type_partenaire }}</p>
                                </div>
                                @endif

                            </div>

                            {{-- Description activites --}}
                            @if($p->description_activites)
                            <div class="bg-white rounded-xl p-3 border border-gray-100 mb-3">
                                <p class="text-xs text-gray-400 mb-1">
                                    <i class="fa-solid fa-file-lines mr-1"></i> Description des activités
                                </p>
                                <p class="text-xs text-gray-700 leading-relaxed">
                                    {{ Str::limit($p->description_activites, 150) }}
                                </p>
                            </div>
                            @endif

                            {{-- Produits --}}
                            @if($p->principaux_produits)
                            <div class="bg-white rounded-xl p-3 border border-gray-100 mb-3">
                                <p class="text-xs text-gray-400 mb-1">
                                    <i class="fa-solid fa-box mr-1"></i> Produits / Savoir-faire
                                </p>
                                <p class="text-xs text-gray-700">
                                    {{ Str::limit($p->principaux_produits, 100) }}
                                </p>
                            </div>
                            @endif

                            {{-- Points de compatibilite --}}
                            @if($participant->secteur_recherche || $participant->type_partenaire)
                            <div class="flex flex-wrap gap-2 mt-2">
                                @if($participant->secteur_recherche)
                                <span class="text-xs px-2 py-1 rounded-full font-medium
                                    {{ $p->secteur_activite == $participant->secteur_recherche
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-red-50 text-red-400' }}">
                                    @if($p->secteur_activite == $participant->secteur_recherche)
                                    <i class="fa-solid fa-check mr-1"></i>
                                    @else
                                    <i class="fa-solid fa-xmark mr-1"></i>
                                    @endif
                                    Secteur {{ $participant->secteur_recherche }}
                                </span>
                                @endif

                                @if($participant->zone_geographique)
                                <span class="text-xs px-2 py-1 rounded-full font-medium
                                    {{ $p->zone_geographique == $participant->zone_geographique
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-red-50 text-red-400' }}">
                                    @if($p->zone_geographique == $participant->zone_geographique)
                                    <i class="fa-solid fa-check mr-1"></i>
                                    @else
                                    <i class="fa-solid fa-xmark mr-1"></i>
                                    @endif
                                    Zone {{ $participant->zone_geographique }}
                                </span>
                                @endif

                                @if($participant->type_partenaire)
                                <span class="text-xs px-2 py-1 rounded-full font-medium
                                    {{ $p->type_partenaire == $participant->type_partenaire
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-red-50 text-red-400' }}">
                                    @if($p->type_partenaire == $participant->type_partenaire)
                                    <i class="fa-solid fa-check mr-1"></i>
                                    @else
                                    <i class="fa-solid fa-xmark mr-1"></i>
                                    @endif
                                    {{ $participant->type_partenaire }}
                                </span>
                                @endif
                            </div>
                            @endif

                        </div>
                    </label>
                    @empty
                    <div class="text-center py-8 text-gray-400">
                        <i class="fa-solid fa-users text-4xl mb-3 block text-gray-300"></i>
                        <p class="text-sm">Aucun participant disponible dans votre événement</p>
                    </div>
                    @endforelse
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 p-6 border-t flex-shrink-0">
                <button wire:click="closeModal"
                    class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                    <i class="fa-solid fa-xmark mr-1"></i> Annuler
                </button>
                <button wire:click="sauvegarder"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-not-allowed"
                    class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm flex items-center gap-2"
                    style="background-color: #C8102E;">
                    <span wire:loading.remove>
                        <i class="fa-solid fa-heart mr-1"></i> Émettre ce souhait
                    </span>
                    <span wire:loading>
                        <i class="fa-solid fa-spinner fa-spin mr-1"></i> Enregistrement...
                    </span>
                </button>
            </div>

        </div>
    </div>
    @endif

    @endif
</div>