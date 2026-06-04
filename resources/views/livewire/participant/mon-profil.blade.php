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

    {{-- Carte entreprise actuelle --}}
    @if($entreprise_actuelle)
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                style="background-color: #e6f4ed;">
                <i class="fa-solid fa-building text-xl" style="color: #007A3D;"></i>
            </div>
            <div>
                <p class="font-bold text-gray-800">Entreprise liée</p>
                <p class="text-sm font-semibold" style="color: #007A3D;">
                    {{ $entreprise_actuelle->nom }}
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $entreprise_actuelle->secteur_activite }}
                    — {{ $entreprise_actuelle->pays }}
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Carte participation RDV --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                    style="background-color: {{ $participation_rdv ? '#e6f4ed' : '#f8f9fa' }}">
                    <i class="fa-solid fa-handshake text-xl"
                        style="color: {{ $participation_rdv ? '#007A3D' : '#9ca3af' }}"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-800">Participation aux rendez-vous d'affaire</p>
                    <p class="text-sm text-gray-400 mt-0.5">
                        @if($participation_rdv)
                            Vous êtes inclus dans le match-making B2B
                        @else
                            Vous n'êtes pas inclus dans le match-making B2B
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span class="px-3 py-1 rounded-full text-xs font-bold text-white
                    {{ $participation_rdv ? 'bg-green-500' : 'bg-gray-400' }}">
                    {{ $participation_rdv ? 'Actif' : 'Inactif' }}
                </span>
                <button wire:click="toggleParticipationRdv"
                    class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors duration-300 focus:outline-none"
                    style="background-color: {{ $participation_rdv ? '#007A3D' : '#d1d5db' }}">
                    <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300
                        {{ $participation_rdv ? 'translate-x-8' : 'translate-x-1' }}">
                    </span>
                </button>
            </div>
        </div>

        @if($participation_rdv)
        <div class="mt-4 bg-green-50 border border-green-200 rounded-xl p-3 text-xs text-green-700 flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            Vous pouvez émettre des souhaits de RDV et vous serez inclus dans le planning.
        </div>
        @else
        <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-xs text-yellow-700 flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i>
            Activez cette option pour participer aux rendez-vous d'affaire du forum.
        </div>
        @endif
    </div>

    {{-- Infos profil --}}
    <div class="bg-white rounded-xl shadow p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-user" style="color: #C8102E;"></i>
                Mon Profil
            </h3>
            @if(!$isEditing)
            <button wire:click="activer"
                class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90"
                style="background-color: #C8102E;">
                <i class="fa-solid fa-pen"></i> Modifier
            </button>
            @endif
        </div>

        @if(!$isEditing)
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Nom</p>
                <p class="font-semibold text-gray-800">{{ $nom }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Prénom</p>
                <p class="font-semibold text-gray-800">{{ $prenom }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Genre</p>
                <p class="font-semibold text-gray-800">
                    @if($genre == 'homme')
                        <i class="fa-solid fa-mars text-blue-500 mr-1"></i> Homme
                    @elseif($genre == 'femme')
                        <i class="fa-solid fa-venus text-pink-500 mr-1"></i> Femme
                    @else
                        -
                    @endif
                </p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Fonction</p>
                <p class="font-semibold text-gray-800">{{ $fonction ?: '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Email</p>
                <p class="font-semibold text-gray-800">{{ $email }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Téléphone</p>
                <p class="font-semibold text-gray-800">{{ $telephone }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Secteur d'activité</p>
                <p class="font-semibold text-gray-800">{{ $secteur_activite ?: '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Numéro IFU</p>
                <p class="font-semibold text-gray-800">{{ $ifu ?: '-' }}</p>
            </div>
        </div>

        @else

        <div class="grid grid-cols-2 gap-5">

            {{-- Nom --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom *</label>
                <input wire:model="nom" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Prénom --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Prénom *</label>
                <input wire:model="prenom" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                @error('prenom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Genre --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Genre</label>
                <select wire:model="genre"
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                    <option value="">-- Choisir le genre --</option>
                    <option value="homme">Homme</option>
                    <option value="femme">Femme</option>
                </select>
            </div>

            {{-- Fonction --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">
                    Fonction <span class="text-gray-400 font-normal">(optionnel)</span>
                </label>
                <input wire:model="fonction" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                    placeholder="Ex: Directeur Commercial">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Email *</label>
                <input wire:model="email" type="email"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Téléphone --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone</label>
                <input wire:model="telephone" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
            </div>

            {{-- Secteur --}}
            <div class="col-span-2">
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Secteur d'activité</label>
                <input wire:model="secteur_activite" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
            </div>

            {{-- IFU --}}
            <div class="col-span-2">
                <label class="block text-gray-600 text-sm font-medium mb-1.5">
                    Numéro IFU
                    <span class="text-gray-400 font-normal">(optionnel)</span>
                </label>
                <input wire:model.live="ifu" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                    placeholder="Ex: BF123456789">

                {{-- Entreprise trouvée --}}
                @if($entreprise_trouvee)
                <div class="mt-2 bg-green-50 border border-green-300 rounded-xl p-3 flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
                    <div>
                        <p class="text-sm font-bold text-green-700">
                            Entreprise trouvée !
                        </p>
                        <p class="text-xs text-green-600">
                            {{ $entreprise_trouvee->nom }}
                            — {{ $entreprise_trouvee->secteur_activite }}
                            — {{ $entreprise_trouvee->pays }}
                        </p>
                        <p class="text-xs text-green-500 mt-0.5">
                            Vous serez automatiquement lié à cette entreprise.
                        </p>
                    </div>
                </div>
                @elseif($ifu && strlen($ifu) >= 3)
                <div class="mt-2 bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-xs text-yellow-700 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Aucune entreprise trouvée avec ce numéro IFU.
                    Vous resterez participant indépendant.
                </div>
                @endif
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
                style="background-color: #C8102E;">
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
</div>