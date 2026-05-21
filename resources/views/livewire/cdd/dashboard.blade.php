<div>

    {{-- Cartes statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #2d5a8e;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #e8f0fb;">
                <i class="fa-solid fa-building" style="color: #2d5a8e;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Mes Entreprises</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalEntreprises }}</p>
            </div>
        </div>

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
                <i class="fa-solid fa-clock" style="color: #C8102E;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">En attente de validation</p>
                <p class="text-3xl font-bold text-gray-800">{{ $entreprisesAttente }}</p>
            </div>
        </div>

    </div>

    {{-- Dernières entreprises --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-building" style="color: #2d5a8e;"></i>
                Dernières entreprises
            </h3>
            <a href="{{ route('cdd.entreprises') }}"
                class="text-sm px-4 py-2 rounded-lg text-white transition hover:opacity-90"
                style="background-color: #2d5a8e;">
                Voir toutes
            </a>
        </div>
        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="pb-3 text-gray-500 font-medium text-sm">Nom</th>
                    <th class="pb-3 text-gray-500 font-medium text-sm">Secteur</th>
                    <th class="pb-3 text-gray-500 font-medium text-sm">Pays</th>
                    <th class="pb-3 text-gray-500 font-medium text-sm">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dernieresEntreprises as $entreprise)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="py-3 font-semibold text-gray-800">{{ $entreprise->nom }}</td>
                    <td class="py-3 text-gray-600 text-sm">{{ $entreprise->secteur_activite }}</td>
                    <td class="py-3 text-gray-600 text-sm">
                        <i class="fa-solid fa-flag text-gray-400 mr-1"></i>
                        {{ $entreprise->pays }}
                    </td>
                    <td class="py-3">
                        @if($entreprise->statut_validation == 'valide')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-circle-check mr-1"></i> Validé
                            </span>
                        @elseif($entreprise->statut_validation == 'rejete')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-red-600">
                                <i class="fa-solid fa-circle-xmark mr-1"></i> Rejeté
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">
                                <i class="fa-solid fa-clock mr-1"></i> En attente
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-gray-400">
                        <i class="fa-solid fa-inbox text-3xl mb-2 block"></i>
                        Aucune entreprise pour le moment
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>