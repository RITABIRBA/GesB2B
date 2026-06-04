<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-300 text-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- Carte paiement groupé --}}
    @if($evenements_avec_impayés->count() > 0)
    <div class="bg-white rounded-xl shadow p-6 mb-6 border-l-4" style="border-color: #C8102E;">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                    style="background-color: #fef2f2;">
                    <i class="fa-solid fa-credit-card text-xl" style="color: #C8102E;"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-800">Paiements groupés en attente</p>
                    <p class="text-sm text-gray-400 mt-0.5">
                        {{ $evenements_avec_impayés->count() }} événement(s) avec participants non payés
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-4 space-y-3">
            @foreach($evenements_avec_impayés as $ev)
            @php
                $nb_impayés = \App\Models\Inscription::whereHas('participant', fn($q) =>
                    $q->where('id_entreprise', $entreprise->id)
                      ->where('id_evenement', $ev->id)
                )->where('statut_paiement', '!=', 'paye')->count();
                $montant_total = $ev->montant_inscription;
            @endphp
            <div class="flex items-center justify-between bg-gray-50 rounded-xl p-4">
                <div>
                    <p class="font-semibold text-gray-800">{{ $ev->nom }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $nb_impayés }} participant(s) non payés —
                        <span class="font-bold" style="color: #C8102E;">
                            {{ number_format($montant_total, 0, ',', ' ') }} FCFA
                        </span>
                    </p>
                </div>
                <button wire:click="openPaiementGroupe({{ $ev->id }})"
                    class="px-4 py-2 rounded-xl text-white text-sm font-medium transition hover:opacity-90"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-credit-card mr-1"></i>
                    Payer maintenant
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Mes Participants</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $participants->count() }} participant(s)
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Ajouter un participant
        </button>
    </div>

    {{-- Recherche --}}
    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher un participant..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none text-sm">
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Genre</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Fonction</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Téléphone</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Rôle</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Statut</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Code</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $participant)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                style="background-color: {{ $participant->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                                {{ strtoupper(substr($participant->prenom, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ $participant->nom }} {{ $participant->prenom }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $participant->email ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($participant->genre == 'homme')
                            <span class="text-blue-600 flex items-center gap-1">
                                <i class="fa-solid fa-mars"></i> M.
                            </span>
                        @elseif($participant->genre == 'femme')
                            <span class="text-pink-600 flex items-center gap-1">
                                <i class="fa-solid fa-venus"></i> Mme
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        {{ $participant->fonction ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        {{ $participant->telephone }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-3 py-1 rounded-full font-medium text-white"
                            style="background-color: #007A3D;">
                            {{ ucfirst($participant->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($participant->statut_historique == 'actif')
                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                                <i class="fa-solid fa-circle-check mr-1"></i> Actif
                            </span>
                        @else
                            <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 font-medium">
                                <i class="fa-solid fa-circle-xmark mr-1"></i> Inactif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded-lg">
                            {{ $participant->code_acces }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button wire:click="modifier({{ $participant->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700 flex items-center gap-1">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </button>
                            <button wire:click="supprimer({{ $participant->id }})"
                                wire:confirm="Supprimer ce participant ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-users text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun participant</p>
                        <button wire:click="openModal"
                            class="mt-3 px-5 py-2 rounded-xl text-white text-sm font-medium"
                            style="background-color: #C8102E;">
                            Ajouter le premier participant
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL PAIEMENT GROUPÉ --}}
    @if($showPaiementGroupeModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #C8102E, #a00d25);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-credit-card"></i>
                    Paiement groupé — GesB2B
                </h3>
                <button wire:click="closePaiementGroupe"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8">

                {{-- Résumé --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-6 border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1 text-center">Montant global à payer</p>
                    <p class="text-3xl font-bold text-center" style="color: #C8102E;">
                        {{ number_format($montant_total, 0, ',', ' ') }} FCFA
                    </p>
                    <p class="text-xs text-gray-400 text-center mt-1">
                        Pour {{ count($participants_a_payer) }} participant(s)
                    </p>
                </div>

                {{-- Liste participants --}}
                <div class="mb-6">
                    <p class="text-sm font-semibold text-gray-700 mb-2">
                        Participants concernés :
                    </p>
                    <div class="space-y-2 max-h-32 overflow-y-auto">
                        @foreach($participants_a_payer as $inscription)
                        <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                style="background-color: #007A3D;">
                                {{ strtoupper(substr($inscription->participant->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $inscription->participant->nom ?? '-' }}
                                {{ $inscription->participant->prenom ?? '' }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- ÉTAPE 1 — Choisir moyen --}}
                @if($etape_paiement == 1)
                <p class="text-sm font-semibold text-gray-700 mb-4">
                    Choisissez votre moyen de paiement :
                </p>
                <div class="space-y-3">
                    <button type="button" wire:click="$set('mode_paiement', 'orange_money')"
                        class="w-full border-2 rounded-xl p-4 transition flex items-center gap-4
                            {{ $mode_paiement === 'orange_money' ? 'border-orange-400 bg-orange-50' : 'border-gray-200 hover:bg-gray-50' }}">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                            style="background-color: #FF6600;">OM</div>
                        <div class="text-left">
                            <p class="font-bold text-gray-800">Orange Money</p>
                            <p class="text-xs text-gray-400">Paiement via votre compte Orange Money</p>
                        </div>
                        @if($mode_paiement === 'orange_money')
                        <i class="fa-solid fa-circle-check ml-auto text-orange-500"></i>
                        @endif
                    </button>

                    <button type="button" wire:click="$set('mode_paiement', 'moov_money')"
                        class="w-full border-2 rounded-xl p-4 transition flex items-center gap-4
                            {{ $mode_paiement === 'moov_money' ? 'border-blue-400 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                            style="background-color: #0066CC;">MM</div>
                        <div class="text-left">
                            <p class="font-bold text-gray-800">Moov Money</p>
                            <p class="text-xs text-gray-400">Paiement via votre compte Moov Money</p>
                        </div>
                        @if($mode_paiement === 'moov_money')
                        <i class="fa-solid fa-circle-check ml-auto text-blue-500"></i>
                        @endif
                    </button>

                    <button type="button" wire:click="$set('mode_paiement', 'carte')"
                        class="w-full border-2 rounded-xl p-4 transition flex items-center gap-4
                            {{ $mode_paiement === 'carte' ? 'border-purple-400 bg-purple-50' : 'border-gray-200 hover:bg-gray-50' }}">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-xl flex-shrink-0"
                            style="background-color: #6d28d9;">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-gray-800">Carte Bancaire</p>
                            <p class="text-xs text-gray-400">VISA, Mastercard</p>
                        </div>
                        @if($mode_paiement === 'carte')
                        <i class="fa-solid fa-circle-check ml-auto text-purple-500"></i>
                        @endif
                    </button>
                </div>

                @if($mode_paiement == 'orange_money' || $mode_paiement == 'moov_money')
                <button wire:click="$set('etape_paiement', 2)"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 mt-4"
                    style="background-color: {{ $mode_paiement == 'orange_money' ? '#FF6600' : '#0066CC' }};">
                    <i class="fa-solid fa-arrow-right mr-1"></i>
                    Continuer avec {{ $mode_paiement == 'orange_money' ? 'Orange Money' : 'Moov Money' }}
                </button>
                @elseif($mode_paiement == 'carte')
                <button wire:click="$set('etape_paiement', 4)"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 mt-4"
                    style="background-color: #6d28d9;">
                    <i class="fa-solid fa-arrow-right mr-1"></i>
                    Continuer avec Carte Bancaire
                </button>
                @endif

                {{-- ÉTAPE 2 — Téléphone --}}
                @elseif($etape_paiement == 2)
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Votre numéro de téléphone *
                        </label>
                        <input wire:model="telephone_paiement" type="text"
                            class="w-full border rounded-xl px-4 py-3 focus:outline-none text-lg text-center font-mono"
                            placeholder="{{ $mode_paiement == 'orange_money' ? '07XXXXXXXX' : '01XXXXXXXX' }}">
                        @error('telephone_paiement')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button wire:click="$set('etape_paiement', 1)"
                        class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Retour
                    </button>
                    <button wire:click="envoyerOtp"
                        class="flex-1 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm"
                        style="background-color: {{ $mode_paiement == 'orange_money' ? '#FF6600' : '#0066CC' }};">
                        <i class="fa-solid fa-paper-plane mr-1"></i> Envoyer le code OTP
                    </button>
                </div>

                {{-- ÉTAPE 3 — OTP --}}
                @elseif($etape_paiement == 3)
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Code OTP reçu par SMS *
                        </label>
                        <input wire:model="otp_saisi" type="text" maxlength="6"
                            class="w-full border rounded-xl px-4 py-3 focus:outline-none text-2xl text-center font-mono tracking-widest"
                            placeholder="_ _ _ _ _ _">
                        @error('otp_saisi')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-center text-xs text-yellow-700">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        <strong>Simulation :</strong> Code OTP :
                        <span class="font-mono font-bold text-red-600 text-lg ml-1">{{ $otp_code }}</span>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button wire:click="$set('etape_paiement', 2)"
                        class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Retour
                    </button>
                    <button wire:click="confirmerOtp"
                        class="flex-1 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-check mr-1"></i> Confirmer le paiement groupé
                    </button>
                </div>

                {{-- ÉTAPE 4 — Carte --}}
                @elseif($etape_paiement == 4)
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Numéro de carte *</label>
                        <input wire:model="carte_numero" type="text" maxlength="19"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm font-mono text-center"
                            placeholder="XXXX XXXX XXXX XXXX">
                        @error('carte_numero') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom sur la carte *</label>
                        <input wire:model="carte_nom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                            placeholder="Ex: TINTO MOUSSA">
                        @error('carte_nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Expiration *</label>
                            <input wire:model="carte_expiration" type="text" maxlength="5"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm font-mono text-center"
                                placeholder="MM/AA">
                            @error('carte_expiration') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">CVV *</label>
                            <input wire:model="carte_cvv" type="password" maxlength="4"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm font-mono text-center"
                                placeholder="XXX">
                            @error('carte_cvv') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button wire:click="$set('etape_paiement', 1)"
                        class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Retour
                    </button>
                    <button wire:click="payerCarte"
                        class="flex-1 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm"
                        style="background-color: #6d28d9;">
                        <i class="fa-solid fa-lock mr-1"></i>
                        Payer {{ number_format($montant_total, 0, ',', ' ') }} FCFA
                    </button>
                </div>
                @endif

            </div>
        </div>
    </div>
    @endif

    {{-- MODAL PARTICIPANT --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center px-8 py-5 border-b sticky top-0 z-10"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-plus' }}"></i>
                    {{ $isEditing ? 'Modifier le participant' : 'Nouveau participant' }}
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-2 gap-5">

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom *</label>
                        <input wire:model="nom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Prénom *</label>
                        <input wire:model="prenom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        @error('prenom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Genre</label>
                        <select wire:model="genre"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                            <option value="">-- Choisir le genre --</option>
                            <option value="homme">Homme</option>
                            <option value="femme">Femme</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Fonction <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <input wire:model="fonction" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                            placeholder="Ex: Directeur Commercial">
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone *</label>
                        <input wire:model="telephone" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        @error('telephone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Email</label>
                        <input wire:model="email" type="email"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Rôle *</label>
                        <select wire:model="role"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                            @foreach($roles as $r)
                            <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Événement *</label>
                        <select wire:model="id_evenement"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($evenements as $e)
                            <option value="{{ $e->id }}">{{ $e->nom }}</option>
                            @endforeach
                        </select>
                        @error('id_evenement') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    @if(!$isEditing)
                    <div class="col-span-2">
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-700 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info"></i>
                            Un code d'accès sera généré automatiquement pour ce participant.
                        </div>
                    </div>
                    @endif

                </div>

                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="closeModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="sauvegarder"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm flex items-center gap-2"
                        style="background-color: #C8102E;">
                        <span wire:loading.remove>
                            <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-floppy-disk' }} mr-1"></i>
                            {{ $isEditing ? 'Modifier' : 'Enregistrer' }}
                        </span>
                        <span wire:loading>
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                            Enregistrement...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>