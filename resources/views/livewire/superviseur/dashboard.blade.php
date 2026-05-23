<div>

    {{-- Cartes statistiques --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #6b2d6b;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #f3e8f3;">
                <i class="fa-solid fa-calendar" style="color: #6b2d6b;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Événements</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalEvenements }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #007A3D;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #e6f4ed;">
                <i class="fa-solid fa-building" style="color: #007A3D;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Entreprises</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalEntreprises }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #C8102E;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #fde8ec;">
                <i class="fa-solid fa-users" style="color: #C8102E;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Participants</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalParticipants }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #2d5a8e;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #e8f0fb;">
                <i class="fa-solid fa-handshake" style="color: #2d5a8e;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Rendez-vous</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalRendezVous }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #f59e0b;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #fef3c7;">
                <i class="fa-solid fa-clock" style="color: #f59e0b;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">En attente validation</p>
                <p class="text-3xl font-bold text-gray-800">{{ $entreprisesAttente }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #06b6d4;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #e0f9fb;">
                <i class="fa-solid fa-calendar-check" style="color: #06b6d4;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">RDV Planifiés</p>
                <p class="text-3xl font-bold text-gray-800">{{ $rdvPlanifies }}</p>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Derniers événements --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fa-solid fa-calendar" style="color: #6b2d6b;"></i>
                    Derniers événements
                </h3>
                <a href="{{ route('superviseur.evenements') }}"
                    class="text-sm px-3 py-1.5 rounded-lg text-white"
                    style="background-color: #6b2d6b;">
                    Voir tous
                </a>
            </div>
            @forelse($derniersEvenements as $evenement)
            <div class="flex items-center justify-between py-3 border-b last:border-0">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $evenement->nom }}</p>
                    <p class="text-xs text-gray-400">
                        <i class="fa-solid fa-location-dot mr-1"></i>{{ $evenement->ville }}
                    </p>
                </div>
                <span class="text-xs text-gray-500">{{ $evenement->date_debut }}</span>
            </div>
            @empty
            <p class="text-center text-gray-400 py-4">Aucun événement</p>
            @endforelse
        </div>

        {{-- Entreprises en attente --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fa-solid fa-clock text-yellow-500"></i>
                    Entreprises en attente
                </h3>
                <a href="{{ route('superviseur.entreprises') }}"
                    class="text-sm px-3 py-1.5 rounded-lg text-white bg-yellow-500">
                    Voir toutes
                </a>
            </div>
            @forelse($dernieresEntreprises as $entreprise)
            <div class="flex items-center justify-between py-3 border-b last:border-0">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $entreprise->nom }}</p>
                    <p class="text-xs text-gray-400">{{ $entreprise->pays }} — {{ $entreprise->secteur_activite }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium">
                    En attente
                </span>
            </div>
            @empty
            <p class="text-center text-gray-400 py-4">Aucune entreprise en attente</p>
            @endforelse
        </div>

    </div>

</div>