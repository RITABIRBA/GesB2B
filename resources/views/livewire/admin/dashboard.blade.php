<div>
    {{-- ══════════════════════════════════════════════
         EN-TÊTE
    ══════════════════════════════════════════════ --}}
    <div class="flex items-center justify-between mb-8 flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tableau de bord</h2>
            <p class="text-sm text-gray-400 mt-1">
                <i class="fa-solid fa-calendar mr-1"></i>
                {{ now()->locale('fr')->translatedFormat('l d F Y') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs px-3 py-1.5 rounded-full font-semibold text-white flex items-center gap-1.5"
                style="background-color: #007A3D;">
                <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse"></span>
                Système opérationnel
            </span>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         STATS PRINCIPALES
    ══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
        $stats = [
            ['label' => 'Événements',     'value' => $totalEvenements,   'icon' => 'fa-calendar-star',  'color' => '#007A3D', 'bg' => '#e6f4ed', 'sub' => $evenementsActifs.' actif(s)'],
            ['label' => 'Participants',    'value' => $totalParticipants,  'icon' => 'fa-users',          'color' => '#C8102E', 'bg' => '#fde8ec', 'sub' => $participantsActifs.' actif(s)'],
            ['label' => 'Entreprises',     'value' => $totalEntreprises,   'icon' => 'fa-building',       'color' => '#2d5a8e', 'bg' => '#e8f0fb', 'sub' => 'enregistrées'],
            ['label' => 'Rendez-vous',     'value' => $totalRendezVous,    'icon' => 'fa-handshake',      'color' => '#8b5cf6', 'bg' => '#f3f0ff', 'sub' => 'planifiés'],
        ];
        @endphp

        @foreach($stats as $stat)
        <div class="bg-white rounded-2xl shadow p-5 flex items-center gap-4 hover:shadow-lg transition border border-gray-50">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0"
                style="background-color: {{ $stat['bg'] }}">
                <i class="fa-solid {{ $stat['icon'] }} text-xl" style="color: {{ $stat['color'] }}"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs mb-0.5">{{ $stat['label'] }}</p>
                <p class="text-3xl font-extrabold text-gray-800">{{ $stat['value'] }}</p>
                <p class="text-xs mt-0.5" style="color: {{ $stat['color'] }}">{{ $stat['sub'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════
         STATS SECONDAIRES
    ══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
        $totalInscriptions   = \App\Models\Inscription::count();
        $inscriptionsPayees  = \App\Models\Inscription::where('statut_paiement', 'paye')->count();
        $totalSouhaits       = \App\Models\Souhait::count();
        $souhaitsMutuels     = \App\Models\Souhait::where('type', 'mutuel')->count();
        $totalPaiements      = \App\Models\Paiement::count();
        $paiementsValides    = \App\Models\Paiement::where('statut', 'valide')->count();
        $montantTotal        = \App\Models\Paiement::where('statut', 'valide')->sum('montant');
        @endphp

        <div class="bg-white rounded-2xl shadow p-5 border-l-4 hover:shadow-lg transition" style="border-color: #007A3D;">
            <p class="text-xs text-gray-500 mb-1">Inscriptions validées</p>
            <p class="text-2xl font-bold text-gray-800">{{ $inscriptionsPayees }}</p>
            <div class="mt-2 bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full transition-all"
                    style="width: {{ $totalInscriptions > 0 ? round($inscriptionsPayees / $totalInscriptions * 100) : 0 }}%; background-color: #007A3D;"></div>
            </div>
            <p class="text-xs text-gray-400 mt-1">
                {{ $totalInscriptions > 0 ? round($inscriptionsPayees / $totalInscriptions * 100) : 0 }}% du total
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow p-5 border-l-4 hover:shadow-lg transition" style="border-color: #C8102E;">
            <p class="text-xs text-gray-500 mb-1">En attente de paiement</p>
            <p class="text-2xl font-bold text-gray-800">{{ $inscriptionsEnAttente }}</p>
            <div class="mt-2 bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full transition-all"
                    style="width: {{ $totalInscriptions > 0 ? round($inscriptionsEnAttente / $totalInscriptions * 100) : 0 }}%; background-color: #C8102E;"></div>
            </div>
            <p class="text-xs text-gray-400 mt-1">
                {{ $totalInscriptions > 0 ? round($inscriptionsEnAttente / $totalInscriptions * 100) : 0 }}% du total
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow p-5 border-l-4 hover:shadow-lg transition" style="border-color: #8b5cf6;">
            <p class="text-xs text-gray-500 mb-1">Souhaits mutuels</p>
            <p class="text-2xl font-bold text-gray-800">{{ $souhaitsMutuels }}</p>
            <div class="mt-2 bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full transition-all"
                    style="width: {{ $totalSouhaits > 0 ? round($souhaitsMutuels / $totalSouhaits * 100) : 0 }}%; background-color: #8b5cf6;"></div>
            </div>
            <p class="text-xs text-gray-400 mt-1">
                {{ $totalSouhaits > 0 ? round($souhaitsMutuels / $totalSouhaits * 100) : 0 }}% des souhaits
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow p-5 border-l-4 hover:shadow-lg transition" style="border-color: #f59e0b;">
            <p class="text-xs text-gray-500 mb-1">Revenus validés</p>
            <p class="text-xl font-bold text-gray-800">{{ number_format($montantTotal, 0, ',', ' ') }}</p>
            <p class="text-xs text-gray-400 mt-1">FCFA</p>
            <p class="text-xs mt-1 font-medium" style="color: #f59e0b;">
                {{ $paiementsValides }} paiement(s) validé(s)
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         GRAPHIQUES
    ══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Graphique 1 — Répartition inscriptions --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie" style="color: #007A3D;"></i>
                Répartition des inscriptions
            </h3>
            @php
                $enAttente   = \App\Models\Inscription::where('statut_paiement', 'en_attente')->count();
                $payees      = \App\Models\Inscription::where('statut_paiement', 'paye')->count();
                $annulees    = \App\Models\Inscription::where('statut_paiement', 'annule')->count();
                $totalIns    = max($enAttente + $payees + $annulees, 1);
                $pctAttente  = round($enAttente / $totalIns * 100);
                $pctPaye     = round($payees / $totalIns * 100);
                $pctAnnule   = round($annulees / $totalIns * 100);
            @endphp

            {{-- Graphique en barres horizontales --}}
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #007A3D;"></span>
                            Payées
                        </span>
                        <span class="font-bold">{{ $payees }} ({{ $pctPaye }}%)</span>
                    </div>
                    <div class="bg-gray-100 rounded-full h-3">
                        <div class="h-3 rounded-full" style="width: {{ $pctPaye }}%; background-color: #007A3D;"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block bg-yellow-400"></span>
                            En attente
                        </span>
                        <span class="font-bold">{{ $enAttente }} ({{ $pctAttente }}%)</span>
                    </div>
                    <div class="bg-gray-100 rounded-full h-3">
                        <div class="h-3 rounded-full bg-yellow-400" style="width: {{ $pctAttente }}%;"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #C8102E;"></span>
                            Annulées
                        </span>
                        <span class="font-bold">{{ $annulees }} ({{ $pctAnnule }}%)</span>
                    </div>
                    <div class="bg-gray-100 rounded-full h-3">
                        <div class="h-3 rounded-full" style="width: {{ $pctAnnule }}%; background-color: #C8102E;"></div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                <p class="text-2xl font-bold text-gray-800">{{ $totalIns }}</p>
                <p class="text-xs text-gray-400">inscriptions au total</p>
            </div>
        </div>

        {{-- Graphique 2 — Répartition souhaits --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-heart" style="color: #C8102E;"></i>
                Répartition des souhaits
            </h3>
            @php
                $sEnvoyes  = \App\Models\Souhait::where('type', 'envoye')->count();
                $sMutuels  = \App\Models\Souhait::where('type', 'mutuel')->count();
                $totalS    = max($sEnvoyes + $sMutuels, 1);
                $pctEnv    = round($sEnvoyes / $totalS * 100);
                $pctMut    = round($sMutuels / $totalS * 100);
            @endphp

            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block bg-purple-500"></span>
                            Mutuels
                        </span>
                        <span class="font-bold">{{ $sMutuels }} ({{ $pctMut }}%)</span>
                    </div>
                    <div class="bg-gray-100 rounded-full h-3">
                        <div class="h-3 rounded-full bg-purple-500" style="width: {{ $pctMut }}%;"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #C8102E;"></span>
                            Envoyés
                        </span>
                        <span class="font-bold">{{ $sEnvoyes }} ({{ $pctEnv }}%)</span>
                    </div>
                    <div class="bg-gray-100 rounded-full h-3">
                        <div class="h-3 rounded-full" style="width: {{ $pctEnv }}%; background-color: #C8102E;"></div>
                    </div>
                </div>
            </div>

            {{-- Taux de matching --}}
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500 mb-2 text-center">Taux de matching</p>
                <div class="flex items-center justify-center">
                    <div class="relative w-24 h-24">
                        <svg viewBox="0 0 36 36" class="w-24 h-24 -rotate-90">
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f3f4f6" stroke-width="3"/>
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#8b5cf6" stroke-width="3"
                                stroke-dasharray="{{ $pctMut }}, 100"
                                stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-lg font-bold text-gray-800">{{ $pctMut }}%</span>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-400 text-center mt-1">des souhaits sont mutuels</p>
            </div>
        </div>

        {{-- Graphique 3 — Répartition RDV --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-handshake" style="color: #2d5a8e;"></i>
                Statut des rendez-vous
            </h3>
            @php
                $rdvPlanifies  = \App\Models\RendezVous::where('statut', 'planifie')->count();
                $rdvConfirmes  = \App\Models\RendezVous::where('statut', 'confirme')->count();
                $rdvAnnules    = \App\Models\RendezVous::where('statut', 'annule')->count();
                $rdvTermines   = \App\Models\RendezVous::where('statut', 'termine')->count();
                $totalRdvG     = max($rdvPlanifies + $rdvConfirmes + $rdvAnnules + $rdvTermines, 1);
            @endphp

            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                            Planifiés
                        </span>
                        <span class="font-bold">{{ $rdvPlanifies }}</span>
                    </div>
                    <div class="bg-gray-100 rounded-full h-3">
                        <div class="h-3 rounded-full bg-blue-500"
                            style="width: {{ round($rdvPlanifies / $totalRdvG * 100) }}%;"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #007A3D;"></span>
                            Confirmés
                        </span>
                        <span class="font-bold">{{ $rdvConfirmes }}</span>
                    </div>
                    <div class="bg-gray-100 rounded-full h-3">
                        <div class="h-3 rounded-full"
                            style="width: {{ round($rdvConfirmes / $totalRdvG * 100) }}%; background-color: #007A3D;"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #C8102E;"></span>
                            Annulés
                        </span>
                        <span class="font-bold">{{ $rdvAnnules }}</span>
                    </div>
                    <div class="bg-gray-100 rounded-full h-3">
                        <div class="h-3 rounded-full"
                            style="width: {{ round($rdvAnnules / $totalRdvG * 100) }}%; background-color: #C8102E;"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-gray-400 inline-block"></span>
                            Terminés
                        </span>
                        <span class="font-bold">{{ $rdvTermines }}</span>
                    </div>
                    <div class="bg-gray-100 rounded-full h-3">
                        <div class="h-3 rounded-full bg-gray-400"
                            style="width: {{ round($rdvTermines / $totalRdvG * 100) }}%;"></div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                <p class="text-2xl font-bold text-gray-800">{{ $totalRendezVous }}</p>
                <p class="text-xs text-gray-400">rendez-vous au total</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         ACTIVITÉ RÉCENTE + PROCHAINS ÉVÉNEMENTS
    ══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Dernières inscriptions --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left" style="color: #C8102E;"></i>
                    Dernières inscriptions
                </h3>
                <a href="{{ route('admin.inscriptions') }}"
                    class="text-xs px-3 py-1.5 rounded-lg text-white transition hover:opacity-90"
                    style="background-color: #007A3D;">
                    Voir tout
                </a>
            </div>

            <div class="space-y-3">
                @forelse($dernieresInscriptions as $inscription)
                <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                        style="background-color: {{ $inscription->statut_paiement === 'paye' ? '#007A3D' : ($inscription->statut_paiement === 'annule' ? '#C8102E' : '#f59e0b') }}">
                        {{ strtoupper(substr($inscription->participant->prenom ?? 'P', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">
                            {{ $inscription->participant->nom ?? '-' }}
                            {{ $inscription->participant->prenom ?? '' }}
                        </p>
                        <p class="text-xs text-gray-400 truncate">{{ $inscription->evenement->nom ?? '-' }}</p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        @if($inscription->statut_paiement === 'paye')
                        <span class="text-xs px-2 py-0.5 rounded-full text-white font-medium" style="background-color: #007A3D;">Payé</span>
                        @elseif($inscription->statut_paiement === 'annule')
                        <span class="text-xs px-2 py-0.5 rounded-full text-white font-medium" style="background-color: #C8102E;">Annulé</span>
                        @else
                        <span class="text-xs px-2 py-0.5 rounded-full text-white font-medium bg-yellow-500">En attente</span>
                        @endif
                        <p class="text-xs text-gray-300 mt-0.5">{{ $inscription->created_at?->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-gray-400">
                    <i class="fa-solid fa-inbox text-2xl mb-2 block text-gray-300"></i>
                    <p class="text-sm">Aucune inscription récente</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Prochains événements --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-star" style="color: #007A3D;"></i>
                    Prochains événements
                </h3>
                <a href="{{ route('admin.evenements') }}"
                    class="text-xs px-3 py-1.5 rounded-lg text-white transition hover:opacity-90"
                    style="background-color: #007A3D;">
                    Voir tout
                </a>
            </div>

            <div class="space-y-3">
                @forelse($prochainsEvenements as $evt)
                @php
                    $estB2B  = ($evt->type_evenement ?? 'avec_b2b') === 'avec_b2b';
                    $nbJours = (int) now()->diffInDays(\Carbon\Carbon::parse($evt->date_debut), false);
                @endphp
                <div class="p-4 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50 transition">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-semibold text-gray-800 text-sm truncate flex-1">{{ $evt->nom }}</h4>
                        @if($estB2B)
                        <span class="text-xs px-2 py-0.5 rounded-full text-white font-bold ml-2 flex-shrink-0" style="background-color: #007A3D;">B2B</span>
                        @else
                        <span class="text-xs px-2 py-0.5 rounded-full text-white font-bold ml-2 flex-shrink-0" style="background-color: #2d5a8e;">Événement</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <span><i class="fa-solid fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($evt->date_debut)->format('d/m/Y') }}</span>
                        @if($evt->ville)
                        <span><i class="fa-solid fa-location-dot mr-1"></i>{{ $evt->ville }}</span>
                        @endif
                        @if($nbJours >= 0)
                        <span class="ml-auto font-semibold {{ $nbJours <= 7 ? 'text-red-500' : 'text-green-600' }}">
                            @if($nbJours == 0) Aujourd'hui
                            @elseif($nbJours == 1) Demain
                            @else dans {{ $nbJours }}j
                            @endif
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-gray-400">
                    <i class="fa-solid fa-calendar-xmark text-2xl mb-2 block text-gray-300"></i>
                    <p class="text-sm">Aucun événement à venir</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>


</div>