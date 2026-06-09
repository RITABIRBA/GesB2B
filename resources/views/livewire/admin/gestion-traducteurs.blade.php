<div>

    {{-- Message succès --}}
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

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Liste des traducteurs</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #007A3D;">
                {{ $traducteurs->count() }} traducteur(s)
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Nouveau traducteur
        </button>
    </div>

    {{-- Recherche --}}
    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher par nom, prénom ou langue..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Traducteur</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Téléphone</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Email</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Langue</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Compte</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($traducteurs as $traducteur)
                <tr class="border-b hover:bg-gray-50 transition">

                    {{-- Nom & Prénom --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                style="background-color: #007A3D;">
                                {{ strtoupper(substr($traducteur->prenom, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ $traducteur->nom }} {{ $traducteur->prenom }}
                                </p>
                                <p class="text-xs text-gray-400">Traducteur</p>
                            </div>
                        </div>
                    </td>

                    {{-- Téléphone --}}
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        <i class="fa-solid fa-phone text-gray-400 mr-1"></i>
                        {{ $traducteur->telephone }}
                    </td>

                    {{-- Email --}}
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        @if($traducteur->email)
                            <i class="fa-solid fa-envelope text-gray-400 mr-1"></i>
                            {{ $traducteur->email }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>

                    {{-- Langue --}}
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium"
                            style="background-color: #007A3D;">
                            <i class="fa-solid fa-language mr-1"></i>
                            {{ $traducteur->langue }}
                        </span>
                    </td>

                    {{-- Statut compte --}}
                    <td class="px-6 py-4">
                        @if($traducteur->user_id)
                        <span class="text-xs text-green-600 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> Compte actif
                        </span>
                        @else
                        <span class="text-xs text-red-400 flex items-center gap-1">
                            <i class="fa-solid fa-circle-xmark"></i> Pas de compte
                        </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            {{-- ← Bouton voir compte --}}
                            @if($traducteur->user_id)
                            <button wire:click="voirCompte({{ $traducteur->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-gray-500 transition hover:bg-gray-600"
                                title="Voir le compte">
                                <i class="fa-solid fa-key"></i>
                            </button>
                            @endif
                            <button wire:click="modifier({{ $traducteur->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700 flex items-center gap-1">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="supprimer({{ $traducteur->id }})"
                                wire:confirm="Voulez-vous vraiment supprimer ce traducteur ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-language text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun traducteur pour le moment</p>
                        <button wire:click="openModal"
                            class="mt-3 px-5 py-2 rounded-xl text-white text-sm font-medium"
                            style="background-color: #C8102E;">
                            Ajouter le premier traducteur
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL COMPTE CRÉÉ --}}
    @if($showModalCompte)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-8 py-6 rounded-t-2xl text-white text-center"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3"
                    style="background-color: rgba(255,255,255,0.2);">
                    <i class="fa-solid fa-user-check text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold">Traducteur créé !</h3>
                <p class="text-green-200 text-sm mt-1">Compte d'accès généré automatiquement</p>
            </div>
            <div class="p-8">
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-4 text-xs text-yellow-700 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                    Notez ces informations ! Elles ne seront plus affichées après fermeture.
                </div>
                <div class="space-y-3">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-500 mb-1">
                            <i class="fa-solid fa-envelope mr-1"></i> Email de connexion
                        </p>
                        <p class="font-semibold text-gray-800">{{ $compte_email }}</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <p class="text-xs text-blue-500 mb-1">
                            <i class="fa-solid fa-lock mr-1"></i> Mot de passe temporaire
                        </p>
                        <p class="font-mono font-bold text-xl text-blue-700 tracking-widest">
                            {{ $compte_password }}
                        </p>
                        <p class="text-xs text-blue-400 mt-1">
                            Le traducteur peut changer ce mot de passe après connexion
                        </p>
                    </div>
                </div>
                <button wire:click="closeModalCompte"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow mt-6 flex items-center justify-center gap-2"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-check"></i>
                    J'ai noté les informations
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ← MODAL VOIR COMPTE --}}
    @if($showModalVoirCompte)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-8 py-6 rounded-t-2xl text-white text-center"
                style="background: linear-gradient(135deg, #2d5a8e, #1e3f6e);">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3"
                    style="background-color: rgba(255,255,255,0.2);">
                    <i class="fa-solid fa-key text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold">Compte du traducteur</h3>
                <p class="text-blue-200 text-sm mt-1">{{ $voir_compte_nom }}</p>
            </div>
            <div class="p-8">

                {{-- Email --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-3">
                    <p class="text-xs text-gray-500 mb-1">
                        <i class="fa-solid fa-envelope mr-1"></i> Email de connexion
                    </p>
                    <p class="font-semibold text-gray-800">{{ $voir_compte_email }}</p>
                </div>

                {{-- Mot de passe réinitialisé --}}
                @if($nouveau_mot_de_passe)
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-3">
                    <p class="text-xs text-green-600 mb-1">
                        <i class="fa-solid fa-lock mr-1"></i> Nouveau mot de passe
                    </p>
                    <p class="font-mono font-bold text-xl text-green-700 tracking-widest">
                        {{ $nouveau_mot_de_passe }}
                    </p>
                    <p class="text-xs text-green-500 mt-1">
                        Transmettez ce mot de passe au traducteur
                    </p>
                </div>
                @else
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-3">
                    <p class="text-xs text-blue-500 mb-1">
                        <i class="fa-solid fa-lock mr-1"></i> Mot de passe
                    </p>
                    <p class="text-xs text-blue-400 italic">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Non visible pour des raisons de sécurité.
                        Utilisez le bouton ci-dessous pour réinitialiser.
                    </p>
                </div>
                @endif

                {{-- Bouton réinitialiser --}}
                <button wire:click="reinitialiserMotDePasse"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow mb-3 flex items-center justify-center gap-2"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-rotate mr-1"></i>
                    Réinitialiser le mot de passe
                </button>

                <button wire:click="closeModalVoirCompte"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow flex items-center justify-center gap-2"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-check"></i>
                    Fermer
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL FORMULAIRE --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-language"></i>
                    {{ $isEditing ? 'Modifier le traducteur' : 'Nouveau traducteur' }}
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl transition">
                    &times;
                </button>
            </div>

            <div class="p-8">

                @if(!$isEditing)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-5 text-xs text-blue-700 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    Un compte de connexion sera automatiquement créé pour ce traducteur.
                </div>
                @endif

                <div class="grid grid-cols-2 gap-5">

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom *</label>
                        <input wire:model="nom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: TRAORE">
                        @error('nom') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Prénom *</label>
                        <input wire:model="prenom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: Albert">
                        @error('prenom') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                        <input wire:model="telephone" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: 70 00 00 00">
                        @error('telephone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Email *
                            @if($isEditing)
                            <span class="text-gray-400 font-normal">(optionnel)</span>
                            @endif
                        </label>
                        <input wire:model="email" type="email"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: traore@email.com">
                        @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-2">Langue *</label>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($langues as $l)
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="langue" value="{{ $l }}" class="hidden peer">
                                <div class="peer-checked:ring-2 peer-checked:ring-red-400 border rounded-xl p-2 text-center transition hover:bg-gray-50 text-xs
                                    {{ $langue === $l ? 'border-red-400 bg-red-50 text-red-700 font-medium' : 'border-gray-200 text-gray-600' }}">
                                    {{ $l }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('langue') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
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