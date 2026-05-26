<div>
    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Statistiques des Souhaits</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #2d5a8e;">
                {{ $total }} participant(s)
            </span>
        </div>
    </div>

    {{-- Statistiques globales --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4 border-l-4"
            style="border-color: #2d5a8e;">
            <i class="fa-solid fa-users text-2xl" style="color: #2d5a8e;"></i>
            <div>
                <p class="text-xs text-gray-500">Total participants</p>
                <p class="text-3xl font-bold text-gray-800">{{ $total }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4 border-l-4"
            style="border-color: #007A3D;">
            <i class="fa-solid fa-circle-check text-2xl" style="color: #007A3D;"></i>
            <div>
                <p class="text-xs text-gray-500">Souhaits suffisants (≥10)</p>
                <p class="text-3xl font-bold text-gray-800">{{ $suffisant }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4 border-l-4"
            style="border-color: #C8102E;">
            <i class="fa-solid fa-triangle-exclamation text-2xl" style="color: #C8102E;"></i>
            <div>
                <p class="text-xs text-gray-500">Souhaits insuffisants (&lt;10)</p>
                <p class="text-3xl font-bold text-gray-800">{{ $insuffisant }}</p>
            </div>
        </div>
    </div>

    {{-- Info --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-4 mb-6 text-sm text-blue-700 flex items-start gap-2">
        <i class="fa-solid fa-circle-info mt-0.5"></i>
        Chaque participant doit émettre <strong>au moins 10 souhaits</strong> de rendez-vous
        pour participer au match-making. Relancez ceux qui n'ont pas encore atteint ce minimum.
    </div>

    {{-- Filtres --}}
    <div class="flex gap-4 mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher un participant..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none text-sm">
        </div>
        <select wire:model.live="filtre_evenement"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Tous les événements</option>
            @foreach($evenements as $evenement)
            <option value="{{ $evenement->id }}">{{ $evenement->nom }}</option>
            @endforeach
        </select>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Entreprise</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Événement</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Souhaits émis</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Progression</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $participant)
                <tr class="border-b hover:bg-gray-50 transition">

                    {{-- Participant --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                style="background-color: #2d5a8e;">
                                {{ strtoupper(substr($participant->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{ $participant->nom }} {{ $participant->prenom }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $participant->email }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Entreprise --}}
                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                            {{ $participant->entreprise->nom ?? 'Indépendant' }}
                        </span>
                    </td>

                    {{-- Événement --}}
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        {{ $participant->evenement->nom ?? '-' }}
                    </td>

                    {{-- Nombre souhaits --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-bold {{ $participant->nb_souhaits >= 10 ? 'text-green-600' : 'text-red-500' }}">
                                {{ $participant->nb_souhaits }}
                            </span>
                            <span class="text-gray-400 text-sm">/ 20</span>
                        </div>
                    </td>

                    {{-- Barre progression --}}
                    <td class="px-6 py-4">
                        <div class="w-32">
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                                <div class="h-2.5 rounded-full transition-all"
                                    style="width: {{ min(($participant->nb_souhaits / 20) * 100, 100) }}%;
                                           background-color: {{ $participant->nb_souhaits >= 10 ? '#007A3D' : '#C8102E' }}">
                                </div>
                            </div>
                            <p class="text-xs text-gray-400">
                                {{ min(($participant->nb_souhaits / 20) * 100, 100) }}%
                            </p>
                        </div>
                    </td>

                    {{-- Statut --}}
                    <td class="px-6 py-4">
                        @if($participant->nb_souhaits >= 10)
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-circle-check mr-1"></i> Suffisant
                            </span>
                        @elseif($participant->nb_souhaits > 0)
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">
                                <i class="fa-solid fa-clock mr-1"></i>
                                Encore {{ 10 - $participant->nb_souhaits }} requis
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-red-600">
                                <i class="fa-solid fa-triangle-exclamation mr-1"></i> Aucun souhait
                            </span>
                        @endif
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-heart text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun participant trouvé</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>