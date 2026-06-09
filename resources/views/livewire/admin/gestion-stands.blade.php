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
            <h3 class="text-xl font-bold text-gray-700">Liste des stands</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #007A3D;">
                {{ $stands->count() }} stand(s)
            </span>
            <span class="text-sm px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                <i class="fa-solid fa-circle-dot mr-1"></i>
                {{ $stands->whereNull('id_entreprise')->count() }} disponible(s)
            </span>
            <span class="text-sm px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                <i class="fa-solid fa-building mr-1"></i>
                {{ $stands->whereNotNull('id_entreprise')->count() }} occupé(s)
            </span>
        </div>
        {{-- ← Seulement le bouton génération --}}
        <button wire:click="openGenerateModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #007A3D;">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
            Générer automatiquement
        </button>
    </div>

    {{-- Recherche --}}
    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher par entreprise ou événement..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">N° Stand</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Événement</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Entreprise</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Superficie</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Standing</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stands as $stand)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white text-sm"
                            style="background-color: {{ $stand->entreprise ? '#2d5a8e' : '#007A3D' }}">
                            {{ $stand->numero_stand }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-700 font-medium">
                        <i class="fa-solid fa-calendar text-gray-400 mr-1"></i>
                        {{ $stand->evenement->nom ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($stand->entreprise)
                        <span class="text-xs px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                            <i class="fa-solid fa-building mr-1"></i>
                            {{ $stand->entreprise->nom }}
                        </span>
                        @else
                        <span class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                            <i class="fa-solid fa-circle-dot mr-1"></i>
                            Disponible
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <i class="fa-solid fa-ruler-combined text-gray-400 mr-1"></i>
                        {{ $stand->superficie }} m²
                    </td>
                    <td class="px-6 py-4">
                        @if($stand->standing == 'vip')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">
                                <i class="fa-solid fa-star mr-1"></i> VIP
                            </span>
                        @elseif($stand->standing == 'premium')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                                <i class="fa-solid fa-gem mr-1"></i> Premium
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium" style="background-color: #007A3D;">
                                <i class="fa-solid fa-store mr-1"></i> Standard
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button wire:click="modifier({{ $stand->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700 flex items-center gap-1">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </button>
                            <button wire:click="supprimer({{ $stand->id }})"
                                wire:confirm="Voulez-vous vraiment supprimer ce stand ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-store text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun stand pour le moment</p>
                        <div class="flex gap-3 justify-center mt-3">
                            <button wire:click="openGenerateModal"
                                class="px-5 py-2 rounded-xl text-white text-sm font-medium"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-wand-magic-sparkles mr-1"></i>
                                Générer automatiquement
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL GÉNÉRATION AUTOMATIQUE --}}
    @if($showGenerateModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    Générer les stands automatiquement
                </h3>
                <button wire:click="closeGenerateModal"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8">

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-5 text-xs text-blue-700 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    Les stands existants de l'événement choisi seront
                    <strong>supprimés et remplacés</strong> par les nouveaux stands générés.
                </div>

                <div class="space-y-4">

                    {{-- Événement --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Événement *
                        </label>
                        <select wire:model="id_evenement_generate"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
                            <option value="">-- Choisir un événement --</option>
                            @foreach($evenements as $evenement)
                            <option value="{{ $evenement->id }}">{{ $evenement->nom }}</option>
                            @endforeach
                        </select>
                        @error('id_evenement_generate')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Nombre de stands --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Nombre de stands *
                        </label>
                        <input wire:model.live="nombre_stands"
                            type="number" min="1" max="100"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                            placeholder="Ex: 20">
                        @error('nombre_stands')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                        @if($nombre_stands > 0)
                        <p class="text-xs text-green-600 mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check"></i>
                            {{ $nombre_stands }} stands seront créés,
                            numérotés de 1 à {{ $nombre_stands }}
                        </p>
                        @endif
                    </div>

                    {{-- Superficie par défaut --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Superficie par défaut (m²) *
                        </label>
                        <input wire:model="superficie_default"
                            type="number" step="0.1" min="1"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                            placeholder="Ex: 9">
                        @error('superficie_default')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Standing par défaut --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Standing par défaut *
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach($standings as $s)
                            <button type="button"
                                wire:click="$set('standing_default', '{{ $s }}')"
                                class="border-2 rounded-xl p-3 text-center transition
                                    {{ $standing_default === $s
                                        ? 'border-green-400 bg-green-50'
                                        : 'border-gray-200 hover:bg-gray-50' }}">
                                @if($s === 'vip')
                                    <i class="fa-solid fa-star text-yellow-500 text-lg mb-1 block"></i>
                                @elseif($s === 'premium')
                                    <i class="fa-solid fa-gem text-blue-500 text-lg mb-1 block"></i>
                                @else
                                    <i class="fa-solid fa-store text-green-600 text-lg mb-1 block"></i>
                                @endif
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst($s) }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="closeGenerateModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="genererStands"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm flex items-center gap-2"
                        style="background-color: #007A3D;">
                        <span wire:loading.remove>
                            <i class="fa-solid fa-wand-magic-sparkles mr-1"></i>
                            Générer {{ $nombre_stands > 0 ? $nombre_stands : '...' }} stands
                        </span>
                        <span wire:loading>
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                            Génération...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL MODIFICATION STAND --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-pen"></i>
                    Modifier le stand
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl transition">
                    &times;
                </button>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-2 gap-5">

                    {{-- Numéro --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Numéro du stand</label>
                        <input wire:model="numero_stand" type="number"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-gray-50"
                            readonly>
                    </div>

                    {{-- Superficie --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Superficie (m²) *</label>
                        <input wire:model="superficie" type="number" step="0.01"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: 25">
                        @error('superficie') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Événement --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Événement</label>
                        <select wire:model="id_evenement"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-gray-50"
                            disabled>
                            <option value="">-- Choisir un événement --</option>
                            @foreach($evenements as $evenement)
                            <option value="{{ $evenement->id }}">{{ $evenement->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Entreprise --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Entreprise
                            <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <select wire:model="id_entreprise"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            <option value="">-- Disponible --</option>
                            @foreach($entreprises as $entreprise)
                            <option value="{{ $entreprise->id }}">{{ $entreprise->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Standing --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Standing *</label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach($standings as $s)
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="standing" value="{{ $s }}" class="hidden peer">
                                <div class="border rounded-xl p-3 text-center transition hover:bg-gray-50
                                    {{ $standing === $s ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                                    @if($s === 'vip')
                                        <i class="fa-solid fa-star text-yellow-500 text-lg mb-1 block"></i>
                                    @elseif($s === 'premium')
                                        <i class="fa-solid fa-gem text-blue-500 text-lg mb-1 block"></i>
                                    @else
                                        <i class="fa-solid fa-store text-green-600 text-lg mb-1 block"></i>
                                    @endif
                                    <span class="text-sm font-medium text-gray-700">{{ ucfirst($s) }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="closeModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="sauvegarder"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2"
                        style="background-color: #C8102E;">
                        <span wire:loading.remove>
                            <i class="fa-solid fa-pen mr-1"></i>
                            Modifier
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