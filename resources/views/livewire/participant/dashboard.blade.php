<div>

    {{-- Info participant --}}
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

    {{-- Cartes statistiques --}}
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

    {{-- Prochains rendez-vous --}}
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
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold"
                    style="background-color: #007A3D;">
                    {{ $rdv->stand->numero_stand ?? '-' }}
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