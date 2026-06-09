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
            <h3 class="text-xl font-bold text-gray-700">Liste des participants</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #007A3D;">
                {{ $participants->count() }} participant(s)
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Nouveau participant
        </button>
    </div>

    {{-- Filtres --}}
    <div class="flex gap-4 mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher par nom, prénom ou email..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
        <select wire:model.live="filtre_statut"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm text-gray-600">
            <option value="">Tous les statuts</option>
            <option value="actif">Actif seulement</option>
            <option value="inactif">Inactif seulement</option>
        </select>
    </div>

    {{-- Tableau avec scroll horizontal --}}
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left" style="min-width: 900px;">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Contact</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Entreprise & Événement</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Rôle</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Statut</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $participant)
                <tr class="border-b hover:bg-gray-50 transition
                    {{ $participant->statut_historique == 'inactif' ? 'opacity-60' : '' }}">

                    {{-- Participant : Nom + Code + Statut compte --}}
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                style="background-color: {{ $participant->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                                {{ strtoupper(substr($participant->prenom, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{ $participant->nom }} {{ $participant->prenom }}
                                    @if($participant->genre == 'femme')
                                        <span class="text-xs text-pink-500">(Mme)</span>
                                    @elseif($participant->genre == 'homme')
                                        <span class="text-xs text-blue-500">(M.)</span>
                                    @endif
                                </p>
                                {{-- Code d'accès --}}
                                <span class="font-mono text-xs font-bold px-2 py-0.5 rounded-lg inline-block"
                                    style="background-color: #fde8ec; color: #C8102E;">
                                    <i class="fa-solid fa-key mr-0.5"></i>{{ $participant->code_acces }}
                                </span>
                                {{-- Statut compte --}}
                                @php
                                    $hasAccount = $participant->email
                                        ? \App\Models\User::where('email', $participant->email)->exists()
                                        : false;
                                @endphp
                                @if($hasAccount)
                                <span class="text-xs text-green-600 block mt-0.5">
                                    <i class="fa-solid fa-circle-check mr-0.5"></i> Compte actif
                                </span>
                                @elseif($participant->email)
                                <span class="text-xs text-orange-500 block mt-0.5">
                                    <i class="fa-solid fa-clock mr-0.5"></i> Sans compte
                                </span>
                                @else
                                <span class="text-xs text-gray-400 block mt-0.5">
                                    <i class="fa-solid fa-key mr-0.5"></i> Code seulement
                                </span>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Contact : Email + Téléphone --}}
                    <td class="px-4 py-4 text-xs">
                        @if($participant->email)
                        <p class="text-gray-600">
                            <i class="fa-solid fa-envelope text-gray-400 mr-1"></i>
                            {{ $participant->email }}
                        </p>
                        @else
                        <p class="text-orange-400">
                            <i class="fa-solid fa-triangle-exclamation mr-0.5"></i>
                            Pas d'email
                        </p>
                        @endif
                        <p class="text-gray-600 mt-0.5">
                            <i class="fa-solid fa-phone text-gray-400 mr-1"></i>
                            {{ $participant->telephone }}
                        </p>
                    </td>

                    {{-- Entreprise & Événement regroupés --}}
                    <td class="px-4 py-4 text-xs">
                        <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium block w-fit mb-1">
                            <i class="fa-solid fa-building mr-0.5"></i>
                            {{ $participant->entreprise->nom ?? 'Indépendant' }}
                        </span>
                        <p class="text-gray-500">
                            <i class="fa-solid fa-calendar text-gray-400 mr-0.5"></i>
                            {{ $participant->evenement->nom ?? '-' }}
                        </p>
                    </td>

                    {{-- Rôle --}}
                    <td class="px-4 py-4">
                        @php
                            $colors = [
                                'exposant'     => '#007A3D',
                                'participant'  => '#2d5a8e',
                                'organisateur' => '#C8102E',
                            ];
                            $color = $colors[$participant->role] ?? '#6b7280';
                        @endphp
                        <span class="text-xs px-2 py-1 rounded-full font-medium text-white"
                            style="background-color: {{ $color }}">
                            {{ ucfirst($participant->role) }}
                        </span>
                    </td>

                    {{-- Statut actif/inactif --}}
                    <td class="px-4 py-4">
                        <button wire:click="toggleStatut({{ $participant->id }})"
                            wire:confirm="{{ $participant->statut_historique == 'actif' ? 'Désactiver ce participant ?' : 'Activer ce participant ?' }}"
                            class="relative inline-flex h-6 w-12 items-center rounded-full transition-colors duration-300"
                            style="background-color: {{ $participant->statut_historique == 'actif' ? '#007A3D' : '#d1d5db' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow-md transition-transform duration-300
                                {{ $participant->statut_historique == 'actif' ? 'translate-x-7' : 'translate-x-1' }}">
                            </span>
                        </button>
                        <span class="text-xs ml-1 {{ $participant->statut_historique == 'actif' ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $participant->statut_historique == 'actif' ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-4">
                        <div class="flex gap-2">
                            <button wire:click="voirCompte({{ $participant->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-gray-500 transition hover:bg-gray-600"
                                title="Voir le compte">
                                <i class="fa-solid fa-key"></i>
                            </button>
                            <button wire:click="modifier({{ $participant->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700"
                                title="Modifier">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="supprimer({{ $participant->id }})"
                                wire:confirm="Voulez-vous vraiment supprimer ce participant ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700"
                                title="Supprimer">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-users text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun participant pour le moment</p>
                        <button wire:click="openModal"
                            class="mt-3 px-5 py-2 rounded-xl text-white text-sm font-medium"
                            style="background-color: #C8102E;">
                            Ajouter le premier participant
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL COMPTE --}}
    @if($showModalCompte)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">

            <div class="px-8 py-6 rounded-t-2xl text-white text-center"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3"
                    style="background-color: rgba(255,255,255,0.2);">
                    <i class="fa-solid fa-user-check text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold">Informations du compte</h3>
                <p class="text-green-200 text-sm mt-1">
                    @if($compte_has_email)
                        Compte email + code d'accès
                    @else
                        Accès par code uniquement
                    @endif
                </p>
            </div>

            <div class="p-8">
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-4 text-xs text-yellow-700 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                    Transmettez ces informations au participant pour qu'il puisse se connecter.
                </div>

                <div class="space-y-3">
                    @if($compte_has_email)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-500 mb-1">
                            <i class="fa-solid fa-envelope mr-1"></i> Email
                        </p>
                        <p class="font-semibold text-gray-800">{{ $compte_email }}</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <p class="text-xs text-blue-500 mb-1">
                            <i class="fa-solid fa-lock mr-1"></i> Mot de passe
                        </p>
                        @if($compte_password)
                        <p class="font-mono font-bold text-xl text-blue-700 tracking-widest">
                            {{ $compte_password }}
                        </p>
                        @else
                        <p class="text-xs text-blue-400 italic">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            Non visible — utiliser "Mot de passe oublié"
                        </p>
                        @endif
                    </div>
                    @else
                    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-sm text-orange-700 flex items-start gap-2">
                        <i class="fa-solid fa-circle-info mt-0.5"></i>
                        <div>
                            <p class="font-semibold mb-1">Pas d'email</p>
                            <p class="text-xs">Connexion uniquement via le <strong>code d'accès</strong>.</p>
                        </div>
                    </div>
                    @endif

                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                        <p class="text-xs text-red-500 mb-1">
                            <i class="fa-solid fa-key mr-1"></i> Code d'accès
                        </p>
                        <p class="font-mono font-bold text-2xl text-red-700 tracking-widest">
                            {{ $compte_code_acces }}
                        </p>
                        <p class="text-xs text-red-400 mt-1">
                            Connexion sans email possible avec ce code
                        </p>
                    </div>
                </div>

                <button wire:click="closeModalCompte"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow mt-6 flex items-center justify-center gap-2"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-check"></i>
                    Fermer
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL PARTICIPANT --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-start justify-center z-50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-auto">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-plus' }}"></i>
                    {{ $isEditing ? 'Modifier le participant' : 'Nouveau participant' }}
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl transition">
                    &times;
                </button>
            </div>

            <div class="p-8">

                @if(!$isEditing)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-5 text-xs text-blue-700 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    <div>
                        <p class="font-semibold mb-1">Compte d'accès :</p>
                        <p>→ Si email fourni : compte email + mot de passe temporaire + code d'accès</p>
                        <p>→ Si pas d'email : code d'accès uniquement</p>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-5">

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom *</label>
                        <input wire:model="nom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: OUEDRAOGO">
                        @error('nom') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Prénom *</label>
                        <input wire:model="prenom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: Moussa">
                        @error('prenom') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Genre</label>
                        <select wire:model="genre"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            <option value="">-- Choisir --</option>
                            <option value="homme">Homme</option>
                            <option value="femme">Femme</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Fonction
                            <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <input wire:model="fonction" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: Directeur Commercial">
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
                            Email
                            <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <input wire:model="email" type="email"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: moussa@email.com">
                        @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            IFU
                            <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <input wire:model="ifu" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: BF123456789">
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Secteur d'activité</label>
                        <select wire:model="secteur_activite"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($secteurs as $secteur)
                            <option value="{{ $secteur }}">{{ $secteur }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Rôle *</label>
                        <select wire:model="role"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            @foreach($roles as $r)
                            <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Événement *</label>
                        <select wire:model="id_evenement"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($evenements as $evenement)
                            <option value="{{ $evenement->id }}">{{ $evenement->nom }}</option>
                            @endforeach
                        </select>
                        @error('id_evenement') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Entreprise
                            <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <select wire:model="id_entreprise"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            <option value="">-- Indépendant --</option>
                            @foreach($entreprises as $entreprise)
                            <option value="{{ $entreprise->id }}">{{ $entreprise->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-2">Statut</label>
                        <div class="flex gap-4">
                            <button type="button"
                                wire:click="$set('statut_historique', 'actif')"
                                class="flex-1 py-2.5 rounded-xl border-2 text-sm font-medium transition flex items-center justify-center gap-2
                                    {{ $statut_historique === 'actif'
                                        ? 'border-green-400 bg-green-50 text-green-700'
                                        : 'border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-circle-check"></i> Actif
                            </button>
                            <button type="button"
                                wire:click="$set('statut_historique', 'inactif')"
                                class="flex-1 py-2.5 rounded-xl border-2 text-sm font-medium transition flex items-center justify-center gap-2
                                    {{ $statut_historique === 'inactif'
                                        ? 'border-red-400 bg-red-50 text-red-700'
                                        : 'border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-circle-xmark"></i> Inactif
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