<div>
    @if($alertSuccess)
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ $alertSuccess }}
    </div>
    @endif

    @if($alertError)
    <div class="bg-red-100 border border-red-300 text-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
        {{ $alertError }}
    </div>
    @endif

    @if(!$cdd)
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-8 text-center">
        <i class="fa-solid fa-triangle-exclamation text-4xl text-orange-500 mb-3 block"></i>
        <p class="text-lg font-medium text-gray-700">Profil CDD non trouvé</p>
        <p class="text-sm text-gray-500 mt-1">Contactez l'administration.</p>
    </div>
    @else

    {{-- En-tête --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold flex-shrink-0"
                style="background-color: #2d5a8e;">
                {{ strtoupper(substr($cdd->nom ?? 'C', 0, 1)) }}
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-800">{{ $cdd->nom ?? 'Chef de Délégation' }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    <i class="fa-solid fa-location-dot text-gray-400 mr-1"></i>
                    {{ $cdd->zone_affichage }}
                </p>
                @if($cdd->evenement)
                <p class="text-xs text-gray-400 mt-0.5">
                    <i class="fa-solid fa-calendar mr-1"></i>
                    {{ $cdd->evenement->nom }}
                </p>
                @endif
            </div>
            <span class="px-3 py-1.5 rounded-full text-xs font-medium text-white" style="background-color: #2d5a8e;">
                Chef de Délégation
            </span>
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4" style="border-color: #2d5a8e;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl" style="background-color: #e8f0fb;">
                <i class="fa-solid fa-users" style="color: #2d5a8e;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Membres de ma délégation</p>
                <p class="text-3xl font-bold text-gray-800">{{ $membres->count() }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4" style="border-color: #007A3D;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl" style="background-color: #e6f4ed;">
                <i class="fa-solid fa-calendar-star" style="color: #007A3D;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Événements couverts</p>
                <p class="text-3xl font-bold text-gray-800">{{ $evenements->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Aide --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-4 mb-6 text-sm text-blue-700 flex items-start gap-2">
        <i class="fa-solid fa-circle-info mt-0.5"></i>
        <div>
            Vous pouvez aider les membres de votre délégation à <strong>s'inscrire</strong>,
            à <strong>payer</strong> leur inscription, et à <strong>émettre des souhaits</strong>
            de rendez-vous directement depuis cette page.
        </div>
    </div>

    {{-- Liste des membres --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-users" style="color: #2d5a8e;"></i>
                Ma délégation
            </h3>
            <button wire:click="openModalMembre"
                class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
                style="background-color: #C8102E;">
                <i class="fa-solid fa-user-plus"></i>
                Ajouter un membre
            </button>
        </div>

        @forelse($membres as $membre)
        @php
            $inscriptionActive = $membre->inscriptions->sortByDesc('id')->first();
        @endphp
        <div class="border border-gray-200 rounded-xl p-4 mb-3 last:mb-0">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                        style="background-color: {{ $membre->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                        {{ strtoupper(substr($membre->prenom ?? 'X', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ $membre->nom }} {{ $membre->prenom }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $membre->evenement->nom ?? '-' }}
                            @if($membre->telephone) · {{ $membre->telephone }} @endif
                        </p>
                        @if($inscriptionActive)
                        @php
                            $colors = ['paye' => 'bg-green-100 text-green-700', 'en_attente' => 'bg-yellow-100 text-yellow-700'];
                        @endphp
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium mt-1 inline-block {{ $colors[$inscriptionActive->statut_paiement] ?? 'bg-gray-100 text-gray-500' }}">
                            {{ ucfirst(str_replace('_', ' ', $inscriptionActive->statut_paiement)) }}
                        </span>
                        @else
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-400 font-medium mt-1 inline-block">
                            Non inscrit
                        </span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2 flex-wrap">
                    @if(!$inscriptionActive)
                    <button wire:click="openModalInscrire({{ $membre->id }})"
                        class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-green-600 transition hover:bg-green-700">
                        <i class="fa-solid fa-clipboard-list mr-1"></i> Inscrire
                    </button>
                    @elseif($inscriptionActive->statut_paiement === 'en_attente')
                    <button wire:click="openModalPaiement({{ $inscriptionActive->id }})"
                        class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-orange-500 transition hover:bg-orange-600">
                        <i class="fa-solid fa-credit-card mr-1"></i> Payer
                    </button>
                    @endif
                    <button wire:click="openModalSouhait({{ $membre->id }})"
                        class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700">
                        <i class="fa-solid fa-heart mr-1"></i> Souhaits
                    </button>
                    <button wire:click="retirerMembre({{ $membre->id }})"
                        wire:confirm="Retirer ce membre de votre délégation ?"
                        class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-10 text-gray-400">
            <i class="fa-solid fa-users text-4xl mb-2 block text-gray-300"></i>
            <p class="text-sm">Aucun membre dans votre délégation</p>
        </div>
        @endforelse
    </div>

    {{-- MODAL AJOUT MEMBRE --}}
    @if($showModalMembre)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center px-8 py-5 border-b sticky top-0 z-10"
                style="background: linear-gradient(135deg, #2d5a8e, #1e3f6e);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-plus"></i> Ajouter un membre
                </h3>
                <button wire:click="closeModalMembre" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8 space-y-4">

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Événement *</label>
                    <select wire:model.live="id_evenement"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                        {{ $cdd->id_evenement ? 'disabled' : '' }}>
                        <option value="">-- Choisir --</option>
                        @foreach($evenements as $evt)
                        <option value="{{ $evt->id }}">{{ $evt->nom }}</option>
                        @endforeach
                    </select>
                    @if($cdd->id_evenement)
                    <p class="text-xs text-gray-400 mt-1">Votre délégation est rattachée à un événement unique.</p>
                    @endif
                    @error('id_evenement') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom *</label>
                        <input wire:model="nom" type="text" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Prénom *</label>
                        <input wire:model="prenom" type="text" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        @error('prenom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Genre</label>
                        <select wire:model="genre" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                            <option value="">-- Choisir --</option>
                            <option value="homme">Homme</option>
                            <option value="femme">Femme</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                        <input wire:model="telephone" type="text" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        @error('telephone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Email</label>
                        <input wire:model="email" type="email" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- ✅ Fonction (liste déroulante, comme l'inscription publique) --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Fonction</label>
                        <select wire:model.live="fonction"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($fonctions as $f)
                            <option value="{{ $f }}">{{ $f }}</option>
                            @endforeach
                        </select>
                        @if($fonction === 'Autre')
                        <input wire:model.live="fonction_autre" type="text"
                            class="w-full mt-2 border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                            placeholder="Précisez la fonction...">
                        @endif
                    </div>

                    {{-- ✅ Filière + Université si Étudiant --}}
                    @if($this->estEtudiant)
                    <div class="col-span-2">
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <p class="text-xs font-bold text-blue-700 mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-graduation-cap"></i>
                                Informations académiques
                            </p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Filière *</label>
                                    <input wire:model="filiere" type="text"
                                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm bg-white"
                                        placeholder="Ex: Informatique">
                                    @error('filiere') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Université *</label>
                                    <input wire:model="universite" type="text"
                                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm bg-white"
                                        placeholder="Ex: Université Aube Nouvelle">
                                    @error('universite') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Date de naissance</label>
                        <input wire:model="date_naissance" type="date" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        @error('date_naissance') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Pays</label>
                        <select wire:model="pays" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($pays_liste as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Ville</label>
                        <input wire:model="ville" type="text" class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                    </div>
                </div>

                {{-- ✅ Note : pas de section "activité professionnelle / partenariat"
                     si l'événement n'est pas B2B, comme côté inscription publique --}}
                @if($id_evenement && !$this->evenementSelectionneEstB2B)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-700 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i>
                    Cet événement ne propose pas de rendez-vous B2B — aucun critère
                    de profil professionnel supplémentaire n'est requis.
                </div>
                @endif

                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="closeModalMembre"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        Annuler
                    </button>
                    <button wire:click="ajouterMembre"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-check mr-1"></i> Ajouter
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL INSCRIRE UN MEMBRE EXISTANT --}}
    @if($showModalInscrire)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list"></i> Inscrire ce membre
                </h3>
                <button wire:click="closeModalInscrire" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Événement *</label>
                    <select wire:model="evenement_a_inscrire"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        <option value="">-- Choisir --</option>
                        @foreach($evenements as $evt)
                        <option value="{{ $evt->id }}">{{ $evt->nom }}</option>
                        @endforeach
                    </select>
                    @error('evenement_a_inscrire') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="closeModalInscrire"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        Annuler
                    </button>
                    <button wire:click="confirmerInscription"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-check mr-1"></i> Inscrire
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL PAIEMENT --}}
    @if($showModalPaiement)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #C8102E, #a00d25);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-credit-card"></i> Payer pour ce membre
                </h3>
                <button wire:click="closeModalPaiement" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">

                <div class="bg-gray-50 rounded-xl p-4 mb-5 text-center border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">Montant à payer</p>
                    @if($pourcentage_remise > 0)
                    <p class="text-sm text-gray-400 line-through">{{ number_format($montant_brut, 0, ',', ' ') }} FCFA</p>
                    @endif
                    <p class="text-2xl font-bold" style="color: #007A3D;">
                        {{ number_format($montant_paiement, 0, ',', ' ') }} FCFA
                    </p>
                    @if($pourcentage_remise > 0)
                    <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">
                        -{{ $pourcentage_remise }}% remise appliquée
                    </span>
                    @endif
                </div>

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-2">Mode de paiement *</label>
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="mode_paiement" value="orange_money" class="hidden peer">
                            <div class="p-3 border-2 rounded-xl text-center text-xs peer-checked:border-orange-400 peer-checked:bg-orange-50 border-gray-200">
                                Orange Money
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="mode_paiement" value="moov_money" class="hidden peer">
                            <div class="p-3 border-2 rounded-xl text-center text-xs peer-checked:border-blue-400 peer-checked:bg-blue-50 border-gray-200">
                                Moov Money
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="mode_paiement" value="cheque" class="hidden peer">
                            <div class="p-3 border-2 rounded-xl text-center text-xs peer-checked:border-red-400 peer-checked:bg-red-50 border-gray-200">
                                Chèque
                            </div>
                        </label>
                    </div>

                    @if($mode_paiement === 'cheque')
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Numéro du chèque *</label>
                        <input wire:model="numero_cheque" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm font-mono">
                        @error('numero_cheque') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="closeModalPaiement"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        Annuler
                    </button>
                    <button wire:click="confirmerPaiement"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-check mr-1"></i> Soumettre le paiement
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL SOUHAIT --}}
    @if($showModalSouhait)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center px-8 py-5 border-b sticky top-0 z-10"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-heart"></i> Émettre un souhait pour ce membre
                </h3>
                <button wire:click="closeModalSouhait" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                <div class="mb-4">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                        <input wire:model.live.debounce.300ms="rechercheCandidat" type="text"
                            class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none text-sm"
                            placeholder="Rechercher un participant...">
                    </div>
                </div>

                <div class="space-y-2 max-h-80 overflow-y-auto">
                    @forelse($candidatsSouhait as $cand)
                    <div class="border border-gray-200 rounded-xl p-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                style="background-color: {{ $cand->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                                {{ strtoupper(substr($cand->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ $cand->nom }} {{ $cand->prenom }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $cand->entreprise->nom ?? 'Indépendant' }}
                                    @if($cand->fonction) · {{ $cand->fonction }} @endif
                                </p>
                            </div>
                        </div>
                        @if($cand->souhait_emis)
                        <span class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 text-gray-400">
                            <i class="fa-solid fa-circle-check text-green-500 mr-1"></i> Émis
                        </span>
                        @else
                        <button wire:click="emettreSouhaitPourMembre({{ $cand->id }})"
                            class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700">
                            <i class="fa-solid fa-heart mr-1"></i> Choisir
                        </button>
                        @endif
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-6">Aucun candidat trouvé.</p>
                    @endforelse
                </div>

                <div class="flex justify-end mt-6">
                    <button wire:click="closeModalSouhait"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @endif
</div>