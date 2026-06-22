<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Liste des Participants</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $participants->count() }} participant(s)
            </span>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="flex gap-4 mb-5 flex-wrap">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher un participant..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none text-sm">
        </div>
        <select wire:model.live="filtre_evenement"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Tous les événements</option>
            @foreach($evenements as $ev)
            <option value="{{ $ev->id }}">{{ $ev->nom }}</option>
            @endforeach
        </select>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Email</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Entreprise</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Événement</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Rôle</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">RDV</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $participant)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                style="background-color: {{ $participant->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                                {{ strtoupper(substr($participant->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ $participant->nom }} {{ $participant->prenom }}
                                </p>
                                @if($participant->telephone)
                                <p class="text-xs text-gray-400">
                                    <i class="fa-solid fa-phone mr-1"></i>{{ $participant->telephone }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">{{ $participant->email ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                            {{ $participant->entreprise->nom ?? 'Indépendant' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">{{ $participant->evenement->nom ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-3 py-1 rounded-full font-medium text-white"
                            style="background-color: #007A3D;">
                            {{ ucfirst($participant->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($participant->participation_rdv)
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                            <i class="fa-solid fa-circle-check mr-1"></i> Activé
                        </span>
                        @else
                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-500 font-medium">
                            <i class="fa-solid fa-circle-xmark mr-1"></i> Désactivé
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button wire:click="toggleRdv({{ $participant->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90"
                                style="background-color: {{ $participant->participation_rdv ? '#C8102E' : '#007A3D' }}"
                                title="{{ $participant->participation_rdv ? 'Désactiver RDV' : 'Activer RDV' }}">
                                <i class="fa-solid {{ $participant->participation_rdv ? 'fa-toggle-off' : 'fa-toggle-on' }} mr-1"></i>
                                {{ $participant->participation_rdv ? 'Désactiver' : 'Activer' }} RDV
                            </button>
                            <button wire:click="supprimer({{ $participant->id }})"
                                wire:confirm="Supprimer ce participant ? Cette action est irréversible."
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700"
                                title="Supprimer">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-users text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun participant</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>