{{-- 
    Vue Blade — Gestion des Traducteurs
    Composant : App\Livewire\Admin\GestionTraducteurs
    Layout : layouts/admin.blade.php
    
    Cette vue affiche la liste des traducteurs avec :
    - Une barre de recherche en temps réel
    - Un tableau listant tous les traducteurs
    - Un modal pour créer/modifier un traducteur
--}}
<div>

    {{-- ================================================
        MESSAGE DE SUCCÈS
        Affiché après création, modification ou suppression
    ================================================ --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- ================================================
        EN-TÊTE DE LA PAGE
        Titre + compteur + bouton "Nouveau traducteur"
    ================================================ --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Liste des traducteurs</h3>
            {{-- Badge indiquant le nombre total de traducteurs --}}
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #007A3D;">
                {{ $traducteurs->count() }} traducteur(s)
            </span>
        </div>
        {{-- Bouton pour ouvrir le modal de création --}}
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Nouveau traducteur
        </button>
    </div>

    {{-- ================================================
        BARRE DE RECHERCHE
        Filtre en temps réel via wire:model.live
    ================================================ --}}
    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search"
                type="text"
                placeholder="Rechercher par nom, prénom ou langue..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
    </div>

    {{-- ================================================
        TABLEAU DES TRADUCTEURS
    ================================================ --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">

            {{-- En-têtes du tableau --}}
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Traducteur</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Téléphone</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Email</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Langue</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>

            <tbody>
                {{-- Boucle sur les traducteurs --}}
                @forelse($traducteurs as $traducteur)
                <tr class="border-b hover:bg-gray-50 transition">

                    {{-- Colonne : Avatar + Nom & Prénom --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            {{-- Avatar avec initiale du prénom --}}
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

                    {{-- Colonne : Téléphone --}}
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        <i class="fa-solid fa-phone text-gray-400 mr-1"></i>
                        {{ $traducteur->telephone }}
                    </td>

                    {{-- Colonne : Email (optionnel) --}}
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        @if($traducteur->email)
                            <i class="fa-solid fa-envelope text-gray-400 mr-1"></i>
                            {{ $traducteur->email }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>

                    {{-- Colonne : Badge langue --}}
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium"
                            style="background-color: #007A3D;">
                            <i class="fa-solid fa-language mr-1"></i>
                            {{ $traducteur->langue }}
                        </span>
                    </td>

                    {{-- Colonne : Boutons d'actions --}}
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            {{-- Bouton Modifier --}}
                            <button wire:click="modifier({{ $traducteur->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700 flex items-center gap-1">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </button>
                            {{-- Bouton Supprimer avec confirmation --}}
                            <button wire:click="supprimer({{ $traducteur->id }})"
                                wire:confirm="Voulez-vous vraiment supprimer ce traducteur ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                                <i class="fa-solid fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </td>

                </tr>
                @empty
                {{-- Message affiché si aucun traducteur --}}
                <tr>
                    <td colspan="5" class="py-16 text-center text-gray-400">
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

    {{-- ================================================
        MODAL — CRÉATION / MODIFICATION
        Affiché uniquement si $showModal === true
    ================================================ --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

            {{-- Header du modal avec dégradé vert --}}
            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-language"></i>
                    {{-- Titre dynamique selon le mode --}}
                    {{ $isEditing ? 'Modifier le traducteur' : 'Nouveau traducteur' }}
                </h3>
                {{-- Bouton fermer --}}
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl transition">
                    &times;
                </button>
            </div>

            {{-- Corps du modal --}}
            <div class="p-8">
                <div class="grid grid-cols-2 gap-5">

                    {{-- Champ : Nom --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom *</label>
                        <input wire:model="nom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: TRAORE">
                        @error('nom')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Champ : Prénom --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Prénom *</label>
                        <input wire:model="prenom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: Albert">
                        @error('prenom')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Champ : Téléphone --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                        <input wire:model="telephone" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: +226 70 00 00 00">
                        @error('telephone')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Champ : Email (optionnel) --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Email <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <input wire:model="email" type="email"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: traore@email.com">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Champ : Langue (sélection visuelle par boutons radio) --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-2">Langue *</label>
                        {{-- Grille de boutons radio stylisés --}}
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($langues as $l)
                            <label class="cursor-pointer">
                                {{-- Input radio caché — contrôlé par wire:model --}}
                                <input type="radio" wire:model="langue" value="{{ $l }}" class="hidden peer">
                                {{-- Apparence visuelle du bouton radio --}}
                                <div class="peer-checked:ring-2 peer-checked:ring-red-400 border rounded-xl p-2 text-center transition hover:bg-gray-50 text-xs
                                    {{ $langue === $l ? 'border-red-400 bg-red-50 text-red-700 font-medium' : 'border-gray-200 text-gray-600' }}">
                                    {{ $l }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('langue')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                {{-- Boutons d'action du modal --}}
                <div class="flex justify-end gap-3 mt-7">
                    {{-- Bouton Annuler --}}
                    <button wire:click="closeModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    {{-- Bouton Enregistrer / Modifier --}}
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

</div>