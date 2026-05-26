<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'GesB2B' }} — Participant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @livewireStyles
    <style>
        @media print {
            body * { visibility: hidden; }

            #recu-print {
                visibility: visible;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                padding: 20px;
            }
            #recu-print * { visibility: visible; }

            #planning-print, #planning-print * { visibility: visible; }
            #planning-print {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
            }

            .no-print { display: none !important; }
            .print-header { display: block !important; }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">

<div class="flex h-screen overflow-hidden">

    <aside class="w-64 flex flex-col shadow-xl flex-shrink-0"
        style="background: linear-gradient(180deg, #006B34 0%, #007A3D 100%);">

        <div class="p-6 text-center border-b border-green-800">
            <div class="flex items-center justify-center gap-2 mb-1">
                <img src="{{ asset('images/logo-ccibf.png') }}"
                    alt="CCI-BF" class="w-8 h-8 object-contain rounded-lg">
                <h1 class="text-xl font-bold text-white">GesB2B</h1>
            </div>
            <p class="text-xs text-green-300 mt-1">Espace Participant</p>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            @php
            $navItems = [
                ['route' => 'participant.dashboard',   'icon' => 'fa-gauge',          'label' => 'Dashboard'],
                ['route' => 'participant.profil',      'icon' => 'fa-user',           'label' => 'Mon Profil'],
                ['route' => 'participant.inscription', 'icon' => 'fa-clipboard-list', 'label' => 'Mon Inscription'],
                ['route' => 'participant.souhaits',    'icon' => 'fa-heart',          'label' => 'Mes Souhaits'],
                ['route' => 'participant.rendez-vous', 'icon' => 'fa-handshake',      'label' => 'Mes RDV'],
                ['route' => 'participant.planning',    'icon' => 'fa-calendar-check', 'label' => 'Mon Planning'],
                ['route' => 'participant.badge',       'icon' => 'fa-id-badge',       'label' => 'Mon Badge'],
                ['route' => 'participant.catalogue',   'icon' => 'fa-book-open',      'label' => 'Catalogue'],
            ];
            @endphp

            @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
                {{ request()->routeIs($item['route'])
                    ? 'text-white font-semibold shadow-lg'
                    : 'text-green-100 hover:text-white hover:bg-white/10' }}"
                @if(request()->routeIs($item['route']))
                    style="background-color: #C8102E;"
                @endif>
                <i class="fa-solid {{ $item['icon'] }} w-5 text-center
                    {{ request()->routeIs($item['route'])
                        ? 'text-white'
                        : 'text-green-300 group-hover:text-white' }}"></i>
                <span class="text-sm">{{ $item['label'] }}</span>
            </a>
            @endforeach
        </nav>

        <div class="p-4 border-t border-green-800">
            <div class="flex items-center gap-3 px-3 py-2 mb-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                    style="background-color: #C8102E;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-white text-sm font-medium">{{ auth()->user()->name }}</p>
                    <p class="text-green-300 text-xs">Participant</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-2 rounded-xl text-green-100 hover:bg-red-600 hover:text-white transition-all duration-200 text-sm">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Déconnexion
                </button>
            </form>
        </div>

    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">

        <header class="bg-white shadow-sm px-8 py-4 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-ccibf.png') }}"
                    alt="CCI-BF" class="w-8 h-8 object-contain">
                <span class="text-2xl font-bold" style="color: #C8102E;">CCI-BF</span>
                <span class="text-gray-300">|</span>
                <h2 class="text-lg font-semibold text-gray-700">{{ $title ?? 'Dashboard' }}</h2>
            </div>
            <div class="flex items-center gap-4">
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

        <main class="flex-1 overflow-y-auto p-8">
            {{ $slot }}
        </main>

    </div>

</div>

@livewireScripts
</body>
</html>