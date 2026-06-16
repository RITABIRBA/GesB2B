<div class="min-h-screen flex" style="background-color: #f8f9fa;">

    {{-- 
         PARTIE GAUCHE — Présentation fixe
     --}}
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12 text-white"
        style="background: linear-gradient(135deg, #006B34 0%, #007A3D 50%, #005a2d 100%);">

        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo-ccibf.png') }}"
                alt="CCI-BF" class="w-12 h-12 object-contain rounded-xl">
            <div>
                <h1 class="text-2xl font-bold">GesB2B</h1>
                <p class="text-green-300 text-sm">CCI-BF Platform</p>
            </div>
        </div>

        <div>
            <h2 class="text-4xl font-bold mb-4 leading-tight">
                Inscription Entreprise
            </h2>
            <p class="text-green-200 text-lg mb-8">
                Inscrivez votre entreprise aux forums économiques B2B
            </p>

            {{-- Étapes sur la gauche --}}
            <div class="space-y-4">
                @php
                $etapesInfo = [
                    1 => ['icon' => 'fa-user-tie',   'label' => 'Vos informations personnelles'],
                    2 => ['icon' => 'fa-building',   'label' => 'Informations de l\'entreprise'],
                    3 => ['icon' => 'fa-handshake',  'label' => 'Profil partenaire recherché'],
                    4 => ['icon' => 'fa-calendar',   'label' => 'Vos disponibilités'],
                    5 => ['icon' => 'fa-circle-check','label' => 'Confirmation'],
                ];
                @endphp
                @foreach($etapesInfo as $num => $info)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition"
                        style="background-color: {{ $etape >= $num ? 'rgba(200,16,46,0.8)' : 'rgba(200,16,46,0.2)' }}">
                        @if($etape > $num)
                        <i class="fa-solid fa-check text-white text-xs"></i>
                        @else
                        <i class="fa-solid {{ $info['icon'] }} text-white text-xs"></i>
                        @endif
                    </div>
                    <span class="text-sm {{ $etape >= $num ? 'text-white font-semibold' : 'text-green-300' }}">
                        {{ $info['label'] }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="text-green-300 text-sm">
            © {{ date('Y') }} CCI-BF — Tous droits réservés
        </div>
    </div>

    {{-- 
         PARTIE DROITE — Formulaire par étapes
     --}}
    <div class="w-full lg:w-1/2 flex items-start justify-center p-8 overflow-y-auto">
        <div class="w-full max-w-lg">

            {{-- Logo mobile --}}
            <div class="lg:hidden flex items-center gap-3 mb-6 justify-center">
                <img src="{{ asset('images/logo-ccibf.png') }}"
                    alt="CCI-BF" class="w-12 h-12 object-contain rounded-xl">
                <h1 class="text-2xl font-bold text-gray-800">GesB2B</h1>
            </div>

            {{-- ← Barre de progression --}}
            @if($etape <= 5)
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-xl font-bold text-gray-800">
                        @if($etape == 1) Vos informations personnelles
                        @elseif($etape == 2) Informations de l'entreprise
                        @elseif($etape == 3) Profil partenaire recherché
                        @elseif($etape == 4) Vos disponibilités
                        @elseif($etape == 5) Confirmation
                        @endif
                    </h2>
                    <span class="text-sm text-gray-400 font-medium">{{ $etape }}/5</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500"
                        style="width: {{ ($etape / 5) * 100 }}%;
                               background: linear-gradient(90deg, #007A3D, #C8102E);">
                    </div>
                </div>
            </div>
            @endif

            {{-- 
                 ÉTAPE 1 — Informations personnelles du représentant
             --}}
            @if($etape == 1)
            <div class="space-y-5">

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-700 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i>
                    Vous serez le représentant principal de votre entreprise sur la plateforme.
                </div>

                {{-- Nom / Prénom --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom *</label>
                        <input wire:model="nom_responsable" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm"
                            placeholder="Votre nom">
                        @error('nom_responsable')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Prénom *</label>
                        <input wire:model="prenom_responsable" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm"
                            placeholder="Votre prénom">
                        @error('prenom_responsable')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Genre --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Genre *</label>
                    <select wire:model="genre"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm">
                        <option value="">-- Choisir --</option>
                        <option value="homme">Homme</option>
                        <option value="femme">Femme</option>
                    </select>
                    @error('genre')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Fonction --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fonction *</label>
                    <select wire:model.live="fonction_responsable"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm">
                        <option value="">-- Choisir votre fonction --</option>
                        @foreach($fonctions as $f)
                        <option value="{{ $f }}">{{ $f }}</option>
                        @endforeach
                    </select>
                    {{-- Saisie libre si "Autre" --}}
                    @if($fonction_responsable == 'Autre')
                    <input wire:model="fonction_autre" type="text"
                        class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm"
                        placeholder="Précisez votre fonction...">
                    @endif
                    @error('fonction_responsable')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email professionnel *</label>
                    <input wire:model="email" type="email"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm"
                        placeholder="contact@entreprise.com">
                    @error('email')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Téléphone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone *</label>
                    <input wire:model="contact" type="text"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm"
                        placeholder="Ex: +226 70 00 00 00">
                    @error('contact')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- IFU --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Numéro IFU de l'entreprise *
                    </label>
                    <input wire:model="ifu" type="text"
                        maxlength="9"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm font-mono uppercase"
                        placeholder="Ex: 12345678A">
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Format : 8 chiffres suivis d'une lettre (ex: 12345678A)
                    </p>
                    @error('ifu')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- CDD optionnel --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Chef de Délégation (CDD)
                        <span class="text-gray-400 font-normal">(optionnel)</span>
                    </label>
                    <select wire:model="id_cdd"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm">
                        <option value="">-- Aucun CDD --</option>
                        @foreach($cdds as $cdd)
                        <option value="{{ $cdd->id }}">{{ $cdd->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Le CDD peut vous accompagner dans votre inscription si besoin.
                    </p>
                </div>

                {{-- Mot de passe --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe *</label>
                        <input wire:model="password" type="password"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm"
                            placeholder="Min. 8 caractères">
                        @error('password')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirmer *</label>
                        <input wire:model="password_confirmation" type="password"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm"
                            placeholder="Répéter">
                    </div>
                </div>

                {{-- Navigation --}}
                <div class="flex justify-between items-center pt-2">
                    <a href="{{ route('login') }}"
                        class="text-sm text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Déjà inscrit ?
                    </a>
                    <button wire:click="suivant"
                        wire:loading.attr="disabled"
                        class="px-8 py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow flex items-center gap-2"
                        style="background-color: #C8102E;">
                        <span wire:loading.remove wire:target="suivant">
                            Continuer <i class="fa-solid fa-arrow-right"></i>
                        </span>
                        <span wire:loading wire:target="suivant">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>

            </div>
            @endif

            {{-- 
                 ÉTAPE 2 — Informations de l'entreprise
            --}}
            @if($etape == 2)
            <div class="space-y-5">

                {{-- Nom entreprise --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nom de l'entreprise *
                    </label>
                    <input wire:model="nom" type="text"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-400 text-sm"
                        placeholder="Ex: ABC SARL">
                    @error('nom')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Secteur / Sous-secteur --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Secteur d'activité *
                        </label>
                        <select wire:model="secteur_activite"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-400 text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($secteurs as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                        @error('secteur_activite')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Sous-secteur *
                        </label>
                        <input wire:model="sous_secteur" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-400 text-sm"
                            placeholder="Ex: Céréales...">
                        @error('sous_secteur')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Description des activités *
                    </label>
                    <textarea wire:model="description_activites" rows="3"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-400 text-sm resize-none"
                        placeholder="Décrivez brièvement vos activités..."></textarea>
                    @error('description_activites')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Produits --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Principaux produits / Savoir-faire
                        <span class="text-gray-400 font-normal">(optionnel)</span>
                    </label>
                    <textarea wire:model="principaux_produits" rows="2"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-400 text-sm resize-none"
                        placeholder="Ex: Maïs, Sorgho, Logiciels..."></textarea>
                </div>

                {{-- Pays / Ville --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Pays *</label>
                        <select wire:model.live="pays"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-400 text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($pays_liste as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                            @endforeach
                        </select>
                        @error('pays')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Ville *</label>
                        @if($pays && count($villesDisponibles) > 1)
                        <select wire:model="ville"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-400 text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($villesDisponibles as $v)
                            <option value="{{ $v }}">{{ $v }}</option>
                            @endforeach
                        </select>
                        @else
                        <input wire:model="ville" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-400 text-sm"
                            placeholder="Votre ville">
                        @endif
                        @error('ville')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Année / Salariés / CA --}}
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Année création *
                        </label>
                        <input wire:model="annee_creation" type="number"
                            min="1900" max="{{ date('Y') }}"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-400 text-sm"
                            placeholder="2010">
                        @error('annee_creation')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Salariés *
                        </label>
                        <input wire:model="nombre_salaries" type="number" min="1"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-400 text-sm"
                            placeholder="Ex: 25">
                        @error('nombre_salaries')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            CA export % *
                        </label>
                        <div class="relative">
                            <input wire:model="chiffre_affaires" type="number"
                                min="0" max="100" step="0.1"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-400 text-sm pr-8"
                                placeholder="30">
                            <span class="absolute right-3 top-3 text-gray-400 text-xs">%</span>
                        </div>
                        @error('chiffre_affaires')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Navigation --}}
                <div class="flex justify-between pt-2">
                    <button wire:click="precedent"
                        class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Précédent
                    </button>
                    <button wire:click="suivant"
                        wire:loading.attr="disabled"
                        class="px-8 py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow flex items-center gap-2"
                        style="background-color: #C8102E;">
                        <span wire:loading.remove wire:target="suivant">
                            Continuer <i class="fa-solid fa-arrow-right"></i>
                        </span>
                        <span wire:loading wire:target="suivant">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>

            </div>
            @endif

            {{-- 
                 ÉTAPE 3 — Profil partenaire recherché
             --}}
            @if($etape == 3)
            <div class="space-y-5">

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-700 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i>
                    Ces informations permettront au système de vous proposer
                    les meilleurs partenaires lors du forum.
                </div>

                {{-- Zone géographique --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Zone géographique ciblée *
                    </label>
                    <select wire:model="zone_geographique"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                        <option value="">-- Choisir --</option>
                        @foreach($zones as $z)
                        <option value="{{ $z }}">{{ $z }}</option>
                        @endforeach
                    </select>
                    @error('zone_geographique')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Secteurs recherchés (max 3) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
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
                    @error('secteur_recherche_autre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                    @error('secteurs_recherche') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Type de partenariat (max 3) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Type de partenariat recherché *
                        <span class="text-gray-400 font-normal">(max 3 — {{ count($types_partenariat) }}/3)</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($types_partenaires as $option)
                        <button type="button"
                            wire:click="toggleTypePartenariat('{{ $option }}')"
                            class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm transition text-left
                                {{ in_array($option, $types_partenariat)
                                    ? 'border-blue-400 bg-blue-50 text-blue-700 font-medium'
                                    : (count($types_partenariat) >= 3 && !in_array($option, $types_partenariat)
                                        ? 'border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed'
                                        : 'border-gray-200 hover:border-blue-300 text-gray-600') }}">
                            <i class="fa-solid {{ in_array($option, $types_partenariat) ? 'fa-circle-check text-blue-500' : 'fa-circle text-gray-300' }}"></i>
                            {{ $option }}
                        </button>
                        @endforeach
                    </div>
                    @if(in_array('Autre', $types_partenariat))
                    <input wire:model="type_partenariat_autre" type="text"
                        class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                        placeholder="Précisez le type de partenariat...">
                    @error('type_partenariat_autre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                    @error('types_partenariat') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Profil de partenaire (max 3) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Profil de partenaire recherché
                        <span class="text-gray-400 font-normal">(max 3 — {{ count($profils_partenaire) }}/3)</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($profilsPartenariatOptions as $option)
                        <button type="button"
                            wire:click="toggleProfilPartenaire('{{ $option }}')"
                            class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm transition text-left
                                {{ in_array($option, $profils_partenaire)
                                    ? 'border-purple-400 bg-purple-50 text-purple-700 font-medium'
                                    : (count($profils_partenaire) >= 3 && !in_array($option, $profils_partenaire)
                                        ? 'border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed'
                                        : 'border-gray-200 hover:border-purple-300 text-gray-600') }}">
                            <i class="fa-solid {{ in_array($option, $profils_partenaire) ? 'fa-circle-check text-purple-500' : 'fa-circle text-gray-300' }}"></i>
                            {{ $option }}
                        </button>
                        @endforeach
                    </div>
                    @if(in_array('Autre', $profils_partenaire))
                    <input wire:model="profil_partenaire_autre" type="text"
                        class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                        placeholder="Précisez le profil de partenaire...">
                    @error('profil_partenaire_autre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                    @error('profils_partenaire') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Navigation --}}
                <div class="flex justify-between pt-2">
                    <button wire:click="precedent"
                        class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Précédent
                    </button>
                    <button wire:click="suivant"
                        wire:loading.attr="disabled"
                        class="px-8 py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow flex items-center gap-2"
                        style="background-color: #C8102E;">
                        <span wire:loading.remove wire:target="suivant">
                            Continuer <i class="fa-solid fa-arrow-right"></i>
                        </span>
                        <span wire:loading wire:target="suivant">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>

            </div>
            @endif

            {{-- 
                 ÉTAPE 4 — Disponibilités
             --}}
            @if($etape == 4)
            <div class="space-y-5">

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-xs text-yellow-700 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                    Précisez les jours où vous serez présent au forum.
                    Le système ne vous programmera des RDV que sur ces jours.
                    Si vous ne précisez rien, vous serez considéré disponible tous les jours.
                </div>

                {{-- Message si pas d'événement sélectionné --}}
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5 text-center">
                    <i class="fa-solid fa-calendar-days text-4xl text-gray-300 mb-3 block"></i>
                    <p class="text-gray-600 font-medium mb-1">
                        Disponibilités à préciser lors de l'inscription à un événement
                    </p>
                    <p class="text-xs text-gray-400">
                        Une fois votre compte créé et votre entreprise validée,
                        vous pourrez vous inscrire aux forums et préciser
                        vos jours de disponibilité.
                    </p>
                </div>

                {{-- Navigation --}}
                <div class="flex justify-between pt-2">
                    <button wire:click="precedent"
                        class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Précédent
                    </button>
                    <button wire:click="suivant"
                        class="px-8 py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow flex items-center gap-2"
                        style="background-color: #C8102E;">
                        Continuer <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>

            </div>
            @endif

            {{-- 
                 ÉTAPE 5 — Confirmation / Récapitulatif
             --}}
            @if($etape == 5)
            <div class="space-y-4">

                <p class="text-gray-500 text-sm">
                    Vérifiez vos informations avant de valider votre inscription.
                </p>

                {{-- Infos personnelles --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-blue-700 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-user-tie"></i>
                        Représentant
                    </p>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-xs text-gray-400">Nom complet</span>
                            <p class="font-semibold text-gray-800">
                                {{ $nom_responsable }} {{ $prenom_responsable }}
                                @if($genre == 'femme')
                                <span class="text-xs text-gray-400">(Mme)</span>
                                @elseif($genre == 'homme')
                                <span class="text-xs text-gray-400">(M.)</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400">Fonction</span>
                            <p class="font-semibold text-gray-800">{{ $fonction_responsable }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400">Email</span>
                            <p class="font-semibold text-gray-800">{{ $email }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400">Téléphone</span>
                            <p class="font-semibold text-gray-800">{{ $contact }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400">IFU</span>
                            <p class="font-semibold text-gray-800 font-mono">{{ strtoupper($ifu) }}</p>
                        </div>
                        @if($id_cdd)
                        <div>
                            <span class="text-xs text-gray-400">CDD choisi</span>
                            <p class="font-semibold text-gray-800">
                                {{ \App\Models\User::find($id_cdd)?->name ?? '-' }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Infos entreprise --}}
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-green-700 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-building"></i>
                        Entreprise
                    </p>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-xs text-gray-400">Nom</span>
                            <p class="font-semibold text-gray-800">{{ $nom }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400">Secteur</span>
                            <p class="font-semibold text-gray-800">
                                {{ $secteur_activite }}
                                @if($sous_secteur) / {{ $sous_secteur }} @endif
                            </p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400">Pays / Ville</span>
                            <p class="font-semibold text-gray-800">{{ $pays }} / {{ $ville }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400">Créée en</span>
                            <p class="font-semibold text-gray-800">{{ $annee_creation }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400">Salariés</span>
                            <p class="font-semibold text-gray-800">{{ $nombre_salaries }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400">CA export</span>
                            <p class="font-semibold text-gray-800">{{ $chiffre_affaires }}%</p>
                        </div>
                    </div>
                </div>

                {{-- Profil recherché --}}
                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-purple-700 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-handshake"></i>
                        Partenaire recherché
                    </p>
                    <div class="space-y-2">
                        <div class="flex flex-wrap gap-1 items-center">
                            <span class="text-xs text-gray-500 mr-1">Zone :</span>
                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                                <i class="fa-solid fa-location-dot mr-1"></i>{{ $zone_geographique }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-1 items-center">
                            <span class="text-xs text-gray-500 mr-1">Secteurs :</span>
                            @foreach($secteurs_recherche as $s)
                            <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                                <i class="fa-solid fa-tag mr-1"></i>{{ $s == 'Autre' ? $secteur_recherche_autre : $s }}
                            </span>
                            @endforeach
                        </div>
                        <div class="flex flex-wrap gap-1 items-center">
                            <span class="text-xs text-gray-500 mr-1">Types :</span>
                            @foreach($types_partenariat as $t)
                            <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">
                                <i class="fa-solid fa-handshake mr-1"></i>{{ $t == 'Autre' ? $type_partenariat_autre : $t }}
                            </span>
                            @endforeach
                        </div>
                        @if(!empty($profils_partenaire))
                        <div class="flex flex-wrap gap-1 items-center">
                            <span class="text-xs text-gray-500 mr-1">Profils :</span>
                            @foreach($profils_partenaire as $p)
                            <span class="text-xs px-2 py-1 rounded-full bg-orange-100 text-orange-700 font-medium">
                                <i class="fa-solid fa-user-tag mr-1"></i>{{ $p == 'Autre' ? $profil_partenaire_autre : $p }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Avertissement validation --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-xs text-yellow-700 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                    Votre entreprise sera soumise à validation avant d'être active sur la plateforme.
                </div>

                {{-- Navigation --}}
                <div class="flex justify-between pt-2">
                    <button wire:click="precedent"
                        class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Précédent
                    </button>
                    <button wire:click="sinscrire"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-8 py-3 rounded-xl text-white font-bold text-sm transition hover:opacity-90 shadow-lg flex items-center gap-2"
                        style="background-color: #007A3D;">
                        <span wire:loading.remove wire:target="sinscrire">
                            <i class="fa-solid fa-circle-check mr-1"></i>
                            Confirmer l'inscription
                        </span>
                        <span wire:loading wire:target="sinscrire">
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                            Inscription en cours...
                        </span>
                    </button>
                </div>

            </div>
            @endif

            {{-- 
                 ÉTAPE 6 — Succès
            --}}
            @if($etape == 6)
            <div class="text-center py-8">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6"
                    style="background-color: #e6f4ed;">
                    <i class="fa-solid fa-circle-check text-5xl" style="color: #007A3D;"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">
                    Inscription réussie !
                </h3>
                <p class="text-gray-500 mb-6">
                    Votre entreprise <strong>{{ $nom }}</strong> est en attente de validation.
                </p>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-left mb-6">
                    <p class="text-sm font-bold text-blue-700 mb-2">
                        <i class="fa-solid fa-list-check mr-1"></i>
                        Prochaines étapes :
                    </p>
                    <ol class="space-y-2 text-xs text-blue-600">
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                style="background-color: #C8102E;">1</span>
                            Connectez-vous avec votre email et mot de passe
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                style="background-color: #C8102E;">2</span>
                            Attendez la validation de l'administration
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                style="background-color: #C8102E;">3</span>
                            Ajoutez les membres de votre entreprise
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                style="background-color: #C8102E;">4</span>
                            Inscrivez-vous à un forum et émettez vos souhaits
                        </li>
                    </ol>
                </div>

                <a href="{{ route('login') }}"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow flex items-center justify-center gap-2"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Se connecter maintenant
                </a>
            </div>
            @endif

        </div>
    </div>

</div>