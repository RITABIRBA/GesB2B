<div>
    <h3 class="text-xl font-bold text-gray-700 mb-6">Mon Badge</h3>

    @if($badge)
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            {{-- Header badge --}}
            <div class="p-6 text-center text-white"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <p class="text-xs text-green-300 mb-1">CCI-BF — GesB2B</p>
                <h2 class="text-2xl font-bold">{{ $badge->typeBadge->libelle ?? 'Participant' }}</h2>
            </div>

            {{-- Corps badge --}}
            <div class="p-8 text-center">
                {{-- Avatar --}}
                <div class="w-20 h-20 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4"
                    style="background-color: #C8102E;">
                    {{ strtoupper(substr($participant->prenom ?? 'X', 0, 1)) }}
                </div>

                <h3 class="text-2xl font-bold text-gray-800">
                    {{ $participant->nom ?? '-' }} {{ $participant->prenom ?? '' }}
                </h3>
                <p class="text-gray-500 mt-1">{{ $participant->entreprise->nom ?? 'Indépendant' }}</p>
                <p class="text-gray-400 text-sm mt-1">{{ ucfirst($participant->role ?? '') }}</p>

                {{-- QR Code simulé --}}
                <div class="mt-6 p-4 bg-gray-50 rounded-xl inline-block">
                    <i class="fa-solid fa-qrcode text-7xl text-gray-800"></i>
                    <p class="font-mono text-sm text-gray-600 mt-2">{{ $badge->qr_code }}</p>
                </div>
            </div>

            {{-- Footer badge --}}
            <div class="px-8 py-4 bg-gray-50 border-t text-center">
                <p class="text-xs text-gray-400">
                    <i class="fa-solid fa-shield-halved mr-1"></i>
                    Badge officiel CCI-BF — GesB2B Platform
                </p>
            </div>
        </div>

        {{-- Bouton imprimer --}}
        <div class="text-center mt-6">
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
            Contactez l'administrateur pour obtenir votre badge.
        </p>
    </div>
    @endif
</div>