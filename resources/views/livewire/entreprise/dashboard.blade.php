<div>

    {{-- ALERTES --}}
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

    {{-- ============================================================
         PARCOURS PAIEMENT ENTREPRISE
    ============================================================ --}}
    @if($entreprise && $entreprise->statut_validation == 'valide')

        {{-- ÉTAPE 1 : Entreprise validée, paiement en attente --}}
        @if($paiementEnAttente)
        <div class="rounded-xl px-6 py-5 mb-6 flex items-center justify-between gap-4 flex-wrap"
            style="background: linear-gradient(135deg, #007A3D, #005a2d);">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center bg-white/20 flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-white text-xl"></i>
                </div>
                <div>
                    <p class="font-bold text-white">Votre entreprise a été validée !</p>
                    <p class="text-sm text-green-200">
                        Procédez maintenant au paiement de
                        @if($remiseApplicable > 0)
                        <span class="line-through opacity-60">{{ number_format($montantBrutAffiche, 0, ',', ' ') }} FCFA</span>
                        <strong>{{ number_format($montantPaiement, 0, ',', ' ') }} FCFA</strong>
                        <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full ml-1">-{{ $remiseApplicable }}% remise</span>
                        @else
                        <strong>{{ number_format($montantPaiement, 0, ',', ' ') }} FCFA</strong>
                        @endif
                        pour finaliser votre inscription.
                    </p>
                </div>
            </div>
            <button wire:click="openModalPaiement"
                class="px-6 py-3 rounded-xl text-white font-bold text-sm transition hover:opacity-90 shadow-lg flex items-center gap-2 flex-shrink-0"
                style="background-color: #C8102E;">
                <i class="fa-solid fa-credit-card"></i>
                Payer maintenant
            </button>
        </div>
        @endif

        {{-- ÉTAPE 2 : Paiement soumis, en attente de validation admin --}}
        @if($statutPaiement === 'en_attente')
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-6 py-5 mb-6 flex items-center gap-3">
            <div class="w-12 h-12 rounded-full flex items-center justify-center bg-yellow-400 flex-shrink-0">
                <i class="fa-solid fa-hourglass-half text-white text-xl"></i>
            </div>
            <div>
                <p class="font-bold text-yellow-800">Paiement soumis avec succès</p>
                <p class="text-sm text-yellow-700">
                    Votre paiement a bien été enregistré. Il sera vérifié et validé
                    par l'administration sous peu. Vous serez notifié dès la validation.
                </p>
            </div>
        </div>
        @endif

        {{-- ÉTAPE 3 : Paiement validé → reçu + souhaits --}}
        @if($statutPaiement === 'valide')
        <div class="rounded-xl px-6 py-5 mb-6 flex items-center justify-between gap-4 flex-wrap"
            style="background: linear-gradient(135deg, #007A3D, #005a2d);">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center bg-white/20 flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-white text-xl"></i>
                </div>
                <div>
                    <p class="font-bold text-white">✅ Paiement validé !</p>
                    <p class="text-sm text-green-200">
                        L'inscription de votre entreprise est désormais complète et confirmée.
                    </p>
                </div>
            </div>
            @if($recuPaiement)
            <a href="{{ route('entreprise.recu.telecharger', $recuPaiement->id) }}" target="_blank"
                class="px-6 py-3 rounded-xl bg-white font-bold text-sm transition hover:bg-gray-50 flex items-center gap-2 flex-shrink-0"
                style="color: #007A3D;">
                <i class="fa-solid fa-download"></i>
                Télécharger le reçu
            </a>
            @endif
        </div>

        @php
            $inscriptionB2BPayee = $representant
                ? \App\Models\Inscription::where('id_participant', $representant->id)
                    ->where('statut_paiement', 'paye')
                    ->whereHas('evenement', fn($q) => $q->where('type_evenement', 'avec_b2b'))
                    ->exists()
                : false;
        @endphp
        @if($inscriptionB2BPayee)
        <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-5 mb-6 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center bg-blue-500 flex-shrink-0">
                    <i class="fa-solid fa-handshake text-white text-xl"></i>
                </div>
                <div>
                    <p class="font-bold text-blue-800">Vous pouvez maintenant faire vos souhaits de RDV !</p>
                    <p class="text-sm text-blue-600">
                        Cet événement inclut des rendez-vous d'affaires B2B.
                        Sélectionnez les entreprises que vous souhaitez rencontrer.
                    </p>
                </div>
            </div>
            <a href="{{ route('entreprise.souhaits') }}"
                class="px-6 py-3 rounded-xl text-white font-bold text-sm transition hover:opacity-90 flex items-center gap-2 flex-shrink-0"
                style="background-color: #007A3D;">
                <i class="fa-solid fa-heart"></i>
                Faire mes souhaits
            </a>
        </div>
        @endif
        @endif

    @elseif($entreprise && $entreprise->statut_validation != 'valide')
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-6 py-5 mb-6 flex items-center gap-3">
            <div class="w-12 h-12 rounded-full flex items-center justify-center bg-yellow-400 flex-shrink-0">
                <i class="fa-solid fa-clock text-white text-xl"></i>
            </div>
            <div>
                <p class="font-bold text-yellow-800">Préinscription en attente de validation</p>
                <p class="text-sm text-yellow-700">
                    Votre dossier entreprise est en cours d'examen par l'administration.
                    Vous serez notifié dès validation.
                </p>
            </div>
        </div>
    @endif

    {{-- ============================================================
         INFO ENTREPRISE
    ============================================================ --}}
    @if($entreprise)
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white text-2xl font-bold flex-shrink-0"
                style="background-color: #007A3D;">
                {{ strtoupper(substr($entreprise->nom, 0, 1)) }}
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-800">{{ $entreprise->nom }}</h2>
                <div class="flex items-center gap-4 mt-1 flex-wrap">
                    <span class="text-sm text-gray-500">
                        <i class="fa-solid fa-industry text-gray-400 mr-1"></i>
                        {{ $entreprise->secteur_activite }}
                    </span>
                    <span class="text-sm text-gray-500">
                        <i class="fa-solid fa-location-dot text-gray-400 mr-1"></i>
                        {{ $entreprise->ville }}, {{ $entreprise->pays }}
                    </span>
                    @if($entreprise->ifu)
                    <span class="text-sm font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-600">
                        IFU: {{ $entreprise->ifu }}
                    </span>
                    @endif
                </div>
            </div>
            @if($entreprise->statut_validation == 'valide')
            <span class="px-3 py-1.5 rounded-full text-xs font-medium text-white flex-shrink-0"
                style="background-color: #007A3D;">
                <i class="fa-solid fa-circle-check mr-1"></i> Validée
            </span>
            @else
            <span class="px-3 py-1.5 rounded-full text-xs font-medium text-white bg-yellow-500 flex-shrink-0">
                <i class="fa-solid fa-clock mr-1"></i> En attente
            </span>
            @endif
        </div>
    </div>
    @endif

    {{-- ============================================================
         STATISTIQUES
    ============================================================ --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #C8102E;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #fde8ec;">
                <i class="fa-solid fa-users" style="color: #C8102E;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Membres</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalMembres }}</p>
                @if($demandesEnAttente > 0)
                <p class="text-xs text-orange-500 mt-1">
                    {{ $demandesEnAttente }} demande(s) en attente
                </p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #007A3D;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #e6f4ed;">
                <i class="fa-solid fa-store" style="color: #007A3D;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Stands</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalStands }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #2d5a8e;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #e8f0fb;">
                <i class="fa-solid fa-handshake" style="color: #2d5a8e;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Rendez-vous</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalRdv }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #f59e0b;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #fef3e2;">
                <i class="fa-solid fa-bell" style="color: #f59e0b;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Notifications</p>
                <p class="text-3xl font-bold text-gray-800">{{ $notifications->count() }}</p>
            </div>
        </div>

    </div>

    {{-- ============================================================
         ÉVÉNEMENTS DISPONIBLES
    ============================================================ --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-calendar-star" style="color: #C8102E;"></i>
                Événements disponibles
            </h3>
            <span class="text-xs text-gray-400">
                {{ $evenementsDisponibles->count() }} événement(s) ouvert(s)
            </span>
        </div>

        @forelse($evenementsDisponibles as $evenement)
        <div class="border border-gray-200 rounded-xl p-5 mb-4 hover:border-green-300 hover:shadow-md transition last:mb-0">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white flex-shrink-0"
                            style="background-color: #007A3D;">
                            <i class="fa-solid fa-calendar text-sm"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">{{ $evenement->nom }}</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 mt-3">
                        <span>
                            <i class="fa-solid fa-calendar mr-1 text-gray-400"></i>
                            {{ \Carbon\Carbon::parse($evenement->date_debut)->format('d/m/Y') }}
                        </span>
                        <span>
                            <i class="fa-solid fa-location-dot mr-1 text-gray-400"></i>
                            {{ $evenement->ville }}
                        </span>
                    </div>
                </div>
                <div class="flex-shrink-0 text-right">
                    @if($evenement->deja_inscrit)
                    <span class="px-4 py-2 rounded-xl text-xs font-medium text-white flex items-center gap-1"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-circle-check"></i> Inscrit
                    </span>
                    @else
                    <a href="{{ route('entreprise.inscription.wizard', $evenement->id) }}"
                        class="px-4 py-2 rounded-xl text-white text-sm font-medium transition hover:opacity-90 flex items-center gap-1 shadow"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-user-plus"></i>
                        S'inscrire
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-400">
            <i class="fa-solid fa-calendar-xmark text-3xl mb-2 block text-gray-300"></i>
            <p class="text-sm">Aucun événement disponible pour le moment</p>
        </div>
        @endforelse
    </div>

    {{-- ============================================================
         DERNIERS MEMBRES
    ============================================================ --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-users" style="color: #007A3D;"></i>
                Derniers membres
            </h3>
            <a href="{{ route('entreprise.participants') }}"
                class="text-sm px-4 py-2 rounded-lg text-white transition hover:opacity-90"
                style="background-color: #007A3D;">
                Voir tous
            </a>
        </div>

        @forelse($derniersMembres as $membre)
        <div class="flex items-center justify-between py-3 border-b last:border-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                    style="background-color: {{ $membre->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                    {{ strtoupper(substr($membre->prenom ?? 'X', 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $membre->nom }} {{ $membre->prenom }}</p>
                    <p class="text-xs text-gray-400">{{ $membre->fonction ?? '-' }}</p>
                </div>
            </div>
            <span class="text-xs px-2 py-0.5 rounded-full text-white font-medium"
                style="background-color: {{ $membre->role == 'representant' ? '#C8102E' : '#007A3D' }}">
                {{ $membre->role == 'representant' ? 'Représentant' : 'Membre' }}
            </span>
        </div>
        @empty
        <div class="text-center py-8 text-gray-400">
            <i class="fa-solid fa-users text-3xl mb-2 block text-gray-300"></i>
            <p class="text-sm">Aucun membre pour le moment</p>
        </div>
        @endforelse
    </div>

    {{-- ============================================================
         MODAL PAIEMENT
    ============================================================ --}}
    @if($showModalPaiement)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="fa-solid fa-credit-card text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Paiement de l'inscription</h3>
                        <p class="text-green-200 text-xs">Choisissez votre mode de paiement</p>
                    </div>
                </div>
                <button wire:click="closeModalPaiement"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8">

                <div class="bg-gray-50 rounded-xl p-4 mb-6 text-center border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">Montant à payer</p>
                    @if($pourcentage_remise > 0)
                    <p class="text-sm text-gray-400 line-through">{{ number_format($montant_brut, 0, ',', ' ') }} FCFA</p>
                    <p class="text-3xl font-bold" style="color: #007A3D;">
                        {{ number_format($montant_paiement, 0, ',', ' ') }} FCFA
                    </p>
                    <span class="inline-block mt-1 text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold">
                        <i class="fa-solid fa-tag mr-1"></i>
                        Remise de {{ $pourcentage_remise }}% appliquée (-{{ number_format($montant_remise, 0, ',', ' ') }} FCFA)
                    </span>
                    @else
                    <p class="text-3xl font-bold" style="color: #007A3D;">
                        {{ number_format($montant_paiement, 0, ',', ' ') }} FCFA
                    </p>
                    @endif
                </div>

                @if($etape_paiement == 1)

                <p class="text-sm font-semibold text-gray-700 mb-4">
                    Choisissez votre mode de paiement :
                </p>
                <div class="space-y-3 mb-5">
                    <button type="button" wire:click="$set('mode_paiement', 'orange_money')"
                        class="w-full border-2 rounded-xl p-4 transition flex items-center gap-4
                            {{ $mode_paiement === 'orange_money' ? 'border-orange-400 bg-orange-50' : 'border-gray-200 hover:bg-orange-50' }}">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                            style="background-color: #FF6600;">OM</div>
                        <div class="text-left">
                            <p class="font-bold text-gray-800">Orange Money</p>
                            <p class="text-xs text-gray-400">Composez *144*4*6# pour votre OTP</p>
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
                            <p class="font-bold text-gray-800">Moov Africa</p>
                            <p class="text-xs text-gray-400">Paiement via Moov Money</p>
                        </div>
                        @if($mode_paiement === 'moov_money')
                        <i class="fa-solid fa-circle-check ml-auto text-blue-500"></i>
                        @else
                        <i class="fa-solid fa-chevron-right ml-auto text-gray-400"></i>
                        @endif
                    </button>

                    <button type="button" wire:click="$set('mode_paiement', 'cheque')"
                        class="w-full border-2 rounded-xl p-4 transition flex items-center gap-4
                            {{ $mode_paiement === 'cheque' ? 'border-red-400 bg-red-50' : 'border-gray-200 hover:bg-red-50' }}">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                            style="background-color: #C8102E;">
                            <i class="fa-solid fa-money-check"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-gray-800">Chèque</p>
                            <p class="text-xs text-gray-400">Paiement par chèque bancaire</p>
                        </div>
                        @if($mode_paiement === 'cheque')
                        <i class="fa-solid fa-circle-check ml-auto text-red-500"></i>
                        @else
                        <i class="fa-solid fa-chevron-right ml-auto text-gray-400"></i>
                        @endif
                    </button>
                </div>

                @if($mode_paiement === 'cheque')
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">
                        Numéro du chèque *
                    </label>
                    <input wire:model="numero_cheque" type="text"
                        class="w-full border rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm font-mono"
                        placeholder="Ex: 0012345">
                    @error('numero_cheque')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-red-600 mt-2">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Remettez le chèque physiquement à l'administration.
                        Votre paiement sera validé après réception et vérification.
                    </p>
                </div>

                <button wire:click="payerParCheque"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-not-allowed"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow-lg flex items-center justify-center gap-2"
                    style="background-color: #C8102E;">
                    <span wire:loading.remove wire:target="payerParCheque">
                        <i class="fa-solid fa-paper-plane mr-1"></i>
                        Soumettre le paiement par chèque
                    </span>
                    <span wire:loading wire:target="payerParCheque">
                        <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                        Envoi...
                    </span>
                </button>

                @else
                <div class="mb-5">
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">
                        Votre numéro de téléphone *
                    </label>
                    <input wire:model="telephone_paiement" type="text"
                        class="w-full border rounded-xl px-4 py-3 focus:outline-none text-lg text-center font-mono"
                        placeholder="{{ $mode_paiement == 'orange_money' ? '07XXXXXXXX' : '01XXXXXXXX' }}">
                    @error('telephone_paiement')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                @if($mode_paiement == 'orange_money')
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-3 mb-5 text-xs text-orange-700 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5 flex-shrink-0"></i>
                    Composez <strong>*144*4*6#</strong> sur votre téléphone Orange
                    pour générer votre OTP avant de continuer.
                </div>
                @endif

                <button wire:click="envoyerOtp"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow-lg flex items-center justify-center gap-2"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-paper-plane"></i>
                    Continuer et recevoir mon OTP
                </button>
                @endif

                @elseif($etape_paiement == 2)
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3"
                        style="background-color: #e6f4ed;">
                        <i class="fa-solid fa-shield-halved text-3xl" style="color: #007A3D;"></i>
                    </div>
                    <p class="font-bold text-gray-800">Vérification OTP</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Code envoyé au <strong>{{ $telephone_paiement }}</strong>
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">
                        Code OTP reçu *
                    </label>
                    <input wire:model="otp_saisi" type="text" maxlength="6"
                        class="w-full border rounded-xl px-4 py-3 text-2xl text-center font-mono tracking-widest"
                        placeholder="_ _ _ _ _ _">
                    @error('otp_saisi')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-center text-xs text-yellow-700 mb-5">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                    <strong>Simulation :</strong> Votre code OTP est
                    <span class="font-mono font-bold text-red-600 text-lg ml-1">{{ $otp_code }}</span>
                </div>

                <div class="flex gap-3">
                    <button wire:click="$set('etape_paiement', 1)"
                        class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Retour
                    </button>
                    <button wire:click="confirmerOtp"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="flex-1 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm flex items-center justify-center gap-2"
                        style="background-color: #007A3D;">
                        <span wire:loading.remove wire:target="confirmerOtp">
                            <i class="fa-solid fa-check mr-1"></i>
                            Confirmer le paiement
                        </span>
                        <span wire:loading wire:target="confirmerOtp">
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                            Traitement...
                        </span>
                    </button>
                </div>
                @endif

            </div>
        </div>
    </div>
    @endif

</div>