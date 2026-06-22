<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Remises & Réductions</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #007A3D;">
                {{ $remises->count() }} remise(s)
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Nouvelle remise
        </button>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 text-sm text-blue-700 flex items-start gap-2">
        <i class="fa-solid fa-circle-info mt-0.5"></i>
        Configurez des remises par événement selon le nombre de participants inscrits
        par une même entreprise, une tranche d'âge, ou le genre. La meilleure remise
        applicable est automatiquement utilisée lors du paiement.
    </div>

    <div class="mb-5">
        <select wire:model.live="filtre_evenement"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm w-full md:w-1/3">
            <option value="">Tous les événements</option>
            @foreach($evenements as $evt)
            <option value="{{ $evt->id }}">{{ $evt->nom }}</option>
            @endforeach
        </select>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Libellé</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Événement</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Condition</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Remise</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Statut</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($remises as $r)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $r->libelle }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $r->evenement->nom ?? '-' }}</td>
                    <td class="px-6 py-4 text-xs">
                        @if($r->type === 'nb_participants')
                        <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                            <i class="fa-solid fa-users mr-1"></i>≥ {{ $r->seuil_min }} participants/entreprise
                        </span>
                        @elseif($r->type === 'age')
                        <span class="px-2 py-1 rounded-full bg-purple-100 text-purple-700">
                            <i class="fa-solid fa-cake-candles mr-1"></i>{{ $r->age_min }}-{{ $r->age_max }} ans
                        </span>
                        @else
                        <span class="px-2 py-1 rounded-full bg-pink-100 text-pink-700">
                            <i class="fa-solid fa-venus-mars mr-1"></i>{{ ucfirst($r->genre) }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-bold" style="color: #007A3D;">-{{ $r->pourcentage }}%</span>
                    </td>
                    <td class="px-6 py-4">
                        <button wire:click="toggleActif({{ $r->id }})"
                            class="text-xs px-3 py-1 rounded-full font-medium transition
                                {{ $r->actif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                            {{ $r->actif ? 'Active' : 'Inactive' }}
                        </button>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button wire:click="modifier({{ $r->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="supprimer({{ $r->id }})"
                                wire:confirm="Supprimer cette remise ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-tags text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucune remise configurée</p>
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
                    <i class="fa-solid fa-tags"></i>
                    {{ $isEditing ? 'Modifier la remise' : 'Nouvelle remise' }}
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8 space-y-5">

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Événement *</label>
                    <select wire:model="id_evenement"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        <option value="">-- Choisir --</option>
                        @foreach($evenements as $evt)
                        <option value="{{ $evt->id }}">{{ $evt->nom }}</option>
                        @endforeach
                    </select>
                    @error('id_evenement') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Libellé *</label>
                    <input wire:model="libelle" type="text"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                        placeholder="Ex: Remise groupe entreprise">
                    @error('libelle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-2">Type de condition *</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="type" value="nb_participants" class="hidden peer">
                            <div class="p-3 border-2 rounded-xl text-center transition text-xs
                                peer-checked:border-blue-400 peer-checked:bg-blue-50 border-gray-200">
                                <i class="fa-solid fa-users block mb-1"></i>Nb participants
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="type" value="age" class="hidden peer">
                            <div class="p-3 border-2 rounded-xl text-center transition text-xs
                                peer-checked:border-purple-400 peer-checked:bg-purple-50 border-gray-200">
                                <i class="fa-solid fa-cake-candles block mb-1"></i>Tranche d'âge
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="type" value="genre" class="hidden peer">
                            <div class="p-3 border-2 rounded-xl text-center transition text-xs
                                peer-checked:border-pink-400 peer-checked:bg-pink-50 border-gray-200">
                                <i class="fa-solid fa-venus-mars block mb-1"></i>Genre
                            </div>
                        </label>
                    </div>
                </div>

                @if($type === 'nb_participants')
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">
                        Nombre minimum de participants par entreprise *
                    </label>
                    <input wire:model="seuil_min" type="number" min="1"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                        placeholder="Ex: 5">
                    @error('seuil_min') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                @endif

                @if($type === 'age')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Âge minimum *</label>
                        <input wire:model="age_min" type="number" min="0"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        @error('age_min') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Âge maximum *</label>
                        <input wire:model="age_max" type="number" min="0"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        @error('age_max') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                @endif

                @if($type === 'genre')
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Genre concerné *</label>
                    <select wire:model="genre"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        <option value="femme">Femme</option>
                        <option value="homme">Homme</option>
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Pourcentage de remise (%) *</label>
                    <input wire:model="pourcentage" type="number" min="0" max="100" step="0.01"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                        placeholder="Ex: 15">
                    @error('pourcentage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model="actif" id="actif" class="w-4 h-4">
                    <label for="actif" class="text-sm text-gray-700 cursor-pointer">Remise active</label>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="closeModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        Annuler
                    </button>
                    <button wire:click="sauvegarder"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #C8102E;">
                        {{ $isEditing ? 'Modifier' : 'Créer' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>