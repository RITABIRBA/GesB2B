<div>

    {{-- BARRE DE PROGRESSION (en haut, fixe) --}}
    @if($etape > 0)
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            @php
            $etapes = [
                1 => 'Événement',
                2 => 'Infos',
                3 => 'Activité',
                4 => 'Partenariat',
                5 => 'Dispo & CDD',
                6 => 'Confirmation',
            ];
            @endphp
            @foreach($etapes as $num => $label)
            <div class="flex flex-col items-center flex-1">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2 transition
                    {{ $etape >= $num ? 'text-white border-transparent' : 'text-gray-400 border-gray-300' }}"
                    style="{{ $etape >= $num ? 'background-color: #C8102E;' : '' }}">
                    @if($etape > $num)
                    <i class="fa-solid fa-check"></i>
                    @else
                    {{ $num }}
                    @endif
                </div>
                <p class="text-xs mt-1 font-medium text-center
                    {{ $etape >= $num ? 'text-gray-800' : 'text-gray-400' }}">
                    {{ $label }}
                </p>
            </div>
            @if($num < count($etapes))
            <div class="flex-1 h-0.5 mb-4 {{ $etape > $num ? '' : 'bg-gray-200' }}"
                style="{{ $etape > $num ? 'background-color: #C8102E;' : '' }}">
            </div>
            @endif
            @endforeach
        </div>
    </div>

    {{-- MINI BARRE STICKY EN BAS — visible pendant le scroll --}}
    <div class="fixed bottom-0 left-0 right-0 z-50 shadow-lg border-t border-gray-100"
        style="background: rgba(255,255,255,0.97); backdrop-filter: blur(12px);">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
            {{-- Étape courante --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                    style="background-color: #C8102E;">
                    {{ $etape }}
                </div>
                <div>
                    <p class="text-xs text-gray-400 leading-none">Étape {{ $etape }} / {{ count($etapes) }}</p>
                    <p class="text-sm font-bold text-gray-800 leading-tight">
                        {{ $etapes[$etape] ?? '' }}
                    </p>
                </div>
            </div>

            {{-- Mini pills de progression --}}
            <div class="hidden sm:flex items-center gap-1.5">
                @foreach($etapes as $num => $label)
                <div class="h-2 rounded-full transition-all duration-300"
                    style="width: {{ $etape > $num ? '28px' : ($etape == $num ? '36px' : '14px') }};
                           background-color: {{ $etape > $num ? '#007A3D' : ($etape == $num ? '#C8102E' : '#e5e7eb') }};">
                </div>
                @endforeach
            </div>

            {{-- Bouton continuer / confirmer rapide --}}
            @if($etape < 6)
            <button wire:click="suivant"
                class="px-5 py-2.5 rounded-xl text-white text-sm font-bold transition hover:opacity-90 flex items-center gap-2 shadow flex-shrink-0"
                style="background-color: #C8102E;">
                Continuer <i class="fa-solid fa-arrow-right"></i>
            </button>
            @elseif($etape == 6)
            <button wire:click="confirmer"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-70 cursor-not-allowed"
                class="px-5 py-2.5 rounded-xl text-white text-sm font-bold transition hover:opacity-90 flex items-center gap-2 shadow flex-shrink-0"
                style="background-color: #007A3D;">
                <span wire:loading.remove>
                    <i class="fa-solid fa-circle-check mr-1"></i> Confirmer
                </span>
                <span wire:loading>
                    <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                </span>
            </button>
            @endif
        </div>
    </div>
    @endif

    {{-- ÉTAPE 0 — ACCUEIL --}}
    @if($etape == 0)
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="p-8 text-white text-center"
            style="background: linear-gradient(135deg, #007A3D, #005a2d);">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 bg-white/20">
                <i class="fa-solid fa-calendar-check text-3xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Inscription à l'événement</h2>
            <p class="text-green-200 text-lg font-semibold">{{ $evenement->nom }}</p>
            <p class="text-green-300 text-sm mt-1">
                <i class="fa-solid fa-location-dot mr-1"></i>{{ $evenement->ville }}
                <span class="mx-2">•</span>
                <i class="fa-solid fa-calendar mr-1"></i>
                {{ \Carbon\Carbon::parse($evenement->date_debut)->format('d/m/Y') }}
                @if($evenement->date_debut != $evenement->date_fin)
                → {{ \Carbon\Carbon::parse($evenement->date_fin)->format('d/m/Y') }}
                @endif
            </p>
        </div>
        <div class="p-8">
            @if($estMembre && $entreprise)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0"
                    style="background-color: #007A3D;">
                    {{ strtoupper(substr($entreprise->nom, 0, 1)) }}
                </div>
                <div>
                    <p class="text-xs text-gray-400">Vous représentez</p>
                    <p class="font-bold text-gray-800">{{ $entreprise->nom }}</p>
                    <p class="text-xs text-gray-500">{{ $entreprise->secteur_activite }} · {{ $entreprise->ville }}</p>
                </div>
            </div>
            @endif

            <p class="text-gray-600 text-center mb-6">
                Vous allez suivre <strong>6 étapes</strong> pour finaliser votre inscription.
            </p>

            <div class="space-y-3 mb-8">
                @foreach([
                    [1, 'Détails de l\'événement', 'Consultez les informations', 'fa-calendar'],
                    [2, 'Informations personnelles', 'Nom, prénom, fonction, contact', 'fa-user'],
                    [3, 'Activité professionnelle', 'Secteur, description, objectif', 'fa-briefcase'],
                    [4, 'Recherche de partenariat', 'Type, profil, secteurs (max 3)', 'fa-handshake'],
                    [5, 'Disponibilités & CDD', 'Jours disponibles et chef de délégation', 'fa-calendar-days'],
                    [6, 'Confirmation', 'Récapitulatif et validation', 'fa-circle-check'],
                ] as [$num, $titre, $desc, $icon])
                <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                        style="background-color: #C8102E;">{{ $num }}</div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $titre }}</p>
                        <p class="text-xs text-gray-500">{{ $desc }}</p>
                    </div>
                    <i class="fa-solid {{ $icon }} text-gray-300 ml-auto text-xl"></i>
                </div>
                @endforeach
            </div>

            <button wire:click="commencer"
                class="w-full py-4 rounded-xl text-white font-bold text-lg transition hover:opacity-90 shadow-lg flex items-center justify-center gap-3"
                style="background-color: #C8102E;">
                <i class="fa-solid fa-play"></i>
                Commencer l'inscription
            </button>

            <a href="javascript:history.back()"
                class="block text-center mt-4 text-sm text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-arrow-left mr-1"></i> Retour
            </a>
        </div>
    </div>
    @endif

    {{-- ÉTAPE 1 — DÉTAILS ÉVÉNEMENT --}}
    @if($etape == 1)
    <div class="bg-white rounded-2xl shadow-lg p-8 pb-24">
        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-calendar" style="color: #C8102E;"></i>
            Détails de l'événement
        </h3>
        <div class="space-y-4">
            <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white flex-shrink-0"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-calendar-star text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">{{ $evenement->nom }}</h4>
                        <span class="text-xs px-2 py-0.5 rounded-full text-white font-medium"
                            style="background-color: #007A3D;">
                            {{ $evenement->typeEvenement->nom ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1"><i class="fa-solid fa-calendar mr-1"></i> Date</p>
                    <p class="font-semibold text-gray-800">
                        {{ \Carbon\Carbon::parse($evenement->date_debut)->format('d/m/Y') }}
                        @if($evenement->date_debut != $evenement->date_fin)
                        → {{ \Carbon\Carbon::parse($evenement->date_fin)->format('d/m/Y') }}
                        @endif
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1"><i class="fa-solid fa-clock mr-1"></i> Horaire</p>
                    <p class="font-semibold text-gray-800">{{ $evenement->heure_debut }} — {{ $evenement->heure_fin }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1"><i class="fa-solid fa-location-dot mr-1"></i> Lieu</p>
                    <p class="font-semibold text-gray-800">{{ $evenement->lieu }}</p>
                    <p class="text-xs text-gray-400">{{ $evenement->ville }}</p>
                </div>
                @if($evenement->nom_salle)
                <div class="bg-blue-50 rounded-xl p-4">
                    <p class="text-xs text-blue-500 mb-1"><i class="fa-solid fa-door-open mr-1"></i> Salle RDV</p>
                    <p class="font-semibold text-gray-800">{{ $evenement->nom_salle }}</p>
                    <p class="text-xs text-gray-400">{{ $evenement->nombre_tables }} tables</p>
                </div>
                @endif
            </div>
            <div class="rounded-xl p-4 {{ $evenement->type_paiement == 'gratuit' ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
                @if($evenement->type_paiement == 'gratuit')
                <p class="font-bold text-green-700"><i class="fa-solid fa-gift mr-1"></i> Événement gratuit</p>
                @else
                <p class="font-bold text-yellow-700">
                    {{ number_format($evenement->montant_inscription, 0, ',', ' ') }} FCFA
                </p>
                @endif
            </div>
        </div>
        <div class="flex justify-between mt-8">
            <button wire:click="precedent"
                class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Précédent
            </button>
            <button wire:click="suivant"
                class="px-6 py-3 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2"
                style="background-color: #C8102E;">
                Continuer <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- ÉTAPE 2 — INFORMATIONS PERSONNELLES --}}
    @if($etape == 2)
    <div class="bg-white rounded-2xl shadow-lg p-8 pb-24">
        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-user" style="color: #C8102E;"></i>
            Informations personnelles
        </h3>

        @if($estMembre && $entreprise)
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5">
            <p class="text-xs font-bold text-green-700 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-building"></i> Votre entreprise
            </p>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-xs text-gray-400">Entreprise</p>
                    <p class="font-semibold text-gray-800">{{ $entreprise->nom }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Secteur</p>
                    <p class="font-semibold text-gray-800">{{ $entreprise->secteur_activite }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom *</label>
                <input wire:model="nom" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Prénom *</label>
                <input wire:model="prenom" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                @error('prenom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Genre *</label>
                <select wire:model="genre"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                    <option value="">-- Choisir --</option>
                    <option value="homme">Homme</option>
                    <option value="femme">Femme</option>
                </select>
                @error('genre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                <input wire:model="telephone" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                    placeholder="+226 70 00 00 00">
                @error('telephone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            @if(!$estMembre)
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Email *</label>
                <input wire:model="email" type="email"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            @endif
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Fonction *</label>
                <select wire:model.live="fonction"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                    <option value="">-- Choisir --</option>
                    @foreach($fonctions as $f)
                    <option value="{{ $f }}">{{ $f }}</option>
                    @endforeach
                </select>
                @if($fonction == 'Autre')
                <input wire:model="fonction_autre" type="text"
                    class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                    placeholder="Précisez votre fonction...">
                @endif
                @error('fonction') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            @if(!$estMembre)
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Pays *</label>
                <select wire:model.live="pays"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                    <option value="">-- Choisir --</option>
                    @foreach($pays_liste as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                    @endforeach
                </select>
                @error('pays') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Ville *</label>
                @if($pays && count($villesDisponibles) > 1)
                <select wire:model="ville"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                    <option value="">-- Choisir --</option>
                    @foreach($villesDisponibles as $v)
                    <option value="{{ $v }}">{{ $v }}</option>
                    @endforeach
                </select>
                @else
                <input wire:model="ville" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                @endif
                @error('ville') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            @endif
        </div>

        <div class="flex justify-between mt-8">
            <button wire:click="precedent"
                class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Précédent
            </button>
            <button wire:click="suivant"
                class="px-6 py-3 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2"
                style="background-color: #C8102E;">
                Continuer <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- ÉTAPE 3 — ACTIVITÉ PROFESSIONNELLE --}}
    @if($etape == 3)
    <div class="bg-white rounded-2xl shadow-lg p-8 pb-24">
        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-briefcase" style="color: #C8102E;"></i>
            Activité professionnelle
        </h3>

        <div class="grid grid-cols-2 gap-4">
            @if(!$estMembre)
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Secteur d'activité *</label>
                <select wire:model.live="secteur_activite"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                    <option value="">-- Choisir --</option>
                    @foreach($secteurs as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
                @if($secteur_activite === 'Autre')
                <input wire:model="secteur_activite_autre" type="text"
                    class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                    placeholder="Précisez le secteur...">
                @endif
                @error('secteur_activite') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Sous-secteur</label>
                <input wire:model="sous_secteur" type="text"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                    placeholder="Ex: Céréales...">
            </div>
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Année de création *</label>
                <input wire:model="annee_creation" type="number" min="1900" max="{{ date('Y') }}"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                @error('annee_creation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Nombre de salariés *</label>
                <input wire:model="nombre_salaries" type="number" min="1"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                @error('nombre_salaries') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">CA export (%) *</label>
                <input wire:model="chiffre_affaires" type="number" min="0" max="100"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                @error('chiffre_affaires') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="col-span-2">
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Description des activités *</label>
                <textarea wire:model="description_activites" rows="3"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm resize-none"
                    placeholder="Décrivez vos activités principales..."></textarea>
                @error('description_activites') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="col-span-2">
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Principaux produits / Savoir-faire</label>
                <textarea wire:model="principaux_produits" rows="2"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm resize-none"
                    placeholder="Ex: Riz, logiciels, textile..."></textarea>
            </div>
            @endif
            <div class="col-span-2">
                <label class="block text-gray-600 text-sm font-medium mb-1.5">
                    Objectif de participation
                    <span class="text-gray-400 font-normal">(200 caractères max)</span>
                </label>
                <textarea wire:model="objectif_participation" rows="3" maxlength="200"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm resize-none"
                    placeholder="Qu'espérez-vous trouver à cet événement ?"></textarea>
                <p class="text-xs text-gray-400 mt-1 text-right">
                    {{ strlen($objectif_participation) }} / 200
                </p>
                @error('objectif_participation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex justify-between mt-8">
            <button wire:click="precedent"
                class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Précédent
            </button>
            <button wire:click="suivant"
                class="px-6 py-3 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2"
                style="background-color: #C8102E;">
                Continuer <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- ÉTAPE 4 — RECHERCHE DE PARTENARIAT --}}
    @if($etape == 4)
    <div class="bg-white rounded-2xl shadow-lg p-8 pb-24">
        <h3 class="text-xl font-bold text-gray-800 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-handshake" style="color: #C8102E;"></i>
            Recherche de partenariat
        </h3>
        <p class="text-gray-500 text-sm mb-6">
            Ces informations permettront au système de vous proposer les meilleurs matchs.
        </p>

        <div class="space-y-6">
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Zone géographique ciblée *</label>
                <select wire:model="zone_geographique"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                    <option value="">-- Choisir --</option>
                    @foreach($zonesGeographiques as $z)
                    <option value="{{ $z }}">{{ $z }}</option>
                    @endforeach
                </select>
                @error('zone_geographique') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-gray-600 text-sm font-medium mb-2">
                    Type de partenariat recherché *
                    <span class="text-gray-400 font-normal">(max 3 — {{ count($types_partenariat) }}/3)</span>
                </label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($typesPartenariatOptions as $option)
                    <button type="button"
                        wire:click="toggleTypePartenariat('{{ $option }}')"
                        class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm transition text-left
                            {{ in_array($option, $types_partenariat)
                                ? 'border-green-400 bg-green-50 text-green-700 font-medium'
                                : (count($types_partenariat) >= 3 && !in_array($option, $types_partenariat)
                                    ? 'border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed'
                                    : 'border-gray-200 hover:border-green-300 text-gray-600') }}">
                        <i class="fa-solid {{ in_array($option, $types_partenariat) ? 'fa-circle-check text-green-500' : 'fa-circle text-gray-300' }}"></i>
                        {{ $option }}
                    </button>
                    @endforeach
                </div>
                @if(in_array('Autre', $types_partenariat))
                <input wire:model="type_partenariat_autre" type="text"
                    class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                    placeholder="Précisez le type de partenariat...">
                @endif
                @error('types_partenariat') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-gray-600 text-sm font-medium mb-2">
                    Profil de partenaire recherché *
                    <span class="text-gray-400 font-normal">(max 3 — {{ count($profils_partenaire) }}/3)</span>
                </label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($profilsPartenariatOptions as $option)
                    <button type="button"
                        wire:click="toggleProfilPartenaire('{{ $option }}')"
                        class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm transition text-left
                            {{ in_array($option, $profils_partenaire)
                                ? 'border-blue-400 bg-blue-50 text-blue-700 font-medium'
                                : (count($profils_partenaire) >= 3 && !in_array($option, $profils_partenaire)
                                    ? 'border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed'
                                    : 'border-gray-200 hover:border-blue-300 text-gray-600') }}">
                        <i class="fa-solid {{ in_array($option, $profils_partenaire) ? 'fa-circle-check text-blue-500' : 'fa-circle text-gray-300' }}"></i>
                        {{ $option }}
                    </button>
                    @endforeach
                </div>
                @error('profils_partenaire') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-gray-600 text-sm font-medium mb-2">
                    Secteurs d'activité recherchés *
                    <span class="text-gray-400 font-normal">(max 3 — {{ count($secteurs_recherche) }}/3)</span>
                </label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($secteurs as $option)
                    <button type="button"
                        wire:click="toggleSecteurRecherche('{{ $option }}')"
                        class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm transition text-left
                            {{ in_array($option, $secteurs_recherche)
                                ? 'border-red-400 bg-red-50 text-red-700 font-medium'
                                : (count($secteurs_recherche) >= 3 && !in_array($option, $secteurs_recherche)
                                    ? 'border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed'
                                    : 'border-gray-200 hover:border-red-300 text-gray-600') }}">
                        <i class="fa-solid {{ in_array($option, $secteurs_recherche) ? 'fa-circle-check text-red-500' : 'fa-circle text-gray-300' }}"></i>
                        {{ $option }}
                    </button>
                    @endforeach
                </div>
                @if(in_array('Autre', $secteurs_recherche))
                <input wire:model="secteur_recherche_autre" type="text"
                    class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                    placeholder="Précisez le secteur recherché...">
                @endif
                @error('secteurs_recherche') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex justify-between mt-8">
            <button wire:click="precedent"
                class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Précédent
            </button>
            <button wire:click="suivant"
                class="px-6 py-3 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2"
                style="background-color: #C8102E;">
                Continuer <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- ÉTAPE 5 — DISPONIBILITÉS + CDD --}}
    @if($etape == 5)
    <div class="bg-white rounded-2xl shadow-lg p-8 pb-24">
        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-calendar-days" style="color: #C8102E;"></i>
            Disponibilités & Chef de Délégation
        </h3>

        <div class="mb-6">
            <p class="text-sm font-semibold text-gray-700 mb-3">
                Jours de disponibilité
                <span class="text-gray-400 font-normal">(optionnel)</span>
            </p>
            @if($isMultiJours)
            <div class="grid grid-cols-3 gap-3">
                @foreach($joursEvenement as $jour)
                <label class="cursor-pointer">
                    <input type="checkbox" wire:model="disponibilites" value="{{ $jour }}" class="hidden peer">
                    <div class="p-4 border-2 rounded-xl text-center transition
                        peer-checked:border-green-400 peer-checked:bg-green-50 hover:bg-gray-50 border-gray-200">
                        <p class="font-semibold text-sm text-gray-800">
                            {{ \Carbon\Carbon::parse($jour)->locale('fr')->translatedFormat('l') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($jour)->format('d/m/Y') }}</p>
                    </div>
                </label>
                @endforeach
            </div>
            @else
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-sm text-blue-700">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    Événement sur 1 jour —
                    <strong>{{ \Carbon\Carbon::parse($evenement->date_debut)->format('d/m/Y') }}</strong>
                </p>
            </div>
            @endif
            @error('disponibilites') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
            <p class="text-sm font-bold text-blue-700 mb-1 flex items-center gap-2">
                <i class="fa-solid fa-user-tie"></i>
                Chef de Délégation
                <span class="font-normal text-blue-500">(optionnel)</span>
            </p>
            <p class="text-xs text-blue-600 mb-3">
                Un Chef de Délégation peut vous aider à compléter votre inscription
                et valider votre préinscription.
            </p>
            <select wire:model="id_chef_delegation"
                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm bg-white">
                <option value="">-- Aucun Chef de Délégation --</option>
                @foreach($chefsDelegation as $cdd)
                <option value="{{ $cdd->id }}">{{ $cdd->name }}</option>
                @endforeach
            </select>
            @if($id_chef_delegation)
            <div class="mt-2 flex items-center gap-2 text-blue-700 text-xs bg-white border border-blue-200 rounded-lg px-3 py-2">
                <i class="fa-solid fa-circle-check text-blue-500"></i>
                Vous avez sélectionné un CDD. Il recevra une notification pour valider votre préinscription.
            </div>
            @endif
        </div>

        @if($estRepresentant && $standsDisponiblesEvenement->count() > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 mt-6">
            <p class="text-sm font-bold text-yellow-700 mb-1 flex items-center gap-2">
                <i class="fa-solid fa-store"></i>
                Réservation d'un stand
                <span class="font-normal text-yellow-600">(optionnel)</span>
            </p>
            <p class="text-xs text-yellow-600 mb-3">
                @if($evenement->type_paiement == 'gratuit')
                Les stands sont gratuits pour cet événement.
                @else
                Le tarif dépend du standing choisi.
                @endif
            </p>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <label class="cursor-pointer">
                    <input type="radio" wire:model="id_stand_choisi" value="" class="hidden peer">
                    <div class="p-3 border-2 rounded-xl text-center transition h-full flex flex-col items-center justify-center
                        peer-checked:border-gray-400 peer-checked:bg-gray-100 hover:bg-gray-50 border-gray-200">
                        <i class="fa-solid fa-ban text-gray-400 text-lg mb-1 block"></i>
                        <span class="text-sm font-medium text-gray-600">Aucun stand</span>
                    </div>
                </label>
                @foreach($standsDisponiblesEvenement as $stand)
                <label class="cursor-pointer">
                    <input type="radio" wire:model="id_stand_choisi" value="{{ $stand->id }}" class="hidden peer">
                    <div class="p-3 border-2 rounded-xl text-center transition
                        peer-checked:border-yellow-400 peer-checked:bg-yellow-50 hover:bg-gray-50 border-gray-200">
                        <p class="font-bold text-gray-800 text-sm">Stand N°{{ $stand->numero_stand }}</p>
                        @if($stand->standing == 'vip')
                        <span class="text-xs px-2 py-0.5 rounded-full text-white bg-yellow-500 font-medium inline-block mt-1">VIP</span>
                        @elseif($stand->standing == 'premium')
                        <span class="text-xs px-2 py-0.5 rounded-full text-white bg-blue-600 font-medium inline-block mt-1">Premium</span>
                        @else
                        <span class="text-xs px-2 py-0.5 rounded-full text-white font-medium inline-block mt-1" style="background-color: #007A3D;">Standard</span>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">{{ $stand->superficie }} m²</p>
                        @if($stand->prix_calcule > 0)
                        <p class="text-xs font-bold mt-1" style="color: #C8102E;">{{ number_format($stand->prix_calcule, 0, ',', ' ') }} FCFA</p>
                        @else
                        <p class="text-xs text-green-600 font-medium mt-1">Gratuit</p>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>
        </div>
        @endif

        <div class="flex justify-between mt-8">
            <button wire:click="precedent"
                class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Précédent
            </button>
            <button wire:click="suivant"
                class="px-6 py-3 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2"
                style="background-color: #C8102E;">
                Continuer <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- ÉTAPE 6 — CONFIRMATION --}}
    @if($etape == 6)
    <div class="bg-white rounded-2xl shadow-lg p-8 pb-24">
        <h3 class="text-xl font-bold text-gray-800 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-circle-check" style="color: #007A3D;"></i>
            Récapitulatif
        </h3>
        <p class="text-gray-500 text-sm mb-6">Vérifiez vos informations avant de confirmer.</p>

        <div class="space-y-4">
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <p class="text-xs font-bold text-green-700 mb-1"><i class="fa-solid fa-calendar mr-1"></i> Événement</p>
                <p class="font-bold text-gray-800">{{ $evenement->nom }}</p>
                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($evenement->date_debut)->format('d/m/Y') }} — {{ $evenement->ville }}</p>
            </div>

            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs font-bold text-gray-600 mb-3"><i class="fa-solid fa-user mr-1"></i> Informations personnelles</p>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-400 text-xs">Nom complet</span>
                        <p class="font-semibold">{{ $nom }} {{ $prenom }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Fonction</span>
                        <p class="font-semibold">{{ $fonction }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Téléphone</span>
                        <p class="font-semibold">{{ $telephone }}</p>
                    </div>
                    @if($email)
                    <div>
                        <span class="text-gray-400 text-xs">Email</span>
                        <p class="font-semibold">{{ $email }}</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($objectif_participation || $secteur_activite)
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs font-bold text-gray-600 mb-3"><i class="fa-solid fa-briefcase mr-1"></i> Activité</p>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    @if($secteur_activite)
                    <div>
                        <span class="text-gray-400 text-xs">Secteur</span>
                        <p class="font-semibold">{{ $secteur_activite }}</p>
                    </div>
                    @endif
                    @if($objectif_participation)
                    <div class="col-span-2">
                        <span class="text-gray-400 text-xs">Objectif</span>
                        <p class="font-semibold">{{ $objectif_participation }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="bg-blue-50 rounded-xl p-4">
                <p class="text-xs font-bold text-blue-700 mb-3"><i class="fa-solid fa-handshake mr-1"></i> Partenariat recherché</p>
                <div class="space-y-2">
                    @if($zone_geographique)
                    <div class="flex flex-wrap gap-1">
                        <span class="text-xs text-gray-500 mr-2">Zone :</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">{{ $zone_geographique }}</span>
                    </div>
                    @endif
                    @if(!empty($types_partenariat))
                    <div class="flex flex-wrap gap-1">
                        <span class="text-xs text-gray-500 mr-2">Type :</span>
                        @foreach($types_partenariat as $t)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">{{ $t }}</span>
                        @endforeach
                    </div>
                    @endif
                    @if(!empty($profils_partenaire))
                    <div class="flex flex-wrap gap-1">
                        <span class="text-xs text-gray-500 mr-2">Profil :</span>
                        @foreach($profils_partenaire as $p)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-medium">{{ $p }}</span>
                        @endforeach
                    </div>
                    @endif
                    @if(!empty($secteurs_recherche))
                    <div class="flex flex-wrap gap-1">
                        <span class="text-xs text-gray-500 mr-2">Secteurs :</span>
                        @foreach($secteurs_recherche as $s)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-medium">{{ $s }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            @if($id_chef_delegation)
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-xs font-bold text-blue-700 mb-1">
                    <i class="fa-solid fa-user-tie mr-1"></i> Chef de Délégation
                </p>
                @php $cddChoisi = $chefsDelegation->find($id_chef_delegation); @endphp
                <p class="font-semibold text-gray-800">{{ $cddChoisi?->name ?? '-' }}</p>
            </div>
            @endif

            @if($estRepresentant && $id_stand_choisi)
            @php $standChoisi = $standsDisponiblesEvenement->find($id_stand_choisi); @endphp
            @if($standChoisi)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <p class="text-xs font-bold text-yellow-700 mb-1">
                    <i class="fa-solid fa-store mr-1"></i> Stand réservé
                </p>
                <p class="font-semibold text-gray-800">
                    Stand N°{{ $standChoisi->numero_stand }} — {{ ucfirst($standChoisi->standing) }}
                    @if($standChoisi->prix_calcule > 0)
                    ({{ number_format($standChoisi->prix_calcule, 0, ',', ' ') }} FCFA)
                    @else
                    (Gratuit)
                    @endif
                </p>
            </div>
            @endif
            @endif

            @if($evenement->type_paiement == 'gratuit')
            <div class="bg-green-50 border border-green-300 rounded-xl p-4 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-green-500 text-2xl"></i>
                <div>
                    <p class="font-bold text-green-700">Événement gratuit</p>
                    <p class="text-xs text-green-600">Inscription validée automatiquement.</p>
                </div>
            </div>
            @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center gap-3">
                <i class="fa-solid fa-credit-card text-yellow-500 text-2xl"></i>
                <div>
                    <p class="font-bold text-yellow-700">
                        {{ number_format($evenement->montant_inscription, 0, ',', ' ') }} FCFA
                    </p>
                    <p class="text-xs text-yellow-600">Le paiement sera requis après confirmation.</p>
                </div>
            </div>
            @endif
        </div>

        <div class="flex justify-between mt-8">
            <button wire:click="precedent"
                class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Précédent
            </button>
            <button wire:click="confirmer"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-70 cursor-not-allowed"
                class="px-8 py-3 rounded-xl text-white font-bold transition hover:opacity-90 text-sm shadow-lg flex items-center gap-2"
                style="background-color: #007A3D;">
                <span wire:loading.remove>
                    <i class="fa-solid fa-circle-check mr-1"></i> Confirmer l'inscription
                </span>
                <span wire:loading>
                    <i class="fa-solid fa-spinner fa-spin mr-1"></i> Confirmation...
                </span>
            </button>
        </div>
    </div>
    @endif

</div>