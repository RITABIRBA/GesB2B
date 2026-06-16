<div>
    {{-- ============================================================
         MESSAGES
    ============================================================ --}}
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

    {{-- ============================================================
         DEMANDES D'ADHÉSION EN ATTENTE
    ============================================================ --}}
    @if($demandesEnAttente->count() > 0)
    <div class="bg-white rounded-xl shadow p-6 mb-6 border-l-4" style="border-color: #C8102E;">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                style="background-color: #fde8ec;">
                <i class="fa-solid fa-user-clock text-xl" style="color: #C8102E;"></i>
            </div>
            <div>
                <p class="font-bold text-gray-800">
                    Demandes d'adhésion en attente
                </p>
                <p class="text-sm text-gray-400">
                    {{ $demandesEnAttente->count() }} personne(s) souhaitent rejoindre votre entreprise.
                    Vérifiez s'ils font bien partie de votre équipe.
                </p>
            </div>
        </div>

        <div class="space-y-3">
            @foreach($demandesEnAttente as $demande)
            <div class="flex items-center justify-between bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                        style="background-color: {{ $demande->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                        {{ strtoupper(substr($demande->prenom ?? 'X', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">
                            {{ $demande->nom }} {{ $demande->prenom }}
                            @if($demande->genre == 'femme')
                            <span class="text-xs text-gray-400">(Mme)</span>
                            @elseif($demande->genre == 'homme')
                            <span class="text-xs text-gray-400">(M.)</span>
                            @endif
                        </p>
                        @if($demande->fonction)
                        <p class="text-xs text-gray-400">
                            <i class="fa-solid fa-briefcase mr-1"></i>
                            {{ $demande->fonction }}
                        </p>
                        @endif
                        <p class="text-xs text-gray-400">
                            <i class="fa-solid fa-phone mr-1"></i>
                            {{ $demande->telephone ?? '-' }}
                            @if($demande->email)
                            · <i class="fa-solid fa-envelope mr-1"></i>{{ $demande->email }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="accepterAdhesion({{ $demande->id }})"
                        wire:confirm="Accepter {{ $demande->nom }} {{ $demande->prenom }} dans votre entreprise ?"
                        class="px-4 py-2 rounded-xl text-white text-sm font-medium transition hover:opacity-90"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-check mr-1"></i> Accepter
                    </button>
                    <button wire:click="rejeterAdhesion({{ $demande->id }})"
                        wire:confirm="Rejeter la demande de {{ $demande->nom }} {{ $demande->prenom }} ?"
                        class="px-4 py-2 rounded-xl text-white text-sm font-medium bg-red-600 transition hover:bg-red-700">
                        <i class="fa-solid fa-xmark mr-1"></i> Rejeter
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ============================================================
         EN-TÊTE
    ============================================================ --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Membres de l'entreprise</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $membres->count() }} membre(s)
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-user-plus"></i>
            Ajouter un membre
        </button>
    </div>

    {{-- ============================================================
         INFO ENTREPRISE
    ============================================================ --}}
    @if($entreprise)
    <div class="bg-white rounded-xl shadow p-5 mb-6 border-l-4" style="border-color: #007A3D;">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0"
                style="background-color: #007A3D;">
                {{ strtoupper(substr($entreprise->nom, 0, 1)) }}
            </div>
            <div>
                <p class="font-bold text-gray-800 text-lg">{{ $entreprise->nom }}</p>
                <p class="text-sm text-gray-500">
                    {{ $entreprise->secteur_activite }}
                    @if($entreprise->sous_secteur) / {{ $entreprise->sous_secteur }} @endif
                    · {{ $entreprise->ville }}, {{ $entreprise->pays }}
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- ============================================================
         RECHERCHE
    ============================================================ --}}
    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher un membre..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none text-sm">
        </div>
    </div>

    {{-- ============================================================
         TABLEAU DES MEMBRES
    ============================================================ --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Membre</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Fonction</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Contact</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Code d'accès</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Profil</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($membres as $membre)
                <tr class="border-b hover:bg-gray-50 transition">

                    {{-- Membre --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                style="background-color: {{ $membre->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                                {{ strtoupper(substr($membre->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ $membre->nom }} {{ $membre->prenom }}
                                    @if($membre->genre == 'femme')
                                    <span class="text-xs text-gray-400">(Mme)</span>
                                    @elseif($membre->genre == 'homme')
                                    <span class="text-xs text-gray-400">(M.)</span>
                                    @endif
                                </p>
                                <span class="text-xs px-2 py-0.5 rounded-full text-white font-medium"
                                    style="background-color: {{ $membre->role == 'representant' ? '#C8102E' : '#007A3D' }}">
                                    {{ $membre->role == 'representant' ? 'Représentant' : 'Membre' }}
                                </span>
                            </div>
                        </div>
                    </td>

                    {{-- Fonction --}}
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        {{ $membre->fonction ?? '—' }}
                    </td>

                    {{-- Contact --}}
                    <td class="px-6 py-4 text-sm">
                        <p class="text-gray-600">
                            <i class="fa-solid fa-phone text-gray-400 mr-1"></i>
                            {{ $membre->telephone ?? '—' }}
                        </p>
                        @if($membre->email)
                        <p class="text-gray-400 text-xs mt-0.5">
                            <i class="fa-solid fa-envelope text-gray-300 mr-1"></i>
                            {{ $membre->email }}
                        </p>
                        @endif
                    </td>

                    {{-- Code d'accès --}}
                    <td class="px-6 py-4">
                        <span class="font-mono text-sm bg-gray-100 px-3 py-1.5 rounded-lg font-bold text-gray-700">
                            {{ $membre->code_acces }}
                        </span>
                    </td>

                    {{-- Statut profil --}}
                    <td class="px-6 py-4">
                        @php
                        $profilComplet = !empty($membre->secteurs_recherche)
                            && !empty($membre->zone_geographique)
                            && !empty($membre->types_partenariat);
                        @endphp
                        @if($profilComplet)
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                            <i class="fa-solid fa-circle-check mr-1"></i> Profil complété
                        </span>
                        @else
                        <span class="text-xs px-2 py-1 rounded-full bg-orange-100 text-orange-700 font-medium">
                            <i class="fa-solid fa-hourglass-half mr-1"></i> Profil à compléter
                        </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button wire:click="modifier({{ $membre->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            @if($membre->role !== 'representant')
                            <button wire:click="supprimer({{ $membre->id }})"
                                wire:confirm="Supprimer ce membre ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-users text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun membre pour le moment</p>
                        <p class="text-sm text-gray-400 mt-1 mb-4">
                            Ajoutez les membres de votre entreprise
                        </p>
                        <button wire:click="openModal"
                            class="px-5 py-2 rounded-xl text-white text-sm font-medium"
                            style="background-color: #C8102E;">
                            <i class="fa-solid fa-user-plus mr-1"></i>
                            Ajouter le premier membre
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{--MODAL AJOUT / MODIFICATION MEMBRE --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-user-plus' }}"></i>
                    {{ $isEditing ? 'Modifier le membre' : 'Ajouter un membre' }}
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl">
                    &times;
                </button>
            </div>

            <div class="p-8">

                @if(!$isEditing)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-5 text-xs text-blue-700 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5 flex-shrink-0"></i>
                    <div>
                        Un <strong>code d'accès</strong> sera généré automatiquement.
                        Le membre se connectera avec ce code et complètera son profil.
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-5">

                    {{-- Nom --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom *</label>
                        <input wire:model="nom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                            placeholder="Nom du membre">
                        @error('nom')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Prénom --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Prénom *</label>
                        <input wire:model="prenom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                            placeholder="Prénom du membre">
                        @error('prenom')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Genre --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Genre</label>
                        <select wire:model="genre"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
                            <option value="">-- Choisir --</option>
                            <option value="homme">Homme</option>
                            <option value="femme">Femme</option>
                        </select>
                    </div>

                    {{-- Fonction --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Fonction *</label>
                        <select wire:model.live="fonction"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($fonctions as $f)
                            <option value="{{ $f }}">{{ $f }}</option>
                            @endforeach
                        </select>
                        @error('fonction')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Saisie libre si Autre --}}
                    @if($fonction == 'Autre')
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Précisez la fonction *
                        </label>
                        <input wire:model="fonction_autre" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                            placeholder="Ex: Responsable Achats, Comptable...">
                    </div>
                    @endif

                    {{-- Téléphone --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                        <input wire:model="telephone" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                            placeholder="Ex: +226 70 00 00 00">
                        @error('telephone')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Email
                            <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <input wire:model="email" type="email"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                            placeholder="email@exemple.com">
                        @if(!$isEditing)
                        <p class="text-xs text-gray-400 mt-1">
                            Si renseigné, un compte sera créé automatiquement.
                        </p>
                        @endif
                        @error('email')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                {{-- Boutons --}}
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
                        <span wire:loading.remove wire:target="sauvegarder">
                            <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-user-plus' }} mr-1"></i>
                            {{ $isEditing ? 'Modifier' : 'Ajouter le membre' }}
                        </span>
                        <span wire:loading wire:target="sauvegarder">
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                            Enregistrement...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

    {{-- ============================================================
         MODAL CODE D'ACCÈS DU NOUVEAU MEMBRE
    ============================================================ --}}
    @if($showCodeModal && count($nouveauMembre) > 0)
    <div class="fixed inset-0 bg-black/50 flex items-start justify-center z-50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md my-8">

            <div class="px-8 py-5 rounded-t-2xl text-white text-center"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 bg-white/20">
                    <i class="fa-solid fa-user-check text-3xl text-white"></i>
                </div>
                <h3 class="text-xl font-bold">Membre ajouté avec succès !</h3>
                <p class="text-green-200 text-sm mt-1">
                    Communiquez ces informations au membre
                </p>
            </div>

            <div class="p-8">

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-5 text-xs text-yellow-700 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
                    Ces informations ne seront plus affichées après fermeture.
                    Notez-les ou transmettez-les maintenant au membre.
                </div>

                <div class="space-y-3">

                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                        <span class="text-sm text-gray-500 flex items-center gap-2">
                            <i class="fa-solid fa-user text-gray-400"></i> Nom
                        </span>
                        <span class="font-semibold text-gray-800">
                            {{ $nouveauMembre['nom'] ?? '' }}
                            {{ $nouveauMembre['prenom'] ?? '' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                        <span class="text-sm text-gray-500 flex items-center gap-2">
                            <i class="fa-solid fa-briefcase text-gray-400"></i> Fonction
                        </span>
                        <span class="font-semibold text-gray-800">
                            {{ $nouveauMembre['fonction'] ?? '' }}
                        </span>
                    </div>

                    @if(!empty($nouveauMembre['email']))
                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                        <span class="text-sm text-gray-500 flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-gray-400"></i> Email
                        </span>
                        <span class="font-semibold text-gray-800 text-sm">
                            {{ $nouveauMembre['email'] }}
                        </span>
                    </div>
                    @endif

                    {{-- Code d'accès --}}
                    <div class="bg-red-50 border-2 border-red-200 rounded-xl px-4 py-4 text-center">
                        <p class="text-xs text-red-500 font-medium mb-1">
                            <i class="fa-solid fa-key mr-1"></i>
                            Code d'accès
                        </p>
                        <p class="font-bold text-red-700 font-mono text-3xl tracking-widest">
                            {{ $nouveauMembre['code_acces'] ?? '' }}
                        </p>
                        <p class="text-xs text-red-400 mt-1">
                            Le membre utilise ce code pour se connecter
                        </p>
                    </div>

                    <div class="flex items-center justify-between bg-blue-50 rounded-xl px-4 py-3">
                        <span class="text-sm text-blue-500 flex items-center gap-2">
                            <i class="fa-solid fa-link text-blue-400"></i> Lien de connexion
                        </span>
                        <span class="font-semibold text-blue-700 text-xs">
                            {{ url('/login') }}
                        </span>
                    </div>

                </div>

                {{-- Instructions --}}
                <div class="bg-green-50 border border-green-200 rounded-xl p-3 mt-4 text-xs text-green-700">
                    <p class="font-bold mb-1">
                        <i class="fa-solid fa-list-check mr-1"></i>
                        Le membre doit :
                    </p>
                    <ol class="space-y-1">
                        <li>1. Aller sur {{ url('/login') }}</li>
                        <li>2. Entrer son code d'accès : <strong>{{ $nouveauMembre['code_acces'] ?? '' }}</strong></li>
                        <li>3. Compléter son profil (secteur recherché, disponibilités)</li>
                    </ol>
                </div>

                {{-- Boutons --}}
                <div class="flex gap-3 mt-6">
                    <button
                        onclick="
                            navigator.clipboard.writeText(
                                'Nom: {{ ($nouveauMembre['nom'] ?? '') . ' ' . ($nouveauMembre['prenom'] ?? '') }}\n' +
                                'Code d\'accès: {{ $nouveauMembre['code_acces'] ?? '' }}\n' +
                                'Lien: {{ url('/login') }}'
                            );
                            this.innerHTML = '<i class=\'fa-solid fa-check mr-1\'></i> Copié !';
                            setTimeout(() => this.innerHTML = '<i class=\'fa-solid fa-copy mr-1\'></i> Copier', 2000);
                        "
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium flex items-center justify-center">
                        <i class="fa-solid fa-copy mr-1"></i> Copier
                    </button>
                    <button wire:click="closeCodeModal"
                        class="flex-1 px-4 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm flex items-center justify-center"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-check mr-1"></i> J'ai noté
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

</div>