<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden max-w-3xl mx-auto">
        <div class="p-8 text-white text-center"
            style="background: linear-gradient(135deg, #007A3D, #005a2d);">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 bg-white/20">
                <i class="fa-solid fa-handshake text-3xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Complétez votre profil B2B</h2>
            <p class="text-green-200 text-sm">
                Ces informations permettent au système de vous proposer
                les meilleurs partenaires compatibles avec vos critères.
            </p>
        </div>

        <div class="p-8 space-y-6">

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
                    class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                    placeholder="Précisez...">
                @endif
                @error('types_partenariat') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
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
                    Secteurs d'activité recherchés *
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
                    class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                    placeholder="Précisez...">
                @endif
                @error('secteurs_recherche') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <button wire:click="enregistrer"
                wire:loading.attr="disabled"
                class="w-full py-4 rounded-xl text-white font-bold text-lg transition hover:opacity-90 shadow-lg flex items-center justify-center gap-3"
                style="background-color: #C8102E;">
                <i class="fa-solid fa-check"></i> Enregistrer mon profil B2B
            </button>
        </div>
    </div>
</div>