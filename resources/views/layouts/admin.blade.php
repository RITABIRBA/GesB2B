<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'GesB2B' }} — CCI-BF</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @livewireStyles
    <style>
        @media print {
            aside, header, .no-print { display: none !important; }
            body { background: white !important; }
            main { padding: 0 !important; overflow: visible !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            tr { page-break-inside: avoid; }
            .page-break { page-break-before: always; }
        }
        /* ✅ Scrollbar fine et discrète pour le menu */
        nav::-webkit-scrollbar { width: 3px; }
        nav::-webkit-scrollbar-track { background: transparent; }
        nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">

<div class="flex h-screen overflow-hidden">

    <aside class="w-56 flex flex-col shadow-xl flex-shrink-0"
        style="background: linear-gradient(180deg, #006B34 0%, #007A3D 100%);">

        {{-- Logo --}}
        <div class="px-4 py-3 text-center border-b border-green-800">
            <div class="flex items-center justify-center gap-2 mb-0.5">
                <img src="{{ asset('images/logo-ccibf.png') }}"
                    alt="CCI-BF" class="w-7 h-7 object-contain rounded-lg">
                <h1 class="text-base font-bold text-white">Business Platform</h1>
            </div>
            <p class="text-xs text-green-300">CCI-BF — Administration</p>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-2 py-2 space-y-0.5 overflow-y-auto">
            @php
            $paiementsEnAttente    = \App\Models\Paiement::where('statut', 'en_attente')->count();
            $inscriptionsEnAttente = \App\Models\Participant::where('statut_preinscription', 'en_attente')->count();
            $standsEnAttente       = \App\Models\Stand::where('statut_reservation', 'en_attente')->count();
            $demandesAideEnAttente = \App\Models\DemandeAide::where('statut', 'en_attente')->count();

            $navItems = [
                ['route' => 'admin.dashboard',        'icon' => 'fa-gauge',           'label' => 'Dashboard',           'badge' => 0],
                ['route' => 'admin.evenements',        'icon' => 'fa-calendar',        'label' => 'Événements',           'badge' => 0],
                ['route' => 'admin.entreprises',       'icon' => 'fa-building',        'label' => 'Entreprises',          'badge' => 0],
                ['route' => 'admin.participants',      'icon' => 'fa-users',           'label' => 'Participants',         'badge' => 0],
                ['route' => 'admin.inscriptions',      'icon' => 'fa-clipboard-list',  'label' => 'Inscriptions',         'badge' => $inscriptionsEnAttente],
                ['route' => 'admin.paiements',         'icon' => 'fa-money-bill',      'label' => 'Paiements',            'badge' => $paiementsEnAttente],
                ['route' => 'admin.stands',            'icon' => 'fa-store',           'label' => 'Stands',               'badge' => $standsEnAttente],
                ['route' => 'admin.souhaits',          'icon' => 'fa-heart',           'label' => 'Souhaits RDV',         'badge' => 0],
                ['route' => 'admin.rendez-vous',       'icon' => 'fa-handshake',       'label' => 'Rendez-vous',          'badge' => 0],
                ['route' => 'admin.badges',            'icon' => 'fa-id-badge',        'label' => 'Badges',               'badge' => 0],
                ['route' => 'admin.traducteurs',       'icon' => 'fa-language',        'label' => 'Traducteurs',          'badge' => 0],
                ['route' => 'admin.notifications',     'icon' => 'fa-bell',            'label' => 'Notifications',        'badge' => 0],
                ['route' => 'admin.chefs-delegation',  'icon' => 'fa-user-tie',        'label' => 'Chefs délégation',     'badge' => 0],
                ['route' => 'admin.remises',           'icon' => 'fa-percent',         'label' => 'Remises',              'badge' => 0],
                ['route' => 'admin.sponsors',          'icon' => 'fa-star',            'label' => 'Sponsors',             'badge' => 0],
                ['route' => 'admin.demandes-aide',     'icon' => 'fa-circle-question', 'label' => "Demandes d'aide",      'badge' => $demandesAideEnAttente],
            ];
            @endphp

            @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
                class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-200 group
                {{ request()->routeIs($item['route'])
                    ? 'text-white font-semibold shadow-lg'
                    : 'text-green-100 hover:text-white hover:bg-white/10' }}"
                @if(request()->routeIs($item['route']))
                    style="background-color: #C8102E;"
                @endif>
                <i class="fa-solid {{ $item['icon'] }} w-4 text-center text-xs
                    {{ request()->routeIs($item['route'])
                        ? 'text-white'
                        : 'text-green-300 group-hover:text-white' }}"></i>
                <span class="text-xs flex-1">{{ $item['label'] }}</span>
                @if($item['badge'] > 0)
                <span class="ml-auto min-w-[18px] h-4 px-1 rounded-full text-xs font-bold flex items-center justify-center
                    {{ request()->routeIs($item['route']) ? 'bg-white text-red-600' : 'bg-red-500 text-white' }}">
                    {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                </span>
                @endif
            </a>
            @endforeach

            <div class="border-t border-green-800 my-2"></div>

            <a href="{{ route('admin.utilisateurs') }}"
                class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-200 group
                {{ request()->routeIs('admin.utilisateurs')
                    ? 'text-white font-semibold shadow-lg'
                    : 'text-green-100 hover:text-white hover:bg-white/10' }}"
                @if(request()->routeIs('admin.utilisateurs'))
                    style="background-color: #C8102E;"
                @endif>
                <i class="fa-solid fa-users-gear w-4 text-center text-xs
                    {{ request()->routeIs('admin.utilisateurs')
                        ? 'text-white'
                        : 'text-green-300 group-hover:text-white' }}"></i>
                <span class="text-xs">Utilisateurs</span>
            </a>
        </nav>

        {{-- Profil + Déconnexion --}}
        <div class="p-3 border-t border-green-800">
            <div class="flex items-center gap-2 px-2 py-1.5 mb-1.5">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                    style="background-color: #C8102E;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-white text-xs font-medium">{{ auth()->user()->name }}</p>
                    <p class="text-green-300 text-xs">Administrateur</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-green-100 hover:bg-red-600 hover:text-white transition-all duration-200 text-xs">
                    <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    Déconnexion
                </button>
            </form>
        </div>

    </aside>

    {{-- CONTENU PRINCIPAL --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Header --}}
        <header class="bg-white shadow-sm px-8 py-4 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-ccibf.png') }}"
                    alt="CCI-BF" class="w-8 h-8 object-contain">
                <span class="text-2xl font-bold" style="color: #C8102E;">CCI-BF</span>
                <span class="text-gray-300">|</span>
                <h2 class="text-lg font-semibold text-gray-700">{{ $title ?? 'Dashboard' }}</h2>
            </div>
            <div class="flex items-center gap-4">
                @if($paiementsEnAttente > 0 || $inscriptionsEnAttente > 0 || $standsEnAttente > 0)
                <div class="flex items-center gap-2">
                    @if($paiementsEnAttente > 0)
                    <a href="{{ route('admin.paiements') }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold text-white transition hover:opacity-90"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-money-bill"></i>
                        {{ $paiementsEnAttente }} paiement(s)
                    </a>
                    @endif
                    @if($inscriptionsEnAttente > 0)
                    <a href="{{ route('admin.inscriptions') }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold text-white transition hover:opacity-90 bg-orange-500">
                        <i class="fa-solid fa-clipboard-list"></i>
                        {{ $inscriptionsEnAttente }} inscription(s)
                    </a>
                    @endif
                    @if($standsEnAttente > 0)
                    <a href="{{ route('admin.stands') }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold text-white transition hover:opacity-90 bg-yellow-500">
                        <i class="fa-solid fa-store"></i>
                        {{ $standsEnAttente }} stand(s)
                    </a>
                    @endif
                </div>
                @endif

                <span class="text-sm text-gray-500">
                    <i class="fa-regular fa-clock mr-1"></i>
                    {{ now()->format('d/m/Y') }}
                </span>
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold"
                    style="background-color: #C8102E;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- Contenu --}}
        <main class="flex-1 overflow-y-auto p-8">
            {{ $slot }}
        </main>

    </div>

</div>

@livewireScripts
<div wire:loading.flex
    class="fixed inset-0 z-[9999] items-center justify-center"
    style="background: rgba(0,0,0,0.4);">
    <div class="bg-white rounded-2xl shadow-2xl px-10 py-8 flex flex-col items-center gap-4">
        <div class="w-14 h-14 rounded-full border-4 border-gray-200 border-t-red-600 animate-spin"></div>
        <p class="text-gray-700 font-semibold text-sm">Chargement en cours...</p>
    </div>
</div>
</body>
</html>