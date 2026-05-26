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
            <h3 class="text-xl font-bold text-gray-700">Gestion des Accès</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #6b2d6b;">
                {{ $utilisateurs->count() }} utilisateur(s)
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Nouvel accès
        </button>
    </div>

    {{-- Info --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-4 mb-6 text-sm text-blue-700 flex items-start gap-2">
        <i class="fa-solid fa-circle-info mt-0.5"></i>
        En tant que Superviseur, vous pouvez créer et gérer les accès pour les
        <strong>CDD</strong>, les <strong>Entreprises</strong> et les <strong>Participants</strong>.
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4"
            style="border-color: #2d5a8e;">
            <i class="fa-solid fa-user-tie text-xl" style="color: #2d5a8e;"></i>
            <div>
                <p class="text-xs text-gray-500">CDD</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $utilisateurs->filter(fn($u) => $u->getRoleNames()->first() === 'cdd')->count() }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4"
            style="border-color: #007A3D;">
            <i class="fa-solid fa-building text-xl" style="color: #007A3D;"></i>
            <div>
                <p class="text-xs text-gray-500">Entreprises</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $utilisateurs->filter(fn($u) => $u->getRoleNames()->first() === 'entreprise')->count() }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4"
            style="border-color: #8b5cf6;">
            <i class="fa-solid fa-users text-xl" style="color: #8b5cf6;"></i>
            <div>
                <p class="text-xs text-gray-500">Participants</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $utilisateurs->filter(fn($u) => $u->getRoleNames()->first() === 'participant')->count() }}
                </p>
            </div>
        </div>
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
                        'cdd'         => '#2d5a8e',
                        'entreprise'  => '#007A3D',
                        'participant' => '#8b5cf6',
                    ];
                    $color = $colors[$roleUser] ?? '#6b7280';
                @endphp
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                style="background-color: {{ $color }}">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        <i class="fa-solid fa-envelope text-gray-400 mr-1"></i>
                        {{ $user->email }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium capitalize"
                            style="background-color: {{ $color }}">
                            {{ $roleUser }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button wire:click="modifier({{ $user->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700 flex items-center gap-1">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </button>
                            <button wire:click="supprimer({{ $user->id }})"
                                wire:confirm="Voulez-vous vraiment supprimer cet utilisateur ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-users-gear text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun utilisateur créé</p>
                        <button wire:click="openModal"
                            class="mt-3 px-5 py-2 rounded-xl text-white text-sm font-medium"
                            style="background-color: #C8102E;">
                            Créer le premier accès
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
                style="background: linear-gradient(135deg, #4a1942, #6b2d6b);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-plus"></i>
                    {{ $isEditing ? 'Modifier l\'accès' : 'Nouvel accès' }}
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

                    @if($role === 'cdd')
<p class="text-xs text-blue-600 mt-1 flex items-center gap-1">
    <i class="fa-solid fa-circle-info"></i>
    Pour un CDD, incluez la région dans le nom.
    Ex: "CDD Bobo-Dioulasso", "CDD Ouagadougou"
</p>
@endif

                    {{-- Email --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Email *</label>
                        <input wire:model="email" type="email"
    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
    placeholder="Ex: jean@email.com">
                        @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Rôle --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">Rôle *</label>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button"
                                wire:click="$set('role', 'cdd')"
                                class="border rounded-xl p-3 transition text-center
                                    {{ $role === 'cdd' ? 'border-red-400 bg-red-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                <div class="w-3 h-3 rounded-full mx-auto mb-1" style="background-color: #2d5a8e;"></div>
                                <span class="text-sm font-medium text-gray-700">CDD</span>
                            </button>
                            <button type="button"
                                wire:click="$set('role', 'entreprise')"
                                class="border rounded-xl p-3 transition text-center
                                    {{ $role === 'entreprise' ? 'border-red-400 bg-red-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                <div class="w-3 h-3 rounded-full mx-auto mb-1" style="background-color: #007A3D;"></div>
                                <span class="text-sm font-medium text-gray-700">Entreprise</span>
                            </button>
                            <button type="button"
                                wire:click="$set('role', 'participant')"
                                class="border rounded-xl p-3 transition text-center
                                    {{ $role === 'participant' ? 'border-red-400 bg-red-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                <div class="w-3 h-3 rounded-full mx-auto mb-1" style="background-color: #8b5cf6;"></div>
                                <span class="text-sm font-medium text-gray-700">Participant</span>
                            </button>
                        </div>
                        @error('role') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Mot de passe (création seulement) --}}
                    @if(!$isEditing)
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Mot de passe *</label>
                        <input wire:model="password" type="password"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Minimum 8 caractères">
                        @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Confirmer *</label>
                        <input wire:model="password_confirmation" type="password"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Répéter le mot de passe">
                    </div>
                    @endif

                </div>

                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="closeModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="sauvegarder"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm"
                        style="background-color: #C8102E;">
                        <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-floppy-disk' }} mr-1"></i>
                        {{ $isEditing ? 'Modifier' : 'Créer l\'accès' }}
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

            {{-- Header --}}
            <div class="px-8 py-5 rounded-t-2xl text-white text-center"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3"
                    style="background-color: rgba(255,255,255,0.2);">
                    <i class="fa-solid fa-circle-check text-4xl text-white"></i>
                </div>
                <h3 class="text-xl font-bold">Compte créé avec succès !</h3>
                <p class="text-green-200 text-sm mt-1">
                    Communiquez ces informations à l'utilisateur
                </p>
            </div>

            {{-- Corps --}}
            <div class="p-8">

                {{-- Avertissement --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-5 text-xs text-yellow-700 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
                    <span>
                        Ces informations ne seront plus affichées après fermeture.
                        Notez-les ou envoyez-les maintenant à l'utilisateur.
                    </span>
                </div>

                {{-- Infos --}}
                <div class="space-y-3">

                    {{-- Nom --}}
                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                        <span class="text-sm text-gray-500 flex items-center gap-2">
                            <i class="fa-solid fa-user text-gray-400"></i>
                            Nom
                        </span>
                        <span class="font-semibold text-gray-800">
                            {{ $identifiants['name'] ?? '' }}
                        </span>
                    </div>

                    {{-- Rôle --}}
                    @php
                    $colors = [
                        'cdd'         => '#2d5a8e',
                        'entreprise'  => '#007A3D',
                        'participant' => '#8b5cf6',
                    ];
                    $color = $colors[$identifiants['role'] ?? ''] ?? '#6b7280';
                    @endphp
                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                        <span class="text-sm text-gray-500 flex items-center gap-2">
                            <i class="fa-solid fa-shield text-gray-400"></i>
                            Rôle
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium capitalize"
                            style="background-color: {{ $color }}">
                            {{ $identifiants['role'] ?? '' }}
                        </span>
                    </div>

                    {{-- Email --}}
                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                        <span class="text-sm text-gray-500 flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-gray-400"></i>
                            Email
                        </span>
                        <span class="font-semibold text-gray-800 text-sm">
                            {{ $identifiants['email'] ?? '' }}
                        </span>
                    </div>

                    {{-- Mot de passe --}}
                    <div class="flex items-center justify-between bg-red-50 rounded-xl px-4 py-3 border border-red-200">
                        <span class="text-sm text-red-500 flex items-center gap-2">
                            <i class="fa-solid fa-key text-red-400"></i>
                            Mot de passe
                        </span>
                        <span class="font-bold text-red-700 font-mono">
                            {{ $identifiants['password'] ?? '' }}
                        </span>
                    </div>

                    {{-- Lien connexion --}}
                    <div class="flex items-center justify-between bg-blue-50 rounded-xl px-4 py-3">
                        <span class="text-sm text-blue-500 flex items-center gap-2">
                            <i class="fa-solid fa-link text-blue-400"></i>
                            Lien
                        </span>
                        <span class="font-semibold text-blue-700 text-xs">
                            {{ url('/login') }}
                        </span>
                    </div>

                </div>

                {{-- Boutons --}}
                <div class="flex gap-3 mt-6">
                    <button
                        onclick="
                            navigator.clipboard.writeText(
                                'Nom: {{ $identifiants['name'] ?? '' }}\n' +
                                'Email: {{ $identifiants['email'] ?? '' }}\n' +
                                'Mot de passe: {{ $identifiants['password'] ?? '' }}\n' +
                                'Lien: {{ url('/login') }}'
                            );
                            this.innerHTML = '<i class=\'fa-solid fa-check mr-1\'></i> Copié !';
                            setTimeout(() => this.innerHTML = '<i class=\'fa-solid fa-copy mr-1\'></i> Copier', 2000);
                        "
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium flex items-center justify-center gap-1">
                        <i class="fa-solid fa-copy mr-1"></i> Copier
                    </button>
                    <button wire:click="closeIdentifiantsModal"
                        class="flex-1 px-4 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm flex items-center justify-center gap-1"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-check mr-1"></i> J'ai noté
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

</div>