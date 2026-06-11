<div>

    {{-- 
         BARRE DE PROGRESSION
    --}}
    @if($etape > 0)
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between">

            @php
            $etapes = [
                1 => 'Détails',
                2 => $estMembre ? 'Mes infos' : 'Infos & Entreprise',
                3 => 'Partenaire',
                4 => 'Disponibilités',
                5 => 'Confirmation',
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
    @endif

    {{-- 
         ÉTAPE 0 — ACCUEIL
    --}}
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

            {{-- Info selon le mode --}}
            @if($estMembre && $entreprise)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0"
                    style="background-color: #007A3D;">
                    {{ strtoupper(substr($entreprise->nom, 0, 1)) }}
                </div>
                <div>
                    <p class="text-xs text-gray-400">Vous représentez</p>
                    <p class="font-bold text-gray-800">{{ $entreprise->nom }}</p>
                    <p class="text-xs text-gray-500">
                        {{ $entreprise->secteur_activite }}
                        · {{ $entreprise->ville }}, {{ $entreprise->pays }}
                    </p>
                </div>
            </div>
            @endif

            <p class="text-gray-600 text-center mb-6">
                Vous allez suivre <strong>5 étapes</strong> pour finaliser votre inscription.
            </p>

            <div class="space-y-3 mb-8">

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
                        <p class="font-semibold text-gray-800">
                            @if($estMembre) Vos informations personnelles
                            @else Vos informations + entreprise
                            @endif
                        </p>
                        <p class="text-xs text-gray-500">
                            @if($estMembre)
                            Vérifiez vos informations (infos entreprise déjà remplies)
                            @else
                            Complétez votre profil et les infos de votre entreprise
                            @endif
                        </p>
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
                        <p class="font-semibold text-gray-800">Disponibilités</p>
                        <p class="text-xs text-gray-500">Précisez les jours où vous serez présent</p>
                    </div>
                    <i class="fa-solid fa-calendar-days text-gray-300 ml-auto text-xl"></i>
                </div>

                <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                        style="background-color: #C8102E;">5</div>
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

            <a href="{{ route($loop ?? 'dashboard') }}"
                onclick="window.history.back(); return false;"
                class="block text-center mt-4 text-sm text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-arrow-left mr-1"></i> Retour
            </a>

        </div>
    </div>
    @endif

    {{--
         ÉTAPE 1 — DÉTAILS ÉVÉNEMENT
    = --}}
    @if($etape == 1)
    <div class="bg-white rounded-2xl shadow-lg p-8">
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
                    <p class="text-xs text-gray-500 mb-1">
                        <i class="fa-solid fa-calendar mr-1"></i> Date
                    </p>
                    <p class="font-semibold text-gray-800">
                        {{ \Carbon\Carbon::parse($evenement->date_debut)->locale('fr')->translatedFormat('d/m/Y') }}
                        @if($evenement->date_debut != $evenement->date_fin)
                        → {{ \Carbon\Carbon::parse($evenement->date_fin)->locale('fr')->translatedFormat('d/m/Y') }}
                        @endif
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1">
                        <i class="fa-solid fa-clock mr-1"></i> Horaire
                    </p>
                    <p class="font-semibold text-gray-800">
                        {{ $evenement->heure_debut }} — {{ $evenement->heure_fin }}
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1">
                        <i class="fa-solid fa-location-dot mr-1"></i> Lieu
                    </p>
                    <p class="font-semibold text-gray-800">{{ $evenement->lieu }}</p>
                    <p class="text-xs text-gray-400">{{ $evenement->ville }}</p>
                </div>
                @if($evenement->nom_salle)
                <div class="bg-blue-50 rounded-xl p-4">
                    <p class="text-xs text-blue-500 mb-1">
                        <i class="fa-solid fa-door-open mr-1"></i> Salle RDV
                    </p>
                    <p class="font-semibold text-gray-800">{{ $evenement->nom_salle }}</p>
                    <p class="text-xs text-gray-400">{{ $evenement->nombre_tables }} tables</p>
                </div>
                @endif
            </div>

            <div class="rounded-xl p-4
                {{ $evenement->type_paiement == 'gratuit'
                    ? 'bg-green-50 border border-green-200'
                    : 'bg-yellow-50 border border-yellow-200' }}">
                <p class="text-xs font-semibold mb-1
                    {{ $evenement->type_paiement == 'gratuit' ? 'text-green-700' : 'text-yellow-700' }}">
                    <i class="fa-solid fa-money-bill mr-1"></i> Paiement
                </p>
                @if($evenement->type_paiement == 'gratuit')
                <p class="font-bold text-green-700">
                    <i class="fa-solid fa-gift mr-1"></i> Événement gratuit
                </p>
                @elseif($evenement->type_paiement == 'par_participant')
                <p class="font-bold text-yellow-700">
                    {{ number_format($evenement->montant_inscription, 0, ',', ' ') }} FCFA par participant
                </p>
                @else
                <p class="font-bold text-yellow-700">
                    {{ number_format($evenement->montant_inscription, 0, ',', ' ') }} FCFA par entreprise
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

    {{-- 
         ÉTAPE 2 — MES INFORMATIONS
         → MODE MEMBRE : infos personnelles uniquement
         → MODE REPRÉSENTANT : infos personnelles + entreprise
     --}}
    @if($etape == 2)
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-user" style="color: #C8102E;"></i>
            @if($estMembre) Vos informations personnelles
            @else Vos informations
            @endif
        </h3>

        <div class="space-y-5">

            {{-- ← Bloc entreprise affiché en lecture seule pour les membres --}}
            @if($estMembre && $entreprise)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <p class="text-xs font-bold text-green-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-building"></i>
                    Votre entreprise
                    <span class="font-normal text-green-500">(informations déjà enregistrées)</span>
                </p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-gray-400">Entreprise</p>
                        <p class="font-semibold text-gray-800">{{ $entreprise->nom }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Secteur</p>
                        <p class="font-semibold text-gray-800">
                            {{ $entreprise->secteur_activite }}
                            @if($entreprise->sous_secteur)
                            / {{ $entreprise->sous_secteur }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Pays / Ville</p>
                        <p class="font-semibold text-gray-800">
                            {{ $entreprise->ville }}, {{ $entreprise->pays }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Année création</p>
                        <p class="font-semibold text-gray-800">{{ $entreprise->annee_creation ?? '-' }}</p>
                    </div>
                </div>
            </div>
            @endif

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
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                        @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Prénom *</label>
                        <input wire:model="prenom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                        @error('prenom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    @if(!$estMembre)
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Email *</label>
                        <input wire:model="email" type="email"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                            placeholder="votre@email.com">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                        <input wire:model="telephone" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                            placeholder="+226 70 00 00 00">
                        @error('telephone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="{{ $estMembre ? 'col-span-2' : 'col-span-2' }}">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Fonction *</label>
                        <select wire:model.live="fonction"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                            <option value="">-- Choisir --</option>
                            @foreach($fonctions as $f)
                            <option value="{{ $f }}">{{ $f }}</option>
                            @endforeach
                        </select>
                        @if($fonction == 'Autre')
                        <input wire:model="fonction_autre" type="text"
                            class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                            placeholder="Précisez votre fonction...">
                        @endif
                        @error('fonction') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                </div>
            </div>

            {{-- ← Infos entreprise UNIQUEMENT pour le représentant --}}
            @if(!$estMembre)
            <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                <p class="text-xs font-bold text-green-700 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-building"></i>
                    Informations sur votre entreprise
                </p>
                <div class="grid grid-cols-2 gap-4">

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
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                        @endif
                        @error('ville') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

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
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Description des activités *
                        </label>
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
                            placeholder="Ex: Logiciels, Céréales..."></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Année de création *</label>
                        <input wire:model="annee_creation" type="number"
                            min="1900" max="{{ date('Y') }}"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                        @error('annee_creation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nombre de salariés *</label>
                        <input wire:model="nombre_salaries" type="number" min="1"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                        @error('nombre_salaries') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            CA export % *
                        </label>
                        <div class="relative">
                            <input wire:model="chiffre_affaires" type="number"
                                min="0" max="100" step="0.1"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white pr-10">
                            <span class="absolute right-4 top-3 text-gray-400 text-sm">%</span>
                        </div>
                        @error('chiffre_affaires') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                </div>
            </div>
            @endif

        </div>

        <div class="flex justify-between mt-8">
            <button wire:click="precedent"
                class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Précédent
            </button>
            <button wire:click="suivant"
                wire:loading.attr="disabled"
                class="px-6 py-3 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2"
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
         ÉTAPE 3 — PROFIL PARTENAIRE
     --}}
    @if($etape == 3)
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h3 class="text-xl font-bold text-gray-800 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-handshake" style="color: #C8102E;"></i>
            Profil partenaire recherché
        </h3>
        <p class="text-gray-500 text-sm mb-6">
            Ces informations permettront au système de vous proposer les meilleurs matchs.
        </p>
        <div class="space-y-5">

            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">
                    Secteur d'activité recherché *
                </label>
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
                <label class="block text-gray-600 text-sm font-medium mb-1.5">
                    Zone géographique ciblée *
                </label>
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
                <label class="block text-gray-600 text-sm font-medium mb-3">
                    Type de partenaire recherché *
                </label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach($types_partenaires as $type)
                    <label class="cursor-pointer">
                        <input type="radio" wire:model="type_partenaire"
                            value="{{ $type }}" class="hidden peer">
                        <div class="p-3 border-2 rounded-xl text-center transition text-sm
                            peer-checked:border-red-400 peer-checked:bg-red-50 peer-checked:text-red-700
                            hover:bg-gray-50 border-gray-200 text-gray-600">
                            {{ $type }}
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('type_partenaire')
                    <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                @enderror
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

    {{-- 
         ÉTAPE 4 — DISPONIBILITÉS
     --}}
    @if($etape == 4)
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h3 class="text-xl font-bold text-gray-800 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-calendar-days" style="color: #C8102E;"></i>
            Vos disponibilités
        </h3>
        <p class="text-gray-500 text-sm mb-6">
            Cochez les jours où vous serez présent au forum.
            Le système ne programmera des RDV que sur ces jours.
        </p>

        @if($isMultiJours)
        <div class="grid grid-cols-3 gap-3 mb-6">
            @foreach($joursEvenement as $jour)
            <label class="cursor-pointer">
                <input type="checkbox"
                    wire:model="disponibilites"
                    value="{{ $jour }}"
                    class="hidden peer">
                <div class="p-4 border-2 rounded-xl text-center transition
                    peer-checked:border-green-400 peer-checked:bg-green-50
                    hover:bg-gray-50 border-gray-200">
                    <p class="font-semibold text-sm text-gray-800">
                        {{ \Carbon\Carbon::parse($jour)->locale('fr')->translatedFormat('l') }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ \Carbon\Carbon::parse($jour)->format('d/m/Y') }}
                    </p>
                </div>
            </label>
            @endforeach
        </div>
        @error('disponibilites')
        <p class="text-red-500 text-xs mb-4">{{ $message }}</p>
        @enderror
        @else
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <p class="text-sm text-blue-700 flex items-center gap-2">
                <i class="fa-solid fa-circle-info"></i>
                Événement sur 1 jour —
                <strong>
                    {{ \Carbon\Carbon::parse($evenement->date_debut)->locale('fr')->translatedFormat('l d/m/Y') }}
                </strong>
            </p>
        </div>
        @endif

        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-xs text-yellow-700 flex items-start gap-2">
            <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
            Si vous ne cochez rien, vous serez considéré disponible tous les jours de l'événement.
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

    {{-- 
         ÉTAPE 5 — CONFIRMATION
     --}}
    @if($etape == 5)
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h3 class="text-xl font-bold text-gray-800 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-circle-check" style="color: #007A3D;"></i>
            Récapitulatif
        </h3>
        <p class="text-gray-500 text-sm mb-6">
            Vérifiez vos informations avant de confirmer votre inscription.
        </p>

        <div class="space-y-4">

            {{-- Événement --}}
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

            {{-- Infos personnelles --}}
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
                        <span class="text-gray-400 text-xs">Téléphone</span>
                        <p class="font-semibold text-gray-800">{{ $telephone }}</p>
                    </div>
                    @if($email)
                    <div>
                        <span class="text-gray-400 text-xs">Email</span>
                        <p class="font-semibold text-gray-800">{{ $email }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Infos entreprise --}}
            @if($estMembre && $entreprise)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <p class="text-xs font-bold text-green-700 mb-3">
                    <i class="fa-solid fa-building mr-1"></i> Votre entreprise
                </p>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-400 text-xs">Entreprise</span>
                        <p class="font-semibold text-gray-800">{{ $entreprise->nom }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Secteur</span>
                        <p class="font-semibold text-gray-800">
                            {{ $entreprise->secteur_activite }}
                            @if($entreprise->sous_secteur) / {{ $entreprise->sous_secteur }} @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Pays / Ville</span>
                        <p class="font-semibold text-gray-800">
                            {{ $entreprise->ville }}, {{ $entreprise->pays }}
                        </p>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs font-bold text-gray-600 mb-3">
                    <i class="fa-solid fa-building mr-1"></i> Votre entreprise
                </p>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-400 text-xs">Secteur</span>
                        <p class="font-semibold text-gray-800">
                            {{ $secteur_activite }} — {{ $sous_secteur }}
                        </p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Pays / Ville</span>
                        <p class="font-semibold text-gray-800">{{ $pays }} / {{ $ville }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Année / Salariés</span>
                        <p class="font-semibold text-gray-800">{{ $annee_creation }} / {{ $nombre_salaries }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">CA export</span>
                        <p class="font-semibold text-gray-800">{{ $chiffre_affaires }}%</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Partenaire recherché --}}
            <div class="bg-blue-50 rounded-xl p-4">
                <p class="text-xs font-bold text-blue-700 mb-3">
                    <i class="fa-solid fa-handshake mr-1"></i> Partenaire recherché
                </p>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                        <i class="fa-solid fa-tag mr-1"></i>{{ $secteur_recherche }}
                    </span>
                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                        <i class="fa-solid fa-location-dot mr-1"></i>{{ $zone_geographique }}
                    </span>
                    <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">
                        <i class="fa-solid fa-handshake mr-1"></i>{{ $type_partenaire }}
                    </span>
                </div>
            </div>

            {{-- Disponibilités --}}
            @if(!empty($disponibilites))
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <p class="text-xs font-bold text-yellow-700 mb-2">
                    <i class="fa-solid fa-calendar-days mr-1"></i> Disponibilités
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach($disponibilites as $dispo)
                    <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium">
                        {{ \Carbon\Carbon::parse($dispo)->locale('fr')->translatedFormat('l d/m/Y') }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Paiement --}}
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
                    <p class="text-xs text-yellow-600">
                        Le paiement sera requis après confirmation.
                    </p>
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