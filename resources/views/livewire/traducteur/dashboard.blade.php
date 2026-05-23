<div>

    {{-- Info traducteur --}}
    @if($traducteur)
    <div class="bg-white rounded-xl shadow p-6 mb-6 flex items-center gap-6">
        <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold flex-shrink-0"
            style="background-color: #007A3D;">
            {{ strtoupper(substr($traducteur->prenom, 0, 1)) }}
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                {{ $traducteur->nom }} {{ $traducteur->prenom }}
            </h2>
            <div class="flex items-center gap-4 mt-1">
                <span class="text-sm text-gray-500">
                    <i class="fa-solid fa-language text-gray-400 mr-1"></i>
                    {{ $traducteur->langue }}
                </span>
                <span class="text-sm text-gray-500">
                    <i class="fa-solid fa-phone text-gray-400 mr-1"></i>
                    {{ $traducteur->telephone }}
                </span>
                @if($traducteur->email)
                <span class="text-sm text-gray-500">
                    <i class="fa-solid fa-envelope text-gray-400 mr-1"></i>
                    {{ $traducteur->email }}
                </span>
                @endif
            </div>
        </div>
        <div class="ml-auto">
            <span class="px-4 py-2 rounded-full text-sm text-white font-medium"
                style="background-color: #007A3D;">
                <i class="fa-solid fa-language mr-1"></i>
                {{ $traducteur->langue }}
            </span>
        </div>
    </div>
    @endif

    {{-- Cartes statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #007A3D;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #e6f4ed;">
                <i class="fa-solid fa-handshake" style="color: #007A3D;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total Rendez-vous</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalRdv }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #C8102E;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #fde8ec;">
                <i class="fa-solid fa-calendar-day" style="color: #C8102E;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">RDV Aujourd'hui</p>
                <p class="text-3xl font-bold text-gray-800">{{ $rdvAujourdhui }}</p>
            </div>
        </div>

    </div>

    {{-- Prochains rendez-vous --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-calendar-check" style="color: #007A3D;"></i>
                Mes Prochains Rendez-vous
            </h3>
            <a href="{{ route('traducteur.rendez-vous') }}"
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
                        {{ $rdv->participant1->nom ?? '-' }}
                        <span class="text-gray-400 mx-1">↔</span>
                        {{ $rdv->participant2->nom ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
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
            <p>Aucun rendez-vous assigné</p>
        </div>
        @endforelse
    </div>

</div>