<div class="min-h-screen flex" style="background-color: #f8f9fa;">

    {{-- PARTIE GAUCHE --}}
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
                Inscription Participant
            </h2>
            <p class="text-green-200 text-lg mb-8">
                Rejoignez les forums économiques B2B de la CCI-BF
            </p>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(200, 16, 46, 0.3);">
                        <i class="fa-solid fa-handshake text-white text-sm"></i>
                    </div>
                    <span class="text-green-100">Rencontrez des partenaires d'affaires</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(200, 16, 46, 0.3);">
                        <i class="fa-solid fa-calendar text-white text-sm"></i>
                    </div>
                    <span class="text-green-100">Planifiez vos rendez-vous B2B</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(200, 16, 46, 0.3);">
                        <i class="fa-solid fa-id-badge text-white text-sm"></i>
                    </div>
                    <span class="text-green-100">Obtenez votre badge officiel</span>
                </div>
            </div>
        </div>

        <div class="text-green-300 text-sm">
            © {{ date('Y') }} CCI-BF — Tous droits réservés
        </div>
    </div>

    {{-- PARTIE DROITE --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 overflow-y-auto">
        <div class="w-full max-w-md">

            {{-- Logo mobile --}}
            <div class="lg:hidden flex items-center gap-3 mb-8 justify-center">
                <img src="{{ asset('images/logo-ccibf.png') }}"
                    alt="CCI-BF" class="w-12 h-12 object-contain rounded-xl">
                <h1 class="text-2xl font-bold text-gray-800">GesB2B</h1>
            </div>

            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-800">S'inscrire</h2>
                <p class="text-gray-500 mt-1">Créez votre compte participant</p>
            </div>

            <div class="space-y-4">

                {{-- Nom + Prénom --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                        <input wire:model="nom" type="text"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                            placeholder="Votre nom">
                        @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                        <input wire:model="prenom" type="text"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                            placeholder="Votre prénom">
                        @error('prenom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Genre --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Genre *</label>
                    <select wire:model="genre"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                        <option value="">-- Choisir le genre --</option>
                        <option value="homme">Homme</option>
                        <option value="femme">Femme</option>
                    </select>
                    @error('genre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Fonction --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fonction / Poste
                        <span class="text-gray-400 font-normal">(optionnel)</span>
                    </label>
                    <input wire:model="fonction" type="text"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                        placeholder="Ex: Directeur Commercial, PDG...">
                </div>

                {{-- IFU --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Numéro IFU
                        <span class="text-gray-400 font-normal">(optionnel)</span>
                    </label>
                    <input wire:model.live="ifu" type="text"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                        placeholder="Ex: BF123456789">

                    {{-- Entreprise trouvée via IFU --}}
                    @if($entreprise_trouvee)
                    <div class="mt-2 bg-green-50 border border-green-300 rounded-xl p-3 flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
                        <div>
                            <p class="text-sm font-bold text-green-700">
                                Entreprise trouvée !
                            </p>
                            <p class="text-xs text-green-600">
                                {{ $entreprise_trouvee->nom }}
                                — {{ $entreprise_trouvee->secteur_activite }}
                                — {{ $entreprise_trouvee->pays }}
                            </p>
                            <p class="text-xs text-green-500 mt-0.5">
                                Vous serez automatiquement lié à cette entreprise.
                            </p>
                        </div>
                    </div>
                    @elseif($ifu && strlen($ifu) >= 3)
                    <div class="mt-2 bg-yellow-50 border border-yellow-200 rounded-xl p-3 flex items-center gap-2 text-xs text-yellow-700">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Aucune entreprise trouvée avec ce numéro IFU.
                        Vous serez inscrit comme participant indépendant.
                    </div>
                    @endif
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input wire:model="email" type="email"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                        placeholder="votre@email.com">
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Téléphone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                    <input wire:model="telephone" type="text"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                        placeholder="Ex: 70000000">
                    @error('telephone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Secteur --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Secteur d'activité</label>
                    <select wire:model="secteur_activite"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                        <option value="">-- Choisir --</option>
                        @foreach($secteurs as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Participation aux RDV --}}
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="participation_rdv"
                            class="rounded border-gray-300 w-5 h-5 text-green-600">
                        <div>
                            <p class="text-sm font-medium text-gray-700">
                                Je souhaite participer aux rendez-vous d'affaire
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Cochez si vous voulez être inclus dans le match-making B2B
                            </p>
                        </div>
                    </label>
                </div>

                {{-- Rôle --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Rôle de participation *
                    </label>
                    <div class="grid grid-cols-2 gap-3">

                        {{-- Exposant --}}
                        <button type="button" wire:click="$set('role', 'exposant')"
                            class="border-2 rounded-xl p-3 text-left transition
                                {{ $role === 'exposant'
                                    ? 'border-red-400 bg-red-50'
                                    : 'border-gray-200 hover:bg-gray-50' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fa-solid fa-store text-sm" style="color: #C8102E;"></i>
                                <p class="font-semibold text-sm text-gray-800">Exposant</p>
                            </div>
                            <p class="text-xs text-gray-400 leading-relaxed">
                                Vous exposez vos produits et services dans un stand dédié
                            </p>
                        </button>

                        {{-- Participant --}}
                        <button type="button" wire:click="$set('role', 'participant')"
                            class="border-2 rounded-xl p-3 text-left transition
                                {{ $role === 'participant'
                                    ? 'border-green-400 bg-green-50'
                                    : 'border-gray-200 hover:bg-gray-50' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fa-solid fa-user text-sm" style="color: #007A3D;"></i>
                                <p class="font-semibold text-sm text-gray-800">Participant</p>
                            </div>
                            <p class="text-xs text-gray-400 leading-relaxed">
                                Vous participez au forum pour rencontrer des partenaires
                            </p>
                        </button>

                    </div>
                    @error('role') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Événement --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Événement *</label>
                    <select wire:model="id_evenement"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                        <option value="">-- Choisir un événement --</option>
                        @foreach($evenements as $evenement)
                        <option value="{{ $evenement->id }}">
                            {{ $evenement->nom }} — {{ $evenement->date_debut }}
                            @if($evenement->type_paiement === 'gratuit') (Gratuit) @endif
                        </option>
                        @endforeach
                    </select>
                    @error('id_evenement') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- CDD --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Chef de Délégation (CDD) *
                    </label>
                    <select wire:model="id_cdd"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                        <option value="">-- Choisir votre CDD --</option>
                        @foreach($cdds as $cdd)
                        <option value="{{ $cdd->id }}">{{ $cdd->name }}</option>
                        @endforeach
                    </select>
                    @error('id_cdd') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Mot de passe --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe *</label>
                    <input wire:model="password" type="password"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                        placeholder="Minimum 8 caractères">
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer *</label>
                    <input wire:model="password_confirmation" type="password"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                        placeholder="Répéter le mot de passe">
                </div>

                {{-- Info --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-xs text-yellow-700 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                    Votre inscription sera soumise à validation par un
                    <strong>Chef de Délégation (CDD)</strong> avant de pouvoir payer.
                </div>

                {{-- Bouton --}}
                <button wire:click="sinscrire"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-not-allowed"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow-lg flex items-center justify-center gap-2"
                    style="background-color: #C8102E;">
                    <span wire:loading.remove>
                        <i class="fa-solid fa-user-plus mr-1"></i>
                        Créer mon compte
                    </span>
                    <span wire:loading>
                        <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                        Création en cours...
                    </span>
                </button>

                {{-- Liens --}}
                <p class="text-center text-sm text-gray-500">
                    Déjà inscrit ?
                    <a href="{{ route('login') }}" class="font-medium hover:underline"
                        style="color: #007A3D;">
                        Se connecter
                    </a>
                </p>
                <p class="text-center text-sm text-gray-500">
                    Vous représentez une entreprise ?
                    <a href="{{ route('inscription.entreprise') }}" class="font-medium hover:underline"
                        style="color: #C8102E;">
                        Inscrire mon entreprise
                    </a>
                </p>

            </div>
        </div>
    </div>

    {{-- MODAL SUCCÈS --}}
    @if($showSuccessModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">

            <div class="px-8 py-6 rounded-t-2xl text-white text-center"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3"
                    style="background-color: rgba(255,255,255,0.2);">
                    <i class="fa-solid fa-circle-check text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold">Inscription réussie !</h3>
                <p class="text-green-200 text-sm mt-1">
                    Votre compte a été créé avec succès
                </p>
            </div>

            <div class="p-8">
                <div class="space-y-3 mb-6">

                    {{-- Code d'accès --}}
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                        <p class="text-xs text-red-500 mb-1 font-medium">
                            <i class="fa-solid fa-key mr-1"></i>
                            Votre code d'accès
                        </p>
                        <p class="font-mono font-bold text-2xl text-red-700 tracking-widest">
                            {{ $code_acces_genere }}
                        </p>
                        <p class="text-xs text-red-400 mt-1">
                            Notez ce code ! Il vous permettra de vous connecter.
                        </p>
                    </div>

                    {{-- Entreprise liée --}}
                    @if($entreprise_trouvee)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-700 flex items-center gap-2">
                        <i class="fa-solid fa-building"></i>
                        Vous êtes lié à <strong>{{ $entreprise_trouvee->nom }}</strong>
                    </div>
                    @endif

                    {{-- Info --}}
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-sm text-yellow-700">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        Votre préinscription est en attente de validation par un CDD.
                    </div>

                    {{-- Étapes suivantes --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-sm text-blue-700">
                        <p class="font-semibold mb-2">Prochaines étapes :</p>
                        <ol class="space-y-1 text-xs">
                            <li>1. Connectez-vous avec votre email et mot de passe</li>
                            <li>2. Attendez la validation de votre CDD</li>
                            <li>3. Effectuez votre paiement</li>
                            <li>4. Émettez vos souhaits de RDV</li>
                        </ol>
                    </div>

                </div>

                <a href="{{ route('login') }}"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow flex items-center justify-center gap-2"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Se connecter maintenant
                </a>

            </div>
        </div>
    </div>
    @endif

</div>