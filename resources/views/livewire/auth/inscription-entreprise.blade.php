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
                Inscription Entreprise
            </h2>
            <p class="text-green-200 text-lg mb-8">
                Inscrivez votre entreprise aux forums économiques B2B
            </p>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(200, 16, 46, 0.3);">
                        <i class="fa-solid fa-building text-white text-sm"></i>
                    </div>
                    <span class="text-green-100">Présentez votre entreprise</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(200, 16, 46, 0.3);">
                        <i class="fa-solid fa-users text-white text-sm"></i>
                    </div>
                    <span class="text-green-100">Gérez vos participants</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(200, 16, 46, 0.3);">
                        <i class="fa-solid fa-handshake text-white text-sm"></i>
                    </div>
                    <span class="text-green-100">Trouvez des partenaires</span>
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
                <h2 class="text-3xl font-bold text-gray-800">Inscrire mon entreprise</h2>
                <p class="text-gray-500 mt-1">Créez votre compte entreprise</p>
            </div>

            <div class="space-y-4">

                {{-- SECTION RESPONSABLE --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                    <h3 class="text-sm font-bold text-blue-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-user-tie"></i>
                        Informations du responsable
                    </h3>
                    <div class="grid grid-cols-2 gap-3">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                            <input wire:model="nom_responsable" type="text"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm"
                                placeholder="Votre nom">
                            @error('nom_responsable')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                            <input wire:model="prenom_responsable" type="text"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm"
                                placeholder="Votre prénom">
                            @error('prenom_responsable')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- ← Fonction obligatoire avec liste --}}
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Fonction / Poste *
                            </label>
                            <div class="flex gap-2">
                                <select wire:model="fonction_responsable"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm">
                                    <option value="">-- Choisir --</option>
                                    <option>Directeur Général</option>
                                    <option>Directeur Commercial</option>
                                    <option>PDG</option>
                                    <option>Gérant</option>
                                    <option>Responsable Export</option>
                                    <option>Responsable Partenariats</option>
                                    <option>Chargé de Développement</option>
                                    <option>Représentant</option>
                                    <option>Autre</option>
                                </select>
                            </div>
                            @if($fonction_responsable == 'Autre')
                            <input wire:model="fonction_responsable" type="text"
                                class="w-full mt-2 border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm"
                                placeholder="Précisez votre fonction...">
                            @endif
                            @error('fonction_responsable')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- SECTION ENTREPRISE --}}
                <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                    <h3 class="text-sm font-bold text-green-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-building"></i>
                        Informations de l'entreprise
                    </h3>
                    <div class="space-y-4">

                        {{-- Nom entreprise --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nom de l'entreprise *
                            </label>
                            <input wire:model="nom" type="text"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                                placeholder="Ex: IsaTech SARL">
                            @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- ← IFU obligatoire avec format --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Numéro IFU *
                            </label>
                            <input wire:model="ifu" type="text"
                                maxlength="9"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-mono uppercase"
                                placeholder="Ex: 12345678A">
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="fa-solid fa-circle-info mr-1"></i>
                                Format : 8 chiffres suivis d'une lettre (ex: 12345678A)
                            </p>
                            @error('ifu') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- ← Secteur obligatoire --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Secteur d'activité *
                            </label>
                            <select wire:model="secteur_activite"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                                <option value="">-- Choisir --</option>
                                @foreach($secteurs as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                                @endforeach
                            </select>
                            @error('secteur_activite') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- ← Sous-secteur obligatoire --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Sous-secteur *
                            </label>
                            <input wire:model="sous_secteur" type="text"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                                placeholder="Ex: Informatique, Agro-alimentaire...">
                            @error('sous_secteur') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Description des activités *
                            </label>
                            <textarea wire:model="description_activites" rows="3"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm resize-none"
                                placeholder="Décrivez brièvement les activités..."></textarea>
                            @error('description_activites') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Produits --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Principaux produits / Savoir-faire
                                <span class="text-gray-400 font-normal">(optionnel)</span>
                            </label>
                            <textarea wire:model="principaux_produits" rows="2"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm resize-none"
                                placeholder="Ex: Logiciels de gestion..."></textarea>
                        </div>

                        {{-- ← Pays + Ville dynamique --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pays *</label>
                                <select wire:model.live="pays"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                                    <option value="">-- Choisir --</option>
                                    @foreach($pays_liste as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                    @endforeach
                                </select>
                                @error('pays') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                                {{-- ← Ville dynamique selon pays --}}
                                @if($pays && count($villesDisponibles) > 1)
                                <select wire:model="ville"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                                    <option value="">-- Choisir --</option>
                                    @foreach($villesDisponibles as $v)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                                @else
                                <input wire:model="ville" type="text"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                                    placeholder="Votre ville">
                                @endif
                                @error('ville') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Contact --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Contact (téléphone) *
                            </label>
                            <input wire:model="contact" type="text"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                                placeholder="Ex: +226 70 00 00 00">
                            @error('contact') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                    </div>
                </div>

                {{-- SECTION COMPTE --}}
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                    <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-lock"></i>
                        Informations de connexion
                    </h3>
                    <div class="space-y-4">

                        {{-- ← CDD optionnel --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Chef de Délégation (CDD)
                                <span class="text-gray-400 font-normal">(optionnel)</span>
                            </label>
                            <select wire:model="id_cdd"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                                <option value="">-- Aucun CDD --</option>
                                @foreach($cdds as $cdd)
                                <option value="{{ $cdd->id }}">{{ $cdd->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="fa-solid fa-circle-info mr-1"></i>
                                Le CDD n'est pas obligatoire pour s'inscrire.
                            </p>
                        </div>

                        {{-- ← Email obligatoire --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input wire:model="email" type="email"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                                placeholder="contact@entreprise.com">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

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

                    </div>
                </div>

                {{-- Info --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-xs text-yellow-700 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                    Votre entreprise sera soumise à validation par l'administration avant d'être active.
                </div>

                {{-- Bouton --}}
                <button wire:click="sinscrire"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-not-allowed"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow-lg flex items-center justify-center gap-2"
                    style="background-color: #C8102E;">
                    <span wire:loading.remove>
                        <i class="fa-solid fa-building mr-1"></i>
                        Inscrire mon entreprise
                    </span>
                    <span wire:loading>
                        <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                        Inscription en cours...
                    </span>
                </button>

                <p class="text-center text-sm text-gray-500">
                    Déjà inscrit ?
                    <a href="{{ route('login') }}" class="font-medium hover:underline"
                        style="color: #007A3D;">
                        Se connecter
                    </a>
                </p>

            </div>
        </div>
    </div>

    {{-- MODAL SUCCÈS --}}
    @if($showSuccessModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 text-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
                style="background-color: #e6f4ed;">
                <i class="fa-solid fa-circle-check text-4xl" style="color: #007A3D;"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Entreprise inscrite !</h3>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-sm text-blue-700 mb-4 text-left">
                <p class="font-semibold mb-1">
                    <i class="fa-solid fa-user-tie mr-1"></i>
                    Responsable enregistré :
                </p>
                <p class="text-xs">
                    {{ $nom_responsable }} {{ $prenom_responsable }}
                    @if($fonction_responsable)
                    — {{ $fonction_responsable }}
                    @endif
                </p>
            </div>

            <p class="text-gray-500 text-sm mb-6">
                Votre entreprise est en attente de validation.
            </p>

            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-sm text-yellow-700 mb-6 text-left">
                <p class="font-semibold mb-1">Prochaines étapes :</p>
                <ol class="space-y-1 text-xs">
                    <li>1. Connectez-vous avec votre email et mot de passe</li>
                    <li>2. Attendez la validation de l'administration</li>
                    <li>3. Ajoutez vos participants</li>
                    <li>4. Émettez vos souhaits de RDV</li>
                </ol>
            </div>

            <a href="{{ route('login') }}"
                class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow flex items-center justify-center gap-2"
                style="background-color: #C8102E;">
                <i class="fa-solid fa-right-to-bracket"></i>
                Se connecter
            </a>
        </div>
    </div>
    @endif

</div>