<div>
    <h3 class="text-xl font-bold text-gray-700 mb-6">Mon Badge</h3>

    @if($badge)
    <div class="max-w-md mx-auto">

        {{-- Carte badge --}}
        <div id="badge-print" class="bg-white rounded-2xl shadow-xl overflow-hidden">

            {{-- Header badge --}}
            <div class="p-6 text-center text-white"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <img src="{{ asset('images/logo-ccibf.png') }}"
                    alt="CCI-BF" class="w-12 h-12 object-contain mx-auto mb-2">
                <p class="text-xs text-green-300 mb-1">CCI-BF — GesB2B</p>
                <h2 class="text-2xl font-bold">
                    {{ $badge->typeBadge->libelle ?? 'Participant' }}
                </h2>

                {{-- ✅ Nom et dates de l'événement --}}
                @if($evenement)
                <div class="mt-3 bg-white/10 rounded-xl px-4 py-2">
                    <p class="text-white font-bold text-sm">{{ $evenement->nom }}</p>
                    <p class="text-green-200 text-xs mt-0.5">
                        <i class="fa-solid fa-calendar mr-1"></i>
                        @if($evenement->date_debut)
                            {{ \Carbon\Carbon::parse($evenement->date_debut)->format('d/m/Y') }}
                        @endif
                        @if($evenement->date_fin && $evenement->date_fin != $evenement->date_debut)
                            → {{ \Carbon\Carbon::parse($evenement->date_fin)->format('d/m/Y') }}
                        @endif
                    </p>
                    @if($evenement->lieu ?? $evenement->ville)
                    <p class="text-green-200 text-xs mt-0.5">
                        <i class="fa-solid fa-location-dot mr-1"></i>
                        {{ $evenement->lieu ?? $evenement->ville }}
                    </p>
                    @endif
                </div>
                @endif
            </div>

            {{-- Corps badge --}}
            <div class="p-8 text-center">
                <div class="w-20 h-20 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4"
                    style="background-color: {{ $participant->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                    {{ strtoupper(substr($participant->prenom ?? 'X', 0, 1)) }}
                </div>

                {{-- ✅ Nom + Prénom sans civilité devant --}}
                <h3 class="text-2xl font-bold text-gray-800">
                    {{ $participant->nom ?? '-' }} {{ $participant->prenom ?? '' }}
                </h3>

                {{-- ✅ Fonction --}}
                @if($participant->fonction)
                <p class="text-gray-500 mt-1 text-sm">
                    <i class="fa-solid fa-briefcase mr-1"></i>
                    {{ $participant->fonction }}
                </p>
                @endif

                {{-- ✅ Entreprise --}}
                <p class="text-gray-600 font-medium mt-1">
                    <i class="fa-solid fa-building mr-1 text-gray-400"></i>
                    {{ $participant->entreprise->nom ?? 'Indépendant' }}
                </p>

                <p class="text-gray-400 text-sm mt-1">
                    {{ ucfirst($participant->role ?? '') }}
                </p>

                {{-- QR Code --}}
                <div class="mt-6 p-4 bg-gray-50 rounded-xl inline-block">
                    <i class="fa-solid fa-qrcode text-7xl text-gray-800"></i>
                    <p class="font-mono text-sm text-gray-600 mt-2">
                        {{ $badge->qr_code }}
                    </p>
                    <a href="{{ route('badge.public', $badge->qr_code) }}"
                        target="_blank"
                        class="mt-2 text-xs text-blue-500 hover:underline flex items-center justify-center gap-1 no-print">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        Voir ma fiche publique
                    </a>
                </div>

                {{-- Infos badge --}}
                <div class="mt-6 grid grid-cols-2 gap-3 text-left">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-1">Code d'accès</p>
                        <p class="font-mono font-bold text-gray-800 text-sm">
                            {{ $participant->code_acces }}
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-1">Secteur</p>
                        <p class="font-semibold text-gray-800 text-sm">
                            {{ $participant->secteur_activite ?: '-' }}
                        </p>
                    </div>
                    @if($participant->entreprise?->ifu)
                    <div class="bg-gray-50 rounded-xl p-3 col-span-2">
                        <p class="text-xs text-gray-400 mb-1">IFU Entreprise</p>
                        <p class="font-mono font-bold text-gray-800 text-sm">
                            {{ $participant->entreprise->ifu }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ✅ Footer badge avec nom événement --}}
            <div class="px-8 py-4 border-t text-center"
                style="background: linear-gradient(135deg, #1e3a5f, #2d5a8e);">
                @if($evenement)
                <p class="text-xs text-blue-200 font-medium mb-1">
                    <i class="fa-solid fa-calendar-check mr-1"></i>
                    {{ $evenement->nom }}
                    @if($evenement->date_debut)
                        — {{ \Carbon\Carbon::parse($evenement->date_debut)->format('d/m/Y') }}
                    @endif
                    @if($evenement->date_fin && $evenement->date_fin != $evenement->date_debut)
                        → {{ \Carbon\Carbon::parse($evenement->date_fin)->format('d/m/Y') }}
                    @endif
                </p>
                @endif
                <p class="text-xs text-blue-300">
                    <i class="fa-solid fa-shield-halved mr-1"></i>
                    Badge officiel CCI-BF — GesB2B Platform
                </p>
            </div>
        </div>

        {{-- Bouton imprimer --}}
        <div class="text-center mt-6 no-print">
            <button onclick="window.print()"
                class="px-6 py-3 rounded-xl text-white font-medium transition hover:opacity-90"
                style="background-color: #C8102E;">
                <i class="fa-solid fa-print mr-2"></i> Imprimer mon badge
            </button>
        </div>

    </div>
    @else
    <div class="bg-white rounded-xl shadow p-16 text-center text-gray-400">
        <i class="fa-solid fa-id-badge text-5xl mb-3 block text-gray-300"></i>
        <p class="text-lg font-medium">Aucun badge attribué</p>
        <p class="text-sm text-gray-400 mt-1">
            Votre badge sera généré après validation
            et confirmation de votre paiement.
        </p>
    </div>
    @endif
</div>