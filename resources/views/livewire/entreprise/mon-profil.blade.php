<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(!$entreprise_id)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center text-yellow-700">
        <i class="fa-solid fa-triangle-exclamation text-4xl mb-3 block text-yellow-400"></i>
        <p class="font-bold text-lg">Entreprise non trouvée</p>
        <p class="text-sm mt-1">Votre compte n'est pas lié à une entreprise.</p>
    </div>
    @else

    {{-- Carte responsable --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-2xl font-bold flex-shrink-0"
                style="background-color: #007A3D;">
                {{ strtoupper(substr($nom_responsable ?: 'R', 0, 1)) }}
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Responsable de l'entreprise</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $nom_responsable }} {{ $prenom_responsable }}
                </p>
                @if($fonction_responsable)
                <p class="text-sm text-gray-500">
                    <i class="fa-solid fa-briefcase mr-1"></i>
                    {{ $fonction_responsable }}
                </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Profil entreprise --}}
    <div class="bg-white rounded-xl shadow p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-building" style="color: #C8102E;"></i>
                Mon Profil Entreprise
            </h3>
            @if(!$isEditing)
            <button wire:click="activer"
                class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90"
                style="background-color: #007A3D;">
                <i class="fa-solid fa-pen"></i> Modifier
            </button>
            @endif
        </div>

        @if(!$isEditing)
        {{-- Mode affichage --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-xl p-4 col-span-2">
                <p class="text-xs text-gray-400 mb-1">Nom de l'entreprise</p>
                <p class="font-semibold text-gray-800 text-lg">{{ $nom }}</p>
            </div>

            @if($ifu)
            <div class="bg-gray-50 rounded-xl p-4 col-span-2">
                <p class="text-xs text-gray-400 mb-1">Numéro IFU</p>
                <p class="font-mono font-bold text-gray-800">{{ $ifu }}</p>
            </div>
            @endif

            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Secteur d'activité</p>
                <p class="font-semibold text-gray-800">{{ $secteur_activite ?: '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Sous-secteur</p>
                <p class="font-semibold text-gray-800">{{ $sous_secteur ?: '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Pays</p>
                <p class="font-semibold text-gray-800">{{ $pays ?: '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Ville</p>
                <p class="font-semibold text-gray-800">{{ $ville ?: '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 col-span-2">
                <p class="text-xs text-gray-400 mb-1">Contact</p>
                <p class="font-semibold text-gray-800">{{ $contact ?: '-' }}</p>
            </div>
        </div>

        @else
        {{-- Mode édition --}}
        <div class="grid grid-cols-2 gap-5">

            {{-- Infos responsable --}}
            <div class="col-span-2">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-2">
                    <p class="text-xs font-bold text-blue-700 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-user-tie"></i>
                        Informations du responsable
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1">Nom</label>
                            <input wire:model="nom_responsable" type="text"
                                class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm"
                                placeholder="Nom du responsable">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1">Prénom</label>
                            <input wire:model="prenom_responsable" type="text"
                                class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm"
                                placeholder="Prénom du responsable">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-gray-600 text-xs font-medium mb-1">Fonction</label>
                            <input wire:model="fonction_responsable" type="text"
                                class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm"
                                placeholder="Ex: Directeur Général, PDG...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Nom entreprise --}}
            <div class="col-span-2">
                <label class="block text-gray-600 text-sm font-medium mb-1.5">
                    Nom de l'entreprise *
                </label>
                <input wire:model="nom" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
                @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- IFU --}}
            <div class="col-span-2">
                <label class="block text-gray-600 text-sm font-medium mb-1.5">
                    Numéro IFU
                    <span class="text-gray-400 font-normal">(optionnel)</span>
                </label>
                <input wire:model="ifu" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm font-mono"
                    placeholder="Ex: BF123456789">
            </div>

            {{-- Secteur --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Secteur *</label>
                <select wire:model="secteur_activite"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
                    <option value="">-- Choisir --</option>
                    @foreach($secteurs as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
                @error('secteur_activite') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Sous-secteur --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Sous-secteur</label>
                <input wire:model="sous_secteur" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
            </div>

            {{-- Pays --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Pays *</label>
                <select wire:model="pays"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
                    <option value="">-- Choisir --</option>
                    @foreach($pays_liste as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                    @endforeach
                </select>
                @error('pays') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Ville --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Ville *</label>
                <input wire:model="ville" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
                @error('ville') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Contact --}}
            <div class="col-span-2">
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Contact *</label>
                <input wire:model="contact" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
                @error('contact') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

        </div>

        <div class="flex justify-end gap-3 mt-6">
            <button wire:click="annuler"
                class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                <i class="fa-solid fa-xmark mr-1"></i> Annuler
            </button>
            <button wire:click="sauvegarder"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-70 cursor-not-allowed"
                class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm flex items-center gap-2"
                style="background-color: #007A3D;">
                <span wire:loading.remove>
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Enregistrer
                </span>
                <span wire:loading>
                    <i class="fa-solid fa-spinner fa-spin mr-1"></i> Enregistrement...
                </span>
            </button>
        </div>
        @endif
    </div>

    @endif
</div>