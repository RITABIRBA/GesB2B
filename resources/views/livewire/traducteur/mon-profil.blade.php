<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow p-8 max-w-2xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-language" style="color: #007A3D;"></i>
                Mon Profil Traducteur
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
        <div class="grid grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Nom</p>
                <p class="font-semibold text-gray-800">{{ $nom }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Prénom</p>
                <p class="font-semibold text-gray-800">{{ $prenom }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Téléphone</p>
                <p class="font-semibold text-gray-800">{{ $telephone }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-1">Email</p>
                <p class="font-semibold text-gray-800">{{ $email ?: '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 col-span-2">
                <p class="text-xs text-gray-400 mb-1">Langue</p>
                <span class="px-3 py-1 rounded-full text-sm text-white font-medium"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-language mr-1"></i>
                    {{ $langue }}
                </span>
            </div>
        </div>
        @else
        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom *</label>
                <input wire:model="nom" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Prénom *</label>
                <input wire:model="prenom" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                @error('prenom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                <input wire:model="telephone" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                @error('telephone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Email</label>
                <input wire:model="email" type="email"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="col-span-2">
                <label class="block text-gray-600 text-sm font-medium mb-2">Langue *</label>
                <div class="grid grid-cols-5 gap-2">
                    @foreach($langues as $l)
                    <label class="cursor-pointer">
                        <input type="radio" wire:model="langue" value="{{ $l }}" class="hidden peer">
                        <div class="border rounded-xl p-2 text-center transition text-xs
                            {{ $langue === $l
                                ? 'border-red-400 bg-red-50 text-red-700 font-medium'
                                : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                            {{ $l }}
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('langue') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button wire:click="annuler"
                class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                Annuler
            </button>
            <button wire:click="sauvegarder"
                class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm"
                style="background-color: #C8102E;">
                Enregistrer
            </button>
        </div>
        @endif
    </div>
</div>