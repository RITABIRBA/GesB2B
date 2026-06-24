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

    {{-- En-tête --}}
    <div class="flex items-center gap-4 mb-6">
        <h3 class="text-xl font-bold text-gray-700">Mes Stands</h3>
        <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
            style="background-color: #007A3D;">
            {{ $mesStands->count() }} stand(s) réservé(s)
        </span>
    </div>

    {{-- Mes stands réservés --}}
    @if($mesStands->count() > 0)
    <div class="mb-8">
        <h4 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-store" style="color: #007A3D;"></i>
            Mes stands réservés
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($mesStands as $stand)
            @php
                $prix              = $stand->prix_calcule;
                $statutReservation = $stand->statut_reservation;
                $statutPaiement    = $stand->statut_paiement_stand;
                $evenement         = $stand->evenement;
                $composants        = is_array($stand->composants)
                    ? $stand->composants
                    : (json_decode($stand->composants ?? '[]', true) ?: []);
            @endphp
            <div class="bg-white rounded-xl shadow p-5 border-2 border-green-100">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg"
                        style="background-color: #007A3D;">
                        {{ $stand->numero_stand }}
                    </div>
                    @if($stand->standing == 'vip')
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">
                        <i class="fa-solid fa-star mr-1"></i> VIP
                    </span>
                    @elseif($stand->standing == 'premium')
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                        <i class="fa-solid fa-gem mr-1"></i> Premium
                    </span>
                    @else
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-store mr-1"></i> Standard
                    </span>
                    @endif
                </div>

                <p class="font-bold text-gray-800 mb-1">Stand N°{{ $stand->numero_stand }}</p>
                <p class="text-xs text-gray-400 mb-1">
                    <i class="fa-solid fa-calendar mr-1"></i>
                    {{ $evenement->nom ?? '-' }}
                </p>
                @if($stand->superficie)
                <p class="text-xs text-gray-500 mb-2">
                    <i class="fa-solid fa-ruler-combined mr-1 text-gray-400"></i>
                    {{ $stand->superficie }}
                </p>
                @endif
                @if(!empty($composants))
                <div class="flex flex-wrap gap-1 mb-2">
                    @foreach($composants as $comp)
                    <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">
                        {{ $comp['qte'] }}x {{ $comp['nom'] }}
                    </span>
                    @endforeach
                </div>
                @endif

                @if($prix > 0)
                <p class="text-sm font-bold mb-2" style="color: #C8102E;">
                    <i class="fa-solid fa-money-bill mr-1"></i>
                    {{ number_format($prix, 0, ',', ' ') }} FCFA
                </p>
                @else
                <p class="text-xs text-green-600 font-semibold mb-2">
                    <i class="fa-solid fa-gift mr-1"></i> Gratuit
                </p>
                @endif

                {{-- Statut réservation --}}
                @if($statutReservation == 'en_attente')
                <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium block text-center mb-2">
                    <i class="fa-solid fa-clock mr-1"></i> En attente de validation
                </span>
                @elseif($statutReservation == 'valide')
                    @if($statutPaiement == 'paye')
                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium block text-center mb-3">
                        <i class="fa-solid fa-circle-check mr-1"></i>
                        {{ $prix > 0 ? 'Stand payé ✅' : 'Réservation confirmée (gratuit) ✅' }}
                    </span>
                    @else
                        @if($prix > 0)
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium block text-center mb-2">
                            <i class="fa-solid fa-circle-check mr-1"></i> Réservation validée
                        </span>
                        <button wire:click="payerStand({{ $stand->id }})"
                            class="w-full py-2 rounded-xl text-xs text-white font-medium transition hover:opacity-90 mb-2"
                            style="background-color: #C8102E;">
                            <i class="fa-solid fa-credit-card mr-1"></i>
                            Payer le stand
                        </button>
                        @else
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium block text-center mb-2">
                            <i class="fa-solid fa-gift mr-1"></i> Réservation confirmée (gratuit)
                        </span>
                        @endif
                    @endif
                @endif

                @if($statutPaiement != 'paye')
                <button wire:click="annulerReservation({{ $stand->id }})"
                    wire:confirm="Voulez-vous annuler la réservation du Stand N°{{ $stand->numero_stand }} ?"
                    class="w-full py-2 rounded-xl text-xs text-red-600 border border-red-200 hover:bg-red-50 transition font-medium">
                    <i class="fa-solid fa-xmark mr-1"></i>
                    Annuler la réservation
                </button>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Stands disponibles --}}
    <div>
        <h4 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-circle-dot text-green-500"></i>
            Stands disponibles
        </h4>

        @if($standsDisponibles->isEmpty())
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400">
            <i class="fa-solid fa-store text-5xl mb-3 block text-gray-300"></i>
            <p class="text-lg font-medium">Aucun stand disponible pour le moment</p>
            <p class="text-sm mt-1">Les stands seront disponibles quand l'admin les aura créés</p>
        </div>
        @else

        @foreach($standsDisponibles as $id_evenement => $stands)
        @php
            $evenement         = $stands->first()->evenement;
            $veille            = $evenement
                ? \Carbon\Carbon::parse($evenement->date_debut)->subDay()->toDateString()
                : null;
            $reservationFermee = $veille && now()->toDateString() > $veille;
        @endphp

        <div class="mb-8">
            {{-- Header événement --}}
            <div class="flex items-center justify-between mb-4 p-4 rounded-xl"
                style="background-color: #e6f4ed;">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-calendar text-xl" style="color: #007A3D;"></i>
                    <div>
                        <p class="font-bold text-gray-800">{{ $evenement->nom ?? 'Événement inconnu' }}</p>
                        @if($evenement)
                        <p class="text-xs text-gray-500">
                            {{ $evenement->ville }}
                            <span class="mx-1">•</span>
                            {{ \Carbon\Carbon::parse($evenement->date_debut)->format('d/m/Y') }}
                            <span class="mx-1">•</span>
                            {{ $stands->count() }} stand(s) disponible(s)
                        </p>
                        @endif
                    </div>
                </div>
                @if($veille)
                <div>
                    @if($reservationFermee)
                    <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 font-medium">
                        <i class="fa-solid fa-lock mr-1"></i> Réservations fermées
                    </span>
                    @else
                    <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium">
                        <i class="fa-solid fa-clock mr-1"></i> Limite : {{ $veille }}
                    </span>
                    @endif
                </div>
                @endif
            </div>

            {{-- Grille des stands --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($stands as $stand)
                @php
                    $prixStand  = $stand->prix_calcule;
                    $composants = is_array($stand->composants)
                        ? $stand->composants
                        : (json_decode($stand->composants ?? '[]', true) ?: []);
                @endphp
                <div class="bg-white rounded-xl shadow p-4 border-2 transition
                    {{ $reservationFermee ? 'border-gray-100 opacity-60' : 'border-gray-200 hover:border-green-400 hover:shadow-md' }}">

                    {{-- Numéro + standing --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold"
                            style="background-color: {{ $reservationFermee ? '#9ca3af' : '#007A3D' }}">
                            {{ $stand->numero_stand }}
                        </div>
                        @if($stand->standing == 'vip')
                        <span class="text-xs px-2 py-0.5 rounded-full text-white bg-yellow-500 font-bold">
                            <i class="fa-solid fa-star mr-0.5"></i> VIP
                        </span>
                        @elseif($stand->standing == 'premium')
                        <span class="text-xs px-2 py-0.5 rounded-full text-white bg-blue-600 font-bold">
                            <i class="fa-solid fa-gem mr-0.5"></i> Premium
                        </span>
                        @else
                        <span class="text-xs px-2 py-0.5 rounded-full text-white font-bold"
                            style="background-color: #007A3D;">
                            <i class="fa-solid fa-store mr-0.5"></i> Standard
                        </span>
                        @endif
                    </div>

                    {{-- Détails du stand --}}
                    <div class="space-y-1 mb-3">
                        @if($stand->superficie)
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <i class="fa-solid fa-ruler-combined text-gray-400 w-3"></i>
                            Superficie : <strong>{{ $stand->superficie }}</strong>
                        </p>
                        @endif

                        {{-- ✅ Composition exacte du stand --}}
                        @if(!empty($composants))
                        <div class="bg-gray-50 rounded-lg p-2 mt-2">
                            <p class="text-xs text-gray-400 font-medium mb-1">
                                <i class="fa-solid fa-box mr-1"></i> Composition :
                            </p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($composants as $comp)
                                <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full font-medium">
                                    {{ $comp['qte'] }}x {{ $comp['nom'] }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Prix --}}
                    @if($prixStand > 0)
                    <p class="text-sm font-bold mb-3" style="color: #C8102E;">
                        <i class="fa-solid fa-money-bill mr-1"></i>
                        {{ number_format($prixStand, 0, ',', ' ') }} FCFA
                    </p>
                    @else
                    <p class="text-xs text-green-600 font-semibold mb-3">
                        <i class="fa-solid fa-gift mr-1"></i> Gratuit
                    </p>
                    @endif

                    {{-- Bouton réserver --}}
                    @if($reservationFermee)
                    <div class="text-center text-xs text-gray-400 py-1">
                        <i class="fa-solid fa-lock mr-1"></i> Fermé
                    </div>
                    @else
                    <button wire:click="reserverStand({{ $stand->id }})"
                        wire:confirm="Réserver le Stand N°{{ $stand->numero_stand }} ?"
                        class="w-full py-2 rounded-lg text-xs text-white font-bold transition hover:opacity-90"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-bookmark mr-1"></i>
                        Réserver ce stand
                    </button>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
        @endif
    </div>

    {{-- MODAL PAIEMENT STAND --}}
    @if($showModalPaiement)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #C8102E, #a00d25);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-credit-card"></i>
                    Paiement du stand
                </h3>
                <button wire:click="closeModalPaiement"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
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
                            <p class="text-xs text-gray-400">Paiement via Orange Money</p>
                        </div>
                        @if($mode_paiement === 'orange_money')
                        <i class="fa-solid fa-circle-check ml-auto text-orange-500"></i>
                        @endif
                    </button>

                    <button type="button" wire:click="$set('mode_paiement', 'moov_money')"
                        class="w-full border-2 rounded-xl p-4 transition flex items-center gap-4
                            {{ $mode_paiement === 'moov_money' ? 'border-blue-400 bg-blue-50' : 'border-gray-200 hover:bg-blue-50' }}">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                            style="background-color: #0066CC;">MM</div>
                        <div class="text-left">
                            <p class="font-bold text-gray-800">Moov Money</p>
                            <p class="text-xs text-gray-400">Paiement via Moov Money</p>
                        </div>
                        @if($mode_paiement === 'moov_money')
                        <i class="fa-solid fa-circle-check ml-auto text-blue-500"></i>
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
                        @endif
                    </button>
                </div>

                @if($mode_paiement == 'orange_money' || $mode_paiement == 'moov_money')
                <button wire:click="$set('etape_paiement', 2)"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow-lg mt-4"
                    style="background-color: {{ $mode_paiement == 'orange_money' ? '#FF6600' : '#0066CC' }};">
                    Continuer <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
                @elseif($mode_paiement == 'carte')
                <button wire:click="$set('etape_paiement', 4)"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow-lg mt-4"
                    style="background-color: #6d28d9;">
                    Continuer <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
                @endif

                @elseif($etape_paiement == 2)
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Votre numéro de téléphone *</label>
                    <input wire:model="telephone_paiement" type="text"
                        class="w-full border rounded-xl px-4 py-3 text-lg text-center font-mono"
                        placeholder="{{ $mode_paiement == 'orange_money' ? '07XXXXXXXX' : '01XXXXXXXX' }}">
                    @error('telephone_paiement') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
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
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Code OTP reçu *</label>
                    <input wire:model="otp_saisi" type="text" maxlength="6"
                        class="w-full border rounded-xl px-4 py-3 text-2xl text-center font-mono tracking-widest"
                        placeholder="_ _ _ _ _ _">
                    @error('otp_saisi') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-center text-xs text-yellow-700 mt-3">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                    <strong>Simulation :</strong> Code OTP :
                    <span class="font-mono font-bold text-red-600 text-lg ml-1">{{ $otp_code }}</span>
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
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Numéro de carte *</label>
                        <input wire:model="carte_numero" type="text" maxlength="19"
                            class="w-full border rounded-xl px-4 py-2.5 text-sm font-mono tracking-widest text-center"
                            placeholder="XXXX XXXX XXXX XXXX">
                        @error('carte_numero') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom sur la carte *</label>
                        <input wire:model="carte_nom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 text-sm"
                            placeholder="Ex: OUEDRAOGO MOUSSA">
                        @error('carte_nom') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Expiration *</label>
                            <input wire:model="carte_expiration" type="text" maxlength="5"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm font-mono text-center"
                                placeholder="MM/AA">
                            @error('carte_expiration') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">CVV *</label>
                            <input wire:model="carte_cvv" type="password" maxlength="4"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm font-mono text-center"
                                placeholder="XXX">
                            @error('carte_cvv') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-xs text-green-700 flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-green-500"></i>
                        Paiement sécurisé SSL
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
</div>