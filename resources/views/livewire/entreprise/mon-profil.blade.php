<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-700">Mon Profil Entreprise</h3>
            @if(!$isEditing)
            <button wire:click="$set('isEditing', true)"
                class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90"
                style="background-color: #007A3D;">
                <i class="fa-solid fa-pen"></i> Modifier
            </button>
            @endif
        </div>

        @if(!$isEditing)
        {{-- Mode affichage --}}
        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Nom de l'entreprise</p>
                <p class="font-semibold text-gray-800 text-lg">{{ $nom }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Secteur d'activité</p>
                <p class="font-semibold text-gray-800">{{ $secteur_activite }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Sous-secteur</p>
                <p class="font-semibold text-gray-800">{{ $sous_secteur ?: '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Pays</p>
                <p class="font-semibold text-gray-800">{{ $pays }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Ville</p>
                <p class="font-semibold text-gray-800">{{ $ville }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Contact</p>
                <p class="font-semibold text-gray-800">{{ $contact }}</p>
            </div>
        </div>
        @else
        {{-- Mode édition --}}
        <div class="grid grid-cols-2 gap-5">
            <div class="col-span-2">
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom *</label>
                <input wire:model="nom" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
                @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
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
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Sous-secteur</label>
                <input wire:model="sous_secteur" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
            </div>
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
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Ville *</label>
                <input wire:model="ville" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
                @error('ville') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="col-span-2">
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Contact *</label>
                <input wire:model="contact" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
                @error('contact') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button wire:click="$set('isEditing', false)"
                class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                <i class="fa-solid fa-xmark mr-1"></i> Annuler
            </button>
            <button wire:click="sauvegarder"
                class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm"
                style="background-color: #007A3D;">
                <i class="fa-solid fa-floppy-disk mr-1"></i> Enregistrer
            </button>
        </div>
        @endif
    </div>
</div>