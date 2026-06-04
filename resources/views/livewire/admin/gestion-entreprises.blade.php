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
            <h3 class="text-xl font-bold text-gray-700">Liste des entreprises</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #007A3D;">
                {{ $entreprises->count() }} entreprise(s)
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Nouvelle entreprise
        </button>
    </div>

    {{-- Recherche --}}
    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher par nom, pays, ville ou IFU..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Nom</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">IFU</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Secteur</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Pays</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Ville</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Statut</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entreprises as $entreprise)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $entreprise->nom }}</td>
                    <td class="px-6 py-4">
                        @if($entreprise->ifu)
                        <span class="font-mono text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded-lg">
                            {{ $entreprise->ifu }}
                        </span>
                        @else
                        <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-3 py-1 rounded-full font-medium bg-blue-100 text-blue-700">
                            {{ $entreprise->secteur_activite }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <i class="fa-solid fa-flag text-gray-400 mr-1"></i>
                        {{ $entreprise->pays }}
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <i class="fa-solid fa-location-dot text-gray-400 mr-1"></i>
                        {{ $entreprise->ville }}
                    </td>
                    <td class="px-6 py-4">
                        @if($entreprise->statut_validation == 'valide')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium" style="background-color: #007A3D;">
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
                    <td class="px-6 py-4">
                        <div class="flex gap-2 flex-wrap">
                            @if($entreprise->statut_validation == 'en_attente')
                            <button wire:click="valider({{ $entreprise->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90 flex items-center gap-1"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-check"></i> Valider
                            </button>
                            <button wire:click="rejeter({{ $entreprise->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                                <i class="fa-solid fa-xmark"></i> Rejeter
                            </button>
                            @endif
                            <button wire:click="modifier({{ $entreprise->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700 flex items-center gap-1">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </button>
                            <button wire:click="supprimer({{ $entreprise->id }})"
                                wire:confirm="Voulez-vous vraiment supprimer cette entreprise ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-gray-500 transition hover:bg-gray-600 flex items-center gap-1">
                                <i class="fa-solid fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-building text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucune entreprise pour le moment</p>
                        <button wire:click="openModal"
                            class="mt-3 px-5 py-2 rounded-xl text-white text-sm font-medium"
                            style="background-color: #C8102E;">
                            Ajouter la première entreprise
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
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-plus' }}"></i>
                    {{ $isEditing ? 'Modifier l\'entreprise' : 'Nouvelle entreprise' }}
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl transition">
                    &times;
                </button>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-2 gap-5">

                    {{-- Nom --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Nom de l'entreprise *
                        </label>
                        <input wire:model="nom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: Société Burkinabè de Commerce">
                        @error('nom')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- IFU --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Numéro IFU
                            <span class="text-gray-400 font-normal">(optionnel — unique par entreprise)</span>
                        </label>
                        <input wire:model="ifu" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm font-mono"
                            placeholder="Ex: BF123456789">
                        @error('ifu')
                            <span class="text-red-500 text-xs mt-1">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i>
                                {{ $message }}
                            </span>
                        @enderror
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            Les participants qui saisissent cet IFU seront automatiquement liés à cette entreprise.
                        </p>
                    </div>

                    {{-- Secteur --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Secteur d'activité *</label>
                        <select wire:model="secteur_activite"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($secteurs as $secteur)
                            <option value="{{ $secteur }}">{{ $secteur }}</option>
                            @endforeach
                        </select>
                        @error('secteur_activite')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Sous-secteur --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Sous-secteur <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <input wire:model="sous_secteur" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: Céréales, BTP...">
                    </div>

                    {{-- Pays --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Pays *</label>
                        <select wire:model="pays"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($pays_liste as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                            @endforeach
                        </select>
                        @error('pays')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Ville --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Ville *</label>
                        <input wire:model="ville" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: Ouagadougou">
                        @error('ville')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Téléphone --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                        <input wire:model="telephone" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: +226 70 00 00 00">
                        @error('telephone')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Email <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <input wire:model="email" type="email"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: contact@entreprise.com">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                {{-- Boutons --}}
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
                            <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-floppy-disk' }} mr-1"></i>
                            {{ $isEditing ? 'Modifier' : 'Enregistrer' }}
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