<div>

    {{-- BARRE DE PROGRESSION --}}
    @if($etape > 0)
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between">

            <div class="flex flex-col items-center flex-1">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2 transition
                    {{ $etape >= 1 ? 'text-white border-transparent' : 'text-gray-400 border-gray-300' }}"
                    style="{{ $etape >= 1 ? 'background-color: #C8102E;' : '' }}">
                    @if($etape > 1) <i class="fa-solid fa-check"></i> @else 1 @endif
                </div>
                <p class="text-xs mt-1 font-medium {{ $etape >= 1 ? 'text-gray-800' : 'text-gray-400' }}">Détails</p>
            </div>

            <div class="flex-1 h-0.5 mb-4 {{ $etape >= 2 ? '' : 'bg-gray-200' }}"
                style="{{ $etape >= 2 ? 'background-color: #C8102E;' : '' }}"></div>

            <div class="flex flex-col items-center flex-1">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2 transition
                    {{ $etape >= 2 ? 'text-white border-transparent' : 'text-gray-400 border-gray-300' }}"
                    style="{{ $etape >= 2 ? 'background-color: #C8102E;' : '' }}">
                    @if($etape > 2) <i class="fa-solid fa-check"></i> @else 2 @endif
                </div>
                <p class="text-xs mt-1 font-medium {{ $etape >= 2 ? 'text-gray-800' : 'text-gray-400' }}">Mes infos</p>
            </div>

            <div class="flex-1 h-0.5 mb-4 {{ $etape >= 3 ? '' : 'bg-gray-200' }}"
                style="{{ $etape >= 3 ? 'background-color: #C8102E;' : '' }}"></div>

            <div class="flex flex-col items-center flex-1">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2 transition
                    {{ $etape >= 3 ? 'text-white border-transparent' : 'text-gray-400 border-gray-300' }}"
                    style="{{ $etape >= 3 ? 'background-color: #C8102E;' : '' }}">
                    @if($etape > 3) <i class="fa-solid fa-check"></i> @else 3 @endif
                </div>
                <p class="text-xs mt-1 font-medium {{ $etape >= 3 ? 'text-gray-800' : 'text-gray-400' }}">Partenaire</p>
            </div>

            <div class="flex-1 h-0.5 mb-4 {{ $etape >= 4 ? '' : 'bg-gray-200' }}"
                style="{{ $etape >= 4 ? 'background-color: #C8102E;' : '' }}"></div>

            <div class="flex flex-col items-center flex-1">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2 transition
                    {{ $etape >= 4 ? 'text-white border-transparent' : 'text-gray-400 border-gray-300' }}"
                    style="{{ $etape >= 4 ? 'background-color: #C8102E;' : '' }}">
                    4
                </div>
                <p class="text-xs mt-1 font-medium {{ $etape >= 4 ? 'text-gray-800' : 'text-gray-400' }}">Confirmation</p>
            </div>

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
                {{ \Carbon\Carbon::parse($evenement->date_debut)->locale('fr')->translatedFormat('d/m/Y') }}
                @if($evenement->date_debut != $evenement->date_fin)
                → {{ \Carbon\Carbon::parse($evenement->date_fin)->locale('fr')->translatedFormat('d/m/Y') }}
                @endif
            </p>
        </div>
        <div class="p-8">
            <p class="text-gray-600 text-center mb-8">
                Vous allez suivre <strong>4 étapes</strong> pour finaliser votre inscription.
            </p>
            <div class="space-y-4 mb-8">
                <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                        style="background-color: #C8102E;">1</div>
                    <div>
                        <p class="font-semibold text-gray-800">Détails de l'événement</p>
                        <p class="text-xs text-gray-500">Consultez les informations de l'événement</p>
                    </div>
                    <i class="fa-solid fa-calendar text-gray-300 ml-auto text-xl"></i>
                </div>
                <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                        style="background-color: #C8102E;">2</div>
                    <div>
                        <p class="font-semibold text-gray-800">Vos informations</p>
                        <p class="text-xs text-gray-500">Complétez votre profil professionnel</p>
                    </div>
                    <i class="fa-solid fa-user text-gray-300 ml-auto text-xl"></i>
                </div>
                <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                        style="background-color: #C8102E;">3</div>
                    <div>
                        <p class="font-semibold text-gray-800">Profil partenaire recherché</p>
                        <p class="text-xs text-gray-500">Définissez le type de partenaire que vous cherchez</p>
                    </div>
                    <i class="fa-solid fa-handshake text-gray-300 ml-auto text-xl"></i>
                </div>
                <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                        style="background-color: #C8102E;">4</div>
                    <div>
                        <p class="font-semibold text-gray-800">Confirmation</p>
                        <p class="text-xs text-gray-500">Récapitulatif et validation finale</p>
                    </div>
                    <i class="fa-solid fa-circle-check text-gray-300 ml-auto text-xl"></i>
                </div>
            </div>
            <button wire:click="commencer"
                class="w-full py-4 rounded-xl text-white font-bold text-lg transition hover:opacity-90 shadow-lg flex items-center justify-center gap-3"
                style="background-color: #C8102E;">
                <i class="fa-solid fa-play"></i>
                Commencer l'inscription
            </button>
            <a href="{{ route('participant.dashboard') }}"
                class="block text-center mt-4 text-sm text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-arrow-left mr-1"></i> Retour au dashboard
            </a>
        </div>
    </div>
    @endif

    {{-- ÉTAPE 1 — DÉTAILS ÉVÉNEMENT --}}
    @if($etape == 1)
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-calendar" style="color: #C8102E;"></i>
            Détails de l'événement
        </h3>
        <div class="space-y-4">
            <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white"
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
                        {{ \Carbon\Carbon::parse($evenement->date_debut)->locale('fr')->translatedFormat('d/m/Y') }}
                        @if($evenement->date_debut != $evenement->date_fin)
                        → {{ \Carbon\Carbon::parse($evenement->date_fin)->locale('fr')->translatedFormat('d/m/Y') }}
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
                <p class="text-xs font-semibold mb-1 {{ $evenement->type_paiement == 'gratuit' ? 'text-green-700' : 'text-yellow-700' }}">
                    <i class="fa-solid fa-money-bill mr-1"></i> Paiement
                </p>
                @if($evenement->type_paiement == 'gratuit')
                <p class="font-bold text-green-700"><i class="fa-solid fa-gift mr-1"></i> Événement gratuit</p>
                @elseif($evenement->type_paiement == 'par_participant')
                <p class="font-bold text-yellow-700">{{ number_format($evenement->montant_inscription, 0, ',', ' ') }} FCFA par participant</p>
                @else
                <p class="font-bold text-yellow-700">{{ number_format($evenement->montant_inscription, 0, ',', ' ') }} FCFA par entreprise</p>
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

    {{-- ÉTAPE 2 — MES INFORMATIONS --}}
    @if($etape == 2)
    <div class="bg-white rounded-2xl shadow-lg p-8">

        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-user" style="color: #C8102E;"></i>
            Vos informations
        </h3>

        <div class="space-y-6">

            {{-- Infos personnelles --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                <p class="text-xs font-bold text-blue-700 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-user-tie"></i>
                    Informations personnelles
                </p>
                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom *</label>
                        <input wire:model="nom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                            placeholder="Votre nom">
                        @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Prénom *</label>
                        <input wire:model="prenom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                            placeholder="Votre prénom">
                        @error('prenom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Email *</label>
                        <input wire:model="email" type="email"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                            placeholder="votre@email.com">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                        <input wire:model="telephone" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                            placeholder="Ex: +226 70 00 00 00">
                        @error('telephone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- ← Fonction avec liste + saisie libre --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Fonction *</label>
                        <select wire:model.live="fonction"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                            <option value="">-- Choisir --</option>
                            @foreach($fonctions as $f)
                            <option value="{{ $f }}">{{ $f }}</option>
                            @endforeach
                        </select>
                        {{-- ← Saisie libre si Autre --}}
                        @if($fonction == 'Autre')
                        <input wire:model="fonction_autre" type="text"
                            class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                            placeholder="Précisez votre fonction...">
                        @endif
                        @error('fonction') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Pays *</label>
                        <select wire:model.live="pays"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
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
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                            <option value="">-- Choisir --</option>
                            @foreach($villesDisponibles as $v)
                            <option value="{{ $v }}">{{ $v }}</option>
                            @endforeach
                        </select>
                        @else
                        <input wire:model="ville" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                            placeholder="Votre ville">
                        @endif
                        @error('ville') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                </div>
            </div>

            {{-- Infos entreprise --}}
            <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                <p class="text-xs font-bold text-green-700 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-building"></i>
                    Informations sur votre entreprise
                </p>
                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Secteur d'activité *</label>
                        <select wire:model="secteur_activite"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                            <option value="">-- Choisir --</option>
                            @foreach($secteurs as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                        @error('secteur_activite') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Sous-secteur *</label>
                        <input wire:model="sous_secteur" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                            placeholder="Ex: Agro-alimentaire...">
                        @error('sous_secteur') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Description des activités *</label>
                        <textarea wire:model="description_activites" rows="3"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white resize-none"
                            placeholder="Décrivez vos activités principales..."></textarea>
                        @error('description_activites') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Principaux produits / Savoir-faire
                            <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <textarea wire:model="principaux_produits" rows="2"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white resize-none"
                            placeholder="Ex: Logiciels de gestion, Céréales..."></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Année de création *</label>
                        <input wire:model="annee_creation" type="number"
                            min="1900" max="{{ date('Y') }}"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                            placeholder="Ex: 2010">
                        @error('annee_creation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nombre de salariés *</label>
                        <input wire:model="nombre_salaries" type="number" min="1"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                            placeholder="Ex: 25">
                        @error('nombre_salaries') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Part du chiffre d'affaires à l'export (%) *
                        </label>
                        <div class="relative">
                            <input wire:model="chiffre_affaires" type="number"
                                min="0" max="100" step="0.1"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white pr-10"
                                placeholder="Ex: 30">
                            <span class="absolute right-4 top-3 text-gray-400 text-sm">%</span>
                        </div>
                        @error('chiffre_affaires') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                </div>
            </div>

            {{-- Disponibilités si multi-jours --}}
            @if($isMultiJours)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                <p class="text-xs font-bold text-yellow-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days"></i>
                    Vos disponibilités *
                    <span class="font-normal text-yellow-600">(cochez les jours où vous serez présent)</span>
                </p>
                <div class="grid grid-cols-3 gap-3">
                    @foreach($joursEvenement as $jour)
                    <label class="cursor-pointer">
                        <input type="checkbox"
                            wire:model="disponibilites"
                            value="{{ $jour }}"
                            class="hidden peer">
                        <div class="p-3 border-2 rounded-xl text-center transition
                            peer-checked:border-green-400 peer-checked:bg-green-50
                            hover:bg-white border-gray-200 bg-white">
                            <p class="font-semibold text-sm text-gray-800">
                                {{ \Carbon\Carbon::parse($jour)->locale('fr')->translatedFormat('l') }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($jour)->format('d/m/Y') }}
                            </p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('disponibilites')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
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

    {{-- ÉTAPE 3 — PROFIL PARTENAIRE --}}
    @if($etape == 3)
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h3 class="text-xl font-bold text-gray-800 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-handshake" style="color: #C8102E;"></i>
            Profil partenaire recherché
        </h3>
        <p class="text-gray-500 text-sm mb-6">
            Ces informations nous aideront à vous proposer les meilleurs matchs.
        </p>
        <div class="space-y-5">

            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Secteur d'activité recherché *</label>
                <select wire:model="secteur_recherche"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                    <option value="">-- Choisir --</option>
                    @foreach($secteurs as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
                @error('secteur_recherche') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Zone géographique *</label>
                <select wire:model="zone_geographique"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                    <option value="">-- Choisir --</option>
                    @foreach($zones as $z)
                    <option value="{{ $z }}">{{ $z }}</option>
                    @endforeach
                </select>
                @error('zone_geographique') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-gray-600 text-sm font-medium mb-3">Type de partenaire recherché *</label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach($types_partenaires as $type)
                    <label class="cursor-pointer">
                        <input type="radio" wire:model="type_partenaire" value="{{ $type }}" class="hidden peer">
                        <div class="p-3 border-2 rounded-xl text-center transition text-sm
                            peer-checked:border-red-400 peer-checked:bg-red-50 peer-checked:text-red-700
                            hover:bg-gray-50 border-gray-200 text-gray-600">
                            {{ $type }}
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('type_partenaire') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
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

    {{-- ÉTAPE 4 — CONFIRMATION --}}
    @if($etape == 4)
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h3 class="text-xl font-bold text-gray-800 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-circle-check" style="color: #007A3D;"></i>
            Récapitulatif
        </h3>
        <p class="text-gray-500 text-sm mb-6">Vérifiez vos informations avant de confirmer.</p>

        <div class="space-y-4">

            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <p class="text-xs font-bold text-green-700 mb-2">
                    <i class="fa-solid fa-calendar mr-1"></i> Événement
                </p>
                <p class="font-bold text-gray-800">{{ $evenement->nom }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ \Carbon\Carbon::parse($evenement->date_debut)->locale('fr')->translatedFormat('d/m/Y') }}
                    — {{ $evenement->ville }}
                </p>
            </div>

            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs font-bold text-gray-600 mb-3">
                    <i class="fa-solid fa-user mr-1"></i> Vos informations
                </p>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-400 text-xs">Nom complet</span>
                        <p class="font-semibold text-gray-800">{{ $nom }} {{ $prenom }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Fonction</span>
                        <p class="font-semibold text-gray-800">{{ $fonction }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Email</span>
                        <p class="font-semibold text-gray-800">{{ $email }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Téléphone</span>
                        <p class="font-semibold text-gray-800">{{ $telephone }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Pays / Ville</span>
                        <p class="font-semibold text-gray-800">{{ $pays }} / {{ $ville }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Secteur</span>
                        <p class="font-semibold text-gray-800">{{ $secteur_activite }} — {{ $sous_secteur }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Année création</span>
                        <p class="font-semibold text-gray-800">{{ $annee_creation }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Salariés / CA export</span>
                        <p class="font-semibold text-gray-800">{{ $nombre_salaries }} / {{ $chiffre_affaires }}%</p>
                    </div>
                </div>

                @if($isMultiJours && !empty($disponibilites))
                <div class="mt-3">
                    <span class="text-gray-400 text-xs">Disponibilités</span>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($disponibilites as $dispo)
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                            {{ \Carbon\Carbon::parse($dispo)->locale('fr')->translatedFormat('l d/m/Y') }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="bg-blue-50 rounded-xl p-4">
                <p class="text-xs font-bold text-blue-700 mb-3">
                    <i class="fa-solid fa-handshake mr-1"></i> Partenaire recherché
                </p>
                <div class="grid grid-cols-3 gap-2 text-sm">
                    <div>
                        <span class="text-gray-400 text-xs">Secteur</span>
                        <p class="font-semibold text-gray-800">{{ $secteur_recherche }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Zone</span>
                        <p class="font-semibold text-gray-800">{{ $zone_geographique }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Type</span>
                        <p class="font-semibold text-gray-800">{{ $type_partenaire }}</p>
                    </div>
                </div>
            </div>

            @if($evenement->type_paiement == 'gratuit')
            <div class="bg-green-50 border border-green-300 rounded-xl p-4 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-green-500 text-2xl"></i>
                <div>
                    <p class="font-bold text-green-700">Événement gratuit</p>
                    <p class="text-xs text-green-600">Votre inscription sera validée automatiquement.</p>
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
                    <i class="fa-solid fa-circle-check mr-1"></i>
                    Confirmer l'inscription
                </span>
                <span wire:loading>
                    <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                    Confirmation...
                </span>
            </button>
        </div>
    </div>
    @endif

</div>