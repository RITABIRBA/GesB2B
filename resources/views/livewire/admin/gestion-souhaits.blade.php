<div>
    {{-- Message succès --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Souhaits de Rendez-vous</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #007A3D;">
                {{ $souhaits->count() }} souhait(s)
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Nouveau souhait
        </button>
    </div>

    {{-- Info cahier des charges --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-4 mb-6 flex items-start gap-3">
        <i class="fa-solid fa-circle-info text-blue-500 text-xl mt-0.5"></i>
        <div class="text-sm text-blue-700">
            <p class="font-semibold mb-1">Règles des souhaits de rendez-vous</p>
            <p>Chaque participant doit émettre <strong>au moins 10 souhaits</strong> (jusqu'à 20) classés par ordre de priorité. Les souhaits mutuels sont traités en priorité lors du match-making.</p>
        </div>
    </div>

    {{-- Recherche --}}
    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher par participant..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Priorité</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Veut rencontrer</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Type</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($souhaits as $souhait)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white text-sm
                            {{ $souhait->priorite <= 3 ? '' : ($souhait->priorite <= 10 ? '' : '') }}"
                            style="background-color: {{ $souhait->priorite <= 3 ? '#C8102E' : ($souhait->priorite <= 10 ? '#007A3D' : '#6b7280') }}">
                            {{ $souhait->priorite }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                style="background-color: #C8102E;">
                                {{ strtoupper(substr($souhait->participant->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ $souhait->participant->nom ?? '-' }} {{ $souhait->participant->prenom ?? '' }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $souhait->participant->entreprise->nom ?? 'Indépendant' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                style="background-color: #007A3D;">
                                {{ strtoupper(substr($souhait->participantCible->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ $souhait->participantCible->nom ?? '-' }} {{ $souhait->participantCible->prenom ?? '' }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $souhait->participantCible->entreprise->nom ?? 'Indépendant' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($souhait->type == 'mutuel')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium" style="background-color: #C8102E;">
                                <i class="fa-solid fa-arrows-left-right mr-1"></i> Mutuel
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                                <i class="fa-solid fa-arrow-right mr-1"></i> Envoyé
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button wire:click="modifier({{ $souhait->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700 flex items-center gap-1">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </button>
                            <button wire:click="supprimer({{ $souhait->id }})"
                                wire:confirm="Voulez-vous vraiment supprimer ce souhait ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                                <i class="fa-solid fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-heart text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun souhait de rendez-vous</p>
                        <button wire:click="openModal"
                            class="mt-3 px-5 py-2 rounded-xl text-white text-sm font-medium"
                            style="background-color: #C8102E;">
                            Ajouter le premier souhait
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-y-auto max-h-[90vh]">

            {{-- Header modal --}}
            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-heart"></i>
                    {{ $isEditing ? 'Modifier le souhait' : 'Nouveau souhait RDV' }}
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl transition">
                    &times;
                </button>
            </div>

            {{-- Body modal --}}
            <div class="p-8">
                <div class="grid grid-cols-1 gap-5">

                    {{-- Participant --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            <i class="fa-solid fa-user mr-1" style="color: #C8102E;"></i>
                            Participant demandeur *
                        </label>
                        <select wire:model="id_participant"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            <option value="">-- Choisir le participant --</option>
                            @foreach($participants as $participant)
                            <option value="{{ $participant->id }}">
                                {{ $participant->nom }} {{ $participant->prenom }}
                                {{ $participant->entreprise ? '('.$participant->entreprise->nom.')' : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_participant')
                            <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Participant cible --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            <i class="fa-solid fa-user-check mr-1" style="color: #007A3D;"></i>
                            Veut rencontrer *
                        </label>
                        <select wire:model="id_participant_cible"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            <option value="">-- Choisir le participant cible --</option>
                            @foreach($participants as $participant)
                            <option value="{{ $participant->id }}">
                                {{ $participant->nom }} {{ $participant->prenom }}
                                {{ $participant->entreprise ? '('.$participant->entreprise->nom.')' : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_participant_cible')
                            <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Priorité --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            <i class="fa-solid fa-ranking-star mr-1" style="color: #C8102E;"></i>
                            Priorité * <span class="text-gray-400 font-normal">(1 = plus important → 20 = moins important)</span>
                        </label>
                        <input wire:model="priorite" type="number" min="1" max="20"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Entre 1 et 20">
                        @error('priorite')
                            <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </span>
                        @enderror
                        <div class="mt-2 grid grid-cols-3 gap-2 text-xs text-center">
                            <div class="bg-red-100 text-red-700 rounded-lg px-2 py-1">
                                <i class="fa-solid fa-star"></i> 1-3 : Haute priorité
                            </div>
                            <div class="bg-green-100 text-green-700 rounded-lg px-2 py-1">
                                <i class="fa-solid fa-circle"></i> 4-10 : Moyenne
                            </div>
                            <div class="bg-gray-100 text-gray-600 rounded-lg px-2 py-1">
                                <i class="fa-solid fa-minus"></i> 11-20 : Basse
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Boutons --}}
                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="closeModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="sauvegarder"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #C8102E;">
                        <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-floppy-disk' }} mr-1"></i>
                        {{ $isEditing ? 'Modifier' : 'Enregistrer' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>