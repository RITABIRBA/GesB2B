<div class="min-h-screen flex items-center justify-center p-6"
    style="background-color: #f8f9fa;">
    <div class="w-full max-w-2xl">

        {{-- Header --}}
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 mb-4">
                <img src="{{ asset('images/logo-ccibf.png') }}" alt="CCI-BF" class="w-12 h-12 object-contain">
                <div class="text-left">
                    <h1 class="text-xl font-bold text-gray-800">Business Forum</h1>
                    <p class="text-xs text-gray-400">CCI-BF Platform</p>
                </div>
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Inscription Entreprise</h2>
            <p class="text-gray-500 text-sm mt-1">Inscrivez votre entreprise au Business Forum CCI-BF</p>
        </div>

        {{-- CONFIRMATION --}}
        @if($confirme)
        <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5" style="background-color: #007A3D;">
                <i class="fa-solid fa-building-circle-check text-white text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-3">Dossier entreprise envoyé !</h3>
            <p class="text-gray-600 mb-4">Votre entreprise a été préenregistrée avec succès. L'équipe CCI-BF va traiter votre demande.</p>
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 text-left">
                <p class="text-sm font-bold text-blue-700 mb-2"><i class="fa-solid fa-circle-info mr-1"></i> Prochaines étapes :</p>
                <ul class="text-xs text-blue-600 space-y-1">
                    <li><i class="fa-solid fa-check mr-1"></i> Vérification de votre IFU et dossier</li>
                    <li><i class="fa-solid fa-check mr-1"></i> Validation par l'administration CCI-BF</li>
                    <li><i class="fa-solid fa-check mr-1"></i> Réception de vos identifiants de connexion</li>
                    <li><i class="fa-solid fa-check mr-1"></i> Accès à votre espace entreprise</li>
                </ul>
            </div>
            <div class="flex gap-3 justify-center">
                <a href="{{ url('/') }}" class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-50 transition text-sm font-medium">
                    <i class="fa-solid fa-home mr-1"></i> Accueil
                </a>
                <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl text-white font-medium transition hover:opacity-90 text-sm" style="background-color: #C8102E;">
                    <i class="fa-solid fa-right-to-bracket mr-1"></i> Se connecter
                </a>
            </div>
        </div>
        @else

        {{-- Barre de progression --}}
        <div class="flex items-center mb-6">
            @php
                $etapes = $evenementEstB2B
                    ? [1 => 'Entreprise', 2 => 'Représentant', 3 => 'Profil B2B', 4 => 'Confirmation']
                    : [1 => 'Entreprise', 2 => 'Représentant', 3 => 'Confirmation'];
            @endphp
            @foreach($etapes as $num => $label)
            <div class="flex items-center {{ $num < 3 ? 'flex-1' : '' }}">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 transition
                        {{ $etape >= $num ? 'text-white' : 'text-gray-400 border-2 border-gray-300' }}"
                        style="{{ $etape >= $num ? 'background-color: #C8102E;' : '' }}">
                        @if($etape > $num)<i class="fa-solid fa-check text-xs"></i>@else{{ $num }}@endif
                    </div>
                    <span class="text-xs font-medium mt-1 {{ $etape >= $num ? 'text-gray-800' : 'text-gray-400' }}">{{ $label }}</span>
                </div>
                @if($num < $nbEtapes)
                <div class="flex-1 h-0.5 mx-2 mb-4 {{ $etape > $num ? '' : 'bg-gray-200' }}"
                    style="{{ $etape > $num ? 'background-color: #C8102E;' : '' }}"></div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

            {{-- ════ ÉTAPE 1 : INFOS ENTREPRISE ════ --}}
            @if($etape == 1)
            <div class="p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-building" style="color: #C8102E;"></i> Informations de l'entreprise
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom de l'entreprise *</label>
                        <input wire:model="nom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: Société Burkinabè de Commerce">
                        @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Numéro IFU *</label>
                        <input wire:model="ifu" type="text" maxlength="9"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm font-mono uppercase"
                            placeholder="Ex: 12345678A">
                        <p class="text-xs text-gray-400 mt-1">Format : 8 chiffres + 1 lettre</p>
                        @error('ifu') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Secteur d'activité *</label>
                        <select wire:model.live="secteur_activite" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($secteurs as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                        @if($secteur_activite === 'Autre')
                        <input wire:model="secteur_autre" type="text" class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm" placeholder="Précisez...">
                        @endif
                        @error('secteur_activite') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Sous-secteur *</label>
                        <input wire:model="sous_secteur" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                            placeholder="Ex: Céréales, BTP résidentiel...">
                        @error('sous_secteur') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Pays *</label>
                        <select wire:model.live="pays" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
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
                        <select wire:model="ville" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($villesDisponibles as $v)
                            <option value="{{ $v }}">{{ $v }}</option>
                            @endforeach
                        </select>
                        @else
                        <input wire:model="ville" type="text" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        @endif
                        @error('ville') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                        <input wire:model="telephone" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                            placeholder="+226 70 00 00 00">
                        @error('telephone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Email entreprise</label>
                        <input wire:model="email" type="email"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                            placeholder="contact@entreprise.com">
                    </div>

                    {{-- Événement --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-3">
                            Événement souhaité <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        @if($evenements->isEmpty())
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-center text-sm text-gray-400">
                            <i class="fa-solid fa-calendar-xmark text-2xl mb-2 block text-gray-300"></i>
                            Aucun événement disponible pour le moment.
                        </div>
                        @else
                        <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                            <label class="cursor-pointer block">
                                <input type="radio" wire:model.live="id_evenement" value="" class="hidden peer">
                                <div class="p-4 border-2 rounded-xl transition flex items-center gap-3 peer-checked:border-gray-400 peer-checked:bg-gray-50 hover:bg-gray-50 border-gray-200">
                                    <i class="fa-solid fa-ban text-gray-400 text-lg"></i>
                                    <span class="text-sm font-medium text-gray-600">Je ne sais pas encore / Aucun événement</span>
                                </div>
                            </label>
                            @foreach($evenements as $evt)
                            @php
                                $estB2B    = ($evt->type_evenement ?? 'avec_b2b') === 'avec_b2b';
                                $gratuit   = ($evt->type_paiement ?? 'payant') === 'gratuit';
                                $dateDebut = \Carbon\Carbon::parse($evt->date_debut);
                                $dateFin   = $evt->date_fin ? \Carbon\Carbon::parse($evt->date_fin) : null;
                            @endphp
                            <label class="cursor-pointer block">
                                <input type="radio" wire:model.live="id_evenement" value="{{ $evt->id }}" class="hidden peer">
                                <div class="p-4 border-2 rounded-xl transition peer-checked:border-red-400 peer-checked:bg-red-50 hover:bg-gray-50 border-gray-200">
                                    <div class="flex items-start justify-between gap-3 flex-wrap">
                                        <div class="flex-1">
                                            <p class="font-bold text-gray-800">{{ $evt->nom }}</p>
                                            <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-500 flex-wrap">
                                                <span><i class="fa-solid fa-calendar mr-1 text-gray-400"></i>{{ $dateDebut->format('d/m/Y') }}@if($dateFin && !$dateFin->isSameDay($dateDebut)) → {{ $dateFin->format('d/m/Y') }}@endif</span>
                                                @if($evt->ville)<span><i class="fa-solid fa-location-dot mr-1 text-gray-400"></i>{{ $evt->ville }}</span>@endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5 flex-shrink-0">
                                            @if($estB2B)
                                            <span class="text-xs px-2 py-0.5 rounded-full font-bold text-white bg-blue-600"><i class="fa-solid fa-handshake mr-1"></i> B2B</span>
                                            @else
                                            <span class="text-xs px-2 py-0.5 rounded-full font-bold text-white bg-purple-600">Événement</span>
                                            @endif
                                            @if($gratuit)
                                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold text-white bg-green-600"><i class="fa-solid fa-gift mr-1"></i> Gratuit</span>
                                            @else
                                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold text-white" style="background-color: #C8102E;"><i class="fa-solid fa-ticket mr-1"></i> Payant</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if(!$gratuit && $evt->montant_inscription)
                                    <p class="text-xs font-bold mt-2" style="color: #C8102E;">{{ number_format($evt->montant_inscription, 0, ',', ' ') }} FCFA</p>
                                    @endif
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-50 transition text-sm font-medium flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Retour
                    </a>
                    <button wire:click="suivant" class="px-6 py-3 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2" style="background-color: #C8102E;">
                        Continuer <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            @endif

            {{-- ════ ÉTAPE 2 : REPRÉSENTANT ════ --}}
            @if($etape == 2)
            <div class="p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-user-tie" style="color: #C8102E;"></i> Représentant de l'entreprise
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom *</label>
                        <input wire:model="rep_nom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                        @error('rep_nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Prénom *</label>
                        <input wire:model="rep_prenom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                        @error('rep_prenom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Genre *</label>
                        <select wire:model="rep_genre" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                            <option value="">-- Choisir --</option>
                            <option value="homme">Homme</option>
                            <option value="femme">Femme</option>
                        </select>
                        @error('rep_genre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Fonction</label>
                        <select wire:model.live="rep_fonction" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($fonctions as $f)
                            <option value="{{ $f }}">{{ $f }}</option>
                            @endforeach
                        </select>
                        @if($rep_fonction === 'Autre')
                        <input wire:model="rep_fonction_autre" type="text"
                            class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                            placeholder="Précisez votre fonction...">
                        @endif
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                        <input wire:model="rep_telephone" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="+226 70 00 00 00">
                        @error('rep_telephone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Email <span class="text-gray-400 font-normal">(recommandé)</span></label>
                        <input wire:model="rep_email" type="email"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                            placeholder="representant@email.com">
                        @error('rep_email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Date de naissance</label>
                        <input wire:model="rep_date_naissance" type="date"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        @error('rep_date_naissance') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- ✅ OBJECTIF DE PARTICIPATION — max 3 + champ Autre --}}
                    <div class="col-span-2">
                        <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                            <p class="text-sm font-bold text-green-700 mb-1 flex items-center gap-2">
                                <i class="fa-solid fa-bullseye"></i> Objectif de participation
                                <span class="text-green-500 font-normal text-xs">(optionnel — max 3 choix)</span>
                            </p>
                            <p class="text-xs text-green-600 mb-4">Qu'espérez-vous obtenir de cet événement ?</p>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($objectifsOptions as $obj)
                                @php $selectionne = in_array($obj, $objectifs_participation); @endphp
                                <label class="cursor-pointer flex items-center gap-2 p-2.5 rounded-xl border transition
                                    {{ $selectionne ? 'border-green-400 bg-green-100' : (count($objectifs_participation) >= 3 && !$selectionne ? 'border-gray-100 bg-gray-50 opacity-50 cursor-not-allowed' : 'border-gray-200 hover:bg-gray-50') }}">
                                    <input type="checkbox"
                                        wire:model.live="objectifs_participation"
                                        value="{{ $obj }}"
                                        class="hidden"
                                        @if(count($objectifs_participation) >= 3 && !$selectionne) disabled @endif>
                                    <div class="w-5 h-5 rounded-md flex items-center justify-center flex-shrink-0 transition
                                        {{ $selectionne ? 'bg-green-500' : 'bg-gray-200' }}">
                                        @if($selectionne)
                                        <i class="fa-solid fa-check text-white text-xs"></i>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-700 font-medium">{{ $obj }}</span>
                                </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-400 mt-3">{{ count($objectifs_participation) }}/3 choix sélectionnés</p>
                            @if(in_array('Autre', $objectifs_participation))
                            <div class="mt-3">
                                <input wire:model="objectif_autre" type="text"
                                    class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm bg-white"
                                    placeholder="Précisez votre objectif...">
                                @error('objectif_autre') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            @endif
                        </div>
                    </div>

                </div>

                <div class="flex justify-between mt-6">
                    <button wire:click="precedent" class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-50 transition text-sm font-medium flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Précédent
                    </button>
                    <button wire:click="suivant" class="px-6 py-3 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2" style="background-color: #C8102E;">
                        Continuer <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            @endif

            {{-- ════ ÉTAPE 3 : PROFIL B2B (si événement avec B2B) ════ --}}
            @if($etape == 3 && $evenementEstB2B)
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-handshake" style="color: #007A3D;"></i> Profil B2B
                    </h3>
                    <span class="text-xs px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                        <i class="fa-solid fa-circle-info mr-1"></i> Optionnel
                    </span>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 flex items-start gap-3">
                    <i class="fa-solid fa-lightbulb text-green-600 mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="text-sm font-semibold text-green-700">Complétez votre profil B2B maintenant</p>
                        <p class="text-xs text-green-600 mt-1">
                            Ces informations permettent au système de vous proposer les meilleurs partenaires.
                            Vous pourrez aussi le compléter plus tard depuis votre espace.
                        </p>
                    </div>
                </div>

                <div class="space-y-5">

                    {{-- Secteur d'activité --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Votre secteur d'activité
                            <span class="text-gray-400 font-normal text-xs">(ce que vous faites / proposez)</span>
                        </label>
                        <select wire:model="secteur_activite"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                            <option value="">-- Choisir votre secteur --</option>
                            @foreach($secteurs as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                        <div class="mt-2">
                            <input wire:model="sous_secteur" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Sous-secteur (optionnel) — Ex: Céréales, Logiciels...">
                        </div>
                    </div>

                    {{-- Zone géographique --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Zone géographique ciblée</label>
                        <select wire:model="zone_geographique"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                            <option value="">-- Choisir --</option>
                            @foreach($zonesGeographiques as $z)
                            <option value="{{ $z }}">{{ $z }}</option>
                            @endforeach
                        </select>
                        @error('zone_geographique') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Types de partenariat --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">
                            Type de partenariat proposé
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
                    </div>

                    {{-- Profils partenaire --}}
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

                    {{-- Secteurs recherchés --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">
                            Secteurs que vous recherchez
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
                    </div>

                </div>

                <div class="flex justify-between mt-6 gap-3">
                    <button wire:click="precedent"
                        class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-50 transition text-sm font-medium flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Précédent
                    </button>
                    <div class="flex gap-3">
                        <button wire:click="passerEtapeB2B"
                            class="px-5 py-3 rounded-xl border border-gray-300 text-gray-500 hover:bg-gray-50 transition text-sm font-medium flex items-center gap-2">
                            <i class="fa-solid fa-forward"></i> Passer cette étape
                        </button>
                        <button wire:click="suivant"
                            class="px-6 py-3 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2"
                            style="background-color: #007A3D;">
                            <i class="fa-solid fa-check mr-1"></i> Enregistrer et continuer
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- ════ ÉTAPE CONFIRMATION ════ --}}
            @if(($etape == 3 && !$evenementEstB2B) || ($etape == 4 && $evenementEstB2B))
            <div class="p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check" style="color: #007A3D;"></i> Récapitulatif
                </h3>

                <div class="space-y-3 mb-6">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-bold text-gray-500 mb-2">ENTREPRISE</p>
                        <p class="font-bold text-gray-800">{{ $nom }}</p>
                        <p class="text-sm text-gray-600">IFU : {{ strtoupper($ifu) }}</p>
                        <p class="text-sm text-gray-600">{{ $secteur_activite }} — {{ $ville }}, {{ $pays }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-bold text-gray-500 mb-2">REPRÉSENTANT</p>
                        <p class="font-bold text-gray-800">{{ $rep_nom }} {{ $rep_prenom }}</p>
                        @if($rep_fonction)<p class="text-sm text-gray-600">{{ $rep_fonction }}</p>@endif
                        <p class="text-sm text-gray-600">{{ $rep_telephone }}</p>
                        @if($rep_email)<p class="text-sm text-gray-600">{{ $rep_email }}</p>@endif
                    </div>

                    @if($id_evenement)
                    @php $evt = $evenements->find($id_evenement); @endphp
                    @if($evt)
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <p class="text-xs font-bold text-blue-700 mb-1">ÉVÉNEMENT</p>
                        <p class="font-semibold text-gray-800">{{ $evt->nom }}</p>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($evt->date_debut)->format('d/m/Y') }}</p>
                    </div>
                    @endif
                    @endif

                    {{-- ✅ Objectifs dans le récapitulatif --}}
                    @if(!empty($objectifs_participation))
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                        <p class="text-xs font-bold text-green-700 mb-2">
                            <i class="fa-solid fa-bullseye mr-1"></i> OBJECTIF DE PARTICIPATION
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($objectifs_participation as $obj)
                            <span class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">{{ $obj }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-700">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        Votre dossier sera examiné par l'administration CCI-BF. Vous serez contacté après validation.
                    </div>
                </div>

                <div class="flex justify-between">
                    <button wire:click="precedent" class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-50 transition text-sm font-medium flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Précédent
                    </button>
                    <button wire:click="soumettre" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-8 py-3 rounded-xl text-white font-bold transition hover:opacity-90 text-sm shadow-lg flex items-center gap-2"
                        style="background-color: #007A3D;">
                        <span wire:loading.remove><i class="fa-solid fa-paper-plane mr-1"></i> Soumettre le dossier</span>
                        <span wire:loading><i class="fa-solid fa-spinner fa-spin mr-1"></i> Envoi...</span>
                    </button>
                </div>
            </div>
            @endif

        </div>
        @endif

        <p class="text-center text-xs text-gray-400 mt-6">
            Vous avez déjà un compte ?
            <a href="{{ route('login') }}" class="underline hover:text-gray-600">Se connecter</a>
        </p>
    </div>
</div>