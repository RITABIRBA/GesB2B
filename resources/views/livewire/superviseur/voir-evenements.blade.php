<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Liste des Événements</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #6b2d6b;">
                {{ $evenements->count() }} événement(s)
            </span>
        </div>
    </div>

    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher par nom ou ville..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none text-sm">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Nom</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Type</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Ville</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Année</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Date début</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Date fin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evenements as $evenement)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $evenement->nom }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-3 py-1 rounded-full font-medium text-white"
                            style="background-color: #6b2d6b;">
                            {{ $evenement->typeEvenement->nom ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <i class="fa-solid fa-location-dot text-gray-400 mr-1"></i>
                        {{ $evenement->ville }}
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $evenement->annee }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $evenement->date_debut }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $evenement->date_fin }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-calendar text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun événement</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>