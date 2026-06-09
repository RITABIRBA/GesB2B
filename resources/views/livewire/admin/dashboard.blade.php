<div>
    {{-- Message bienvenue --}}
    <div class="mb-8 p-6 rounded-2xl text-white"
        style="background: linear-gradient(135deg, #007A3D, #005a2d);">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-1">
                    Bonjour, {{ auth()->user()->name }} 👋
                </h2>
                <p class="text-green-200 text-sm">
                    Tableau de bord — GesB2B CCI-BF
                    <span class="mx-2">•</span>
                    {{ now()->format('d/m/Y') }}
                </p>
            </div>
            <div class="w-14 h-14 rounded-xl flex items-center justify-center"
                style="background: rgba(255,255,255,0.15);">
                <i class="fa-solid fa-shield-halved text-white text-2xl"></i>
            </div>
        </div>
    </div>

    {{-- Cartes stats principales --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #C8102E;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                style="background-color: #fde8ec;">
                <i class="fa-solid fa-calendar-days text-xl" style="color: #C8102E;"></i>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Événements</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalEvenements }}</p>
                <p class="text-xs text-green-600 mt-0.5">
                    {{ $evenementsActifs }} actif(s)
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #007A3D;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                style="background-color: #e6f4ed;">
                <i class="fa-solid fa-building text-xl" style="color: #007A3D;"></i>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Entreprises</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalEntreprises }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #C8102E;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                style="background-color: #fde8ec;">
                <i class="fa-solid fa-users text-xl" style="color: #C8102E;"></i>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Participants</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalParticipants }}</p>
                <p class="text-xs text-green-600 mt-0.5">
                    {{ $participantsActifs }} actif(s)
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #007A3D;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                style="background-color: #e6f4ed;">
                <i class="fa-solid fa-handshake text-xl" style="color: #007A3D;"></i>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Rendez-vous</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalRendezVous }}</p>
            </div>
        </div>

    </div>

    {{-- Cartes stats secondaires --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">

        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 hover:shadow-lg transition">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-purple-50">
                <i class="fa-solid fa-id-badge text-purple-500"></i>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Badges générés</p>
                <p class="text-xl font-bold text-gray-800">{{ $totalBadges }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 hover:shadow-lg transition">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-yellow-50">
                <i class="fa-solid fa-clock text-yellow-500"></i>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Paiements en attente</p>
                <p class="text-xl font-bold text-gray-800">{{ $inscriptionsEnAttente }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 hover:shadow-lg transition">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-blue-50">
                <i class="fa-solid fa-calendar-check text-blue-500"></i>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Événements à venir</p>
                <p class="text-xl font-bold text-gray-800">{{ $evenementsActifs }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 hover:shadow-lg transition">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                style="background-color: #e6f4ed;">
                <i class="fa-solid fa-user-check" style="color: #007A3D;"></i>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Participants actifs</p>
                <p class="text-xl font-bold text-gray-800">{{ $participantsActifs }}</p>
            </div>
        </div>

    </div>

    {{-- Contenu principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Dernières inscriptions --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-bold text-gray-700 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus" style="color: #C8102E;"></i>
                    Dernières inscriptions
                </h3>
                <a href="{{ route('admin.inscriptions') }}"
                    class="text-xs px-3 py-1.5 rounded-lg text-white transition hover:opacity-90"
                    style="background-color: #C8102E;">
                    Voir toutes
                </a>
            </div>
            <div class="space-y-3">
                @forelse($dernieresInscriptions as $inscription)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                            style="background-color: {{ ($inscription->participant->genre ?? '') == 'femme' ? '#C8102E' : '#007A3D' }}">
                            {{ strtoupper(substr($inscription->participant->prenom ?? 'X', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">
                                {{ $inscription->participant->nom ?? '-' }}
                                {{ $inscription->participant->prenom ?? '' }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $inscription->evenement->nom ?? '-' }}
                            </p>
                        </div>
                    </div>
                    @if($inscription->statut_paiement == 'paye')
                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                        <i class="fa-solid fa-circle-check mr-1"></i> Payé
                    </span>
                    @elseif($inscription->statut_paiement == 'en_attente')
                    <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium">
                        <i class="fa-solid fa-clock mr-1"></i> En attente
                    </span>
                    @else
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600 font-medium">
                        {{ ucfirst($inscription->statut_paiement ?? '-') }}
                    </span>
                    @endif
                </div>
                @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fa-solid fa-inbox text-3xl mb-2 block text-gray-300"></i>
                    <p class="text-sm">Aucune inscription</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Prochains événements --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-bold text-gray-700 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-star" style="color: #007A3D;"></i>
                    Prochains événements
                </h3>
                <a href="{{ route('admin.evenements') }}"
                    class="text-xs px-3 py-1.5 rounded-lg text-white transition hover:opacity-90"
                    style="background-color: #007A3D;">
                    Voir tous
                </a>
            </div>
            <div class="space-y-3">
                @forelse($prochainsEvenements as $evenement)
                <div class="p-4 rounded-xl border border-gray-100 hover:border-green-200 transition">
                    <p class="font-semibold text-gray-800 text-sm mb-1">
                        {{ $evenement->nom }}
                    </p>
                    <p class="text-xs text-gray-400 flex items-center gap-1 mb-1">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $evenement->ville }}
                    </p>
                    <p class="text-xs text-gray-400 flex items-center gap-1 mb-2">
                        <i class="fa-solid fa-calendar"></i>
                        {{ $evenement->date_debut }}
                    </p>
                    @if($evenement->type_paiement == 'gratuit')
                    <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">
                        Gratuit
                    </span>
                    @else
                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">
                        {{ number_format($evenement->montant_inscription, 0, ',', ' ') }} FCFA
                    </span>
                    @endif
                </div>
                @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fa-solid fa-calendar-xmark text-3xl mb-2 block text-gray-300"></i>
                    <p class="text-sm">Aucun événement à venir</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>