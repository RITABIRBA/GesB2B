<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Souhaits de Rendez-vous</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
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

    <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-4 mb-6 text-sm text-blue-700 flex items-start gap-2">
        <i class="fa-solid fa-circle-info mt-0.5"></i>
        Émettez au moins <strong>10 souhaits</strong> (jusqu'à 20) classés par priorité pour maximiser vos rendez-vous.
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Priorité</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Mon participant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Veut rencontrer</th>
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
                    <td class="px-6 py-4 font-semibold text-gray-800">
                        {{ $souhait->participant->nom ?? '-' }} {{ $souhait->participant->prenom ?? '' }}
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ $souhait->participantCible->nom ?? '-' }} {{ $souhait->participantCible->prenom ?? '' }}
                    </td>
                    <td class="px-6 py-4">
                        <button wire:click="supprimer({{ $souhait->id }})"
                            wire:confirm="Supprimer ce souhait ?"
                            class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-heart text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun souhait émis</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-heart"></i> Nouveau souhait RDV
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Mon participant *</label>
                        <select wire:model="id_participant"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($mesParticipants as $p)
                            <option value="{{ $p->id }}">{{ $p->nom }} {{ $p->prenom }}</option>
                            @endforeach
                        </select>
                        @error('id_participant') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Veut rencontrer *</label>
                        <select wire:model="id_participant_cible"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($tousParticipants as $p)
                            <option value="{{ $p->id }}">{{ $p->nom }} {{ $p->prenom }}</option>
                            @endforeach
                        </select>
                        @error('id_participant_cible') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Priorité * <span class="text-gray-400 font-normal">(1 = plus important → 20)</span>
                        </label>
                        <input wire:model="priorite" type="number" min="1" max="20"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                            placeholder="Entre 1 et 20">
                        @error('priorite') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="closeModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        Annuler
                    </button>
                    <button wire:click="sauvegarder"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm"
                        style="background-color: #C8102E;">
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>