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
            <h2 class="text-2xl font-bold text-gray-800">Inscription</h2>
            <p class="text-gray-500 text-sm mt-1">Remplissez ce formulaire pour vous préinscrire</p>
        </div>

        {{-- CONFIRMATION --}}
        @if($confirme)
        <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5" style="background-color: #007A3D;">
                <i class="fa-solid fa-circle-check text-white text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-3">Préinscription envoyée !</h3>
            <p class="text-gray-600 mb-4">Votre dossier a bien été reçu. L'équipe CCI-BF va traiter votre demande dans les meilleurs délais.</p>
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 text-left">
                <p class="text-sm font-bold text-blue-700 mb-2"><i class="fa-solid fa-circle-info mr-1"></i> Prochaines étapes :</p>
                <ul class="text-xs text-blue-600 space-y-1.5">
                    <li><i class="fa-solid fa-check text-green-500 mr-1"></i> Votre dossier sera examiné par l'administration</li>
                    <li><i class="fa-solid fa-check text-green-500 mr-1"></i> Vous serez contacté pour la validation</li>
                    <li><i class="fa-solid fa-check text-green-500 mr-1"></i> Après validation, vous recevrez vos identifiants de connexion</li>
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
                    ? [1 => 'Type', 2 => 'Informations', 3 => 'Profil B2B', 4 => 'Confirmation']
                    : [1 => 'Type', 2 => 'Informations', 3 => 'Confirmation'];
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

            {{-- ════ ÉTAPE 1 : TYPE D'INSCRIT ════ --}}
            @if($etape == 1)
            <div class="p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-user" style="color: #C8102E;"></i> Qui êtes-vous ?
                </h3>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <label class="cursor-pointer">
                        <input type="radio" wire:model.live="type_inscrit" value="particulier" class="hidden peer">
                        <div class="p-5 border-2 rounded-xl text-center transition peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50 border-gray-200">
                            <i class="fa-solid fa-user text-3xl mb-3 block text-gray-300"></i>
                            <p class="font-bold text-gray-800">Participant individuel</p>
                            <p class="text-xs text-gray-400 mt-1">Je m'inscris à titre personnel en tant que professionnel ou représentant</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" wire:model.live="type_inscrit" value="membre_entreprise" class="hidden peer">
                        <div class="p-5 border-2 rounded-xl text-center transition peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50 border-gray-200">
                            <i class="fa-solid fa-building text-3xl mb-3 block text-gray-300"></i>
                            <p class="font-bold text-gray-800">Membre d'entreprise</p>
                            <p class="text-xs text-gray-400 mt-1">Je représente une entreprise déjà enregistrée</p>
                        </div>
                    </label>
                </div>

                @if($type_inscrit === 'membre_entreprise')
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-5">
                    <p class="text-sm font-bold text-blue-700 mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-building"></i> Numéro IFU de votre entreprise
                    </p>
                    <p class="text-xs text-blue-600 mb-3">Saisissez l'IFU pour lier votre compte à l'entreprise.</p>
                    <input wire:model.live="ifu" type="text" maxlength="9" placeholder="Ex: 12345678A"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm font-mono uppercase bg-white">
                    @if($entreprise_trouvee)
                    <div class="mt-3 bg-green-50 border border-green-200 rounded-xl p-3 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0" style="background-color: #007A3D;">
                            {{ strtoupper(substr($entreprise_trouvee->nom, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-green-800 text-sm">{{ $entreprise_trouvee->nom }}</p>
                            <p class="text-xs text-green-600">{{ $entreprise_trouvee->secteur_activite }} — {{ $entreprise_trouvee->ville }}</p>
                        </div>
                        <i class="fa-solid fa-circle-check text-green-500 text-xl ml-auto"></i>
                    </div>
                    @elseif($erreur_ifu)
                    <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                        <i class="fa-solid fa-triangle-exclamation"></i> {{ $erreur_ifu }}
                    </p>
                    @endif
                </div>
                @endif

                {{-- Événement --}}
                <div class="mb-5">
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

                {{-- CDD référent --}}
                @if($cdds->isNotEmpty())
                <div class="mb-5">
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">
                        CDD référent <span class="text-gray-400 font-normal">(optionnel)</span>
                    </label>
                    <select wire:model="id_cdd" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                        <option value="">-- Aucun CDD référent --</option>
                        @foreach($cdds as $cdd)
                        <option value="{{ $cdd->id }}">{{ $cdd->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="flex justify-between">
                    <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-50 transition text-sm font-medium flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Retour
                    </a>
                    <button wire:click="suivant" class="px-6 py-3 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2" style="background-color: #C8102E;">
                        Continuer <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            @endif

            {{-- ════ ÉTAPE 2 : INFORMATIONS PERSONNELLES ════ --}}
            @if($etape == 2)
            <div class="p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-address-card" style="color: #C8102E;"></i> Informations personnelles
                </h3>

                @if($entreprise_trouvee)
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0" style="background-color: #007A3D;">
                        {{ strtoupper(substr($entreprise_trouvee->nom, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Vous représentez</p>
                        <p class="font-bold text-gray-800">{{ $entreprise_trouvee->nom }}</p>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom *</label>
                        <input wire:model="nom" type="text" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                        @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Prénom *</label>
                        <input wire:model="prenom" type="text" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                        @error('prenom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Genre *</label>
                        <select wire:model="genre" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                            <option value="">-- Choisir --</option>
                            <option value="homme">Homme</option>
                            <option value="femme">Femme</option>
                        </select>
                        @error('genre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                        <input wire:model="telephone" type="text" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm" placeholder="+226 70 00 00 00">
                        @error('telephone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Email <span class="text-gray-400 font-normal">(recommandé)</span></label>
                        <input wire:model="email" type="email" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm" placeholder="votre@email.com">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Date de naissance</label>
                        <input wire:model="date_naissance" type="date" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        @error('date_naissance') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Fonction --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Fonction</label>
                        <select wire:model.live="fonction" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($fonctions as $f)
                            <option value="{{ $f }}">{{ $f }}</option>
                            @endforeach
                        </select>
                        @if($fonction === 'Autre')
                        <input wire:model.live="fonction_autre" type="text" class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm" placeholder="Précisez votre fonction...">
                        @endif
                    </div>

                    {{-- Filière + Université si Étudiant --}}
                    @if($estEtudiant)
                    <div class="col-span-2">
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <p class="text-xs font-bold text-blue-700 mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-graduation-cap"></i> Informations académiques
                            </p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Filière *</label>
                                    <input wire:model="filiere" type="text" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm bg-white" placeholder="Ex: Informatique, Droit...">
                                    @error('filiere') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Université / École *</label>
                                    <input wire:model="universite" type="text" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm bg-white" placeholder="Ex: Université Aube Nouvelle...">
                                    @error('universite') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Pays --}}
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

                    {{-- Ville --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Ville *</label>
                        @if($pays && count($villesDisponibles) > 1)
                        <select wire:model.live="ville" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($villesDisponibles as $v)
                            <option value="{{ $v }}">{{ $v }}</option>
                            @endforeach
                        </select>
                        @if($ville === 'Autre')
                        <div class="mt-2">
                            <input wire:model="ville_autre" list="villes-suggestions" type="text" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm" placeholder="Saisissez votre ville...">
                            <datalist id="villes-suggestions">
                                <option value="Ouagadougou"><option value="Bobo-Dioulasso"><option value="Abidjan"><option value="Dakar"><option value="Bamako">
                            </datalist>
                        </div>
                        @error('ville_autre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @endif
                        @else
                        <input wire:model="ville" type="text" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm" placeholder="Ex: Ouagadougou">
                        @endif
                        @error('ville') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                        <p class="text-xs font-bold text-gray-500 mb-2">IDENTITÉ</p>
                        <p class="font-bold text-gray-800">{{ $nom }} {{ $prenom }}</p>
                        <p class="text-sm text-gray-600">{{ $genre == 'homme' ? 'M.' : 'Mme' }}@if($fonction) — {{ $fonction }}@endif</p>
                        @if($date_naissance)
                        <p class="text-xs text-gray-400 mt-1">Né(e) le {{ \Carbon\Carbon::parse($date_naissance)->format('d/m/Y') }}</p>
                        @endif
                        @if($estEtudiant && $filiere)
                        <div class="mt-2 bg-blue-50 rounded-lg px-3 py-2 text-xs text-blue-600 flex items-center gap-2">
                            <i class="fa-solid fa-graduation-cap"></i> {{ $filiere }} — {{ $universite }}
                        </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-bold text-gray-500 mb-2">CONTACT</p>
                        <p class="text-sm text-gray-700"><i class="fa-solid fa-phone mr-1 text-gray-400"></i>{{ $telephone }}</p>
                        @if($email)
                        <p class="text-sm text-gray-700 mt-1"><i class="fa-solid fa-envelope mr-1 text-gray-400"></i>{{ $email }}</p>
                        @endif
                        <p class="text-sm text-gray-700 mt-1"><i class="fa-solid fa-location-dot mr-1 text-gray-400"></i>{{ $ville === 'Autre' ? $ville_autre : $ville }}, {{ $pays }}</p>
                    </div>

                    @if($entreprise_trouvee)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                        <p class="text-xs font-bold text-green-700 mb-1">ENTREPRISE</p>
                        <p class="font-semibold text-gray-800">{{ $entreprise_trouvee->nom }}</p>
                        <p class="text-xs text-gray-500">IFU : {{ $entreprise_trouvee->ifu }}</p>
                    </div>
                    @endif

                    @if($id_evenement)
                    @php $evt = $evenements->find($id_evenement); @endphp
                    @if($evt)
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <p class="text-xs font-bold text-blue-700 mb-1">ÉVÉNEMENT</p>
                        <p class="font-semibold text-gray-800">{{ $evt->nom }}</p>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($evt->date_debut)->format('d/m/Y') }} — {{ $evt->ville }}</p>
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

                    @if($id_cdd)
                    @php $cddSelectionne = $cdds->find($id_cdd); @endphp
                    @if($cddSelectionne)
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                        <p class="text-xs font-bold text-purple-700 mb-1">CDD RÉFÉRENT</p>
                        <p class="font-semibold text-gray-800"><i class="fa-solid fa-user-tie mr-1 text-purple-500"></i>{{ $cddSelectionne->name }}</p>
                    </div>
                    @endif
                    @endif

                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-700">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        En soumettant ce formulaire, votre dossier sera examiné par l'administration.
                    </div>
                </div>

                <div class="flex justify-between">
                    <button wire:click="precedent" class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-50 transition text-sm font-medium flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Précédent
                    </button>
                    <button wire:click="soumettre" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-8 py-3 rounded-xl text-white font-bold transition hover:opacity-90 text-sm shadow-lg flex items-center gap-2"
                        style="background-color: #007A3D;">
                        <span wire:loading.remove><i class="fa-solid fa-paper-plane mr-1"></i> Soumettre ma préinscription</span>
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