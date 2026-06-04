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

    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Gestion des Badges</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $badges->count() }} badge(s)
            </span>
        </div>
        <div class="flex gap-3">
            {{-- ← Nouveau bouton génération par type --}}
            <button wire:click="openGenererModal"
                class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow bg-purple-600">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                Générer par type
            </button>
            <button wire:click="genererTousBadges"
                wire:confirm="Générer les badges pour tous les participants sans badge ?"
                class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow bg-blue-600">
                <i class="fa-solid fa-users"></i>
                Générer tous
            </button>
            <button wire:click="openModal"
                class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
                style="background-color: #C8102E;">
                <i class="fa-solid fa-plus"></i>
                Nouveau badge
            </button>
        </div>
    </div>

    {{-- Stats par type --}}
    @if($typesBadges->count() > 0)
    <div class="grid grid-cols-{{ min($typesBadges->count(), 4) }} gap-4 mb-6">
        @foreach($typesBadges as $type)
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4"
            style="border-color: #C8102E;">
            <i class="fa-solid fa-id-badge text-2xl" style="color: #C8102E;"></i>
            <div>
                <p class="text-xs text-gray-500">{{ $type->libelle }}</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $badges->where('id_type_badge', $type->id)->count() }}
                </p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Recherche --}}
    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher par nom ou QR code..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Entreprise</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Type de badge</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">QR Code</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($badges as $badge)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                style="background-color: #C8102E;">
                                {{ strtoupper(substr($badge->participant->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ $badge->participant->nom ?? '-' }}
                                    {{ $badge->participant->prenom ?? '' }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ ucfirst($badge->participant->role ?? '-') }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                            <i class="fa-solid fa-building mr-1"></i>
                            {{ $badge->participant->entreprise->nom ?? 'Indépendant' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $colors = [
                                'VIP'         => 'background-color: #f59e0b',
                                'Exposant'    => 'background-color: #007A3D',
                                'Participant' => 'background-color: #3b82f6',
                                'Visiteur'    => 'background-color: #6b7280',
                            ];
                            $color = $colors[$badge->typeBadge->libelle ?? ''] ?? 'background-color: #C8102E';
                        @endphp
                        <span class="text-xs px-3 py-1 rounded-full text-white font-medium"
                            style="{{ $color }}">
                            <i class="fa-solid fa-id-badge mr-1"></i>
                            {{ $badge->typeBadge->libelle ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-qrcode text-gray-400 text-lg"></i>
                            <span class="font-mono text-xs bg-gray-100 px-3 py-1.5 rounded-lg text-gray-700">
                                {{ $badge->qr_code }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button wire:click="modifier({{ $badge->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700 flex items-center gap-1">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </button>
                            <button wire:click="supprimer({{ $badge->id }})"
                                wire:confirm="Voulez-vous vraiment supprimer ce badge ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                                <i class="fa-solid fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-id-badge text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun badge pour le moment</p>
                        <div class="flex justify-center gap-3 mt-3">
                            <button wire:click="openGenererModal"
                                class="px-5 py-2 rounded-xl text-white text-sm font-medium bg-purple-600">
                                <i class="fa-solid fa-wand-magic-sparkles mr-1"></i>
                                Générer par type
                            </button>
                            <button wire:click="openModal"
                                class="px-5 py-2 rounded-xl text-white text-sm font-medium"
                                style="background-color: #C8102E;">
                                <i class="fa-solid fa-plus mr-1"></i>
                                Créer manuellement
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL — GÉNÉRATION PAR TYPE --}}
    @if($showGenererModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #6d28d9, #4c1d95);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    Générer des badges par type
                </h3>
                <button wire:click="closeGenererModal"
                    class="text-white/70 hover:text-white text-2xl transition">
                    &times;
                </button>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 gap-5">

                    {{-- Type de badge --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">
                            Type de badge *
                        </label>
                        <div class="grid grid-cols-2 gap-3">

                            @foreach(['VIP', 'Exposant', 'Participant', 'Visiteur'] as $type)
                            @php
                                $colors = [
                                    'VIP'         => ['border' => 'border-yellow-400', 'bg' => 'bg-yellow-50', 'icon' => 'text-yellow-500'],
                                    'Exposant'    => ['border' => 'border-green-400',  'bg' => 'bg-green-50',  'icon' => 'text-green-600'],
                                    'Participant' => ['border' => 'border-blue-400',   'bg' => 'bg-blue-50',   'icon' => 'text-blue-600'],
                                    'Visiteur'    => ['border' => 'border-gray-400',   'bg' => 'bg-gray-50',   'icon' => 'text-gray-600'],
                                ];
                                $c = $colors[$type];
                            @endphp
                            <button type="button"
                                wire:click="$set('type_badge_generer', '{{ $type }}')"
                                class="border-2 rounded-xl p-3 text-left transition
                                    {{ $type_badge_generer === $type
                                        ? $c['border'] . ' ' . $c['bg']
                                        : 'border-gray-200 hover:bg-gray-50' }}">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-id-badge {{ $c['icon'] }}"></i>
                                    <p class="font-semibold text-sm text-gray-800">{{ $type }}</p>
                                </div>
                            </button>
                            @endforeach

                        </div>

                        {{-- Type personnalisé --}}
                        <div class="mt-3">
                            <label class="block text-gray-500 text-xs font-medium mb-1.5">
                                Ou saisissez un type personnalisé :
                            </label>
                            <input wire:model="type_badge_generer" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-300 text-sm"
                                placeholder="Ex: Organisateur, Presse...">
                        </div>
                        @error('type_badge_generer')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Événement --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Événement *
                        </label>
                        <select wire:model.live="id_evenement_generer"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-300 text-sm">
                            <option value="">-- Choisir un événement --</option>
                            @foreach($evenements as $evenement)
                            <option value="{{ $evenement->id }}">
                                {{ $evenement->nom }} — {{ $evenement->date_debut }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_evenement_generer')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Liste des participants --}}
                    @if($id_evenement_generer && count($participants_disponibles) > 0)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-gray-600 text-sm font-medium">
                                Participants *
                                <span class="text-gray-400 font-normal">
                                    ({{ count($participants_selectionnes) }}/{{ count($participants_disponibles) }} sélectionnés)
                                </span>
                            </label>
                            <div class="flex gap-2">
                                <button type="button" wire:click="selectionnerTous"
                                    class="text-xs text-blue-600 hover:underline">
                                    Tout sélectionner
                                </button>
                                <span class="text-gray-300">|</span>
                                <button type="button" wire:click="deselectionnerTous"
                                    class="text-xs text-red-500 hover:underline">
                                    Tout désélectionner
                                </button>
                            </div>
                        </div>

                        <div class="border rounded-xl overflow-hidden max-h-64 overflow-y-auto">
                            @foreach($participants_disponibles as $p)
                            <label class="flex items-center gap-3 p-3 cursor-pointer hover:bg-gray-50 transition border-b last:border-0
                                {{ in_array($p['id'], $participants_selectionnes) ? 'bg-purple-50' : '' }}">
                                <input type="checkbox"
                                    wire:model="participants_selectionnes"
                                    value="{{ $p['id'] }}"
                                    class="rounded border-gray-300 text-purple-600 w-4 h-4">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                    style="background-color: {{ ($p['genre'] ?? '') == 'femme' ? '#C8102E' : '#007A3D' }}">
                                    {{ strtoupper(substr($p['prenom'] ?? 'X', 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800 text-sm">
                                        {{ $p['nom'] }} {{ $p['prenom'] }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ ucfirst($p['role'] ?? '-') }}
                                    </p>
                                </div>
                            </label>
                            @endforeach
                        </div>

                        @error('participants_selectionnes')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    @elseif($id_evenement_generer && count($participants_disponibles) == 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-700">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        Aucun participant pour cet événement.
                    </div>
                    @endif

                </div>

                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="closeGenererModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="genererParType"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2 bg-purple-600">
                        <span wire:loading.remove>
                            <i class="fa-solid fa-wand-magic-sparkles mr-1"></i>
                            Générer les badges
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

    {{-- MODAL — BADGE INDIVIDUEL --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-id-badge"></i>
                    {{ $isEditing ? 'Modifier le badge' : 'Nouveau badge' }}
                </h3>
                <button wire:click="closeModal"
                    class="text-white/70 hover:text-white text-2xl transition">
                    &times;
                </button>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 gap-5">

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            <i class="fa-solid fa-user mr-1" style="color: #C8102E;"></i>
                            Participant *
                        </label>
                        <select wire:model="id_participant"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            <option value="">-- Choisir un participant --</option>
                            @foreach($participants as $participant)
                            <option value="{{ $participant->id }}">
                                {{ $participant->nom }} {{ $participant->prenom }}
                                {{ $participant->entreprise ? ' ('.$participant->entreprise->nom.')' : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_participant')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">
                            <i class="fa-solid fa-id-badge mr-1" style="color: #007A3D;"></i>
                            Type de badge *
                        </label>
                        @if($typesBadges->isEmpty())
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 text-sm text-yellow-700">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                            Aucun type de badge configuré.
                        </div>
                        @else
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($typesBadges as $type)
                            <label class="cursor-pointer">
                                <input type="radio"
                                    wire:model="id_type_badge"
                                    value="{{ $type->id }}"
                                    class="hidden peer">
                                <div class="border rounded-xl p-3 transition hover:bg-gray-50
                                    {{ $id_type_badge == $type->id
                                        ? 'border-red-400 bg-red-50'
                                        : 'border-gray-200' }}">
                                    <div class="flex items-center gap-2 mb-1">
                                        <i class="fa-solid fa-id-badge"
                                            style="color: {{ $id_type_badge == $type->id ? '#C8102E' : '#9ca3af' }}"></i>
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $type->libelle }}
                                        </p>
                                    </div>
                                    @if($type->description)
                                    <p class="text-xs text-gray-400">
                                        {{ $type->description }}
                                    </p>
                                    @endif
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @endif
                        @error('id_type_badge')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            <i class="fa-solid fa-qrcode mr-1" style="color: #C8102E;"></i>
                            QR Code
                        </label>
                        <div class="flex gap-2">
                            <input wire:model="qr_code" type="text" readonly
                                class="w-full border rounded-xl px-4 py-2.5 bg-gray-50 text-gray-600 font-mono text-sm focus:outline-none cursor-not-allowed">
                            <button wire:click="regenererQrCode" type="button"
                                class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition flex items-center gap-1 flex-shrink-0">
                                <i class="fa-solid fa-arrows-rotate"></i>
                            </button>
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