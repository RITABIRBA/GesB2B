<div>

    {{-- Info entreprise --}}
    @if($entreprise)
    <div class="bg-white rounded-xl shadow p-6 mb-6 flex items-center gap-6">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl font-bold flex-shrink-0"
            style="background-color: #007A3D;">
            {{ strtoupper(substr($entreprise->nom, 0, 1)) }}
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $entreprise->nom }}</h2>
            <div class="flex items-center gap-4 mt-1">
                <span class="text-sm text-gray-500">
                    <i class="fa-solid fa-industry text-gray-400 mr-1"></i>
                    {{ $entreprise->secteur_activite }}
                </span>
                <span class="text-sm text-gray-500">
                    <i class="fa-solid fa-flag text-gray-400 mr-1"></i>
                    {{ $entreprise->pays }}
                </span>
                <span class="text-sm text-gray-500">
                    <i class="fa-solid fa-location-dot text-gray-400 mr-1"></i>
                    {{ $entreprise->ville }}
                </span>
            </div>
        </div>
        <div class="ml-auto">
            @if($entreprise->statut_validation == 'valide')
                <span class="px-4 py-2 rounded-full text-sm text-white font-medium"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-circle-check mr-1"></i> Validée
                </span>
            @elseif($entreprise->statut_validation == 'en_attente')
                <span class="px-4 py-2 rounded-full text-sm text-white font-medium bg-yellow-500">
                    <i class="fa-solid fa-clock mr-1"></i> En attente
                </span>
            @else
                <span class="px-4 py-2 rounded-full text-sm text-white font-medium bg-red-600">
                    <i class="fa-solid fa-circle-xmark mr-1"></i> Rejetée
                </span>
            @endif
        </div>
    </div>
    @endif

    {{-- Cartes statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #007A3D;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #e6f4ed;">
                <i class="fa-solid fa-users" style="color: #007A3D;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Mes Participants</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalParticipants }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #C8102E;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #fde8ec;">
                <i class="fa-solid fa-store" style="color: #C8102E;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Mes Stands</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalStands }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #2d5a8e;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #e8f0fb;">
                <i class="fa-solid fa-handshake" style="color: #2d5a8e;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Mes Rendez-vous</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalRdv }}</p>
            </div>
        </div>

    </div>

    {{-- Derniers participants --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-users" style="color: #007A3D;"></i>
                Mes Participants
            </h3>
            <a href="{{ route('entreprise.participants') }}"
                class="text-sm px-4 py-2 rounded-lg text-white transition hover:opacity-90"
                style="background-color: #007A3D;">
                Voir tous
            </a>
        </div>
        @forelse($derniersParticipants as $participant)
        <div class="flex items-center justify-between py-3 border-b last:border-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold"
                    style="background-color: #C8102E;">
                    {{ strtoupper(substr($participant->prenom, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">
                        {{ $participant->nom }} {{ $participant->prenom }}
                    </p>
                    <p class="text-xs text-gray-400">{{ $participant->email }}</p>
                </div>
            </div>
            <span class="text-xs px-3 py-1 rounded-full font-medium text-white"
                style="background-color: #007A3D;">
                {{ ucfirst($participant->role) }}
            </span>
        </div>
        @empty
        <p class="text-center text-gray-400 py-4">Aucun participant</p>
        @endforelse
    </div>

</div>