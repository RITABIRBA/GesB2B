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
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left" style="min-width: 900px;">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Contact</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Entreprise & Événement</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Rôle</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $participant)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                style="background-color: {{ $participant->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                                {{ strtoupper(substr($participant->prenom, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">
    @if($participant->genre == 'femme')
    <span class="text-xs text-pink-500">Mme</span>
    @elseif($participant->genre == 'homme')
    <span class="text-xs text-blue-500">M.</span>
    @endif
    {{ $participant->nom }} {{ $participant->prenom }}
</p>
                                <span class="font-mono text-xs font-bold px-2 py-0.5 rounded-lg inline-block"
                                    style="background-color: #fde8ec; color: #C8102E;">
                                    <i class="fa-solid fa-key mr-0.5"></i>{{ $participant->code_acces }}
                                </span>
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
                    <td class="px-4 py-4">
                        @php
                            $colors = [
                                'representant'    => '#007A3D',
                                'membre'          => '#2d5a8e',
                                'chef_delegation' => '#C8102E',
                            ];
                            $color = $colors[$participant->role] ?? '#6b7280';
                        @endphp
                        <span class="text-xs px-2 py-1 rounded-full font-medium text-white"
                            style="background-color: {{ $color }}">
                            {{ ucfirst(str_replace('_', ' ', $participant->role)) }}
                        </span>
                    </td>
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
                    <td colspan="5" class="py-16 text-center text-gray-400">
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
                    @if($compte_has_email) Compte email + code d'accès
                    @else Accès par code uniquement @endif
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
                        <p class="text-xs text-gray-500 mb-1"><i class="fa-solid fa-envelope mr-1"></i> Email</p>
                        <p class="font-semibold text-gray-800">{{ $compte_email }}</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <p class="text-xs text-blue-500 mb-1"><i class="fa-solid fa-lock mr-1"></i> Mot de passe</p>
                        @if($compte_password)
                        <p class="font-mono font-bold text-xl text-blue-700 tracking-widest">{{ $compte_password }}</p>
                        @else
                        <p class="text-xs text-blue-400 italic">Non visible — utiliser "Mot de passe oublié"</p>
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
                        <p class="text-xs text-red-500 mb-1"><i class="fa-solid fa-key mr-1"></i> Code d'accès</p>
                        <p class="font-mono font-bold text-2xl text-red-700 tracking-widest">{{ $compte_code_acces }}</p>
                        <p class="text-xs text-red-400 mt-1">Connexion sans email possible avec ce code</p>
                    </div>
                </div>
                <button wire:click="closeModalCompte"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow mt-6 flex items-center justify-center gap-2"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-check"></i> Fermer
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL FORMULAIRE PARTICIPANT --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-start justify-center z-50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl my-6">

            <div class="flex justify-between items-center px-8 py-5 border-b sticky top-0 z-10"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-plus' }}"></i>
                    {{ $isEditing ? 'Modifier le participant' : 'Nouveau participant' }}
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl transition">
                    &times;
                </button>
            </div>

            <div class="p-8 space-y-6">

                @if(!$isEditing)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-700 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    <div>
                        <p class="font-semibold mb-1">Compte d'accès :</p>
                        <p>→ Si email fourni : compte email + mot de passe temporaire + code d'accès</p>
                        <p>→ Si pas d'email : code d'accès uniquement</p>
                    </div>
                </div>
                @endif

                {{-- SECTION 1 : Informations personnelles --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-user" style="color: #007A3D;"></i>
                        Informations personnelles
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom *</label>
                            <input wire:model="nom" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                                placeholder="Ex: OUEDRAOGO">
                            @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Prénom *</label>
                            <input wire:model="prenom" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                                placeholder="Ex: Moussa">
                            @error('prenom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Genre</label>
                            <select wire:model="genre"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                                <option value="">-- Choisir --</option>
                                <option value="homme">Homme</option>
                                <option value="femme">Femme</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Fonction</label>
                            <input wire:model="fonction" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                                placeholder="Ex: Directeur Commercial">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                            <input wire:model="telephone" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                                placeholder="Ex: 70 00 00 00">
                            @error('telephone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Email</label>
                            <input wire:model="email" type="email"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                                placeholder="Ex: moussa@email.com">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- PAYS --}}
<div>
    <label class="block text-gray-600 text-sm font-medium mb-1.5">Pays</label>
    <select wire:model.live="pays"
        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
        <option value="">-- Choisir un pays --</option>
        @foreach($pays_liste as $p)
        <option value="{{ $p }}">{{ $p }}</option>
        @endforeach
    </select>
    @if($pays === 'Autre')
    <input wire:model.live="pays" type="text"
        class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
        placeholder="Saisissez votre pays...">
    @endif
</div>

{{-- VILLE --}}
<div>
    <label class="block text-gray-600 text-sm font-medium mb-1.5">Ville</label>
    @if($pays && $pays !== 'Autre')
        @php $villes = $villes_par_pays[$pays] ?? []; @endphp
        @if(count($villes) > 0)
        <select wire:model.live="ville"
            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
            <option value="">-- Choisir une ville --</option>
            @foreach($villes as $v)
            <option value="{{ $v }}">{{ $v }}</option>
            @endforeach
        </select>
        @if($ville === 'Autre')
        <input wire:model="ville" type="text"
            class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
            placeholder="Saisissez votre ville...">
        @endif
        @else
        <input wire:model="ville" type="text"
            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
            placeholder="Ex: Ouagadougou">
        @endif
    @else
    <input wire:model="ville" type="text"
        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
        placeholder="Ex: Ouagadougou">
    @endif
</div>

                {{-- SECTION 2 : Inscription --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-list" style="color: #C8102E;"></i>
                        Inscription & Rôle
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Événement *</label>
                            <select wire:model="id_evenement"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                                <option value="">-- Choisir un événement --</option>
                                @foreach($evenements as $evenement)
                                <option value="{{ $evenement->id }}">{{ $evenement->nom }}</option>
                                @endforeach
                            </select>
                            @error('id_evenement') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Rôle *</label>
                            <select wire:model="role"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                                @foreach($roles as $r)
                                <option value="{{ $r }}">{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Chef de délégation</label>
                            <select wire:model="id_chef_delegation"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                                <option value="">-- Aucun --</option>
                                @foreach($chefsDelegation as $cdd)
                                <option value="{{ $cdd->id }}">{{ $cdd->nom }} {{ $cdd->prenom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">
                                IFU
                                <span class="text-gray-400 font-normal">(lie automatiquement à une entreprise)</span>
                            </label>
                            <input wire:model.live="ifu" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                                placeholder="Ex: 12345678A">
                            @if($entreprise_trouvee)
                            <div class="mt-2 flex items-center gap-2 text-green-700 text-xs bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                                <i class="fa-solid fa-circle-check"></i>
                                Entreprise trouvée : <strong>{{ $entreprise_trouvee }}</strong>
                            </div>
                            @elseif($ifu && strlen($ifu) >= 8)
                            <div class="mt-2 flex items-center gap-2 text-orange-600 text-xs bg-orange-50 border border-orange-200 rounded-lg px-3 py-2">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                Aucune entreprise trouvée avec cet IFU.
                            </div>
                            @endif
                        </div>
                        <div class="col-span-2 flex items-center gap-3">
                            <input type="checkbox" wire:model="participation_rdv"
                                id="participation_rdv" class="w-4 h-4 rounded">
                            <label for="participation_rdv" class="text-sm text-gray-700 cursor-pointer">
                                Participe aux rendez-vous d'affaires
                            </label>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3 : Activité professionnelle --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-briefcase" style="color: #007A3D;"></i>
                        Activité professionnelle
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="{{ $secteur_activite === 'Autre' ? 'col-span-2' : '' }}">
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Secteur d'activité</label>
                            <select wire:model.live="secteur_activite"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                                <option value="">-- Choisir --</option>
                                @foreach($secteurs as $secteur)
                                <option value="{{ $secteur }}">{{ $secteur }}</option>
                                @endforeach
                            </select>
                            @if($secteur_activite === 'Autre')
                            <div class="mt-2">
                                <input wire:model="secteur_activite_autre" type="text"
                                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                                    placeholder="Précisez le secteur d'activité...">
                            </div>
                            @endif
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Sous-secteur</label>
                            <input wire:model="sous_secteur" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: Céréales">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Année de création</label>
                            <input wire:model="annee_creation" type="number" min="1900" max="2026"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: 2010">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Nombre de salariés</label>
                            <input wire:model="nombre_salaries" type="number" min="0"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: 50">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Part CA export (%)</label>
                            <input wire:model="chiffre_affaires" type="number" min="0" max="100"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: 30">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Description des activités</label>
                            <textarea wire:model="description_activites" rows="3"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Décrivez les activités principales..."></textarea>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Principaux produits / Savoir-faire</label>
                            <textarea wire:model="principaux_produits" rows="2"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: Riz, maïs, sorgho..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- SECTION 4 : Objectif de participation --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-bullseye" style="color: #C8102E;"></i>
                        Objectif de participation
                    </h4>
                    <textarea wire:model="objectif_participation" rows="3" maxlength="200"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                        placeholder="Décrivez votre objectif de participation (200 caractères max)..."></textarea>
                    <p class="text-xs text-gray-400 mt-1 text-right">
                        {{ strlen($objectif_participation) }} / 200 caractères
                    </p>
                    @error('objectif_participation')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- SECTION 5 : Recherche de partenariat --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-handshake" style="color: #007A3D;"></i>
                        Recherche de partenariat
                    </h4>

                    <div class="mb-4">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Zone géographique ciblée</label>
                        <select wire:model="zone_geographique"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                            <option value="">-- Choisir --</option>
                            @foreach($zonesGeographiques as $zone)
                            <option value="{{ $zone }}">{{ $zone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-600 text-sm font-medium mb-2">
                            Type de partenariat recherché
                            <span class="text-gray-400 font-normal">(max 3 — {{ count($types_partenariat) }}/3)</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($typesPartenariatOptions as $option)
                            <button type="button" wire:click="toggleTypePartenariat('{{ $option }}')"
                                class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm transition text-left
                                    {{ in_array($option, $types_partenariat) ? 'border-green-400 bg-green-50 text-green-700 font-medium' : (count($types_partenariat) >= 3 && !in_array($option, $types_partenariat) ? 'border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed' : 'border-gray-200 hover:border-green-300 text-gray-600') }}">
                                <i class="fa-solid {{ in_array($option, $types_partenariat) ? 'fa-circle-check text-green-500' : 'fa-circle text-gray-300' }}"></i>
                                {{ $option }}
                            </button>
                            @endforeach
                        </div>
                        @if(in_array('Autre', $types_partenariat))
                        <input wire:model="type_partenariat_autre" type="text"
                            class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                            placeholder="Précisez le type de partenariat...">
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-600 text-sm font-medium mb-2">
                            Profil de partenaire recherché
                            <span class="text-gray-400 font-normal">(max 3 — {{ count($profils_partenaire) }}/3)</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($profilsPartenariatOptions as $option)
                            <button type="button" wire:click="toggleProfilPartenaire('{{ $option }}')"
                                class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm transition text-left
                                    {{ in_array($option, $profils_partenaire) ? 'border-blue-400 bg-blue-50 text-blue-700 font-medium' : (count($profils_partenaire) >= 3 && !in_array($option, $profils_partenaire) ? 'border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed' : 'border-gray-200 hover:border-blue-300 text-gray-600') }}">
                                <i class="fa-solid {{ in_array($option, $profils_partenaire) ? 'fa-circle-check text-blue-500' : 'fa-circle text-gray-300' }}"></i>
                                {{ $option }}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">
                            Secteurs d'activité recherchés
                            <span class="text-gray-400 font-normal">(max 3 — {{ count($secteurs_recherche) }}/3)</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($secteurs as $option)
                            <button type="button" wire:click="toggleSecteurRecherche('{{ $option }}')"
                                class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm transition text-left
                                    {{ in_array($option, $secteurs_recherche) ? 'border-red-400 bg-red-50 text-red-700 font-medium' : (count($secteurs_recherche) >= 3 && !in_array($option, $secteurs_recherche) ? 'border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed' : 'border-gray-200 hover:border-red-300 text-gray-600') }}">
                                <i class="fa-solid {{ in_array($option, $secteurs_recherche) ? 'fa-circle-check text-red-500' : 'fa-circle text-gray-300' }}"></i>
                                {{ $option }}
                            </button>
                            @endforeach
                        </div>
                        @if(in_array('Autre', $secteurs_recherche))
                        <input wire:model="secteur_recherche_autre" type="text"
                            class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                            placeholder="Précisez le secteur recherché...">
                        @endif
                    </div>
                </div>

                {{-- SECTION 6 : Disponibilités --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-check" style="color: #007A3D;"></i>
                        Disponibilités
                    </h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($joursDisponibles as $jour)
                        <button type="button" wire:click="toggleDisponibilite('{{ $jour }}')"
                            class="px-4 py-2 rounded-xl border-2 text-sm font-medium transition
                                {{ in_array($jour, $disponibilites) ? 'border-green-400 bg-green-50 text-green-700' : 'border-gray-200 text-gray-600 hover:border-green-300' }}">
                            {{ $jour }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex justify-end gap-3 pt-2">
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