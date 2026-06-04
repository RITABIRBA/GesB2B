<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-300 text-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
        {{ session('error') }}
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Mes Souhaits de RDV</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: {{ $souhaits->count() >= 10 ? '#007A3D' : '#f59e0b' }}">
                {{ $souhaits->count() }}/10 minimum
            </span>
        </div>
        <button wire:click="openModal"
            wire:loading.attr="disabled"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i> Nouveau souhait
        </button>
    </div>

    {{-- Barre de progression --}}
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <div class="flex justify-between text-sm mb-2">
            <span class="text-gray-600">Progression des souhaits</span>
            <span class="font-semibold {{ $souhaits->count() >= 10 ? 'text-green-600' : 'text-orange-500' }}">
                {{ $souhaits->count() }}/20
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3">
            <div class="h-3 rounded-full transition-all duration-500"
                style="width: {{ min(($souhaits->count() / 20) * 100, 100) }}%;
                       background-color: {{ $souhaits->count() >= 10 ? '#007A3D' : '#f59e0b' }}">
            </div>
        </div>
        @if($souhaits->count() < 10)
        <p class="text-xs text-orange-500 mt-2">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
            Il vous manque encore {{ 10 - $souhaits->count() }} souhait(s) pour atteindre le minimum requis.
        </p>
        @else
        <p class="text-xs text-green-600 mt-2">
            <i class="fa-solid fa-circle-check mr-1"></i>
            Excellent ! Vous avez atteint le minimum requis.
        </p>
        @endif
    </div>

    {{-- Tableau des souhaits --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Priorité</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Veut rencontrer</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Entreprise</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Type</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($souhaits as $souhait)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white text-sm"
                            style="background-color: {{ $souhait->priorite <= 3 ? '#C8102E' : ($souhait->priorite <= 10 ? '#007A3D' : '#6b7280') }}">
                            {{ $souhait->priorite }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-semibold text-gray-800">
                                {{ $souhait->participantCible->nom ?? '-' }}
                                {{ $souhait->participantCible->prenom ?? '' }}
                            </p>
                            {{-- ← Point 4 : Fonction visible --}}
                            @if($souhait->participantCible?->fonction)
                            <p class="text-xs text-gray-400 mt-0.5">
                                <i class="fa-solid fa-briefcase mr-1"></i>
                                {{ $souhait->participantCible->fonction }}
                            </p>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                            {{ $souhait->participantCible->entreprise->nom ?? 'Indépendant' }}
                        </span>
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
                        <button wire:click="supprimer({{ $souhait->id }})"
                            wire:confirm="Supprimer ce souhait ?"
                            wire:loading.attr="disabled"
                            class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-heart text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun souhait émis</p>
                        <p class="text-sm text-gray-400 mt-1">
                            Émettez au moins 10 souhaits pour participer au match-making
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-heart"></i> Nouveau souhait
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 gap-5">

                    {{-- Participant cible --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Veut rencontrer *
                        </label>

                        {{-- Info même événement --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-3 text-xs text-blue-700 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info"></i>
                            Seuls les participants de votre événement sont affichés.
                        </div>

                        {{-- Liste des participants --}}
                        <div class="space-y-2 max-h-60 overflow-y-auto border rounded-xl p-2">
                            @forelse($autresParticipants as $p)
                            <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition hover:bg-gray-50
                                {{ $id_participant_cible == $p->id ? 'bg-green-50 border border-green-300' : 'border border-transparent' }}">
                                <input type="radio"
                                    wire:model="id_participant_cible"
                                    value="{{ $p->id }}"
                                    class="text-green-600">
                                <div class="flex items-center gap-3 flex-1">
                                    {{-- Avatar --}}
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                        style="background-color: {{ $p->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                                        {{ strtoupper(substr($p->prenom ?? 'X', 0, 1)) }}
                                    </div>
                                    <div>
                                        {{-- Nom --}}
                                        <p class="font-semibold text-gray-800 text-sm">
                                            {{ $p->nom }} {{ $p->prenom }}
                                            @if($p->genre == 'femme')
                                                <span class="text-xs text-gray-400">(Mme)</span>
                                            @elseif($p->genre == 'homme')
                                                <span class="text-xs text-gray-400">(M.)</span>
                                            @endif
                                        </p>
                                        {{-- Fonction --}}
                                        @if($p->fonction)
                                        <p class="text-xs text-gray-400">
                                            <i class="fa-solid fa-briefcase mr-1"></i>
                                            {{ $p->fonction }}
                                        </p>
                                        @endif
                                        {{-- Entreprise --}}
                                        <p class="text-xs text-gray-400">
                                            <i class="fa-solid fa-building mr-1"></i>
                                            {{ $p->entreprise->nom ?? 'Indépendant' }}
                                        </p>
                                        {{-- Secteur --}}
                                        @if($p->secteur_activite)
                                        <p class="text-xs text-gray-400">
                                            <i class="fa-solid fa-tag mr-1"></i>
                                            {{ $p->secteur_activite }}
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </label>
                            @empty
                            <div class="text-center py-6 text-gray-400">
                                <i class="fa-solid fa-users text-3xl mb-2 block text-gray-300"></i>
                                <p class="text-sm">Aucun participant disponible dans votre événement</p>
                            </div>
                            @endforelse
                        </div>
                        @error('id_participant_cible')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Priorité --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Priorité *
                            <span class="text-gray-400 font-normal">(1 = plus important)</span>
                        </label>
                        <input wire:model="priorite" type="number" min="1" max="20"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                            placeholder="Entre 1 et 20">
                        @error('priorite')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="closeModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="sauvegarder"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm flex items-center gap-2"
                        style="background-color: #C8102E;">
                        <span wire:loading.remove>
                            <i class="fa-solid fa-floppy-disk mr-1"></i>
                            Enregistrer
                        </span>
                        <span wire:loading>
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                            Enregistrement...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>