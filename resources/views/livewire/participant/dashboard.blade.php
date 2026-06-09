<div>

    {{-- NOTIFICATIONS --}}
    @foreach($inscriptionsValidees as $inscription)
    <div class="bg-green-50 border border-green-300 rounded-xl px-6 py-4 mb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center"
                style="background-color: #007A3D;">
                <i class="fa-solid fa-circle-check text-white text-lg"></i>
            </div>
            <div>
                <p class="font-semibold text-green-800">🎉 Préinscription validée !</p>
                <p class="text-sm text-green-600">
                    Votre préinscription à <strong>{{ $inscription->evenement->nom ?? '-' }}</strong>
                    a été validée. Vous pouvez maintenant payer.
                </p>
            </div>
        </div>
        <a href="{{ route('participant.inscription') }}"
            class="px-4 py-2 rounded-xl text-white text-sm font-medium transition hover:opacity-90 flex-shrink-0"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-credit-card mr-1"></i> Payer maintenant
        </a>
    </div>
    @endforeach

    @foreach($paiementsValides as $inscription)
    <div class="bg-blue-50 border border-blue-300 rounded-xl px-6 py-4 mb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-blue-600">
                <i class="fa-solid fa-receipt text-white text-lg"></i>
            </div>
            <div>
                <p class="font-semibold text-blue-800">✅ Paiement confirmé !</p>
                <p class="text-sm text-blue-600">
                    Votre paiement pour <strong>{{ $inscription->evenement->nom ?? '-' }}</strong>
                    a été confirmé.
                </p>
            </div>
        </div>
        <a href="{{ route('participant.inscription') }}"
            class="px-4 py-2 rounded-xl text-white text-sm font-medium transition hover:opacity-90 flex-shrink-0 bg-blue-600">
            <i class="fa-solid fa-receipt mr-1"></i> Voir le reçu
        </a>
    </div>
    @endforeach

    {{-- INFO PARTICIPANT --}}
    @if($participant)
    <div class="bg-white rounded-xl shadow p-6 mb-6 flex items-center gap-6">
        <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold flex-shrink-0"
            style="background-color: #C8102E;">
            {{ strtoupper(substr($participant->prenom, 0, 1)) }}
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                {{ $participant->nom }} {{ $participant->prenom }}
            </h2>
            <div class="flex items-center gap-4 mt-1">
                <span class="text-sm text-gray-500">
                    <i class="fa-solid fa-briefcase text-gray-400 mr-1"></i>
                    {{ ucfirst($participant->role) }}
                </span>
                <span class="text-sm text-gray-500">
                    <i class="fa-solid fa-building text-gray-400 mr-1"></i>
                    {{ $participant->entreprise->nom ?? 'Indépendant' }}
                </span>
                <span class="text-sm font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-600">
                    {{ $participant->code_acces }}
                </span>
            </div>
        </div>
        @if($badge)
        <div class="ml-auto text-center">
            <div class="w-16 h-16 rounded-xl flex items-center justify-center mb-2"
                style="background-color: #e6f4ed;">
                <i class="fa-solid fa-qrcode text-3xl" style="color: #007A3D;"></i>
            </div>
            <p class="text-xs text-gray-500">Badge actif</p>
        </div>
        @endif
    </div>
    @endif

    {{-- CARTES STATISTIQUES --}}
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
                @if($totalSouhaits < 10)
                <p class="text-xs text-orange-500 mt-1">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                    Minimum 10 requis
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
                <p class="text-lg font-bold text-gray-800">
                    {{ $badge ? $badge->qr_code : 'Non attribué' }}
                </p>
            </div>
        </div>
    </div>

    {{-- ← ÉVÉNEMENTS DISPONIBLES --}}
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

                {{-- Infos événement --}}
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

                    {{-- Salle RDV --}}
                    @if($evenement->nom_salle)
                    <div class="mt-2 text-xs text-blue-600">
                        <i class="fa-solid fa-door-open mr-1"></i>
                        Salle RDV : {{ $evenement->nom_salle }}
                        ({{ $evenement->nombre_tables }} tables)
                    </div>
                    @endif

                    {{-- Type paiement --}}
                    <div class="mt-2">
                        @if($evenement->type_paiement == 'gratuit')
                        <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">
                            <i class="fa-solid fa-gift mr-1"></i> Gratuit
                        </span>
                        @elseif($evenement->type_paiement == 'par_participant')
                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">
                            <i class="fa-solid fa-user mr-1"></i>
                            {{ number_format($evenement->montant_inscription, 0, ',', ' ') }} FCFA / participant
                        </span>
                        @else
                        <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-medium">
                            <i class="fa-solid fa-building mr-1"></i>
                            {{ number_format($evenement->montant_inscription, 0, ',', ' ') }} FCFA / entreprise
                        </span>
                        @endif
                    </div>
                </div>

                {{-- ← Bouton S'inscrire --}}
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

    {{-- PROCHAINS RENDEZ-VOUS --}}
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
                {{-- ← Salle + table au lieu de stand --}}
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
                        {{ $rdv->participant1->nom ?? '-' }} ↔ {{ $rdv->participant2->nom ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-400">
                        <i class="fa-solid fa-calendar mr-1"></i>{{ $rdv->date }}
                        <i class="fa-solid fa-clock ml-2 mr-1"></i>{{ $rdv->heure_debut }} - {{ $rdv->heure_fin }}
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
            <p>Aucun rendez-vous planifié</p>
        </div>
        @endforelse
    </div>

</div>