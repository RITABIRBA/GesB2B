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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Genre *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" wire:click="$set('genre', 'homme')"
                            class="border rounded-xl p-2.5 text-center text-sm transition flex items-center justify-center gap-2
                                {{ $genre === 'homme' ? 'border-blue-400 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-mars"></i> Homme
                        </button>
                        <button type="button" wire:click="$set('genre', 'femme')"
                            class="border rounded-xl p-2.5 text-center text-sm transition flex items-center justify-center gap-2
                                {{ $genre === 'femme' ? 'border-pink-400 bg-pink-50 text-pink-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-venus"></i> Femme
                        </button>
                    </div>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rôle *</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" wire:click="$set('role', 'exposant')"
                            class="border rounded-xl p-2 text-center text-sm transition
                                {{ $role === 'exposant' ? 'border-red-400 bg-red-50 text-red-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-store mr-1"></i> Exposant
                        </button>
                        <button type="button" wire:click="$set('role', 'visiteur')"
                            class="border rounded-xl p-2 text-center text-sm transition
                                {{ $role === 'visiteur' ? 'border-red-400 bg-red-50 text-red-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-user mr-1"></i> Visiteur
                        </button>
                        <button type="button" wire:click="$set('role', 'vip')"
                            class="border rounded-xl p-2 text-center text-sm transition
                                {{ $role === 'vip' ? 'border-yellow-400 bg-yellow-50 text-yellow-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-star mr-1"></i> VIP
                        </button>
                        <button type="button" wire:click="$set('role', 'organisateur')"
                            class="border rounded-xl p-2 text-center text-sm transition
                                {{ $role === 'organisateur' ? 'border-red-400 bg-red-50 text-red-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-crown mr-1"></i> Organisateur
                        </button>
                    </div>
                </div>

                {{-- Événement --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Événement *</label>
                    <select wire:model="id_evenement"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                        <option value="">-- Choisir un événement --</option>
                        @foreach($evenements as $evenement)
                        <option value="{{ $evenement->id }}">{{ $evenement->nom }} — {{ $evenement->date_debut }}</option>
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
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow-lg"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-user-plus mr-1"></i>
                    Créer mon compte
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

                    {{-- Info --}}
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-sm text-yellow-700">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        Votre préinscription est en attente de validation par un CDD.
                        Vous serez notifié après validation.
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