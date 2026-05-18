<div class="flex h-screen bg-gray-100">

    <aside class="w-64 text-white flex flex-col" style="background-color: #007A3D;">
        <div class="p-6 text-center border-b border-green-800">
            <h1 class="text-xl font-bold text-white"> CCI-BF</h1>
            <p class="text-sm mt-1 text-green-200">GesB2B — Administration</p>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white font-medium" style="background-color: #C8102E;">
                 Dashboard
            </a>
            <a href="{{ route('admin.evenements') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Événements
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Entreprises
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Participants
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Stands
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Rendez-vous
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Badges
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Traducteurs
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Notifications
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Utilisateurs
            </a>
        </nav>

        <div class="p-4 border-t border-green-800">
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:bg-red-700 hover:text-white transition">
                    🚪 Déconnexion
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <header class="bg-white shadow px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <span class="text-2xl font-bold" style="color: #C8102E;">CCI-BF</span>
                <span class="text-gray-400">|</span>
                <h2 class="text-lg font-semibold text-gray-700">Tableau de bord Admin</h2>
            </div>
            <span class="text-gray-500"> {{ auth()->user()->name }}</span>
        </header>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4" style="border-color: #C8102E;">
                    <div class="p-4 rounded-full text-2xl" style="background-color: #fde8ec;"></div>
                    <div>
                        <p class="text-gray-500 text-sm">Événements</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalEvenements }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4" style="border-color: #007A3D;">
                    <div class="p-4 rounded-full text-2xl" style="background-color: #e6f4ed;"></div>
                    <div>
                        <p class="text-gray-500 text-sm">Entreprises</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalEntreprises }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4" style="border-color: #C8102E;">
                    <div class="p-4 rounded-full text-2xl" style="background-color: #fde8ec;"></div>
                    <div>
                        <p class="text-gray-500 text-sm">Participants</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalParticipants }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4" style="border-color: #007A3D;">
                    <div class="p-4 rounded-full text-2xl" style="background-color: #e6f4ed;"></div>
                    <div>
                        <p class="text-gray-500 text-sm">Rendez-vous</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalRendezVous }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4"> Derniers événements</h3>
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="pb-3 text-gray-500 font-medium">Nom</th>
                            <th class="pb-3 text-gray-500 font-medium">Ville</th>
                            <th class="pb-3 text-gray-500 font-medium">Date début</th>
                            <th class="pb-3 text-gray-500 font-medium">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($derniersEvenements as $evenement)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3">{{ $evenement->nom }}</td>
                            <td class="py-3">{{ $evenement->ville }}</td>
                            <td class="py-3">{{ $evenement->date_debut }}</td>
                            <td class="py-3">
                                <span class="px-3 py-1 rounded-full text-sm text-white" style="background-color: #007A3D;">
                                    Actif
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400">
                                Aucun événement pour le moment
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>