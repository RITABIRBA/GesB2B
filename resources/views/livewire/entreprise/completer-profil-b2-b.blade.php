<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden max-w-3xl mx-auto">
        <div class="p-8 text-white text-center" style="background: linear-gradient(135deg, #007A3D, #005a2d);">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 bg-white/20">
                <i class="fa-solid fa-handshake text-3xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Complétez votre profil B2B</h2>
            <p class="text-green-200 text-sm">Ces informations permettent au système de vous proposer les meilleurs partenaires.</p>
        </div>

        <div class="p-8 space-y-6">

            {{-- SECTEUR D'ACTIVITÉ --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">
                    Votre secteur d'activité *
                    <span class="text-gray-400 font-normal text-xs">(ce que vous faites / proposez)</span>
                </label>
                @if($secteurAutoEntreprise)
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                    <i class="fa-solid fa-building text-green-600 text-xl"></i>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Secteur récupéré automatiquement depuis votre entreprise</p>
                        <p class="font-bold text-gray-800">{{ $secteur_activite }}</p>
                        @if($sous_secteur)
                        <p class="text-xs text-gray-500">Sous-secteur : {{ $sous_secteur }}</p>
                        @endif
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fa-solid fa-circle-check mr-1"></i>{{ $secteurNomEntreprise }}
                        </p>
                    </div>
                </div>
                @else
                <select wire:model="secteur_activite"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                    <option value="">-- Choisir votre secteur --</option>
                    @foreach($secteurs as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
                @error('secteur_activite') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                <div class="mt-2">
                    <input wire:model="sous_secteur" type="text"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                        placeholder="Sous-secteur (optionnel)">
                </div>
                @endif
            </div>

            {{-- Zone géographique --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-1.5">Zone géographique ciblée *</label>
                <select wire:model="zone_geographique"
                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                    <option value="">-- Choisir --</option>
                    @foreach($zonesGeographiques as $z)
                    <option value="{{ $z }}">{{ $z }}</option>
                    @endforeach
                </select>
                @error('zone_geographique') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Type de partenariat --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-2">
                    Type de partenariat que vous proposez *
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
                <input wire:model="type_partenariat_autre" type="text" class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm" placeholder="Précisez...">
                @endif
                @error('types_partenariat') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Profil de partenaire recherché --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-2">
                    Profil de partenaire recherché
                    <span class="text-gray-400 font-normal">(max 3 — {{ count($profils_partenaire) }}/3)</span>
                </label>
                <p class="text-xs text-gray-400 mb-2">Quel type d'acteur cherchez-vous à rencontrer ?</p>
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

            {{-- Secteurs recherchés --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-2">
                    Secteurs que vous recherchez chez un partenaire *
                    <span class="text-gray-400 font-normal">(max 3 — {{ count($secteurs_recherche) }}/3)</span>
                </label>
                <p class="text-xs text-gray-400 mb-2">Dans quels secteurs cherchez-vous des opportunités ?</p>
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
                <input wire:model="secteur_recherche_autre" type="text" class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm" placeholder="Précisez...">
                @endif
                @error('secteurs_recherche') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- ✅ DISPONIBILITÉS — logique ABSENCE --}}
            <div>
                <label class="block text-gray-600 text-sm font-medium mb-2">
                    Jours d'absence
                    <span class="text-gray-400 font-normal">(optionnel)</span>
                </label>

                @if(count($joursEvenement) > 1)

                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-4 flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-orange-500 mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="text-sm font-semibold text-orange-700">Cochez les jours où vous NE SEREZ PAS là</p>
                        <p class="text-xs text-orange-600 mt-1">
                            Par défaut, vous êtes considéré présent tous les jours de l'événement.
                            Si vous savez que vous serez absent certains jours, cochez-les ici.
                            Le système ne vous programmera aucun rendez-vous ces jours-là.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($joursEvenement as $jour)
                    @php $estAbsent = in_array($jour, $jours_absence); @endphp
                    <label class="cursor-pointer">
                        <input type="checkbox"
                            wire:model.live="jours_absence"
                            value="{{ $jour }}"
                            class="hidden peer">
                        <div class="p-4 border-2 rounded-xl text-center transition
                            {{ $estAbsent ? 'border-red-400 bg-red-50' : 'border-green-200 bg-green-50 hover:bg-gray-50' }}">
                            <p class="font-semibold text-sm {{ $estAbsent ? 'text-red-700' : 'text-green-700' }}">
                                {{ \Carbon\Carbon::parse($jour)->locale('fr')->translatedFormat('l') }}
                            </p>
                            <p class="text-xs mt-0.5 {{ $estAbsent ? 'text-red-400' : 'text-green-400' }}">
                                {{ \Carbon\Carbon::parse($jour)->format('d/m/Y') }}
                            </p>
                            @if($estAbsent)
                            <span class="inline-flex items-center gap-1 mt-2 text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-600 font-medium">
                                <i class="fa-solid fa-xmark"></i> Absent
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 mt-2 text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-600 font-medium">
                                <i class="fa-solid fa-check"></i> Présent
                            </span>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>

                @php
                    $joursPresents = array_diff($joursEvenement, $jours_absence);
                    $joursAbsents  = $jours_absence;
                @endphp
                <div class="mt-4 bg-gray-50 rounded-xl p-4 flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span>
                        <span class="text-gray-600">Présent : <strong>{{ count($joursPresents) }} jour(s)</strong></span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span>
                        <span class="text-gray-600">Absent : <strong>{{ count($joursAbsents) }} jour(s)</strong></span>
                    </div>
                </div>

                @else
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <p class="text-sm text-blue-700">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        @if(count($joursEvenement) == 1)
                        Événement sur 1 seul jour —
                        <strong>{{ \Carbon\Carbon::parse($joursEvenement[0])->locale('fr')->translatedFormat('l d/m/Y') }}</strong>.
                        Vous serez automatiquement présent ce jour.
                        @else
                        Aucun événement associé à votre compte.
                        @endif
                    </p>
                </div>
                @endif
            </div>

            {{-- Bouton --}}
            <button wire:click="enregistrer"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-70 cursor-not-allowed"
                class="w-full py-4 rounded-xl text-white font-bold text-lg transition hover:opacity-90 shadow-lg flex items-center justify-center gap-3"
                style="background-color: #C8102E;">
                <span wire:loading.remove><i class="fa-solid fa-check mr-1"></i> Enregistrer mon profil B2B</span>
                <span wire:loading><i class="fa-solid fa-spinner fa-spin mr-1"></i> Enregistrement...</span>
            </button>

            <a href="{{ route('entreprise.souhaits') }}"
                class="block text-center text-sm text-gray-400 hover:text-gray-600 mt-2">
                <i class="fa-solid fa-arrow-left mr-1"></i> Retour aux souhaits
            </a>
        </div>
    </div>
</div>