<div>

    {{-- ============================================================
         STATUT INSCRIPTIONS
    ============================================================ --}}
    @foreach($mesInscriptions as $inscription)
    @php
        $estGratuit = $inscription->evenement?->type_paiement === 'gratuit';
        $parEntreprise = $inscription->evenement?->type_paiement === 'par_entreprise';
    @endphp

    {{-- ← Inscription en attente de validation --}}
    @if($inscription->statut_presence == 'absent' && $inscription->statut_paiement == 'en_attente')
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-6 py-4 mb-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-yellow-400 flex-shrink-0">
            <i class="fa-solid fa-clock text-white"></i>
        </div>
        <div>
            <p class="font-semibold text-yellow-800">En attente de validation</p>
            <p class="text-sm text-yellow-600">
                Votre inscription à <strong>{{ $inscription->evenement->nom ?? '-' }}</strong>
                est en cours de traitement.
            </p>
        </div>
    </div>
    @endif

    {{-- ← Inscription validée → badge disponible --}}
    @if($inscription->statut_presence == 'present' || $inscription->statut_paiement == 'paye')
    <div class="rounded-xl px-6 py-4 mb-4 flex items-center justify-between"
        style="background: linear-gradient(135deg, #007A3D, #005a2d);">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-white/20 flex-shrink-0">
                <i class="fa-solid fa-id-badge text-white text-lg"></i>
            </div>
            <div>
                <p class="font-semibold text-white">
                    🎉 Inscription confirmée !
                </p>
                <p class="text-sm text-green-200">
                    Votre inscription à <strong>{{ $inscription->evenement->nom ?? '-' }}</strong>
                    est confirmée. Votre badge sera disponible à l'entrée de l'événement.
                </p>
            </div>
        </div>
        <div class="flex-shrink-0 text-center ml-4">
            <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center">
                <i class="fa-solid fa-qrcode text-white text-2xl"></i>
            </div>
            <p class="text-xs text-green-200 mt-1">Badge</p>
        </div>
    </div>
    @endif

    @endforeach

    {{-- ============================================================
         INFO PARTICIPANT
    ============================================================ --}}
    @if($participant)
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold flex-shrink-0"
                style="background-color: #C8102E;">
                {{ strtoupper(substr($participant->prenom ?? 'P', 0, 1)) }}
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ $participant->nom }} {{ $participant->prenom }}
                </h2>
                <div class="flex items-center gap-4 mt-1 flex-wrap">
                    @if($participant->fonction)
                    <span class="text-sm text-gray-500">
                        <i class="fa-solid fa-briefcase text-gray-400 mr-1"></i>
                        {{ $participant->fonction }}
                    </span>
                    @endif
                    @if($entreprise)
                    <span class="text-sm text-gray-500">
                        <i class="fa-solid fa-building text-gray-400 mr-1"></i>
                        {{ $entreprise->nom }}
                    </span>
                    @endif
                    <span class="text-sm font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-600">
                        <i class="fa-solid fa-key text-gray-400 mr-1"></i>
                        {{ $participant->code_acces }}
                    </span>
                </div>
            </div>
            @if($badge)
            <div class="text-center flex-shrink-0">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-1"
                    style="background-color: #e6f4ed;">
                    <i class="fa-solid fa-qrcode text-3xl" style="color: #007A3D;"></i>
                </div>
                <p class="text-xs text-green-600 font-medium">Badge actif</p>
            </div>
            @endif
        </div>

        {{-- Entreprise info si membre --}}
        @if($entreprise)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0"
                    style="background-color: #007A3D;">
                    {{ strtoupper(substr($entreprise->nom, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700">{{ $entreprise->nom }}</p>
                    <p class="text-xs text-gray-400">
                        {{ $entreprise->secteur_activite }}
                        · {{ $entreprise->ville }}, {{ $entreprise->pays }}
                    </p>
                </div>
                @if($entreprise->statut_validation == 'valide')
                <span class="ml-auto text-xs px-2 py-1 rounded-full text-white font-medium"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-circle-check mr-1"></i> Validée
                </span>
                @else
                <span class="ml-auto text-xs px-2 py-1 rounded-full text-white font-medium bg-yellow-500">
                    <i class="fa-solid fa-clock mr-1"></i> En attente
                </span>
                @endif
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ============================================================
         STATISTIQUES
    ============================================================ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #C8102E;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #fde8ec;">
                <i class="fa-solid fa-heart" style="color: #C8102E;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Mes Souhaits</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalSouhaits }}</p>
                @if($totalSouhaits < 5)
                <p class="text-xs text-orange-500 mt-1">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                    Ajoutez des souhaits
                </p>
                @else
                <p class="text-xs text-green-600 mt-1">
                    <i class="fa-solid fa-circle-check mr-1"></i>
                    Objectif atteint
                </p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #007A3D;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #e6f4ed;">
                <i class="fa-solid fa-handshake" style="color: #007A3D;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Mes Rendez-vous</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalRdv }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #2d5a8e;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #e8f0fb;">
                <i class="fa-solid fa-id-badge" style="color: #2d5a8e;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Mon Badge</p>
                @if($badge)
                <p class="text-sm font-bold text-green-600 mt-1">
                    <i class="fa-solid fa-circle-check mr-1"></i> Disponible
                </p>
                @else
                <p class="text-sm text-gray-400 mt-1">
                    En attente
                </p>
                @endif
            </div>
        </div>

    </div>

    {{-- ============================================================
         ÉVÉNEMENTS DISPONIBLES
    ============================================================ --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-calendar-star" style="color: #C8102E;"></i>
                Événements disponibles
            </h3>
            <span class="text-xs text-gray-400">
                {{ $evenementsDisponibles->count() }} événement(s) ouvert(s)
            </span>
        </div>

        @forelse($evenementsDisponibles as $evenement)
        <div class="border border-gray-200 rounded-xl p-5 mb-4 hover:border-green-300 hover:shadow-md transition last:mb-0">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white flex-shrink-0"
                            style="background-color: #007A3D;">
                            <i class="fa-solid fa-calendar text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">{{ $evenement->nom }}</h4>
                            <span class="text-xs px-2 py-0.5 rounded-full text-white font-medium"
                                style="background-color: #007A3D;">
                                {{ $evenement->typeEvenement->nom ?? '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 mt-3">
                        <span>
                            <i class="fa-solid fa-calendar mr-1 text-gray-400"></i>
                            {{ \Carbon\Carbon::parse($evenement->date_debut)->format('d/m/Y') }}
                            @if($evenement->date_debut != $evenement->date_fin)
                            → {{ \Carbon\Carbon::parse($evenement->date_fin)->format('d/m/Y') }}
                            @endif
                        </span>
                        <span>
                            <i class="fa-solid fa-clock mr-1 text-gray-400"></i>
                            {{ $evenement->heure_debut }} - {{ $evenement->heure_fin }}
                        </span>
                        <span>
                            <i class="fa-solid fa-location-dot mr-1 text-gray-400"></i>
                            {{ $evenement->ville }}
                        </span>
                        <span>
                            <i class="fa-solid fa-map-pin mr-1 text-gray-400"></i>
                            {{ $evenement->lieu }}
                        </span>
                    </div>
                    <div class="mt-2">
                        @if($evenement->type_paiement == 'gratuit')
                        <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">
                            <i class="fa-solid fa-gift mr-1"></i> Gratuit
                        </span>
                        @elseif($evenement->type_paiement == 'par_entreprise')
                        <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-medium">
                            <i class="fa-solid fa-building mr-1"></i>
                            Paiement par l'entreprise
                        </span>
                        @else
                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">
                            <i class="fa-solid fa-user mr-1"></i>
                            {{ number_format($evenement->montant_inscription, 0, ',', ' ') }} FCFA / participant
                        </span>
                        @endif
                    </div>
                </div>

                <div class="flex-shrink-0 text-right">
                    @if($evenement->deja_inscrit)
                    <span class="px-4 py-2 rounded-xl text-xs font-medium text-white flex items-center gap-1"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-circle-check"></i> Inscrit
                    </span>
                    @else
                    <a href="{{ route('participant.inscription.wizard', $evenement->id) }}"
                        class="px-4 py-2 rounded-xl text-white text-sm font-medium transition hover:opacity-90 flex items-center gap-1 shadow"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-user-plus"></i>
                        S'inscrire
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-400">
            <i class="fa-solid fa-calendar-xmark text-3xl mb-2 block text-gray-300"></i>
            <p class="text-sm">Aucun événement disponible pour le moment</p>
        </div>
        @endforelse
    </div>

    {{-- ============================================================
         PROCHAINS RENDEZ-VOUS
    ============================================================ --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-calendar-check" style="color: #007A3D;"></i>
                Prochains Rendez-vous
            </h3>
            <a href="{{ route('participant.rendez-vous') }}"
                class="text-sm px-4 py-2 rounded-lg text-white transition hover:opacity-90"
                style="background-color: #007A3D;">
                Voir tous
            </a>
        </div>

        @forelse($prochainRdv as $rdv)
        <div class="flex items-center justify-between py-4 border-b last:border-0">
            <div class="flex items-center gap-4">
                <div class="text-center flex-shrink-0">
                    @if($rdv->salle)
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg"
                        style="background-color: #2d5a8e;">
                        {{ $rdv->numero_table }}
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $rdv->salle }}</p>
                    @else
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-gray-200">
                        <i class="fa-solid fa-question text-gray-400"></i>
                    </div>
                    @endif
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">
                        {{ $rdv->participant1->nom ?? '-' }}
                        ↔
                        {{ $rdv->participant2->nom ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-400">
                        <i class="fa-solid fa-calendar mr-1"></i>{{ $rdv->date }}
                        <i class="fa-solid fa-clock ml-2 mr-1"></i>
                        {{ $rdv->heure_debut }} - {{ $rdv->heure_fin }}
                    </p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                Planifié
            </span>
        </div>
        @empty
        <div class="text-center py-8 text-gray-400">
            <i class="fa-solid fa-calendar-xmark text-3xl mb-2 block text-gray-300"></i>
            <p class="text-sm">Aucun rendez-vous planifié pour le moment</p>
            <p class="text-xs text-gray-300 mt-1">
                Émettez vos souhaits pour obtenir des rendez-vous
            </p>
        </div>
        @endforelse
    </div>

</div>