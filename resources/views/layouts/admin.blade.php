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
</head>
<body class="font-sans antialiased bg-gray-100">

<div class="flex h-screen overflow-hidden">

    <aside class="w-64 flex flex-col shadow-xl flex-shrink-0"
        style="background: linear-gradient(180deg, #006B34 0%, #007A3D 100%);">

        <div class="p-6 text-center border-b border-green-800">
            <div class="flex items-center justify-center gap-2 mb-1">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm"
                    style="background-color: #C8102E;">B</div>
                <h1 class="text-xl font-bold text-white">GesB2B</h1>
            </div>
            <p class="text-xs text-green-300 mt-1">CCI-BF — Administration</p>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            @php
           $navItems = [
    ['route' => 'admin.dashboard',    'icon' => 'fa-gauge',           'label' => 'Dashboard'],
    ['route' => 'admin.evenements',   'icon' => 'fa-calendar',        'label' => 'Événements'],
    ['route' => 'admin.entreprises',  'icon' => 'fa-building',        'label' => 'Entreprises'],
    ['route' => 'admin.participants', 'icon' => 'fa-users',           'label' => 'Participants'],
    ['route' => 'admin.inscriptions', 'icon' => 'fa-clipboard-list',  'label' => 'Inscriptions'],
    ['route' => 'admin.paiements',    'icon' => 'fa-money-bill',      'label' => 'Paiements'],
    ['route' => 'admin.stands',       'icon' => 'fa-store',           'label' => 'Stands'],
    ['route' => 'admin.souhaits',     'icon' => 'fa-heart',           'label' => 'Souhaits RDV'],
    ['route' => 'admin.rendez-vous',  'icon' => 'fa-handshake',       'label' => 'Rendez-vous'],
    ['route' => 'admin.badges',       'icon' => 'fa-id-badge',        'label' => 'Badges'],
    ['route' => 'admin.traducteurs',  'icon' => 'fa-language',        'label' => 'Traducteurs'],
    ['route' => 'admin.notifications','icon' => 'fa-bell',            'label' => 'Notifications'],
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

            <div class="border-t border-green-800 my-3"></div>

            <a href="{{ route('admin.utilisateurs') }}"
    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group
    {{ request()->routeIs('admin.utilisateurs')
        ? 'text-white font-semibold shadow-lg'
        : 'text-green-100 hover:text-white hover:bg-white/10' }}"
    @if(request()->routeIs('admin.utilisateurs'))
        style="background-color: #C8102E;"
    @endif>
    <i class="fa-solid fa-users-gear w-5 text-center
        {{ request()->routeIs('admin.utilisateurs')
            ? 'text-white'
            : 'text-green-300 group-hover:text-white' }}"></i>
    <span class="text-sm">Utilisateurs</span>
</a>
        </nav>

        <div class="p-4 border-t border-green-800">
            <div class="flex items-center gap-3 px-3 py-2 mb-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                    style="background-color: #C8102E;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-white text-sm font-medium">{{ auth()->user()->name }}</p>
                    <p class="text-green-300 text-xs">Administrateur</p>
                </div>
            </div>
            <form method="POST" action="/logout">
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