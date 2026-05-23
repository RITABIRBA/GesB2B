<div>

    {{-- Messages --}}
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
            <h3 class="text-xl font-bold text-gray-700">Gestion des Utilisateurs</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $utilisateurs->count() }} utilisateur(s)
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Nouvel utilisateur
        </button>
    </div>

    {{-- Statistiques par rôle --}}
    <div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-6">
        @foreach($roles as $role)
        @php
            $colors = [
                'admin'       => '#C8102E',
                'superviseur' => '#f59e0b',
                'cdd'         => '#2d5a8e',
                'entreprise'  => '#007A3D',
                'participant' => '#8b5cf6',
                'traducteur'  => '#06b6d4',
            ];
            $color = $colors[$role->name] ?? '#6b7280';
        @endphp
        <div class="bg-white rounded-xl shadow p-3 text-center border-t-4"
            style="border-color: {{ $color }}">
            <p class="text-lg font-bold text-gray-800">
                {{ $utilisateurs->filter(fn($u) => $u->getRoleNames()->first() === $role->name)->count() }}
            </p>
            <p class="text-xs text-gray-500 capitalize">{{ $role->name }}</p>
        </div>
        @endforeach
    </div>

    {{-- Recherche --}}
    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher par nom ou email..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Utilisateur</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Email</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Rôle</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Créé le</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($utilisateurs as $user)
                @php
                    $roleUser = $user->getRoleNames()->first() ?? 'aucun';
                    $colors = [
                        'admin'       => '#C8102E',
                        'superviseur' => '#f59e0b',
                        'cdd'         => '#2d5a8e',
                        'entreprise'  => '#007A3D',
                        'participant' => '#8b5cf6',
                        'traducteur'  => '#06b6d4',
                    ];
                    $color = $colors[$roleUser] ?? '#6b7280';
                @endphp
                <tr class="border-b hover:bg-gray-50 transition">

                    {{-- Utilisateur --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                style="background-color: {{ $color }}">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                                @if($user->id === auth()->id())
                                <span class="text-xs text-green-600 font-medium">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Vous
                                </span>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Email --}}
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        <i class="fa-solid fa-envelope text-gray-400 mr-1"></i>
                        {{ $user->email }}
                    </td>

                    {{-- Rôle --}}
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium capitalize"
                            style="background-color: {{ $color }}">
                            {{ $roleUser }}
                        </span>
                    </td>

                    {{-- Date --}}
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        <i class="fa-regular fa-clock text-gray-400 mr-1"></i>
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>

                    {{-- Actions --}}
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button wire:click="modifier({{ $user->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700 flex items-center gap-1">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </button>
                            <button wire:click="ouvrirModalPassword({{ $user->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-yellow-500 transition hover:bg-yellow-600 flex items-center gap-1">
                                <i class="fa-solid fa-key"></i> MDP
                            </button>
                            @if($user->id !== auth()->id())
                            <button wire:click="supprimer({{ $user->id }})"
                                wire:confirm="Voulez-vous vraiment supprimer cet utilisateur ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-users-gear text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun utilisateur trouvé</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL — CRÉATION / MODIFICATION --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-plus"></i>
                    {{ $isEditing ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' }}
                </h3>
                <button wire:click="closeModal"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 gap-5">

                    {{-- Nom --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom complet *</label>
                        <input wire:model="name" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: Jean Dupont">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Email *</label>
                        <input wire:model="email" type="email"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: jean@cci.bf">
                        @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Rôle --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">Rôle *</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($roles as $r)
                            @php
                                $colors = [
                                    'admin'       => '#C8102E',
                                    'superviseur' => '#f59e0b',
                                    'cdd'         => '#2d5a8e',
                                    'entreprise'  => '#007A3D',
                                    'participant' => '#8b5cf6',
                                    'traducteur'  => '#06b6d4',
                                ];
                                $c = $colors[$r->name] ?? '#6b7280';
                            @endphp
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="role"
                                    value="{{ $r->name }}" class="hidden peer">
                                <div class="border rounded-xl p-3 transition hover:bg-gray-50 flex items-center gap-2
                                    {{ $role === $r->name ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                                    <div class="w-3 h-3 rounded-full flex-shrink-0"
                                        style="background-color: {{ $c }}"></div>
                                    <span class="text-sm font-medium text-gray-700 capitalize">{{ $r->name }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('role') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Mot de passe (seulement en création) --}}
                    @if(!$isEditing)
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Mot de passe *</label>
                        <input wire:model="password" type="password"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Minimum 8 caractères">
                        @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Confirmer le mot de passe *</label>
                        <input wire:model="password_confirmation" type="password"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Répéter le mot de passe">
                    </div>
                    @endif

                </div>

                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="closeModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="sauvegarder"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #C8102E;">
                        <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-floppy-disk' }} mr-1"></i>
                        {{ $isEditing ? 'Modifier' : 'Enregistrer' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL — CHANGEMENT MOT DE PASSE --}}
    @if($showPasswordModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-key"></i>
                    Changer le mot de passe
                </h3>
                <button wire:click="fermerModalPassword"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nouveau mot de passe *</label>
                        <input wire:model="new_password" type="password"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-yellow-300 text-sm"
                            placeholder="Minimum 8 caractères">
                        @error('new_password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Confirmer *</label>
                        <input wire:model="new_password_confirmation" type="password"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-yellow-300 text-sm"
                            placeholder="Répéter le mot de passe">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="fermerModalPassword"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="changerMotDePasse"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow bg-yellow-500">
                        <i class="fa-solid fa-key mr-1"></i> Changer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>