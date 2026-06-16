<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(!$entreprise_id)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center text-yellow-700">
        <i class="fa-solid fa-triangle-exclamation text-4xl mb-3 block text-yellow-400"></i>
        <p class="font-bold text-lg">Entreprise non trouvée</p>
        <p class="text-sm mt-1">Votre compte n'est pas lié à une entreprise.</p>
    </div>
    @else

    {{-- ============================================================
         CARTE REPRÉSENTANT
    ============================================================ --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-2xl font-bold flex-shrink-0"
                style="background-color: #007A3D;">
                {{ strtoupper(substr($nom_responsable ?: 'R', 0, 1)) }}
            </div>
            <div class="flex-1">
                <p class="text-xs text-gray-400 mb-0.5">Représentant principal</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $nom_responsable }} {{ $prenom_responsable }}
                </p>
                @if($fonction_responsable)
                <p class="text-sm text-gray-500">
                    <i class="fa-solid fa-briefcase mr-1 text-gray-400"></i>
                    {{ $fonction_responsable }}
                </p>
                @endif
                @if($email_responsable)
                <p class="text-sm text-gray-500">
                    <i class="fa-solid fa-envelope mr-1 text-gray-400"></i>
                    {{ $email_responsable }}
                </p>
                @endif
                @if($contact)
                <p class="text-sm text-gray-500">
                    <i class="fa-solid fa-phone mr-1 text-gray-400"></i>
                    {{ $contact }}
                </p>
                @endif
            </div>
        </div>
    </div>

    {{-- ============================================================
         PROFIL ENTREPRISE
    ============================================================ --}}
    <div class="bg-white rounded-xl shadow p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-building" style="color: #C8102E;"></i>
                Mon Profil Entreprise
            </h3>
            @if(!$isEditing)
            <button wire:click="activer"
                class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90"
                style="background-color: #007A3D;">
                <i class="fa-solid fa-pen"></i> Modifier
            </button>
            @endif
        </div>

        {{-- ============================================================
             MODE AFFICHAGE
        ============================================================ --}}
        @if(!$isEditing)
        <div class="space-y-4">

            {{-- Bloc Entreprise --}}
            <div class="bg-gray-50 rounded-xl p-5">
                <p class="text-xs font-bold text-gray-500 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-building"></i>
                    Informations entreprise
                </p>
                <div class="grid grid-cols-2 gap-4">

                    <div class="col-span-2">
                        <p class="text-xs text-gray-400">Nom de l'entreprise</p>
                        <p class="font-semibold text-gray-800 text-lg">{{ $nom ?: '-' }}</p>
                    </div>

                    {{-- ← IFU toujours affiché --}}
                    <div class="col-span-2">
                        <p class="text-xs text-gray-400 mb-1">Numéro IFU</p>
                        @if($ifu)
                        <span class="font-mono font-bold text-gray-800 bg-gray-200 px-3 py-1 rounded-lg text-sm">
                            {{ $ifu }}
                        </span>
                        @else
                        <span class="text-gray-400 italic text-sm">Non renseigné</span>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Secteur d'activité</p>
                        <p class="font-semibold text-gray-800">
                            {{ $secteur_activite ?: '-' }}
                            @if($sous_secteur)
                            <span class="text-gray-400">/ {{ $sous_secteur }}</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Localisation</p>
                        <p class="font-semibold text-gray-800">
                            {{ $ville ?: '-' }}, {{ $pays ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Année de création</p>
                        <p class="font-semibold text-gray-800">
                            {{ $annee_creation ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Nombre de salariés</p>
                        <p class="font-semibold text-gray-800">
                            {{ $nombre_salaries ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">CA export</p>
                        <p class="font-semibold text-gray-800">
                            {{ $chiffre_affaires ? $chiffre_affaires . '%' : '-' }}
                        </p>
                    </div>

                </div>
            </div>

            {{-- Bloc Description --}}
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs font-bold text-gray-500 mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-file-lines"></i>
                    Description des activités
                </p>
                @if($description_activites)
                <p class="text-sm text-gray-700 leading-relaxed">{{ $description_activites }}</p>
                @else
                <p class="text-sm text-gray-400 italic">Non renseigné</p>
                @endif
            </div>

            {{-- Bloc Produits --}}
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs font-bold text-gray-500 mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-box"></i>
                    Principaux produits / Savoir-faire
                </p>
                @if($principaux_produits)
                <p class="text-sm text-gray-700">{{ $principaux_produits }}</p>
                @else
                <p class="text-sm text-gray-400 italic">Non renseigné</p>
                @endif
            </div>

            {{-- Bloc Partenaire recherché --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-xs font-bold text-blue-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-handshake"></i>
                    Partenaire recherché
                </p>

                <div class="space-y-3">

                    <div>
                        <p class="text-xs text-gray-400 mb-1.5">Zone géographique ciblée</p>
                        @if($zone_geographique)
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                            <i class="fa-solid fa-location-dot mr-1"></i>{{ $zone_geographique }}
                        </span>
                        @else
                        <p class="text-sm text-gray-400 italic">Non renseigné</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 mb-1.5">Secteurs d'activité recherchés</p>
                        @if(!empty($secteurs_recherche))
                        <div class="flex flex-wrap gap-2">
                            @foreach($secteurs_recherche as $sr)
                            <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                                <i class="fa-solid fa-tag mr-1"></i>
                                {{ $sr === 'Autre' ? $secteur_recherche_autre : $sr }}
                            </span>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-400 italic">Non renseigné</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 mb-1.5">Types de partenariat recherchés</p>
                        @if(!empty($types_partenariat))
                        <div class="flex flex-wrap gap-2">
                            @foreach($types_partenariat as $tp)
                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                                <i class="fa-solid fa-handshake mr-1"></i>
                                {{ $tp === 'Autre' ? $type_partenariat_autre : $tp }}
                            </span>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-400 italic">Non renseigné</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 mb-1.5">Profils de partenaire recherchés</p>
                        @if(!empty($profils_partenaire))
                        <div class="flex flex-wrap gap-2">
                            @foreach($profils_partenaire as $pp)
                            <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">
                                <i class="fa-solid fa-id-card mr-1"></i>{{ $pp }}
                            </span>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-400 italic">Non renseigné</p>
                        @endif
                    </div>

                </div>
            </div>

        </div>

        {{-- ============================================================
             MODE ÉDITION
        ============================================================ --}}
        @else
        <div class="space-y-6">

            {{-- Bloc Responsable --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-xs font-bold text-blue-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-user-tie"></i>
                    Informations du responsable
                </p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-600 text-xs font-medium mb-1">Nom</label>
                        <input wire:model="nom_responsable" type="text"
                            class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs font-medium mb-1">Prénom</label>
                        <input wire:model="prenom_responsable" type="text"
                            class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs font-medium mb-1">Fonction</label>
                        <input wire:model="fonction_responsable" type="text"
                            class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs font-medium mb-1">Téléphone *</label>
                        <input wire:model="contact" type="text"
                            class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm bg-white">
                        @error('contact')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Bloc Entreprise --}}
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <p class="text-xs font-bold text-green-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-building"></i>
                    Informations de l'entreprise
                </p>
                <div class="grid grid-cols-2 gap-4">

                    {{-- Nom entreprise --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-xs font-medium mb-1">
                            Nom de l'entreprise *
                        </label>
                        <input wire:model="nom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                        @error('nom')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- IFU en lecture seule --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-xs font-medium mb-1">
                            Numéro IFU
                            <span class="text-gray-400 font-normal">(non modifiable)</span>
                        </label>
                        <div class="w-full border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-100 text-sm font-mono font-bold text-gray-600">
                            {{ $ifu ?: 'Non renseigné' }}
                        </div>
                    </div>

                    {{-- Secteur --}}
                    <div>
                        <label class="block text-gray-600 text-xs font-medium mb-1">
                            Secteur d'activité *
                        </label>
                        <select wire:model="secteur_activite"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                            <option value="">-- Choisir --</option>
                            @foreach($secteurs as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                        @error('secteur_activite')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Sous-secteur --}}
                    <div>
                        <label class="block text-gray-600 text-xs font-medium mb-1">
                            Sous-secteur *
                        </label>
                        <input wire:model="sous_secteur" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                            placeholder="Ex: Céréales, BTP...">
                        @error('sous_secteur')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Pays --}}
                    <div>
                        <label class="block text-gray-600 text-xs font-medium mb-1">Pays *</label>
                        <select wire:model.live="pays"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                            <option value="">-- Choisir --</option>
                            @foreach($pays_liste as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                            @endforeach
                        </select>
                        @error('pays')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Ville --}}
                    <div>
                        <label class="block text-gray-600 text-xs font-medium mb-1">Ville *</label>
                        @if($pays && count($villesDisponibles) > 1)
                        <select wire:model="ville"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                            <option value="">-- Choisir --</option>
                            @foreach($villesDisponibles as $v)
                            <option value="{{ $v }}">{{ $v }}</option>
                            @endforeach
                        </select>
                        @else
                        <input wire:model="ville" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                        @endif
                        @error('ville')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Année création --}}
                    <div>
                        <label class="block text-gray-600 text-xs font-medium mb-1">
                            Année de création *
                        </label>
                        <input wire:model="annee_creation" type="number"
                            min="1900" max="{{ date('Y') }}"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                            placeholder="Ex: 2010">
                        @error('annee_creation')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Salariés --}}
                    <div>
                        <label class="block text-gray-600 text-xs font-medium mb-1">
                            Nombre de salariés *
                        </label>
                        <input wire:model="nombre_salaries" type="number" min="1"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                            placeholder="Ex: 25">
                        @error('nombre_salaries')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- CA export --}}
                    <div>
                        <label class="block text-gray-600 text-xs font-medium mb-1">
                            CA export % *
                        </label>
                        <div class="relative">
                            <input wire:model="chiffre_affaires" type="number"
                                min="0" max="100" step="0.1"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white pr-8"
                                placeholder="Ex: 30">
                            <span class="absolute right-3 top-2.5 text-gray-400 text-xs">%</span>
                        </div>
                        @error('chiffre_affaires')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-xs font-medium mb-1">
                            Description des activités *
                        </label>
                        <textarea wire:model="description_activites" rows="3"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white resize-none"
                            placeholder="Décrivez vos activités..."></textarea>
                        @error('description_activites')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Produits --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-xs font-medium mb-1">
                            Principaux produits / Savoir-faire
                            <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <textarea wire:model="principaux_produits" rows="2"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white resize-none"
                            placeholder="Ex: Maïs, Sorgho, Logiciels..."></textarea>
                    </div>

                </div>
            </div>

            {{-- Bloc Partenaire recherché --}}
            <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                <p class="text-xs font-bold text-purple-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-handshake"></i>
                    Partenaire recherché
                </p>

                {{-- Zone géographique --}}
                <div class="mb-4">
                    <label class="block text-gray-600 text-xs font-medium mb-1">
                        Zone géographique ciblée *
                    </label>
                    <select wire:model="zone_geographique"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                        <option value="">-- Choisir --</option>
                        @foreach($zonesGeographiques as $zone)
                        <option value="{{ $zone }}">{{ $zone }}</option>
                        @endforeach
                    </select>
                    @error('zone_geographique')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Secteurs recherchés (max 3) --}}
                <div class="mb-4">
                    <label class="block text-gray-600 text-xs font-medium mb-2">
                        Secteurs d'activité recherchés *
                        <span class="text-gray-400 font-normal">(max 3 — {{ count($secteurs_recherche) }}/3)</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($secteurs as $option)
                        <button type="button" wire:click="toggleSecteurRecherche('{{ $option }}')"
                            class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm transition text-left
                                {{ in_array($option, $secteurs_recherche) ? 'border-red-400 bg-red-50 text-red-700 font-medium' : (count($secteurs_recherche) >= 3 && !in_array($option, $secteurs_recherche) ? 'border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed' : 'border-gray-200 bg-white hover:border-red-300 text-gray-600') }}">
                            <i class="fa-solid {{ in_array($option, $secteurs_recherche) ? 'fa-circle-check text-red-500' : 'fa-circle text-gray-300' }}"></i>
                            {{ $option }}
                        </button>
                        @endforeach
                    </div>
                    @if(in_array('Autre', $secteurs_recherche))
                    <input wire:model="secteur_recherche_autre" type="text"
                        class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                        placeholder="Précisez le secteur recherché...">
                    @error('secteur_recherche_autre')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                    @endif
                    @error('secteurs_recherche')
                        <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Types de partenariat (max 3) --}}
                <div class="mb-4">
                    <label class="block text-gray-600 text-xs font-medium mb-2">
                        Type de partenariat recherché *
                        <span class="text-gray-400 font-normal">(max 3 — {{ count($types_partenariat) }}/3)</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($typesPartenariatOptions as $option)
                        <button type="button" wire:click="toggleTypePartenariat('{{ $option }}')"
                            class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm transition text-left
                                {{ in_array($option, $types_partenariat) ? 'border-green-400 bg-green-50 text-green-700 font-medium' : (count($types_partenariat) >= 3 && !in_array($option, $types_partenariat) ? 'border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed' : 'border-gray-200 bg-white hover:border-green-300 text-gray-600') }}">
                            <i class="fa-solid {{ in_array($option, $types_partenariat) ? 'fa-circle-check text-green-500' : 'fa-circle text-gray-300' }}"></i>
                            {{ $option }}
                        </button>
                        @endforeach
                    </div>
                    @if(in_array('Autre', $types_partenariat))
                    <input wire:model="type_partenariat_autre" type="text"
                        class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                        placeholder="Précisez le type de partenariat...">
                    @error('type_partenariat_autre')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                    @endif
                    @error('types_partenariat')
                        <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Profils de partenaire (max 3) --}}
                <div>
                    <label class="block text-gray-600 text-xs font-medium mb-2">
                        Profil de partenaire recherché
                        <span class="text-gray-400 font-normal">(max 3 — {{ count($profils_partenaire) }}/3)</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($profilsPartenariatOptions as $option)
                        <button type="button" wire:click="toggleProfilPartenaire('{{ $option }}')"
                            class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm transition text-left
                                {{ in_array($option, $profils_partenaire) ? 'border-blue-400 bg-blue-50 text-blue-700 font-medium' : (count($profils_partenaire) >= 3 && !in_array($option, $profils_partenaire) ? 'border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed' : 'border-gray-200 bg-white hover:border-blue-300 text-gray-600') }}">
                            <i class="fa-solid {{ in_array($option, $profils_partenaire) ? 'fa-circle-check text-blue-500' : 'fa-circle text-gray-300' }}"></i>
                            {{ $option }}
                        </button>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>

        {{-- Boutons --}}
        <div class="flex justify-end gap-3 mt-6">
            <button wire:click="annuler"
                class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                <i class="fa-solid fa-xmark mr-1"></i> Annuler
            </button>
            <button wire:click="sauvegarder"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-70 cursor-not-allowed"
                class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm flex items-center gap-2"
                style="background-color: #007A3D;">
                <span wire:loading.remove>
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Enregistrer
                </span>
                <span wire:loading>
                    <i class="fa-solid fa-spinner fa-spin mr-1"></i> Enregistrement...
                </span>
            </button>
        </div>
        @endif

    </div>

    @endif
</div>