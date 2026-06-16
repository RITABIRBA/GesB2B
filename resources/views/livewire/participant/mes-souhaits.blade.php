<div>

    {{-- Messages de succès et d'erreur --}}
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

    {{-- Blocage si la participation aux RDV est désactivée --}}
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

    {{-- Blocage si l'inscription n'est pas encore validée --}}
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

    {{-- Bandeau de fermeture des souhaits --}}
    @if($souhaitsfermes)
    <div class="bg-gray-100 border border-gray-300 rounded-xl p-5 mb-6 flex items-start gap-3">
        <i class="fa-solid fa-lock text-2xl text-gray-500 mt-0.5"></i>
        <div>
            <h4 class="font-bold text-gray-700">Souhaits clôturés</h4>
            <p class="text-sm text-gray-500 mt-1">
                La période d'émission des souhaits est terminée (clôture automatique 3 jours
                avant l'événement). Vous ne pouvez plus ajouter, modifier ou supprimer de souhaits.
                Le planning sera généré par les organisateurs.
            </p>
        </div>
    </div>
    @elseif(!is_null($joursRestants) && $joursRestants <= 7)
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6 flex items-center gap-3">
        <i class="fa-solid fa-triangle-exclamation text-xl text-orange-500"></i>
        <p class="text-sm text-orange-700">
            <span class="font-bold">Attention :</span>
            les souhaits seront clôturés automatiquement dans
            <span class="font-bold">{{ max($joursRestants - 3, 0) }} jour(s)</span>
            (3 jours avant l'événement). Finalisez vos souhaits dès que possible.
        </p>
    </div>
    @endif

    {{-- En-tête avec nom de l'événement et barre de progression des souhaits --}}
    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="text-xl font-bold text-gray-700">Souhaits de RDV</h3>
                @if($evenement)
                <p class="text-sm text-gray-400 mt-0.5">
                    <i class="fa-solid fa-calendar mr-1"></i>
                    {{ $evenement->nom }}
                    @if($evenement->date_debut)
                    · {{ \Carbon\Carbon::parse($evenement->date_debut)->format('d/m/Y') }}
                    @endif
                </p>
                @endif
            </div>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: {{ $objectifAtteint ? '#007A3D' : '#f59e0b' }}">
                {{ $nbSouhaits }} / {{ $minSouhaits }} minimum
            </span>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-3 relative">
            <div class="h-3 rounded-full transition-all duration-500"
                style="width: {{ min(($nbSouhaits / max($maxSouhaits, 1)) * 100, 100) }}%;
                       background-color: {{ $objectifAtteint ? '#007A3D' : '#f59e0b' }}">
            </div>
            {{-- Marqueur du minimum requis --}}
            <div class="absolute top-0 h-3 w-0.5 bg-red-500"
                style="left: {{ ($minSouhaits / max($maxSouhaits, 1)) * 100 }}%">
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
            Il vous manque {{ $minSouhaits - $nbSouhaits }} souhait(s) pour atteindre le minimum.
        </p>
        @endif
    </div>

    {{-- Liste des participants du même événement triés par compatibilité --}}
    @if(!$souhaitsfermes)
    <div class="mb-5">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher par nom, prénom ou entreprise..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm bg-white">
        </div>
    </div>

    <div class="mb-8">
        <h4 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-users" style="color: #007A3D;"></i>
            Participants de l'événement
            <span class="text-sm font-normal text-gray-400">
                ({{ $candidats->total() }} disponible(s))
            </span>
            @if($candidats->hasPages())
            <span class="text-xs font-normal text-gray-400 ml-auto">
                Page {{ $candidats->currentPage() }} / {{ $candidats->lastPage() }}
            </span>
            @endif
        </h4>

        @forelse($candidats as $p)
        @php $points = $p->score_compatibilite; @endphp

        <div class="bg-white rounded-xl shadow mb-4 overflow-hidden {{ $p->souhait_emis ? 'opacity-75' : '' }}">

            {{-- Bandeau de compatibilité en haut de chaque fiche --}}
            <div class="px-5 py-2 flex items-center justify-between flex-wrap gap-2
                {{ $points == 3 ? 'bg-green-50 border-b border-green-200' :
                   ($points == 2 ? 'bg-blue-50 border-b border-blue-200' :
                   'bg-gray-50 border-b border-gray-200') }}">
                <div class="flex items-center gap-2">
                    @if($points == 3)
                    <span class="text-xs font-bold text-green-700">
                        ⭐⭐⭐ Très compatible
                    </span>
                    @elseif($points == 2)
                    <span class="text-xs font-bold text-blue-700">
                        ⭐⭐ Compatible
                    </span>
                    @elseif($points == 1)
                    <span class="text-xs font-medium text-gray-500">
                        ⭐ Peu compatible
                    </span>
                    @else
                    <span class="text-xs font-medium text-gray-400">
                        Profil différent
                    </span>
                    @endif

                    @if($p->est_mutuel)
                    <span class="text-xs px-2 py-0.5 rounded-full font-bold text-white" style="background-color: #C8102E;">
                        <i class="fa-solid fa-arrows-left-right mr-1"></i> Il/elle vous a aussi sélectionné !
                    </span>
                    @endif
                </div>

                @if($p->souhait_emis)
                <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">
                    <i class="fa-solid fa-circle-check mr-1"></i> Souhait émis
                </span>
                @endif
            </div>

            <div class="p-5">

                {{-- Identité du participant --}}
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl font-bold flex-shrink-0"
                        style="background-color: {{ $p->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                        {{ strtoupper(substr($p->prenom ?? 'X', 0, 1)) }}
                    </div>
                    <div class="flex-1">
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
                            @if($p->pays)
                            · <i class="fa-solid fa-location-dot mr-1 text-gray-400"></i>{{ $p->pays }}
                            @if($p->ville) / {{ $p->ville }} @endif
                            @endif
                        </p>
                        @if($p->entreprise->secteur_activite ?? false)
                        <p class="text-sm text-gray-400">
                            <i class="fa-solid fa-industry mr-1"></i>
                            {{ $p->entreprise->secteur_activite }}
                            @if($p->entreprise->sous_secteur ?? false)
                            / {{ $p->entreprise->sous_secteur }}
                            @endif
                        </p>
                        @endif
                        @if($p->entreprise->ifu ?? false)
                        <p class="text-xs text-gray-400 font-mono mt-0.5">
                            IFU : {{ $p->entreprise->ifu }}
                        </p>
                        @endif
                        @else
                        @if($p->pays)
                        <p class="text-sm text-gray-400">
                            <i class="fa-solid fa-location-dot mr-1"></i>{{ $p->pays }}
                            @if($p->ville) / {{ $p->ville }} @endif
                        </p>
                        @endif
                        @endif
                        @if($p->email)
                        <p class="text-sm text-gray-400">
                            <i class="fa-solid fa-envelope mr-1"></i>{{ $p->email }}
                        </p>
                        @endif
                        @if($p->telephone)
                        <p class="text-sm text-gray-400">
                            <i class="fa-solid fa-phone mr-1"></i>{{ $p->telephone }}
                        </p>
                        @endif
                    </div>
                </div>

                {{-- Données professionnelles du participant --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-4">

                    @if($p->secteur_activite)
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-1">
                            <i class="fa-solid fa-tag mr-1"></i> Secteur d'activité
                        </p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $p->secteur_activite }}
                            @if($p->sous_secteur)
                            <span class="text-xs text-gray-400 block">{{ $p->sous_secteur }}</span>
                            @endif
                        </p>
                    </div>
                    @endif

                    @if($p->zone_geographique)
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-1">
                            <i class="fa-solid fa-location-dot mr-1"></i> Zone géographique ciblée
                        </p>
                        <p class="text-sm font-semibold text-gray-800">{{ $p->zone_geographique }}</p>
                    </div>
                    @endif

                    @php
                        $pTypesPartenariat = is_array($p->types_partenariat)
                            ? $p->types_partenariat
                            : (json_decode($p->types_partenariat ?? '[]', true) ?: []);
                        $pSecteursRecherche = is_array($p->secteurs_recherche)
                            ? $p->secteurs_recherche
                            : (json_decode($p->secteurs_recherche ?? '[]', true) ?: []);
                        $pProfilsPartenaire = is_array($p->profils_partenaire)
                            ? $p->profils_partenaire
                            : (json_decode($p->profils_partenaire ?? '[]', true) ?: []);
                    @endphp

                    @if(!empty($pTypesPartenariat))
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-1">
                            <i class="fa-solid fa-handshake mr-1"></i> Types de partenariat recherchés
                        </p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ implode(', ', $pTypesPartenariat) }}
                        </p>
                    </div>
                    @endif

                    @if(!empty($pSecteursRecherche))
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-1">
                            <i class="fa-solid fa-magnifying-glass mr-1"></i> Secteurs recherchés
                        </p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ implode(', ', $pSecteursRecherche) }}
                        </p>
                    </div>
                    @endif

                    @if(!empty($pProfilsPartenaire))
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-1">
                            <i class="fa-solid fa-id-card mr-1"></i> Profils partenaire recherchés
                        </p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ implode(', ', $pProfilsPartenaire) }}
                        </p>
                    </div>
                    @endif

                    @if($p->annee_creation)
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-1">
                            <i class="fa-solid fa-calendar mr-1"></i> Année de création
                        </p>
                        <p class="text-sm font-semibold text-gray-800">{{ $p->annee_creation }}</p>
                    </div>
                    @endif

                    @if($p->nombre_salaries)
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-1">
                            <i class="fa-solid fa-users mr-1"></i> Nombre de salariés
                        </p>
                        <p class="text-sm font-semibold text-gray-800">{{ $p->nombre_salaries }}</p>
                    </div>
                    @endif

                    @if($p->chiffre_affaires)
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-1">
                            <i class="fa-solid fa-chart-line mr-1"></i> Part CA export
                        </p>
                        <p class="text-sm font-semibold text-gray-800">{{ $p->chiffre_affaires }}%</p>
                    </div>
                    @endif

                </div>

                {{-- Description des activités --}}
                @if($p->description_activites)
                <div class="bg-gray-50 rounded-xl p-3 mb-3">
                    <p class="text-xs text-gray-400 mb-1">
                        <i class="fa-solid fa-file-lines mr-1"></i> Description des activités
                    </p>
                    <p class="text-sm text-gray-700 leading-relaxed">
                        {{ Str::limit($p->description_activites, 200) }}
                    </p>
                </div>
                @endif

                {{-- Principaux produits ou savoir-faire --}}
                @if($p->principaux_produits)
                <div class="bg-gray-50 rounded-xl p-3 mb-3">
                    <p class="text-xs text-gray-400 mb-1">
                        <i class="fa-solid fa-box mr-1"></i> Produits / Savoir-faire
                    </p>
                    <p class="text-sm text-gray-700">{{ $p->principaux_produits }}</p>
                </div>
                @endif

                {{-- Indicateurs de compatibilité avec mon profil --}}
                @php
                    $monZone = $participant->zone_geographique;
                    $mesSecteursRecherche = is_array($participant->secteurs_recherche)
                        ? $participant->secteurs_recherche
                        : (json_decode($participant->secteurs_recherche ?? '[]', true) ?: []);
                    $mesTypesPartenariat = is_array($participant->types_partenariat)
                        ? $participant->types_partenariat
                        : (json_decode($participant->types_partenariat ?? '[]', true) ?: []);

                    $matchSecteur = empty($mesSecteursRecherche)
                        || in_array($p->secteur_activite, $mesSecteursRecherche);
                    $matchZone = !$monZone || !$p->zone_geographique
                        || $monZone === $p->zone_geographique;
                    $matchType = empty($mesTypesPartenariat) || empty($pTypesPartenariat)
                        || count(array_intersect($mesTypesPartenariat, $pTypesPartenariat)) > 0;
                @endphp
                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="text-xs px-2 py-1 rounded-full font-medium
                        {{ $matchSecteur ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-400' }}">
                        <i class="fa-solid {{ $matchSecteur ? 'fa-check' : 'fa-xmark' }} mr-1"></i>
                        Secteur
                    </span>
                    <span class="text-xs px-2 py-1 rounded-full font-medium
                        {{ $matchZone ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-400' }}">
                        <i class="fa-solid {{ $matchZone ? 'fa-check' : 'fa-xmark' }} mr-1"></i>
                        Zone géographique
                    </span>
                    <span class="text-xs px-2 py-1 rounded-full font-medium
                        {{ $matchType ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-400' }}">
                        <i class="fa-solid {{ $matchType ? 'fa-check' : 'fa-xmark' }} mr-1"></i>
                        Type de partenariat
                    </span>
                </div>

                {{-- Bouton d'action en bas de chaque fiche --}}
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-400">
                        {{ $nbSouhaits }} / {{ $maxSouhaits }} souhaits émis
                    </p>

                    @if($p->souhait_emis)
                    <span class="px-5 py-2.5 rounded-xl text-sm font-medium bg-gray-100 text-gray-400 flex items-center gap-2 cursor-not-allowed">
                        <i class="fa-solid fa-circle-check text-green-500"></i>
                        Souhait émis
                    </span>
                    @elseif($maxAtteint)
                    <span class="px-5 py-2.5 rounded-xl text-sm font-medium bg-gray-100 text-gray-400 flex items-center gap-2 cursor-not-allowed">
                        <i class="fa-solid fa-lock"></i>
                        Maximum atteint
                    </span>
                    @elseif($points == 0)
                    <span class="px-5 py-2.5 rounded-xl text-sm font-medium bg-gray-100 text-gray-400 flex items-center gap-2 cursor-not-allowed"
                        title="Aucune compatibilité de profil">
                        <i class="fa-solid fa-ban"></i>
                        Non compatible
                    </span>
                    @else
                    <button wire:click="emettresouhait({{ $p->id }})"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        wire:target="emettresouhait({{ $p->id }})"
                        class="px-5 py-2.5 rounded-xl text-white text-sm font-medium transition hover:opacity-90 shadow flex items-center gap-2"
                        style="background-color: #C8102E;">
                        <span wire:loading.remove wire:target="emettresouhait({{ $p->id }})">
                            <i class="fa-solid fa-heart mr-1"></i>
                            Je suis intéressé
                        </span>
                        <span wire:loading wire:target="emettresouhait({{ $p->id }})">
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                            En cours...
                        </span>
                    </button>
                    @endif
                </div>

            </div>
        </div>

        @empty
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400">
            <i class="fa-solid fa-users text-5xl mb-3 block text-gray-300"></i>
            <p class="text-lg font-medium">Aucun participant disponible</p>
            <p class="text-sm mt-1 text-gray-400">
                Aucun autre participant compatible n'est disponible pour le moment.
            </p>
        </div>
        @endforelse

        {{-- Pagination (LOT D) --}}
        @if($candidats->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $candidats->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- Tableau récapitulatif des souhaits émis avec gestion des priorités --}}
    @if($souhaits->count() > 0)
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b" style="background-color: #f8f9fa;">
            <h4 class="font-bold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-list-ol" style="color: #C8102E;"></i>
                Souhaits par priorité
                <span class="text-sm font-normal text-gray-400">
                    ({{ $nbSouhaits }} souhait(s))
                </span>
            </h4>
        </div>
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-3 text-gray-500 font-semibold text-sm">Priorité</th>
                    <th class="px-6 py-3 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-6 py-3 text-gray-500 font-semibold text-sm">Entreprise</th>
                    <th class="px-6 py-3 text-gray-500 font-semibold text-sm">Type</th>
                    <th class="px-6 py-3 text-gray-500 font-semibold text-sm">Statut</th>
                    @if(!$souhaitsfermes)
                    <th class="px-6 py-3 text-gray-500 font-semibold text-sm">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($souhaits as $souhait)
                <tr class="border-b hover:bg-gray-50 transition">

                    {{-- Numéro de priorité + flèches de réorganisation --}}
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-white text-sm flex-shrink-0"
                                style="background-color:
                                    {{ $souhait->priorite <= 3
                                        ? '#C8102E'
                                        : ($souhait->priorite <= 10 ? '#007A3D' : '#6b7280') }}">
                                {{ $souhait->priorite }}
                            </div>
                            @if(!$souhaitsfermes)
                            <div class="flex flex-col gap-0.5">
                                <button wire:click="monterPriorite({{ $souhait->id }})"
                                    {{ $souhait->priorite <= 1 ? 'disabled' : '' }}
                                    class="w-5 h-5 rounded flex items-center justify-center transition text-xs
                                        {{ $souhait->priorite <= 1
                                            ? 'bg-gray-100 text-gray-300 cursor-not-allowed'
                                            : 'bg-green-100 text-green-600 hover:bg-green-200' }}">
                                    <i class="fa-solid fa-chevron-up"></i>
                                </button>
                                <button wire:click="descendrePriorite({{ $souhait->id }})"
                                    {{ $souhait->priorite >= $souhaits->count() ? 'disabled' : '' }}
                                    class="w-5 h-5 rounded flex items-center justify-center transition text-xs
                                        {{ $souhait->priorite >= $souhaits->count()
                                            ? 'bg-gray-100 text-gray-300 cursor-not-allowed'
                                            : 'bg-orange-100 text-orange-600 hover:bg-orange-200' }}">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                    </td>

                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                style="background-color:
                                    {{ $souhait->participantCible?->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                                {{ strtoupper(substr($souhait->participantCible->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{ $souhait->participantCible->nom ?? '-' }}
                                    {{ $souhait->participantCible->prenom ?? '' }}
                                </p>
                                @if($souhait->participantCible?->fonction)
                                <p class="text-xs text-gray-400">{{ $souhait->participantCible->fonction }}</p>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-3">
                        <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                            {{ $souhait->participantCible->entreprise->nom ?? 'Indépendant' }}
                        </span>
                    </td>

                    <td class="px-6 py-3">
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

                    <td class="px-6 py-3">
                        @php
                            $statutLabels = [
                                'en_attente'   => ['En attente', 'bg-yellow-100 text-yellow-700'],
                                'compatible'   => ['Compatible', 'bg-green-100 text-green-700'],
                                'incompatible' => ['Incompatible', 'bg-red-100 text-red-700'],
                                'accepte'      => ['Accepté', 'bg-green-100 text-green-700'],
                                'rejete'       => ['Rejeté', 'bg-red-100 text-red-700'],
                                'annule'       => ['Annulé', 'bg-gray-100 text-gray-500'],
                            ];
                            [$label, $classes] = $statutLabels[$souhait->statut] ?? ['En attente', 'bg-gray-100 text-gray-500'];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $classes }}">
                            {{ $label }}
                        </span>
                    </td>

                    @if(!$souhaitsfermes)
                    <td class="px-6 py-3">
                        <button wire:click="supprimer({{ $souhait->id }})"
                            wire:confirm="Supprimer ce souhait ?"
                            class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @endif
</div>