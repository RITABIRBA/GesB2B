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
         ✅ BARRE DE PROGRESSION
    ============================================================ --}}
    @if($representant)
    @php
        $preinscrit      = true;
        $valide          = ($representant->statut_preinscription ?? '') === 'valide';
        $paye            = $mesInscriptions->contains(fn($i) => $i->statut_paiement === 'paye');
        $evenementB2B    = $mesInscriptions->contains(fn($i) =>
            $i->evenement && ($i->evenement->type_evenement ?? 'avec_b2b') === 'avec_b2b'
        );
        $profilB2BComplet = !empty($representant->zone_geographique)
            && !empty($representant->types_partenariat)
            && !empty($representant->secteurs_recherche);
        $aSouhaits = $totalSouhaits > 0;

        if (!$valide)          $etapeCourante = 1;
        elseif (!$paye)        $etapeCourante = 2;
        elseif ($evenementB2B && !$profilB2BComplet) $etapeCourante = 3;
        elseif ($evenementB2B && !$aSouhaits)        $etapeCourante = 4;
        else                   $etapeCourante = 5;

        $etapes = $evenementB2B ? [
            ['num' => 1, 'label' => 'Préinscrit',  'icon' => 'fa-building',      'desc' => 'Dossier soumis'],
            ['num' => 2, 'label' => 'Validé',       'icon' => 'fa-user-check',    'desc' => 'Compte créé'],
            ['num' => 3, 'label' => 'Payé',         'icon' => 'fa-credit-card',   'desc' => 'Inscription confirmée'],
            ['num' => 4, 'label' => 'Profil B2B',   'icon' => 'fa-handshake',     'desc' => 'Profil complété'],
            ['num' => 5, 'label' => 'Souhaits',     'icon' => 'fa-heart',         'desc' => 'RDV en cours'],
        ] : [
            ['num' => 1, 'label' => 'Préinscrit',   'icon' => 'fa-building',      'desc' => 'Dossier soumis'],
            ['num' => 2, 'label' => 'Validé',        'icon' => 'fa-user-check',    'desc' => 'Compte créé'],
            ['num' => 3, 'label' => 'Payé',          'icon' => 'fa-credit-card',   'desc' => 'Inscription confirmée'],
        ];
    @endphp

    <div class="bg-white rounded-2xl shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-600 flex items-center gap-2">
                <i class="fa-solid fa-list-check" style="color: #007A3D;"></i>
                Parcours d'inscription
            </h3>
            <span class="text-xs text-gray-400">
                Étape {{ min($etapeCourante, count($etapes)) }} / {{ count($etapes) }}
            </span>
        </div>

        <div class="relative flex items-start justify-between">
            <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-200 mx-8 z-0"></div>
            <div class="absolute top-5 left-0 h-0.5 z-0 transition-all duration-700"
                style="background: linear-gradient(90deg, #007A3D, #C8102E);
                       right: calc({{ 100 - (($etapeCourante - 1) / max(count($etapes) - 1, 1)) * 100 }}% + 2rem);
                       margin-left: 2rem;">
            </div>

            @foreach($etapes as $etape)
            @php
                $estFait    = $etape['num'] < $etapeCourante;
                $estCourant = $etape['num'] === $etapeCourante;
            @endphp
            <div class="flex flex-col items-center gap-2 relative z-10 flex-1">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shadow-md
                    {{ $estFait ? 'text-white' : ($estCourant ? 'text-white ring-4 ring-offset-2' : 'text-gray-400 bg-gray-100') }}"
                    style="{{ $estFait ? 'background-color: #007A3D;' : ($estCourant ? 'background-color: #C8102E;' : '') }}">
                    @if($estFait)
                        <i class="fa-solid fa-check text-xs"></i>
                    @elseif($estCourant)
                        <i class="fa-solid {{ $etape['icon'] }} text-xs"></i>
                    @else
                        <span class="text-xs">{{ $etape['num'] }}</span>
                    @endif
                </div>
                <div class="text-center">
                    <p class="text-xs font-semibold {{ $estFait ? 'text-green-700' : ($estCourant ? 'text-red-700' : 'text-gray-400') }}">
                        {{ $etape['label'] }}
                    </p>
                    <p class="text-xs text-gray-400 hidden sm:block">{{ $etape['desc'] }}</p>
                </div>
                @if($estCourant)
                <span class="text-xs px-2 py-0.5 rounded-full font-bold text-white animate-pulse" style="background-color: #C8102E;">En cours</span>
                @elseif($estFait)
                <span class="text-xs px-2 py-0.5 rounded-full font-medium text-green-700 bg-green-100">✓ Fait</span>
                @endif
            </div>
            @endforeach
        </div>

        <div class="mt-5 pt-4 border-t border-gray-100">
            @if($etapeCourante == 1)
            <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3">
                <i class="fa-solid fa-hourglass-half text-yellow-500"></i>
                <p class="text-sm text-yellow-700">Votre dossier est en attente de validation par l'administration.</p>
            </div>
            @elseif($etapeCourante == 2)
            <div class="flex items-center justify-between gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-green-500"></i>
                    <p class="text-sm text-green-700">Préinscription validée ! Procédez au paiement pour confirmer votre inscription.</p>
                </div>
                <button wire:click="openModalPaiement"
                    class="px-4 py-2 rounded-xl text-white text-xs font-bold flex-shrink-0 transition hover:opacity-90"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-credit-card mr-1"></i> Payer
                </button>
            </div>
            @elseif($etapeCourante == 3 && $evenementB2B)
            <div class="flex items-center justify-between gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-blue-500"></i>
                    <p class="text-sm text-blue-700">Paiement confirmé ! Complétez votre profil B2B pour accéder aux souhaits.</p>
                </div>
                <a href="{{ route('entreprise.completer-profil-b2b') }}"
                    class="px-4 py-2 rounded-xl text-white text-xs font-bold flex-shrink-0 transition hover:opacity-90"
                    style="background-color: #2d5a8e;">
                    <i class="fa-solid fa-pen mr-1"></i> Compléter
                </a>
            </div>
            @elseif($etapeCourante == 4 && $evenementB2B)
            <div class="flex items-center justify-between gap-3 bg-purple-50 border border-purple-200 rounded-xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-handshake text-purple-500"></i>
                    <p class="text-sm text-purple-700">Profil B2B complété ! Émettez vos souhaits de rendez-vous.</p>
                </div>
                <a href="{{ route('entreprise.souhaits') }}"
                    class="px-4 py-2 rounded-xl text-white text-xs font-bold flex-shrink-0 transition hover:opacity-90"
                    style="background-color: #8b5cf6;">
                    <i class="fa-solid fa-heart mr-1"></i> Mes souhaits
                </a>
            </div>
            @else
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
                <i class="fa-solid fa-circle-check text-green-500"></i>
                <p class="text-sm text-green-700 font-medium">🎉 Tout est en ordre ! Vous participez activement à l'événement.</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ============================================================
         PARCOURS PAIEMENT
    ============================================================ --}}
    @if($paiementEnAttente)
    <div class="rounded-xl px-6 py-5 mb-6 flex items-center justify-between gap-4 flex-wrap"
        style="background: linear-gradient(135deg, #007A3D, #005a2d);">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full flex items-center justify-center bg-white/20 flex-shrink-0">
                <i class="fa-solid fa-circle-check text-white text-xl"></i>
            </div>
            <div>
                <p class="font-bold text-white">Votre préinscription a été validée !</p>
                <p class="text-sm text-green-200">
                    Procédez au paiement de
                    @if($remiseApplicable > 0)
                    <span class="line-through opacity-60">{{ number_format($montantBrutAffiche, 0, ',', ' ') }} FCFA</span>
                    <strong>{{ number_format($montantPaiement, 0, ',', ' ') }} FCFA</strong>
                    <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full ml-1">-{{ $remiseApplicable }}%</span>
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
            <i class="fa-solid fa-credit-card"></i> Payer maintenant
        </button>
    </div>
    @endif

    @if($statutPaiement === 'en_attente')
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-6 py-5 mb-6 flex items-center gap-3">
        <div class="w-12 h-12 rounded-full flex items-center justify-center bg-yellow-400 flex-shrink-0">
            <i class="fa-solid fa-hourglass-half text-white text-xl"></i>
        </div>
        <div>
            <p class="font-bold text-yellow-800">Paiement soumis avec succès</p>
            <p class="text-sm text-yellow-700">Votre paiement est en cours de vérification par l'administration.</p>
        </div>
    </div>
    @endif

    @if($statutPaiement === 'valide')
    <div class="rounded-xl px-6 py-5 mb-6 flex items-center justify-between gap-4 flex-wrap"
        style="background: linear-gradient(135deg, #007A3D, #005a2d);">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full flex items-center justify-center bg-white/20 flex-shrink-0">
                <i class="fa-solid fa-circle-check text-white text-xl"></i>
            </div>
            <div>
                <p class="font-bold text-white">✅ Paiement validé !</p>
                <p class="text-sm text-green-200">Votre inscription est désormais complète et confirmée.</p>
            </div>
        </div>
        {{-- ✅ Bouton reçu petit et sans target blank --}}
        @if($recuPaiement)
        <a href="{{ route('entreprise.recu.telecharger', $recuPaiement->id) }}"
            class="px-3 py-1.5 rounded-lg bg-white/20 text-white text-xs font-medium transition hover:bg-white/30 flex items-center gap-1.5 flex-shrink-0">
            <i class="fa-solid fa-download text-xs"></i> Reçu
        </a>
        @endif
    </div>

    {{-- ✅ Bandeau souhaits — masqué si déjà des souhaits --}}
    @if($evenementB2B && $totalSouhaits == 0)
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-5 mb-6 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full flex items-center justify-center bg-blue-500 flex-shrink-0">
                <i class="fa-solid fa-handshake text-white text-xl"></i>
            </div>
            <div>
                <p class="font-bold text-blue-800">Vous pouvez maintenant faire vos souhaits de RDV !</p>
                <p class="text-sm text-blue-600">Cet événement inclut des rendez-vous d'affaires B2B.</p>
            </div>
        </div>
        <a href="{{ route('entreprise.souhaits') }}"
            class="px-6 py-3 rounded-xl text-white font-bold text-sm transition hover:opacity-90 flex items-center gap-2 flex-shrink-0"
            style="background-color: #007A3D;">
            <i class="fa-solid fa-heart"></i> Faire mes souhaits
        </a>
    </div>
    @endif
    @endif

    {{-- ============================================================
         INFO ENTREPRISE + REPRÉSENTANT
    ============================================================ --}}
    @if($entreprise)
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-2xl font-bold flex-shrink-0"
                style="background-color: #007A3D;">
                {{ strtoupper(substr($entreprise->nom, 0, 1)) }}
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-800">{{ $entreprise->nom }}</h2>
                <div class="flex items-center gap-3 mt-1 flex-wrap text-sm text-gray-500">
                    @if($entreprise->ifu)
                    <span><i class="fa-solid fa-hashtag text-gray-400 mr-1"></i>IFU : {{ $entreprise->ifu }}</span>
                    @endif
                    @if($entreprise->secteur_activite)
                    <span><i class="fa-solid fa-industry text-gray-400 mr-1"></i>{{ $entreprise->secteur_activite }}</span>
                    @endif
                    @if($entreprise->ville)
                    <span><i class="fa-solid fa-location-dot text-gray-400 mr-1"></i>{{ $entreprise->ville }}, {{ $entreprise->pays }}</span>
                    @endif
                </div>
            </div>
            @if($entreprise->statut_validation == 'valide')
            <span class="text-xs px-3 py-1.5 rounded-full text-white font-medium flex-shrink-0" style="background-color: #007A3D;">
                <i class="fa-solid fa-circle-check mr-1"></i> Validée
            </span>
            @else
            <span class="text-xs px-3 py-1.5 rounded-full text-white font-medium bg-yellow-500 flex-shrink-0">
                <i class="fa-solid fa-clock mr-1"></i> En attente
            </span>
            @endif
        </div>

        @if($representant)
        <div class="border-t border-gray-100 pt-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                style="background-color: #C8102E;">
                {{ strtoupper(substr($representant->prenom ?? 'R', 0, 1)) }}
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-700 text-sm">
                    {{ $representant->nom }} {{ $representant->prenom }}
                    <span class="text-xs text-gray-400 ml-1">(Représentant)</span>
                </p>
                <p class="text-xs text-gray-400">{{ $representant->fonction }}</p>
            </div>
            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded text-gray-600">
                <i class="fa-solid fa-key text-gray-400 mr-1"></i>
                {{ $representant->code_acces }}
            </span>
        </div>
        @endif
    </div>
    @endif

    {{-- ============================================================
         STATISTIQUES
    ============================================================ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #C8102E;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl" style="background-color: #fde8ec;">
                <i class="fa-solid fa-heart" style="color: #C8102E;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Mes Souhaits</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalSouhaits }}</p>
                @if($totalSouhaits < 5)
                <p class="text-xs text-orange-500 mt-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Ajoutez des souhaits</p>
                @else
                <p class="text-xs text-green-600 mt-1"><i class="fa-solid fa-circle-check mr-1"></i>Objectif atteint</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #007A3D;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl" style="background-color: #e6f4ed;">
                <i class="fa-solid fa-handshake" style="color: #007A3D;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Mes Rendez-vous</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalRdv }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #2d5a8e;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl" style="background-color: #e8f0fb;">
                <i class="fa-solid fa-users" style="color: #2d5a8e;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Membres inscrits</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalMembres ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- ============================================================
         PROCHAINS RENDEZ-VOUS
    ============================================================ --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-calendar-check" style="color: #007A3D;"></i>
                Prochains Rendez-vous
            </h3>
            <a href="{{ route('entreprise.rendez-vous') }}"
                class="text-sm px-4 py-2 rounded-lg text-white transition hover:opacity-90"
                style="background-color: #007A3D;">
                Voir tous
            </a>
        </div>

        @forelse($prochainRdv as $rdv)
        <div class="flex items-center justify-between py-4 border-b last:border-0">
            <div class="flex items-center gap-4">
                <div class="text-center flex-shrink-0">
                    @if($rdv->salle)
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg"
                        style="background-color: #2d5a8e;">
                        {{ $rdv->numero_table }}
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $rdv->salle }}</p>
                    @else
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-gray-200">
                        <i class="fa-solid fa-question text-gray-400"></i>
                    </div>
                    @endif
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">
                        {{ $rdv->participant1->nom ?? '-' }} ↔ {{ $rdv->participant2->nom ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-400">
                        <i class="fa-solid fa-calendar mr-1"></i>{{ $rdv->date }}
                        <i class="fa-solid fa-clock ml-2 mr-1"></i>{{ $rdv->heure_debut }} - {{ $rdv->heure_fin }}
                    </p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">Planifié</span>
        </div>
        @empty
        <div class="text-center py-8 text-gray-400">
            <i class="fa-solid fa-calendar-xmark text-3xl mb-2 block text-gray-300"></i>
            <p class="text-sm">Aucun rendez-vous planifié pour le moment</p>
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
                <button wire:click="closeModalPaiement" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                <div class="bg-gray-50 rounded-xl p-4 mb-6 text-center border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">Montant à payer</p>
                    @if($pourcentage_remise > 0)
                    <p class="text-sm text-gray-400 line-through">{{ number_format($montant_brut, 0, ',', ' ') }} FCFA</p>
                    <p class="text-3xl font-bold" style="color: #007A3D;">{{ number_format($montant_paiement, 0, ',', ' ') }} FCFA</p>
                    <span class="inline-block mt-1 text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold">
                        Remise de {{ $pourcentage_remise }}% (-{{ number_format($montant_remise, 0, ',', ' ') }} FCFA)
                    </span>
                    @else
                    <p class="text-3xl font-bold" style="color: #007A3D;">{{ number_format($montant_paiement, 0, ',', ' ') }} FCFA</p>
                    @endif
                </div>

                @if($etape_paiement == 1)
                <div class="space-y-3 mb-5">
                    <button type="button" wire:click="$set('mode_paiement', 'orange_money')"
                        class="w-full border-2 rounded-xl p-4 transition flex items-center gap-4
                            {{ $mode_paiement === 'orange_money' ? 'border-orange-400 bg-orange-50' : 'border-gray-200 hover:bg-orange-50' }}">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0" style="background-color: #FF6600;">OM</div>
                        <div class="text-left flex-1">
                            <p class="font-bold text-gray-800">Orange Money</p>
                            <p class="text-xs text-gray-400">Composez *144*4*6# pour votre OTP</p>
                        </div>
                        @if($mode_paiement === 'orange_money')
                        <i class="fa-solid fa-circle-check text-orange-500"></i>
                        @endif
                    </button>
                    <button type="button" wire:click="$set('mode_paiement', 'moov_money')"
                        class="w-full border-2 rounded-xl p-4 transition flex items-center gap-4
                            {{ $mode_paiement === 'moov_money' ? 'border-blue-400 bg-blue-50' : 'border-gray-200 hover:bg-blue-50' }}">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0" style="background-color: #0066CC;">MM</div>
                        <div class="text-left flex-1">
                            <p class="font-bold text-gray-800">Moov Africa</p>
                            <p class="text-xs text-gray-400">Paiement via Moov Money</p>
                        </div>
                        @if($mode_paiement === 'moov_money')
                        <i class="fa-solid fa-circle-check text-blue-500"></i>
                        @endif
                    </button>
                    <button type="button" wire:click="$set('mode_paiement', 'cheque')"
                        class="w-full border-2 rounded-xl p-4 transition flex items-center gap-4
                            {{ $mode_paiement === 'cheque' ? 'border-red-400 bg-red-50' : 'border-gray-200 hover:bg-red-50' }}">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0" style="background-color: #C8102E;">
                            <i class="fa-solid fa-money-check"></i>
                        </div>
                        <div class="text-left flex-1">
                            <p class="font-bold text-gray-800">Chèque</p>
                            <p class="text-xs text-gray-400">Paiement par chèque bancaire</p>
                        </div>
                        @if($mode_paiement === 'cheque')
                        <i class="fa-solid fa-circle-check text-red-500"></i>
                        @endif
                    </button>
                </div>

                @if($mode_paiement === 'cheque')
                <div class="mb-4">
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Numéro du chèque *</label>
                    <input wire:model="numero_cheque" type="text"
                        class="w-full border rounded-xl px-4 py-3 focus:outline-none text-sm font-mono"
                        placeholder="Ex: 0012345">
                    @error('numero_cheque')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                </div>
                <button wire:click="payerParCheque"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow-lg"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-paper-plane mr-1"></i> Soumettre le paiement par chèque
                </button>
                @else
                <div class="mb-4">
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Votre numéro de téléphone *</label>
                    <input wire:model="telephone_paiement" type="text"
                        class="w-full border rounded-xl px-4 py-3 text-xl text-center font-mono"
                        placeholder="{{ $mode_paiement == 'orange_money' ? '07XXXXXXXX' : '01XXXXXXXX' }}">
                    @error('telephone_paiement')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                </div>
                <button wire:click="envoyerOtp"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow-lg"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-paper-plane mr-1"></i> Continuer et recevoir mon OTP
                </button>
                @endif

                @elseif($etape_paiement == 2)
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3" style="background-color: #e6f4ed;">
                        <i class="fa-solid fa-shield-halved text-3xl" style="color: #007A3D;"></i>
                    </div>
                    <p class="font-bold text-gray-800">Vérification OTP</p>
                    <p class="text-sm text-gray-500 mt-1">Code envoyé au <strong>{{ $telephone_paiement }}</strong></p>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Code OTP reçu *</label>
                    <input wire:model="otp_saisi" type="text" maxlength="6"
                        class="w-full border rounded-xl px-4 py-3 text-2xl text-center font-mono tracking-widest"
                        placeholder="_ _ _ _ _ _">
                    @error('otp_saisi')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
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
                        class="flex-1 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-check mr-1"></i> Confirmer le paiement
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

</div>