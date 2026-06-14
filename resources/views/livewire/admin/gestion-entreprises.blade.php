<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Liste des entreprises</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #007A3D;">
                {{ $entreprises->count() }} entreprise(s)
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Nouvelle entreprise
        </button>
    </div>

    {{-- Recherche --}}
    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher par nom, pays ou IFU..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Entreprise</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">IFU</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Secteur</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Représentant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Pays / Ville</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Statut</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entreprises as $entreprise)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0"
                                style="background-color: #007A3D;">
                                {{ strtoupper(substr($entreprise->nom, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $entreprise->nom }}</p>
                                @if($entreprise->sous_secteur)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $entreprise->sous_secteur }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($entreprise->ifu)
                        <span class="font-mono text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded-lg">
                            {{ $entreprise->ifu }}
                        </span>
                        @else
                        <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-3 py-1 rounded-full font-medium bg-blue-100 text-blue-700">
                            {{ $entreprise->secteur_activite }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($entreprise->nom_responsable)
                        <p class="font-medium text-gray-700">
                            {{ $entreprise->nom_responsable }}
                            {{ $entreprise->prenom_responsable }}
                        </p>
                        @if($entreprise->email_responsable)
                        <p class="text-xs text-gray-400">{{ $entreprise->email_responsable }}</p>
                        @endif
                        @else
                        <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        <p><i class="fa-solid fa-flag text-gray-400 mr-1"></i>{{ $entreprise->pays }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            <i class="fa-solid fa-location-dot text-gray-300 mr-1"></i>
                            {{ $entreprise->ville }}
                        </p>
                    </td>
                    <td class="px-6 py-4">
                        @if($entreprise->statut_validation == 'valide')
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium" style="background-color: #007A3D;">
                            <i class="fa-solid fa-circle-check mr-1"></i> Validée
                        </span>
                        @elseif($entreprise->statut_validation == 'rejete')
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-red-600">
                            <i class="fa-solid fa-circle-xmark mr-1"></i> Rejetée
                        </span>
                        @else
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">
                            <i class="fa-solid fa-clock mr-1"></i> En attente
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2 flex-wrap">
                            @if($entreprise->statut_validation == 'en_attente')
                            <button wire:click="valider({{ $entreprise->id }})"
                                wire:confirm="Valider {{ $entreprise->nom }} ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90 flex items-center gap-1"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-check"></i> Valider
                            </button>
                            <button wire:click="rejeter({{ $entreprise->id }})"
                                wire:confirm="Rejeter {{ $entreprise->nom }} ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                                <i class="fa-solid fa-xmark"></i> Rejeter
                            </button>
                            @endif
                            @if($entreprise->statut_validation == 'rejete')
                            <button wire:click="valider({{ $entreprise->id }})"
                                wire:confirm="Revalider {{ $entreprise->nom }} ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-rotate-left"></i> Revalider
                            </button>
                            @endif
                            <button wire:click="modifier({{ $entreprise->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="supprimer({{ $entreprise->id }})"
                                wire:confirm="Supprimer {{ $entreprise->nom }} ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-gray-500 transition hover:bg-gray-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-building text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucune entreprise pour le moment</p>
                        <button wire:click="openModal"
                            class="mt-3 px-5 py-2 rounded-xl text-white text-sm font-medium"
                            style="background-color: #C8102E;">
                            Ajouter la première entreprise
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
                <h3 class="text-xl font-bold">Compte représentant créé</h3>
                <p class="text-green-200 text-sm mt-1">
                    @if($compte_has_email) Compte email + code d'accès
                    @else Accès par code uniquement @endif
                </p>
            </div>
            <div class="p-8">
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-4 text-xs text-yellow-700 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                    Transmettez ces informations au représentant pour qu'il puisse se connecter.
                </div>
                <div class="space-y-3">
                    @if($compte_has_email)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-500 mb-1"><i class="fa-solid fa-envelope mr-1"></i> Email</p>
                        <p class="font-semibold text-gray-800">{{ $compte_email }}</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <p class="text-xs text-blue-500 mb-1"><i class="fa-solid fa-lock mr-1"></i> Mot de passe temporaire</p>
                        @if($compte_password)
                        <p class="font-mono font-bold text-xl text-blue-700 tracking-widest">{{ $compte_password }}</p>
                        @endif
                    </div>
                    @endif
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                        <p class="text-xs text-red-500 mb-1"><i class="fa-solid fa-key mr-1"></i> Code d'accès</p>
                        <p class="font-mono font-bold text-2xl text-red-700 tracking-widest">{{ $compte_code_acces }}</p>
                    </div>
                </div>
                <button wire:click="closeModalCompte"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow mt-6"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-check mr-1"></i> Fermer
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL FORMULAIRE --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-start justify-center z-50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl my-6">

            <div class="flex justify-between items-center px-8 py-5 border-b sticky top-0 z-10"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-plus' }}"></i>
                    {{ $isEditing ? "Modifier l'entreprise" : 'Nouvelle entreprise' }}
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8 space-y-6">

                {{-- SECTION 1 : Infos entreprise --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-building" style="color: #007A3D;"></i>
                        Informations de l'entreprise
                    </h4>
                    <div class="grid grid-cols-2 gap-4">

                        <div class="col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom de l'entreprise *</label>
                            <input wire:model="nom" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                                placeholder="Ex: Société Burkinabè de Commerce">
                            @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Numéro IFU *</label>
                            <input wire:model="ifu" type="text" maxlength="9"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm font-mono uppercase bg-white"
                                placeholder="Ex: 12345678A">
                            <p class="text-xs text-gray-400 mt-1">Format : 8 chiffres + 1 lettre</p>
                            @error('ifu') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Secteur d'activité *</label>
                            <select wire:model.live="secteur_activite"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                                <option value="">-- Choisir --</option>
                                @foreach($secteurs as $secteur)
                                <option value="{{ $secteur }}">{{ $secteur }}</option>
                                @endforeach
                            </select>
                            @error('secteur_activite') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        @if($secteur_activite === 'Autre')
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Précisez le secteur *</label>
                            <input wire:model="secteur_activite_autre" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                                placeholder="Ex: Pêche, Mines...">
                        </div>
                        @else
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Sous-secteur *</label>
                            <input wire:model="sous_secteur" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: Céréales, BTP...">
                            @error('sous_secteur') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        {{-- PAYS ENTREPRISE --}}
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Pays *</label>
                            <select wire:model.live="pays"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                                <option value="">-- Choisir un pays --</option>
                                @foreach($pays_liste as $p)
                                <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </select>
                            @if($pays === 'Autre')
                            <input wire:model.live="pays" type="text"
                                class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Saisissez votre pays...">
                            @endif
                            @error('pays') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- VILLE ENTREPRISE --}}
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Ville *</label>
                            @if($pays && $pays !== 'Autre')
                                @php $villes = $villes_par_pays[$pays] ?? []; @endphp
                                @if(count($villes) > 0)
                                <select wire:model.live="ville"
                                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                                    <option value="">-- Choisir une ville --</option>
                                    @foreach($villes as $v)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                                @if($ville === 'Autre')
                                <input wire:model="ville" type="text"
                                    class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                    placeholder="Saisissez votre ville...">
                                @endif
                                @else
                                <input wire:model="ville" type="text"
                                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                    placeholder="Ex: Ouagadougou">
                                @endif
                            @else
                            <input wire:model="ville" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: Ouagadougou">
                            @endif
                            @error('ville') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                            <input wire:model="telephone" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: +226 70 00 00 00">
                            @error('telephone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Email entreprise</label>
                            <input wire:model="email" type="email"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: contact@entreprise.com">
                        </div>

                        @if($isEditing)
                        <div class="col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-3">Statut de validation</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="statut_validation" value="en_attente" class="hidden peer">
                                    <div class="p-3 border-2 rounded-xl text-center transition text-sm peer-checked:border-yellow-400 peer-checked:bg-yellow-50 hover:bg-gray-50 border-gray-200 text-gray-600">
                                        <i class="fa-solid fa-clock text-yellow-500 mr-1"></i> En attente
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="statut_validation" value="valide" class="hidden peer">
                                    <div class="p-3 border-2 rounded-xl text-center transition text-sm peer-checked:border-green-400 peer-checked:bg-green-50 hover:bg-gray-50 border-gray-200 text-gray-600">
                                        <i class="fa-solid fa-circle-check text-green-500 mr-1"></i> Validée
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="statut_validation" value="rejete" class="hidden peer">
                                    <div class="p-3 border-2 rounded-xl text-center transition text-sm peer-checked:border-red-400 peer-checked:bg-red-50 hover:bg-gray-50 border-gray-200 text-gray-600">
                                        <i class="fa-solid fa-circle-xmark text-red-500 mr-1"></i> Rejetée
                                    </div>
                                </label>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- SECTION 2 : Représentant --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-user-tie" style="color: #C8102E;"></i>
                        Représentant de l'entreprise
                    </h4>
                    <div class="grid grid-cols-2 gap-4">

                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom *</label>
                            <input wire:model="rep_nom" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: OUEDRAOGO">
                            @error('rep_nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Prénom *</label>
                            <input wire:model="rep_prenom" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: Moussa">
                            @error('rep_prenom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Genre</label>
                            <select wire:model="rep_genre"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                                <option value="">-- Choisir --</option>
                                <option value="homme">Homme</option>
                                <option value="femme">Femme</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Fonction</label>
                            <input wire:model="rep_fonction" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: Directeur Général">
                        </div>

                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                            <input wire:model="rep_telephone" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: 70 00 00 00">
                            @error('rep_telephone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Email</label>
                            <input wire:model="rep_email" type="email"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: moussa@email.com">
                            @error('rep_email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- PAYS REPRÉSENTANT --}}
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Pays</label>
                            <select wire:model.live="rep_pays"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                                <option value="">-- Choisir un pays --</option>
                                @foreach($pays_liste as $p)
                                <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </select>
                            @if($rep_pays === 'Autre')
                            <input wire:model.live="rep_pays" type="text"
                                class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Saisissez votre pays...">
                            @endif
                        </div>

                        {{-- VILLE REPRÉSENTANT --}}
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Ville</label>
                            @if($rep_pays && $rep_pays !== 'Autre')
                                @php $villes_rep = $villes_par_pays[$rep_pays] ?? []; @endphp
                                @if(count($villes_rep) > 0)
                                <select wire:model.live="rep_ville"
                                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                                    <option value="">-- Choisir une ville --</option>
                                    @foreach($villes_rep as $v)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                                @if($rep_ville === 'Autre')
                                <input wire:model="rep_ville" type="text"
                                    class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                    placeholder="Saisissez votre ville...">
                                @endif
                                @else
                                <input wire:model="rep_ville" type="text"
                                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                    placeholder="Ex: Ouagadougou">
                                @endif
                            @else
                            <input wire:model="rep_ville" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: Ouagadougou">
                            @endif
                        </div>

                        <div class="col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Événement</label>
                            <select wire:model="rep_id_evenement"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                                <option value="">-- Choisir un événement --</option>
                                @foreach($evenements as $evenement)
                                <option value="{{ $evenement->id }}">{{ $evenement->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3 : Objectif --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-bullseye" style="color: #C8102E;"></i>
                        Objectif de participation
                    </h4>
                    <textarea wire:model="objectif_participation" rows="3" maxlength="200"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                        placeholder="Décrivez l'objectif de participation (200 caractères max)..."></textarea>
                    <p class="text-xs text-gray-400 mt-1 text-right">{{ strlen($objectif_participation) }} / 200</p>
                </div>

                {{-- SECTION 4 : Recherche de partenariat --}}
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

                {{-- SECTION 5 : Disponibilités --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-check" style="color: #007A3D;"></i>
                        Disponibilités du représentant
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