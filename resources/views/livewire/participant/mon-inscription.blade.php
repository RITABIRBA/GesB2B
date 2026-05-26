{{--
    Vue Blade — Mon Inscription (Espace Participant)
    Composant : App\Livewire\Participant\MonInscription
    Layout : layouts/participant.blade.php
--}}
<div>

    {{-- ================================================
        MESSAGES
    ================================================ --}}
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

    {{-- ================================================
        EN-TÊTE
    ================================================ --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Mes Inscriptions</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $inscriptions->count() }} inscription(s)
            </span>
        </div>
        <button wire:click="openModalInscription"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Nouvelle inscription
        </button>
    </div>

    {{-- ================================================
        EXPLICATION DU FLUX
    ================================================ --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-4 mb-6 text-sm text-blue-700">
        <p class="font-semibold mb-2">
            <i class="fa-solid fa-circle-info mr-1"></i>
            Comment ça marche ?
        </p>
        <div class="flex items-center gap-3 flex-wrap">
            <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium">
                1. Préinscription
            </span>
            <i class="fa-solid fa-arrow-right text-gray-400"></i>
            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                2. Validation CDD
            </span>
            <i class="fa-solid fa-arrow-right text-gray-400"></i>
            <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                3. Paiement
            </span>
            <i class="fa-solid fa-arrow-right text-gray-400"></i>
            <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">
                4. Confirmation CDD
            </span>
        </div>
    </div>

    {{-- ================================================
        LISTE DES INSCRIPTIONS
    ================================================ --}}
    <div class="grid grid-cols-1 gap-4">
        @forelse($inscriptions as $inscription)
        <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    {{-- Nom de l'événement --}}
                    <h4 class="font-bold text-gray-800 text-lg">
                        {{ $inscription->evenement->nom ?? '-' }}
                    </h4>
                    {{-- Infos --}}
                    <div class="flex items-center gap-4 mt-1 text-sm text-gray-500">
                        <span>
                            <i class="fa-solid fa-calendar text-gray-400 mr-1"></i>
                            {{ $inscription->date_inscription }}
                        </span>
                        <span>
                            <i class="fa-solid fa-money-bill text-gray-400 mr-1"></i>
                            {{ number_format($inscription->montant_paye, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                </div>

                {{-- Actions à droite --}}
                <div class="flex items-center gap-3 flex-wrap justify-end">

                    {{-- ÉTAPE 1 : Préinscription en attente de validation --}}
                    @if($inscription->statut_presence == 'absent' && $inscription->statut_paiement == 'en_attente')
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">
                        <i class="fa-solid fa-clock mr-1"></i> En attente validation CDD
                    </span>
                    @endif

                    {{-- ÉTAPE 2 : Validée par CDD, peut payer --}}
                    @if($inscription->statut_presence == 'present' && $inscription->statut_paiement == 'en_attente' && !$inscription->paiement)
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                        <i class="fa-solid fa-circle-check mr-1"></i> Validée par CDD
                    </span>
                    <button wire:click="openModalPaiement({{ $inscription->id }})"
                        class="px-4 py-2 rounded-xl text-white text-sm font-medium transition hover:opacity-90"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-credit-card mr-1"></i> Payer maintenant
                    </button>
                    @endif

                    {{-- Paiement soumis en attente de confirmation CDD --}}
                    @if($inscription->paiement && $inscription->paiement->statut == 'en_attente')
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-orange-500">
                        <i class="fa-solid fa-clock mr-1"></i> Paiement en attente confirmation CDD
                    </span>
                    @endif

                    {{-- Paiement validé --}}
                    @if($inscription->statut_paiement == 'paye')
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-circle-check mr-1"></i> Payé
                    </span>
                    @endif

                    {{-- Annulé --}}
                    @if($inscription->statut_paiement == 'annule')
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-red-600">
                        <i class="fa-solid fa-circle-xmark mr-1"></i> Annulée
                    </span>
                    @endif

                    {{-- Bouton Voir le reçu --}}
                    @if($inscription->paiement && $inscription->paiement->recu)
                    <button wire:click="voirRecu({{ $inscription->id }})"
                        class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600 transition hover:bg-blue-700">
                        <i class="fa-solid fa-receipt mr-1"></i> Voir le reçu
                    </button>
                    @endif

                </div>
            </div>

            {{-- Détails du paiement --}}
            @if($inscription->paiement)
            <div class="mt-4 pt-4 border-t bg-gray-50 rounded-xl p-3">
                <p class="text-xs text-gray-500 mb-1">Détails du paiement</p>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <span>
                        <i class="fa-solid fa-money-bill text-gray-400 mr-1"></i>
                        {{ number_format($inscription->paiement->montant, 0, ',', ' ') }} FCFA
                    </span>
                    <span>
                        <i class="fa-solid fa-credit-card text-gray-400 mr-1"></i>
                        {{ ucfirst(str_replace('_', ' ', $inscription->paiement->mode_paiement)) }}
                    </span>
                    <span>
                        <i class="fa-solid fa-calendar text-gray-400 mr-1"></i>
                        {{ $inscription->paiement->date_paiement }}
                    </span>
                    @if($inscription->paiement->statut == 'en_attente')
                    <span class="text-orange-600 font-medium">
                        <i class="fa-solid fa-clock mr-1"></i> En attente de confirmation CDD
                    </span>
                    @elseif($inscription->paiement->statut == 'valide')
                    <span class="text-green-600 font-medium">
                        <i class="fa-solid fa-circle-check mr-1"></i> Confirmé par CDD
                    </span>
                    @elseif($inscription->paiement->statut == 'rejete')
                    <span class="text-red-600 font-medium">
                        <i class="fa-solid fa-circle-xmark mr-1"></i> Rejeté par CDD
                    </span>
                    @endif
                </div>
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white rounded-xl shadow p-16 text-center text-gray-400">
            <i class="fa-solid fa-clipboard-list text-5xl mb-3 block text-gray-300"></i>
            <p class="text-lg font-medium">Aucune inscription</p>
            <p class="text-sm text-gray-400 mt-1">Inscrivez-vous à un événement pour commencer</p>
            <button wire:click="openModalInscription"
                class="mt-4 px-5 py-2 rounded-xl text-white text-sm font-medium"
                style="background-color: #C8102E;">
                <i class="fa-solid fa-plus mr-1"></i> S'inscrire maintenant
            </button>
        </div>
        @endforelse
    </div>

    {{-- ================================================
        MODAL — NOUVELLE INSCRIPTION
    ================================================ --}}
    @if($showModalInscription)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list"></i>
                    Nouvelle Préinscription
                </h3>
                <button wire:click="closeModalInscription"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8">

                {{-- Info --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 mb-5 text-sm text-yellow-700 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                    <div>
                        Votre préinscription sera soumise à votre
                        <strong>Chef de Délégation (CDD)</strong> pour validation.
                        Vous pourrez payer après sa validation.
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5">

                    {{-- Événement --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            <i class="fa-solid fa-calendar mr-1" style="color: #007A3D;"></i>
                            Événement *
                        </label>
                        <select wire:model="id_evenement"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            <option value="">-- Choisir un événement --</option>
                            @foreach($evenements as $evenement)
                            <option value="{{ $evenement->id }}">
                                {{ $evenement->nom }} — {{ $evenement->date_debut }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_evenement')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Montant --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            <i class="fa-solid fa-money-bill mr-1" style="color: #C8102E;"></i>
                            Montant à payer (FCFA) *
                        </label>
                        <input wire:model="montant_paye" type="number" min="0"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: 50000">
                        @error('montant_paye')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="closeModalInscription"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="inscrire"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-paper-plane mr-1"></i> Envoyer la préinscription
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ================================================
        MODAL — PAIEMENT
    ================================================ --}}
    @if($showModalPaiement)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #C8102E, #a00d25);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-credit-card"></i>
                    Effectuer un Paiement
                </h3>
                <button wire:click="closeModalPaiement"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 gap-5">

                    {{-- Mode de paiement --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">
                            Mode de paiement *
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button"
                                wire:click="$set('mode_paiement', 'especes')"
                                class="border rounded-xl p-3 transition flex items-center gap-2 text-sm font-medium
                                    {{ $mode_paiement === 'especes'
                                        ? 'border-red-400 bg-red-50 text-red-700'
                                        : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-money-bill"></i> Espèces
                            </button>
                            <button type="button"
                                wire:click="$set('mode_paiement', 'virement')"
                                class="border rounded-xl p-3 transition flex items-center gap-2 text-sm font-medium
                                    {{ $mode_paiement === 'virement'
                                        ? 'border-red-400 bg-red-50 text-red-700'
                                        : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-building-columns"></i> Virement
                            </button>
                            <button type="button"
                                wire:click="$set('mode_paiement', 'mobile_money')"
                                class="border rounded-xl p-3 transition flex items-center gap-2 text-sm font-medium
                                    {{ $mode_paiement === 'mobile_money'
                                        ? 'border-red-400 bg-red-50 text-red-700'
                                        : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-mobile"></i> Mobile Money
                            </button>
                            <button type="button"
                                wire:click="$set('mode_paiement', 'carte')"
                                class="border rounded-xl p-3 transition flex items-center gap-2 text-sm font-medium
                                    {{ $mode_paiement === 'carte'
                                        ? 'border-red-400 bg-red-50 text-red-700'
                                        : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                <i class="fa-solid fa-credit-card"></i> Carte bancaire
                            </button>
                        </div>
                    </div>

                    {{-- Montant --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Montant (FCFA) *
                        </label>
                        <input wire:model="montant_paiement" type="number" min="1"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: 50000">
                        @error('montant_paiement')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Info --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-sm text-blue-700 flex items-start gap-2">
                        <i class="fa-solid fa-circle-info mt-0.5"></i>
                        Votre paiement sera confirmé par votre CDD.
                        Un reçu vous sera généré après confirmation.
                    </div>

                </div>

                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="closeModalPaiement"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="payerInscription"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-paper-plane mr-1"></i> Soumettre le paiement
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ================================================
        MODAL — REÇU DE PAIEMENT
    ================================================ --}}
    @if($showModalRecu && $recu_courant)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-receipt"></i>
                    Reçu de Paiement
                </h3>
                <button wire:click="closeModalRecu"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8" id="recu-print">

                {{-- Logo --}}
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-2"
                        style="background-color: #C8102E;">B</div>
                    <h2 class="text-xl font-bold text-gray-800">GesB2B — CCI-BF</h2>
                    <p class="text-sm text-gray-400">Reçu de paiement officiel</p>
                </div>

                {{-- Numéro reçu --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-4 text-center">
                    <p class="text-xs text-gray-400 mb-1">Numéro de reçu</p>
                    <p class="font-mono font-bold text-gray-800 text-lg">
                        REC-{{ str_pad($recu_courant->paiement->recu->id, 6, '0', STR_PAD_LEFT) }}
                    </p>
                </div>

                {{-- Détails --}}
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-sm text-gray-500">Participant</span>
                        <span class="font-semibold text-gray-800 text-sm">
                            {{ $recu_courant->participant->nom }}
                            {{ $recu_courant->participant->prenom }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-sm text-gray-500">Événement</span>
                        <span class="font-semibold text-gray-800 text-sm">
                            {{ $recu_courant->evenement->nom ?? '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-sm text-gray-500">Mode de paiement</span>
                        <span class="font-semibold text-gray-800 text-sm capitalize">
                            {{ ucfirst(str_replace('_', ' ', $recu_courant->paiement->mode_paiement)) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-sm text-gray-500">Date de paiement</span>
                        <span class="font-semibold text-gray-800 text-sm">
                            {{ $recu_courant->paiement->recu->date }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-3 bg-green-50 rounded-xl px-3 mt-2">
                        <span class="font-bold text-gray-700">Montant payé</span>
                        <span class="font-bold text-xl" style="color: #007A3D;">
                            {{ number_format($recu_courant->paiement->recu->montant, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                </div>

                {{-- Footer reçu --}}
                <div class="text-center mt-4 text-xs text-gray-400">
                    <i class="fa-solid fa-shield-halved mr-1"></i>
                    Reçu officiel CCI-BF — GesB2B Platform
                </div>

                {{-- Boutons --}}
                <div class="flex justify-between gap-3 mt-6 no-print">
                    <button wire:click="closeModalRecu"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        <i class="fa-solid fa-xmark mr-1"></i> Fermer
                    </button>
                    <button onclick="window.print()"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-print mr-1"></i> Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>