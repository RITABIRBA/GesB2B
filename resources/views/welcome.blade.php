<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GesB2B — CCI-BF</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

    {{-- 
        NAVBAR
     --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

            {{-- Logo --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-lg"
                    style="background-color: #C8102E;">B</div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">GesB2B</h1>
                    <p class="text-xs text-gray-400 -mt-1">CCI-BF Platform</p>
                </div>
            </div>

            {{-- Bouton connexion --}}
            @if(Route::has('login'))
            <div class="flex items-center gap-3">
                @auth
                <a href="{{ url('/dashboard') }}"
                    class="px-5 py-2.5 rounded-xl text-white font-medium text-sm transition hover:opacity-90"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-gauge mr-1"></i> Mon espace
                </a>
                @else
                <a href="{{ route('login') }}"
                    class="px-5 py-2.5 rounded-xl text-white font-medium text-sm transition hover:opacity-90"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-right-to-bracket mr-1"></i> Se connecter
                </a>
                @endauth
            </div>
            @endif

        </div>
    </nav>

    {{-- 
        HERO SECTION
     --}}
    <section class="min-h-screen flex items-center pt-20"
    style="background-image: linear-gradient(135deg, rgba(0, 107, 52, 0.82) 0%, rgba(0, 122, 61, 0.72) 60%, rgba(0, 90, 45, 0.82) 100%), url('/images/hero-bg.jpg');
           background-size: cover;
           background-position: center top;
           background-repeat: no-repeat;
           background-attachment: fixed;">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                {{-- Texte --}}
                <div class="text-white">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium mb-6"
                        style="background-color: rgba(200, 16, 46, 0.3);">
                        <i class="fa-solid fa-star text-yellow-400"></i>
                        Plateforme officielle CCI-BF
                    </div>
                    <h1 class="text-5xl font-bold leading-tight mb-6">
                        Gestion des<br>
                        <span style="color: #fbbf24;">Rencontres B2B</span>
                    </h1>
                    <p class="text-green-100 text-lg mb-8 leading-relaxed">
                        La plateforme digitale de la Chambre de Commerce et d'Industrie
                        du Burkina Faso pour organiser et gérer les forums économiques
                        et les rendez-vous d'affaires.
                    </p>
                    <div class="flex items-center gap-4">
                        @auth
                        <a href="{{ url('/dashboard') }}"
                            class="px-8 py-4 rounded-xl text-white font-semibold text-lg transition hover:opacity-90 shadow-xl flex items-center gap-2"
                            style="background-color: #C8102E;">
                            <i class="fa-solid fa-gauge"></i>
                            Mon espace
                        </a>
                        @else
                        <a href="{{ route('login') }}"
                            class="px-8 py-4 rounded-xl text-white font-semibold text-lg transition hover:opacity-90 shadow-xl flex items-center gap-2"
                            style="background-color: #C8102E;">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            Se connecter
                        </a>
                        @endauth
                        <a href="#features"
                            class="px-8 py-4 rounded-xl font-semibold text-lg transition border-2 border-white/30 text-white hover:bg-white/10 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info"></i>
                            En savoir plus
                        </a>
                    </div>
                </div>

                {{-- Cartes stats --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-6 text-white border border-white/20">
                        <i class="fa-solid fa-building text-3xl mb-3" style="color: #fbbf24;"></i>
                        <p class="text-3xl font-bold">{{ \App\Models\Entreprise::count() }}+</p>
                        <p class="text-green-200 text-sm mt-1">Entreprises inscrites</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-6 text-white border border-white/20">
                        <i class="fa-solid fa-users text-3xl mb-3" style="color: #fbbf24;"></i>
                        <p class="text-3xl font-bold">{{ \App\Models\Participant::count() }}+</p>
                        <p class="text-green-200 text-sm mt-1">Participants enregistrés</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-6 text-white border border-white/20">
                        <i class="fa-solid fa-handshake text-3xl mb-3" style="color: #fbbf24;"></i>
                        <p class="text-3xl font-bold">{{ \App\Models\RendezVous::count() }}+</p>
                        <p class="text-green-200 text-sm mt-1">Rendez-vous planifiés</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-6 text-white border border-white/20">
                        <i class="fa-solid fa-calendar text-3xl mb-3" style="color: #fbbf24;"></i>
                        <p class="text-3xl font-bold">{{ \App\Models\Evenement::count() }}+</p>
                        <p class="text-green-200 text-sm mt-1">Événements organisés</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- 
        FEATURES SECTION
     --}}
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">

            {{-- Titre --}}
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">
                    Une plateforme complète
                </h2>
                <p class="text-gray-500 text-lg max-w-2xl mx-auto">
                    Tous les outils nécessaires pour organiser et gérer
                    vos forums économiques B2B
                </p>
            </div>

            {{-- Cards features --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                @php
                $features = [
                    [
                        'icon'  => 'fa-handshake',
                        'color' => '#007A3D',
                        'bg'    => '#e6f4ed',
                        'title' => 'Match-making B2B',
                        'desc'  => 'Génération automatique du planning des rendez-vous basée sur les souhaits des participants.',
                    ],
                    [
                        'icon'  => 'fa-building',
                        'color' => '#C8102E',
                        'bg'    => '#fde8ec',
                        'title' => 'Gestion des Entreprises',
                        'desc'  => 'Inscrivez et gérez les entreprises participantes avec validation et suivi en temps réel.',
                    ],
                    [
                        'icon'  => 'fa-id-badge',
                        'color' => '#2d5a8e',
                        'bg'    => '#e8f0fb',
                        'title' => 'Badges & QR Codes',
                        'desc'  => 'Génération automatique des badges avec QR codes pour chaque participant.',
                    ],
                    [
                        'icon'  => 'fa-money-bill',
                        'color' => '#007A3D',
                        'bg'    => '#e6f4ed',
                        'title' => 'Paiements & Reçus',
                        'desc'  => 'Gestion des inscriptions et paiements avec génération automatique des reçus.',
                    ],
                    [
                        'icon'  => 'fa-language',
                        'color' => '#8b5cf6',
                        'bg'    => '#f3e8ff',
                        'title' => 'Traducteurs',
                        'desc'  => 'Assignation des traducteurs aux rendez-vous selon leurs disponibilités.',
                    ],
                    [
                        'icon'  => 'fa-users-gear',
                        'color' => '#f59e0b',
                        'bg'    => '#fef3c7',
                        'title' => 'Multi-rôles',
                        'desc'  => 'Admin, Superviseur, CDD, Entreprise, Participant et Traducteur avec des droits adaptés.',
                    ],
                ];
                @endphp

                @foreach($features as $feature)
                <div class="bg-white rounded-2xl shadow p-8 hover:shadow-lg transition">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mb-4"
                        style="background-color: {{ $feature['bg'] }}">
                        <i class="fa-solid {{ $feature['icon'] }}"
                            style="color: {{ $feature['color'] }}"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $feature['title'] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
                </div>
                @endforeach

            </div>
        </div>
    </section>

    {{-- 
        ESPACES SECTION
     --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">

            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">
                    Des espaces dédiés à chaque acteur
                </h2>
                <p class="text-gray-500 text-lg">
                    Chaque utilisateur dispose d'un espace personnalisé selon son rôle
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @php
                $espaces = [
                    ['label' => 'Admin',       'icon' => 'fa-shield-halved', 'color' => '#C8102E', 'desc' => 'Gestion complète'],
                    ['label' => 'Superviseur', 'icon' => 'fa-eye',           'color' => '#f59e0b', 'desc' => 'Vue globale'],
                    ['label' => 'CDD',         'icon' => 'fa-user-tie',      'color' => '#2d5a8e', 'desc' => 'Sa délégation'],
                    ['label' => 'Entreprise',  'icon' => 'fa-building',      'color' => '#007A3D', 'desc' => 'Ses participants'],
                    ['label' => 'Participant', 'icon' => 'fa-user',          'color' => '#8b5cf6', 'desc' => 'Ses RDV'],
                    ['label' => 'Traducteur',  'icon' => 'fa-language',      'color' => '#06b6d4', 'desc' => 'Ses missions'],
                ];
                @endphp

                @foreach($espaces as $espace)
                <div class="text-center p-6 rounded-2xl border border-gray-100 hover:shadow-lg transition hover:border-gray-200">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl mx-auto mb-3"
                        style="background-color: {{ $espace['color'] }}">
                        <i class="fa-solid {{ $espace['icon'] }}"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 text-sm">{{ $espace['label'] }}</h4>
                    <p class="text-gray-400 text-xs mt-1">{{ $espace['desc'] }}</p>
                </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- 
        CTA SECTION
     --}}
    <section class="py-20" style="background: linear-gradient(135deg, #C8102E 0%, #a00d25 100%);">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold text-white mb-4">
                Prêt à commencer ?
            </h2>
            <p class="text-red-200 text-lg mb-8">
                Connectez-vous à votre espace et gérez vos rencontres B2B
            </p>
            @auth
            <a href="{{ url('/dashboard') }}"
                class="px-10 py-4 rounded-xl bg-white font-bold text-lg transition hover:bg-gray-100 inline-flex items-center gap-2"
                style="color: #C8102E;">
                <i class="fa-solid fa-gauge"></i>
                Accéder à mon espace
            </a>
            @else
            <a href="{{ route('login') }}"
                class="px-10 py-4 rounded-xl bg-white font-bold text-lg transition hover:bg-gray-100 inline-flex items-center gap-2"
                style="color: #C8102E;">
                <i class="fa-solid fa-right-to-bracket"></i>
                Se connecter maintenant
            </a>
            @endauth
        </div>
    </section>

    {{-- 
        FOOTER
     --}}
    <footer class="py-8 text-center text-sm text-gray-500 bg-gray-50 border-t">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold text-sm"
                    style="background-color: #C8102E;">B</div>
                <span class="font-semibold text-gray-700">GesB2B — CCI-BF</span>
            </div>
            <p>© {{ date('Y') }} Chambre de Commerce et d'Industrie du Burkina Faso. Tous droits réservés.</p>
            <div class="flex items-center gap-4 text-gray-400">
                <span>Laravel {{ app()->version() }}</span>
                <span>•</span>
                <span>PHP {{ PHP_VERSION }}</span>
            </div>
        </div>
    </footer>

</body>
</html>