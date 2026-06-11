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
                Rejoignez votre entreprise
            </h2>
            <p class="text-green-200 text-lg mb-8">
                Inscrivez-vous en tant que membre d'une entreprise
                déjà enregistrée sur la plateforme.
            </p>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(200, 16, 46, 0.3);">
                        <i class="fa-solid fa-building text-white text-sm"></i>
                    </div>
                    <span class="text-green-100">Liez-vous à votre entreprise via l'IFU</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(200, 16, 46, 0.3);">
                        <i class="fa-solid fa-user-check text-white text-sm"></i>
                    </div>
                    <span class="text-green-100">Le représentant valide votre adhésion</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(200, 16, 46, 0.3);">
                        <i class="fa-solid fa-handshake text-white text-sm"></i>
                    </div>
                    <span class="text-green-100">Participez aux forums B2B</span>
                </div>
            </div>
        </div>

        <div class="text-green-300 text-sm">
            © {{ date('Y') }} CCI-BF — Tous droits réservés
        </div>
    </div>

    {{-- PARTIE DROITE --}}
    <div class="w-full lg:w-1/2 flex items-start justify-center p-8 overflow-y-auto">
        <div class="w-full max-w-md">

            {{-- Logo mobile --}}
            <div class="lg:hidden flex items-center gap-3 mb-6 justify-center">
                <img src="{{ asset('images/logo-ccibf.png') }}"
                    alt="CCI-BF" class="w-12 h-12 object-contain rounded-xl">
                <h1 class="text-2xl font-bold text-gray-800">GesB2B</h1>
            </div>

            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-800">S'inscrire</h2>
                <p class="text-gray-500 mt-1">
                    Créez votre compte membre d'entreprise
                </p>
            </div>

            {{-- Info --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-5 text-xs text-blue-700 flex items-start gap-2">
                <i class="fa-solid fa-circle-info mt-0.5 flex-shrink-0"></i>
                <div>
                    Vous devez connaître le <strong>numéro IFU</strong> de votre entreprise.
                    Votre représentant devra <strong>valider votre adhésion</strong>
                    avant que vous puissiez accéder à la plateforme.
                </div>
            </div>

            <div class="space-y-5">

                {{-- IFU --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        <i class="fa-solid fa-building mr-1" style="color: #007A3D;"></i>
                        Numéro IFU de votre entreprise *
                    </label>
                    <input wire:model.live="ifu" type="text"
                        maxlength="9"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm font-mono uppercase"
                        placeholder="Ex: 12345678A">
                    <p class="text-xs text-gray-400 mt-1">
                        Format : 8 chiffres + 1 lettre (ex: 12345678A)
                    </p>
                    @error('ifu')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror

                    @if($entreprise_trouvee)
                    <div class="mt-2 bg-green-50 border border-green-300 rounded-xl p-3 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0"
                            style="background-color: #007A3D;">
                            {{ strtoupper(substr($entreprise_trouvee->nom, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-green-700">
                                <i class="fa-solid fa-circle-check mr-1"></i>
                                Entreprise trouvée !
                            </p>
                            <p class="text-xs text-green-600">
                                {{ $entreprise_trouvee->nom }}
                                · {{ $entreprise_trouvee->secteur_activite }}
                                · {{ $entreprise_trouvee->ville }}, {{ $entreprise_trouvee->pays }}
                            </p>
                        </div>
                    </div>
                    @elseif(strlen($ifu) >= 3)
                    <div class="mt-2 bg-red-50 border border-red-200 rounded-xl p-3 text-xs text-red-600 flex items-center gap-2">
                        <i class="fa-solid fa-circle-xmark flex-shrink-0"></i>
                        Aucune entreprise trouvée avec ce numéro IFU.
                    </div>
                    @endif
                </div>

                {{-- Nom / Prénom --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom *</label>
                        <input wire:model="nom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                            placeholder="Votre nom">
                        @error('nom')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Prénom *</label>
                        <input wire:model="prenom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                            placeholder="Votre prénom">
                        @error('prenom')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Genre --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Genre *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="genre" value="homme" class="hidden peer">
                            <div class="p-3 border-2 rounded-xl text-center transition text-sm
                                peer-checked:border-blue-400 peer-checked:bg-blue-50
                                hover:bg-gray-50 border-gray-200 text-gray-600">
                                <i class="fa-solid fa-mars text-blue-500 mr-1"></i> Homme
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="genre" value="femme" class="hidden peer">
                            <div class="p-3 border-2 rounded-xl text-center transition text-sm
                                peer-checked:border-pink-400 peer-checked:bg-pink-50
                                hover:bg-gray-50 border-gray-200 text-gray-600">
                                <i class="fa-solid fa-venus text-pink-500 mr-1"></i> Femme
                            </div>
                        </label>
                    </div>
                    @error('genre')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Fonction --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fonction *</label>
                    <select wire:model.live="fonction"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
                        <option value="">-- Choisir votre fonction --</option>
                        @foreach($fonctions as $f)
                        <option value="{{ $f }}">{{ $f }}</option>
                        @endforeach
                    </select>
                    @if($fonction == 'Autre')
                    <input wire:model="fonction_autre" type="text"
                        class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                        placeholder="Précisez votre fonction...">
                    @endif
                    @error('fonction')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Téléphone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone *</label>
                    <input wire:model="telephone" type="text"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                        placeholder="Ex: +226 70 00 00 00">
                    @error('telephone')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email optionnel --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Email
                        <span class="text-gray-400 font-normal">(optionnel)</span>
                    </label>
                    <input wire:model.live="email" type="email"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                        placeholder="votre@email.com">
                    @if(!$email)
                    <p class="text-xs text-orange-500 mt-1">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        Sans email, vous vous connecterez uniquement avec votre code d'accès.
                    </p>
                    @endif
                    @error('email')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Mot de passe si email fourni --}}
                @if($email)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe *</label>
                        <input wire:model="password" type="password"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                            placeholder="Min. 8 caractères">
                        @error('password')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirmer *</label>
                        <input wire:model="password_confirmation" type="password"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                            placeholder="Répéter">
                    </div>
                </div>
                @endif

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

                <div class="text-center space-y-2">
                    <p class="text-sm text-gray-500">
                        Déjà inscrit ?
                        <a href="{{ route('login') }}" class="font-medium hover:underline"
                            style="color: #007A3D;">Se connecter</a>
                    </p>
                    <p class="text-sm text-gray-500">
                        Vous représentez une entreprise ?
                        <a href="{{ route('inscription.entreprise') }}" class="font-medium hover:underline"
                            style="color: #C8102E;">Inscrire mon entreprise</a>
                    </p>
                </div>

            </div>
        </div>
    </div>

    {{-- ============================================================
         MODAL SUCCÈS
    ============================================================ --}}
    @if($showSuccessModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-y-auto max-h-[90vh]">

            {{-- Header --}}
            <div class="px-8 py-6 rounded-t-2xl text-white text-center"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 bg-white/20">
                    <i class="fa-solid fa-circle-check text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold">Compte créé !</h3>
                <p class="text-green-200 text-sm mt-1">
                    En attente de validation par le représentant
                </p>
            </div>

            <div class="p-8 space-y-4">

                {{-- Entreprise --}}
                @if($entreprise_trouvee)
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0"
                        style="background-color: #007A3D;">
                        {{ strtoupper(substr($entreprise_trouvee->nom, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Entreprise</p>
                        <p class="font-bold text-gray-800">{{ $entreprise_trouvee->nom }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $entreprise_trouvee->secteur_activite }}
                            · {{ $entreprise_trouvee->ville }}
                        </p>
                    </div>
                </div>
                @endif

                {{-- Code d'accès --}}
                <div class="bg-red-50 border-2 border-red-200 rounded-xl p-4 text-center">
                    <p class="text-xs text-red-500 font-medium mb-1">
                        <i class="fa-solid fa-key mr-1"></i>
                        Votre code d'accès
                    </p>
                    <p class="font-mono font-bold text-red-700 text-3xl tracking-widest">
                        {{ $code_acces_genere }}
                    </p>
                    <p class="text-xs text-red-400 mt-1">
                        Notez ce code ! Il vous permettra de vous connecter.
                    </p>
                </div>

                {{-- Statut --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-xs text-yellow-700 flex items-start gap-2">
                    <i class="fa-solid fa-clock mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="font-bold mb-1">En attente de validation</p>
                        Votre demande d'adhésion à
                        <strong>{{ $entreprise_trouvee->nom ?? '' }}</strong>
                        est en cours de traitement.
                    </div>
                </div>

                {{-- Prochaines étapes --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-700">
                    <p class="font-bold mb-1">
                        <i class="fa-solid fa-list-check mr-1"></i>
                        Prochaines étapes :
                    </p>
                    <ol class="space-y-1">
                        <li>1. Votre adhésion sera validée par le représentant</li>
                        <li>2. Connectez-vous avec votre code : <strong>{{ $code_acces_genere }}</strong></li>
                        <li>3. Complétez votre profil partenaire</li>
                        <li>4. Émettez vos souhaits de RDV</li>
                    </ol>
                </div>

                {{-- ← Bouton selon si email fourni ou non --}}
                @if($email)
                <button wire:click="allerAuDashboard"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow flex items-center justify-center gap-2"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-gauge"></i>
                    Accéder à mon espace
                </button>
                @else
                <a href="{{ route('login') }}"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow flex items-center justify-center gap-2 block text-center"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Se connecter avec mon code
                </a>
                @endif

            </div>
        </div>
    </div>
    @endif

</div>