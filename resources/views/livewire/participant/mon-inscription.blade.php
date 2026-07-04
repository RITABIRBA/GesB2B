<div>

    {{-- MESSAGES --}}
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

    {{-- EN-TÊTE --}}
    <div class="flex items-center gap-4 mb-6">
        <h3 class="text-xl font-bold text-gray-700">Mes Inscriptions</h3>
        <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
            style="background-color: #007A3D;">
            {{ $inscriptions->count() }} inscription(s)
        </span>
    </div>

    {{-- FLUX --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-4 mb-6 text-sm text-blue-700">
        <p class="font-semibold mb-2">
            <i class="fa-solid fa-circle-info mr-1"></i>
            Comment ça marche ?
        </p>
        <div class="flex items-center gap-3 flex-wrap">
            <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium">1. Inscription</span>
            <i class="fa-solid fa-arrow-right text-gray-400"></i>
            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">2. Validation</span>
            <i class="fa-solid fa-arrow-right text-gray-400"></i>
            <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">3. Paiement</span>
            <i class="fa-solid fa-arrow-right text-gray-400"></i>
            <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">4. Confirmation</span>
        </div>
    </div>

    {{-- LISTE DES INSCRIPTIONS --}}
    <div class="grid grid-cols-1 gap-4">
        @forelse($inscriptions as $inscription)
        @php
            $estGratuit      = $inscription->evenement?->type_paiement === 'gratuit';
            $estParEntreprise = $inscription->evenement?->type_paiement === 'par_entreprise';
            $estParticipant  = in_array($inscription->evenement?->type_paiement, ['payant', 'par_participant']);
            $dejaPaye        = $inscription->paiement && $inscription->paiement->statut !== 'rejete';
        @endphp
        <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h4 class="font-bold text-gray-800 text-lg">
                        {{ $inscription->evenement->nom ?? '-' }}
                    </h4>
                    <div class="flex items-center gap-4 mt-1 text-sm text-gray-500 flex-wrap">
                        <span>
                            <i class="fa-solid fa-calendar text-gray-400 mr-1"></i>
                            {{ $inscription->date_inscription }}
                        </span>
                        @if($estGratuit)
                        <span class="text-green-600 font-medium">
                            <i class="fa-solid fa-gift mr-1"></i> Gratuit
                        </span>
                        @else
                        <span>
                            <i class="fa-solid fa-money-bill text-gray-400 mr-1"></i>
                            {{ number_format($inscription->montant_paye, 0, ',', ' ') }} FCFA
                        </span>
                        @endif
                        @if($estParEntreprise)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">
                            <i class="fa-solid fa-building mr-1"></i> Paiement par entreprise
                        </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3 flex-wrap justify-end">

                    {{-- ✅ EN ATTENTE DE VALIDATION --}}
                    @if($inscription->statut_paiement == 'en_attente' && !$dejaPaye
                        && $inscription->statut_presence == 'absent')
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">
                        <i class="fa-solid fa-clock mr-1"></i> En attente de validation
                    </span>
                    @endif

                    {{-- ✅ VALIDÉE — BOUTON PAYER (par_participant) --}}
                    @if($inscription->statut_presence == 'present'
                        && $inscription->statut_paiement == 'en_attente'
                        && !$dejaPaye
                        && !$estGratuit
                        && $estParticipant)
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                        <i class="fa-solid fa-circle-check mr-1"></i> Validée
                    </span>
                    <button wire:click="openModalPaiement({{ $inscription->id }})"
                        class="px-4 py-2 rounded-xl text-white text-sm font-medium transition hover:opacity-90"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-credit-card mr-1"></i> Payer maintenant
                    </button>
                    @endif

                    {{-- ✅ VALIDÉE — EN ATTENTE PAIEMENT ENTREPRISE --}}
                    @if($inscription->statut_presence == 'present'
                        && $inscription->statut_paiement == 'en_attente'
                        && $estParEntreprise)
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-purple-600">
                        <i class="fa-solid fa-building mr-1"></i> En attente paiement entreprise
                    </span>
                    @endif

                    {{-- ✅ PAIEMENT SOUMIS EN ATTENTE CONFIRMATION --}}
                    @if($dejaPaye && $inscription->paiement->statut == 'en_attente')
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-orange-500">
                        <i class="fa-solid fa-clock mr-1"></i> Paiement en attente de confirmation
                    </span>
                    @endif

                    {{-- ✅ PAYÉ --}}
                    @if($inscription->statut_paiement == 'paye')
                        @if($estGratuit)
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-green-500">
                            <i class="fa-solid fa-circle-check mr-1"></i> Confirmée (Gratuit)
                        </span>
                        @else
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium"
                            style="background-color: #007A3D;">
                            <i class="fa-solid fa-circle-check mr-1"></i> Payé ✅
                        </span>
                        @endif
                    @endif

                    {{-- ✅ ANNULÉE --}}
                    @if($inscription->statut_paiement == 'annule')
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-red-600">
                        <i class="fa-solid fa-circle-xmark mr-1"></i> Annulée
                    </span>
                    @endif

                    {{-- ✅ VOIR LE REÇU --}}
                    @if(!$estGratuit && $inscription->paiement && $inscription->paiement->recu)
                    <button wire:click="voirRecu({{ $inscription->id }})"
                        class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600 transition hover:bg-blue-700">
                        <i class="fa-solid fa-receipt mr-1"></i> Voir le reçu
                    </button>
                    @endif

                    {{-- ✅ TÉLÉCHARGER LE REÇU PDF --}}
                    @if(!$estGratuit && $inscription->paiement && $inscription->paiement->recu && $inscription->statut_paiement == 'paye')
                    <a href="{{ route('participant.recu.telecharger', $inscription->paiement->recu->id) }}"
                        target="_blank"
                        class="px-3 py-1 rounded-full text-xs text-white font-medium bg-green-600 transition hover:bg-green-700">
                        <i class="fa-solid fa-download mr-1"></i> Télécharger le reçu
                    </a>
                    @endif

                </div>
            </div>

            {{-- Détails paiement --}}
            @if(!$estGratuit && $inscription->paiement)
            <div class="mt-4 pt-4 border-t bg-gray-50 rounded-xl p-3">
                <p class="text-xs text-gray-500 mb-1">Détails du paiement</p>
                <div class="flex items-center gap-4 text-sm text-gray-600 flex-wrap">
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
                        <i class="fa-solid fa-clock mr-1"></i> En attente
                    </span>
                    @elseif($inscription->paiement->statut == 'valide')
                    <span class="text-green-600 font-medium">
                        <i class="fa-solid fa-circle-check mr-1"></i> Confirmé
                    </span>
                    @elseif($inscription->paiement->statut == 'rejete')
                    <span class="text-red-600 font-medium">
                        <i class="fa-solid fa-circle-xmark mr-1"></i> Rejeté
                    </span>
                    @endif
                </div>
            </div>
            @endif

            {{-- Événement gratuit --}}
            @if($estGratuit && $inscription->statut_paiement == 'paye')
            <div class="mt-4 pt-4 border-t bg-green-50 rounded-xl p-3 text-xs text-green-700 flex items-center gap-2">
                <i class="fa-solid fa-gift"></i>
                Cet événement est gratuit. Votre inscription a été confirmée automatiquement.
            </div>
            @endif

        </div>
        @empty
        <div class="bg-white rounded-xl shadow p-16 text-center text-gray-400">
            <i class="fa-solid fa-clipboard-list text-5xl mb-3 block text-gray-300"></i>
            <p class="text-lg font-medium">Aucune inscription</p>
            <p class="text-sm mt-2 text-gray-400">
                Rendez-vous sur votre
                <a href="{{ route('participant.dashboard') }}"
                    class="font-medium hover:underline"
                    style="color: #007A3D;">
                    tableau de bord
                </a>
                pour vous inscrire à un événement.
            </p>
        </div>
        @endforelse
    </div>

    {{-- MODAL PAIEMENT --}}
    @if($showModalPaiement)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #C8102E, #a00d25);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-credit-card"></i>
                    Paiement sécurisé — GesB2B
                </h3>
                <button wire:click="closeModalPaiement" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8">
                <div class="bg-gray-50 rounded-xl p-4 mb-6 text-center border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">Montant à payer</p>
                    <p class="text-3xl font-bold" style="color: #C8102E;">
                        {{ number_format($montant_paiement, 0, ',', ' ') }} FCFA
                    </p>
                </div>

                @if($etape_paiement == 1)
                <p class="text-sm font-semibold text-gray-700 mb-4">Choisissez votre moyen de paiement :</p>
                <div class="space-y-3">
                    <button type="button" wire:click="$set('mode_paiement', 'orange_money')"
                        class="w-full border-2 rounded-xl p-4 transition flex items-center gap-4
                            {{ $mode_paiement === 'orange_money' ? 'border-orange-400 bg-orange-50' : 'border-gray-200 hover:bg-orange-50' }}">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                            style="background-color: #FF6600;">OM</div>
                        <div class="text-left">
                            <p class="font-bold text-gray-800">Orange Money</p>
                            <p class="text-xs text-gray-400">Paiement via votre compte Orange Money</p>
                        </div>
                        @if($mode_paiement === 'orange_money')
                        <i class="fa-solid fa-circle-check ml-auto text-orange-500"></i>
                        @else
                        <i class="fa-solid fa-chevron-right ml-auto text-gray-400"></i>
                        @endif
                    </button>

                    <button type="button" wire:click="$set('mode_paiement', 'moov_money')"
                        class="w-full border-2 rounded-xl p-4 transition flex items-center gap-4
                            {{ $mode_paiement === 'moov_money' ? 'border-blue-400 bg-blue-50' : 'border-gray-200 hover:bg-blue-50' }}">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                            style="background-color: #0066CC;">MM</div>
                        <div class="text-left">
                            <p class="font-bold text-gray-800">Moov Money</p>
                            <p class="text-xs text-gray-400">Paiement via votre compte Moov Money</p>
                        </div>
                        @if($mode_paiement === 'moov_money')
                        <i class="fa-solid fa-circle-check ml-auto text-blue-500"></i>
                        @else
                        <i class="fa-solid fa-chevron-right ml-auto text-gray-400"></i>
                        @endif
                    </button>

                    <button type="button" wire:click="$set('mode_paiement', 'carte')"
                        class="w-full border-2 rounded-xl p-4 transition flex items-center gap-4
                            {{ $mode_paiement === 'carte' ? 'border-purple-400 bg-purple-50' : 'border-gray-200 hover:bg-purple-50' }}">
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
                        @else
                        <i class="fa-solid fa-chevron-right ml-auto text-gray-400"></i>
                        @endif
                    </button>
                </div>

                @if($mode_paiement == 'orange_money' || $mode_paiement == 'moov_money')
                <button wire:click="$set('etape_paiement', 2)"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow-lg mt-4"
                    style="background-color: {{ $mode_paiement == 'orange_money' ? '#FF6600' : '#0066CC' }};">
                    <i class="fa-solid fa-arrow-right mr-1"></i>
                    Continuer avec {{ $mode_paiement == 'orange_money' ? 'Orange Money' : 'Moov Money' }}
                </button>
                @elseif($mode_paiement == 'carte')
                <button wire:click="$set('etape_paiement', 4)"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow-lg mt-4"
                    style="background-color: #6d28d9;">
                    <i class="fa-solid fa-arrow-right mr-1"></i> Continuer avec Carte Bancaire
                </button>
                @endif

                @elseif($etape_paiement == 2)
                <div class="text-center mb-6">
                    @if($mode_paiement == 'orange_money')
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-white font-bold text-xl mx-auto mb-2"
                        style="background-color: #FF6600;">OM</div>
                    <p class="font-bold text-gray-800">Orange Money</p>
                    @else
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-white font-bold text-xl mx-auto mb-2"
                        style="background-color: #0066CC;">MM</div>
                    <p class="font-bold text-gray-800">Moov Money</p>
                    @endif
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Votre numéro de téléphone *</label>
                    <input wire:model="telephone_paiement" type="text"
                        class="w-full border rounded-xl px-4 py-3 focus:outline-none text-lg text-center font-mono"
                        placeholder="{{ $mode_paiement == 'orange_money' ? '07XXXXXXXX' : '01XXXXXXXX' }}">
                    @error('telephone_paiement')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
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

                @elseif($etape_paiement == 3)
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3"
                        style="background-color: #e6f4ed;">
                        <i class="fa-solid fa-shield-halved text-3xl" style="color: #007A3D;"></i>
                    </div>
                    <p class="font-bold text-gray-800">Vérification OTP</p>
                    <p class="text-sm text-gray-500 mt-1">Code envoyé au <strong>{{ $telephone_paiement }}</strong></p>
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Code OTP reçu par SMS *</label>
                    <input wire:model="otp_saisi" type="text" maxlength="6"
                        class="w-full border rounded-xl px-4 py-3 text-2xl text-center font-mono tracking-widest"
                        placeholder="_ _ _ _ _ _">
                    @error('otp_saisi')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-center text-xs text-yellow-700 mt-3">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        <strong>Simulation :</strong> Votre code OTP est
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
                        <i class="fa-solid fa-check mr-1"></i> Confirmer le paiement
                    </button>
                </div>

                @elseif($etape_paiement == 4)
                <div class="text-center mb-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-2"
                        style="background-color: #6d28d9;">
                        <i class="fa-solid fa-credit-card"></i>
                    </div>
                    <p class="font-bold text-gray-800">Paiement par Carte Bancaire</p>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Numéro de carte *</label>
                        <input wire:model="carte_numero" type="text" maxlength="19"
                            class="w-full border rounded-xl px-4 py-2.5 text-sm font-mono tracking-widest text-center"
                            placeholder="XXXX XXXX XXXX XXXX">
                        @error('carte_numero')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom sur la carte *</label>
                        <input wire:model="carte_nom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 text-sm"
                            placeholder="Ex: TINTO MOUSSA">
                        @error('carte_nom')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Expiration *</label>
                            <input wire:model="carte_expiration" type="text" maxlength="5"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm font-mono text-center"
                                placeholder="MM/AA">
                            @error('carte_expiration')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">CVV *</label>
                            <input wire:model="carte_cvv" type="password" maxlength="4"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm font-mono text-center"
                                placeholder="XXX">
                            @error('carte_cvv')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-xs text-green-700 flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-green-500"></i> Paiement sécurisé SSL
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
                        Payer {{ number_format($montant_paiement, 0, ',', ' ') }} FCFA
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL REÇU --}}
    @if($showModalRecu && $recu_courant)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-receipt"></i> Reçu de Paiement
                </h3>
                <button wire:click="closeModalRecu" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8">
                <div class="text-center mb-6">
                    <img src="{{ asset('images/logo-ccibf.png') }}"
                        alt="CCI-BF" class="w-16 h-16 object-contain mx-auto mb-2">
                    <h2 class="text-xl font-bold text-gray-800">GesB2B — CCI-BF</h2>
                    <p class="text-sm text-gray-400">Reçu de paiement officiel</p>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 mb-4 text-center">
                    <p class="text-xs text-gray-400 mb-1">Numéro de reçu</p>
                    <p class="font-mono font-bold text-gray-800 text-lg">
                        REC-{{ str_pad($recu_courant->paiement->recu->id, 6, '0', STR_PAD_LEFT) }}
                    </p>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-sm text-gray-500">Participant</span>
                        <span class="font-semibold text-gray-800 text-sm">
                            {{ $recu_courant->participant->nom }} {{ $recu_courant->participant->prenom }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-sm text-gray-500">Événement</span>
                        <span class="font-semibold text-gray-800 text-sm">
                            {{ $recu_courant->evenement->nom ?? '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-sm text-gray-500">Mode paiement</span>
                        <span class="font-semibold text-gray-800 text-sm capitalize">
                            {{ ucfirst(str_replace('_', ' ', $recu_courant->paiement->mode_paiement)) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-sm text-gray-500">Date</span>
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

                <div class="text-center mt-4 text-xs text-gray-400">
                    <i class="fa-solid fa-shield-halved mr-1"></i>
                    Reçu officiel CCI-BF — GesB2B Platform
                </div>

                <div class="flex justify-between gap-3 mt-6">
                    <button wire:click="closeModalRecu"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Fermer
                    </button>
                    <a href="{{ route('participant.recu.telecharger', $recu_courant->paiement->recu->id) }}"
                        target="_blank"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-download mr-1"></i> Télécharger PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>