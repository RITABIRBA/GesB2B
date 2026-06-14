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

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Chefs de Délégation</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #2d5a8e;">
                {{ $cdds->count() }} CDD
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Nouveau CDD
        </button>
    </div>

    {{-- Info --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-4 mb-6 text-sm text-blue-700 flex items-start gap-2">
        <i class="fa-solid fa-circle-info mt-0.5"></i>
        Les Chefs de Délégation peuvent inscrire des participants et des représentants
        d'entreprise depuis leur espace dédié.
    </div>

    {{-- Recherche --}}
    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher par nom ou email..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none text-sm">
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Chef de Délégation</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Email</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Nb. participants</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Créé le</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cdds as $cdd)
                @php
                    $participantCdd = \App\Models\Participant::where('email', $cdd->email)->first();
                    $nbParticipants = $participantCdd
                        ? \App\Models\Participant::where('id_chef_delegation', $participantCdd->id)->count()
                        : 0;
                @endphp
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                style="background-color: #2d5a8e;">
                                {{ strtoupper(substr($cdd->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $cdd->name }}</p>
                                <span class="text-xs px-2 py-0.5 rounded-full text-white font-medium"
                                    style="background-color: #2d5a8e;">
                                    CDD
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        <i class="fa-solid fa-envelope text-gray-400 mr-1"></i>
                        {{ $cdd->email }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-gray-700">{{ $nbParticipants }}</span>
                        <span class="text-xs text-gray-400 ml-1">participant(s)</span>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        {{ $cdd->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button wire:click="modifier({{ $cdd->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="supprimer({{ $cdd->id }})"
                                wire:confirm="Supprimer ce Chef de Délégation ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-user-tie text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun Chef de Délégation</p>
                        <button wire:click="openModal"
                            class="mt-3 px-5 py-2 rounded-xl text-white text-sm font-medium"
                            style="background-color: #C8102E;">
                            Créer le premier CDD
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL CRÉATION / MODIFICATION --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #2d5a8e, #1e3f6e);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-tie"></i>
                    {{ $isEditing ? 'Modifier le CDD' : 'Nouveau Chef de Délégation' }}
                </h3>
                <button wire:click="closeModal"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8 space-y-5">

                @if(!$isEditing)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-700 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    Le compte créé aura automatiquement le rôle <strong>CDD</strong>.
                    Exemple : "CDD Bobo-Dioulasso"
                </div>
                @endif

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom complet *</label>
                    <input wire:model="name" type="text"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm"
                        placeholder="Ex: CDD Bobo-Dioulasso">
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Email *</label>
                    <input wire:model="email" type="email"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm"
                        placeholder="Ex: cdd.bobo@ccibf.bf">
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                @if(!$isEditing)
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Mot de passe *</label>
                    <input wire:model="password" type="password"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm"
                        placeholder="Minimum 8 caractères">
                    @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Confirmer *</label>
                    <input wire:model="password_confirmation" type="password"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm"
                        placeholder="Répéter le mot de passe">
                </div>
                @endif

                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="closeModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="sauvegarder"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #2d5a8e;">
                        <span wire:loading.remove>
                            <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-floppy-disk' }} mr-1"></i>
                            {{ $isEditing ? 'Modifier' : 'Créer le CDD' }}
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

    {{-- MODAL IDENTIFIANTS --}}
    @if($showIdentifiantsModal && count($identifiants) > 0)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-8 py-5 rounded-t-2xl text-white text-center"
                style="background: linear-gradient(135deg, #2d5a8e, #1e3f6e);">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3"
                    style="background-color: rgba(255,255,255,0.2);">
                    <i class="fa-solid fa-user-tie text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold">CDD créé avec succès !</h3>
                <p class="text-blue-200 text-sm mt-1">Communiquez ces informations au CDD</p>
            </div>
            <div class="p-8">
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-5 text-xs text-yellow-700 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                    Ces informations ne seront plus affichées après fermeture.
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                        <span class="text-sm text-gray-500 flex items-center gap-2">
                            <i class="fa-solid fa-user text-gray-400"></i> Nom
                        </span>
                        <span class="font-semibold text-gray-800">{{ $identifiants['name'] ?? '' }}</span>
                    </div>
                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                        <span class="text-sm text-gray-500 flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-gray-400"></i> Email
                        </span>
                        <span class="font-semibold text-gray-800 text-sm">{{ $identifiants['email'] ?? '' }}</span>
                    </div>
                    <div class="flex items-center justify-between bg-red-50 rounded-xl px-4 py-3 border border-red-200">
                        <span class="text-sm text-red-500 flex items-center gap-2">
                            <i class="fa-solid fa-key text-red-400"></i> Mot de passe
                        </span>
                        <span class="font-bold text-red-700 font-mono">{{ $identifiants['password'] ?? '' }}</span>
                    </div>
                    <div class="flex items-center justify-between bg-blue-50 rounded-xl px-4 py-3">
                        <span class="text-sm text-blue-500 flex items-center gap-2">
                            <i class="fa-solid fa-link text-blue-400"></i> Lien de connexion
                        </span>
                        <span class="font-semibold text-blue-700 text-xs">{{ url('/login') }}</span>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button
                        onclick="navigator.clipboard.writeText('Nom: {{ $identifiants['name'] ?? '' }}\nEmail: {{ $identifiants['email'] ?? '' }}\nMot de passe: {{ $identifiants['password'] ?? '' }}\nLien: {{ url('/login') }}'); this.innerHTML='<i class=\'fa-solid fa-check mr-1\'></i> Copié !'; setTimeout(() => this.innerHTML='<i class=\'fa-solid fa-copy mr-1\'></i> Copier', 2000);"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium flex items-center justify-center gap-1">
                        <i class="fa-solid fa-copy mr-1"></i> Copier
                    </button>
                    <button wire:click="closeIdentifiantsModal"
                        class="flex-1 px-4 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm flex items-center justify-center gap-1"
                        style="background-color: #2d5a8e;">
                        <i class="fa-solid fa-check mr-1"></i> J'ai noté
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>