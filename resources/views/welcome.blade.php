<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GesB2B — CCI-BF</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Inter', sans-serif; }
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #004d27 0%, #007A3D 40%, #005a2d 100%);
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: rgba(200, 16, 46, 0.08);
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
        }
        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .feature-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #f0f0f0;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            border-color: #007A3D;
            box-shadow: 0 25px 50px rgba(0, 122, 61, 0.12);
        }
        .feature-card:hover .feature-icon {
            background-color: #007A3D !important;
        }
        .feature-card:hover .feature-icon i {
            color: white !important;
        }
        .btn-primary {
            background: #C8102E;
            color: white;
            padding: 16px 36px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .btn-primary:hover {
            background: #a00d25;
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(200, 16, 46, 0.3);
        }
        .btn-outline {
            background: transparent;
            color: white;
            padding: 16px 36px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            border: 2px solid rgba(255,255,255,0.4);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .btn-outline:hover {
            background: rgba(255,255,255,0.1);
            border-color: white;
        }
        .divider {
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #C8102E, #007A3D);
            border-radius: 2px;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .float { animation: float 4s ease-in-out infinite; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp 0.9s ease forwards; }
        .delay-1 { animation-delay: 0.2s; opacity: 0; }
        .delay-2 { animation-delay: 0.4s; opacity: 0; }
        .delay-3 { animation-delay: 0.6s; opacity: 0; }
        .role-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .role-card:hover {
            border-color: #007A3D;
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(0,122,61,0.1);
        }
        .section-tag {
            display: inline-block;
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }
        .value-card {
            transition: all 0.3s ease;
        }
        .value-card:hover {
            transform: translateY(-4px);
        }
    </style>
</head>
<body class="antialiased bg-white">

    {{-- NAVBAR --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-ccibf.png') }}" alt="CCI-BF" class="w-11 h-11 object-contain">
                <div>
                    <h1 class="text-lg font-bold text-gray-900 leading-none">GesB2B</h1>
                    <p class="text-xs mt-0.5" style="color: #007A3D;">CCI-BF Platform</p>
                </div>
            </div>
            @if(Route::has('login'))
            <div class="flex items-center gap-3">
                @auth
                <a href="{{ url('/dashboard') }}"
                    class="px-5 py-2.5 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 flex items-center gap-2"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-gauge text-xs"></i> Mon espace
                </a>
                @else
                <a href="{{ route('inscription.participant') }}"
                    class="px-5 py-2.5 rounded-xl font-semibold text-sm transition flex items-center gap-2 border-2"
                    style="color: #007A3D; border-color: #007A3D;">
                    <i class="fa-solid fa-user-plus text-xs"></i> S'inscrire
                </a>
                <a href="{{ route('login') }}"
                    class="px-5 py-2.5 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 flex items-center gap-2"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-right-to-bracket text-xs"></i> Se connecter
                </a>
                @endauth
            </div>
            @endif
        </div>
    </nav>

    {{-- HERO --}}
    <section class="hero flex items-center pt-20">
        <div class="max-w-7xl mx-auto px-6 w-full py-24 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">

                {{-- Contenu gauche --}}
                <div class="text-white fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold mb-8 uppercase tracking-wider"
                        style="background: rgba(200,16,46,0.25); border: 1px solid rgba(200,16,46,0.4);">
                        <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                        Plateforme officielle CCI-BF
                    </div>
                    <h1 class="text-6xl font-extrabold leading-none mb-6 tracking-tight">
                        Rencontres<br>
                        <span style="color: rgba(255,255,255,0.6);">B2B</span>
                        <span style="color: #C8102E;"> .</span>
                    </h1>
                    <p class="text-white/70 text-xl mb-10 leading-relaxed max-w-lg">
                        La Chambre de Commerce et d'Industrie du Burkina Faso
                        digitalise et automatise l'organisation de ses forums
                        économiques internationaux.
                    </p>
                    <div class="flex items-center gap-4 flex-wrap">
                        @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary">
                            <i class="fa-solid fa-gauge"></i> Mon espace
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="btn-primary">
                            <i class="fa-solid fa-right-to-bracket"></i> Accéder à la plateforme
                        </a>
                        <a href="#fonctionnalites" class="btn-outline">
                            Découvrir <i class="fa-solid fa-arrow-down"></i>
                        </a>
                        @endauth
                    </div>
                </div>

                {{-- ← 4 cartes valeurs au lieu des stats --}}
                <div class="grid grid-cols-2 gap-5 fade-up delay-1">

                    <div class="glass rounded-2xl p-7 value-card float">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4"
                            style="background: rgba(200,16,46,0.2);">
                            <i class="fa-solid fa-shield-halved text-white text-xl"></i>
                        </div>
                        <p class="text-white font-bold text-lg mb-1">Sécurisé</p>
                        <p class="text-white/50 text-sm leading-relaxed">
                            Accès sécurisé avec gestion des rôles et permissions
                        </p>
                    </div>

                    <div class="glass rounded-2xl p-7 value-card" style="animation: float 4s ease-in-out 1s infinite;">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4"
                            style="background: rgba(200,16,46,0.2);">
                            <i class="fa-solid fa-wand-magic-sparkles text-white text-xl"></i>
                        </div>
                        <p class="text-white font-bold text-lg mb-1">Intelligent</p>
                        <p class="text-white/50 text-sm leading-relaxed">
                            Match-making automatique et optimisé des rendez-vous
                        </p>
                    </div>

                    <div class="glass rounded-2xl p-7 value-card" style="animation: float 4s ease-in-out 0.5s infinite;">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4"
                            style="background: rgba(200,16,46,0.2);">
                            <i class="fa-solid fa-mobile-screen text-white text-xl"></i>
                        </div>
                        <p class="text-white font-bold text-lg mb-1">Accessible</p>
                        <p class="text-white/50 text-sm leading-relaxed">
                            Disponible sur tous les appareils, partout et à tout moment
                        </p>
                    </div>

                    <div class="glass rounded-2xl p-7 value-card" style="animation: float 4s ease-in-out 1.5s infinite;">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4"
                            style="background: rgba(200,16,46,0.2);">
                            <i class="fa-solid fa-bolt text-white text-xl"></i>
                        </div>
                        <p class="text-white font-bold text-lg mb-1">Efficace</p>
                        <p class="text-white/50 text-sm leading-relaxed">
                            Gestion complète des forums de A à Z en temps réel
                        </p>
                    </div>

                </div>
            </div>
        </div>

        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white/40 text-xs flex flex-col items-center gap-2">
            <span>Défiler</span>
            <i class="fa-solid fa-chevron-down animate-bounce"></i>
        </div>
    </section>

    {{-- FLUX --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <div class="section-tag text-white" style="background-color: #C8102E;">Processus</div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Comment ça fonctionne</h2>
                <div class="divider mx-auto mb-4"></div>
                <p class="text-gray-500 text-lg max-w-xl mx-auto">
                    Un processus fluide et entièrement automatisé de l'inscription au rendez-vous
                </p>
            </div>
            <div class="relative">
                <div class="hidden lg:block absolute top-10 left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-gray-200 to-transparent mx-32"></div>
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                    @php
                    $etapes = [
                        ['num' => '01', 'icon' => 'fa-user-plus',      'color' => '#007A3D', 'label' => 'Préinscription',  'desc' => "Le participant s'inscrit en ligne et choisit son chef de délégation"],
                        ['num' => '02', 'icon' => 'fa-user-check',     'color' => '#2d5a8e', 'label' => 'Validation CDD',  'desc' => 'Le chef de délégation valide les informations du participant'],
                        ['num' => '03', 'icon' => 'fa-credit-card',    'color' => '#C8102E', 'label' => 'Paiement',        'desc' => 'Paiement sécurisé via Orange Money, Moov Money ou carte bancaire'],
                        ['num' => '04', 'icon' => 'fa-heart',          'color' => '#8b5cf6', 'label' => 'Souhaits RDV',    'desc' => 'Le participant émet ses souhaits de rencontre par ordre de priorité'],
                        ['num' => '05', 'icon' => 'fa-calendar-check', 'color' => '#007A3D', 'label' => 'Planning généré', 'desc' => 'Le système génère automatiquement le planning optimisé des RDV'],
                    ];
                    @endphp
                    @foreach($etapes as $etape)
                    <div class="text-center relative">
                        <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-white text-2xl mx-auto mb-5 shadow-lg relative z-10"
                            style="background-color: {{ $etape['color'] }}">
                            <i class="fa-solid {{ $etape['icon'] }}"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-300 tracking-widest">{{ $etape['num'] }}</span>
                        <h4 class="font-bold text-gray-900 mt-1 mb-2">{{ $etape['label'] }}</h4>
                        <p class="text-gray-400 text-xs leading-relaxed">{{ $etape['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- FONCTIONNALITÉS --}}
    <section id="fonctionnalites" class="py-24" style="background-color: #f8faf9;">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <div class="section-tag text-white" style="background-color: #007A3D;">Fonctionnalités</div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Une solution complète et digitalisée</h2>
                <div class="divider mx-auto mb-4"></div>
                <p class="text-gray-500 text-lg max-w-xl mx-auto">
                    Une solution complète pour gérer vos forums économiques de bout en bout
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                $features = [
                    ['icon' => 'fa-wand-magic-sparkles', 'color' => '#007A3D', 'title' => 'Match-making intelligent',  'desc' => 'Le système génère automatiquement le planning optimal en priorisant les rendez-vous mutuels.'],
                    ['icon' => 'fa-mobile-screen',       'color' => '#C8102E', 'title' => 'Paiement Mobile Money',     'desc' => 'Paiement Orange Money et Moov Money avec confirmation OTP. Carte bancaire acceptée.'],
                    ['icon' => 'fa-id-badge',            'color' => '#2d5a8e', 'title' => 'Badge électronique',        'desc' => 'Génération automatique de badges électroniques avec QR code scannable après validation du paiement.'],
                    ['icon' => 'fa-rotate',              'color' => '#8b5cf6', 'title' => 'Gestion des absences',      'desc' => 'Signalement des absences et re-match automatique pour remplacer les participants absents.'],
                    ['icon' => 'fa-book-open',           'color' => '#007A3D', 'title' => 'Catalogue en ligne',        'desc' => 'Moteur de recherche des entreprises et participants visible après clôture des inscriptions.'],
                    ['icon' => 'fa-shield-halved',       'color' => '#C8102E', 'title' => 'Multi-rôles sécurisé',      'desc' => "Six rôles distincts avec des droits d'accès adaptés à chaque acteur du forum."],
                ];
                @endphp
                @foreach($features as $feature)
                <div class="feature-card bg-white rounded-2xl p-8">
                    <div class="feature-icon w-14 h-14 rounded-xl flex items-center justify-center mb-6 transition-all duration-300"
                        style="background-color: {{ $feature['color'] }}15;">
                        <i class="fa-solid {{ $feature['icon'] }} text-xl transition-all duration-300"
                            style="color: {{ $feature['color'] }}"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">{{ $feature['title'] }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- RÔLES --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <div class="section-tag text-white" style="background-color: #C8102E;">Les acteurs</div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Un espace pour chaque acteur</h2>
                <div class="divider mx-auto mb-4"></div>
                <p class="text-gray-500 text-lg max-w-xl mx-auto">
                    Chaque utilisateur accède à un espace personnalisé adapté à son rôle dans le forum
                </p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @php
                $roles = [
                    ['label' => 'Administrateur', 'icon' => 'fa-shield-halved', 'color' => '#C8102E', 'desc' => 'Gestion complète'],
                    ['label' => 'Superviseur',    'icon' => 'fa-eye',           'color' => '#2d5a8e', 'desc' => 'Vue globale'],
                    ['label' => 'CDD',            'icon' => 'fa-user-tie',      'color' => '#007A3D', 'desc' => 'Sa délégation'],
                    ['label' => 'Entreprise',     'icon' => 'fa-building',      'color' => '#8b5cf6', 'desc' => 'Ses participants'],
                    ['label' => 'Participant',    'icon' => 'fa-user',          'color' => '#007A3D', 'desc' => 'Ses rendez-vous'],
                    ['label' => 'Traducteur',     'icon' => 'fa-language',      'color' => '#C8102E', 'desc' => 'Ses missions'],
                ];
                @endphp
                @foreach($roles as $role)
                <div class="role-card bg-gray-50 rounded-2xl p-6 text-center cursor-default">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-xl mx-auto mb-4"
                        style="background-color: {{ $role['color'] }}">
                        <i class="fa-solid {{ $role['icon'] }}"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm mb-1">{{ $role['label'] }}</h4>
                    <p class="text-gray-400 text-xs">{{ $role['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA FINAL --}}
    <section class="py-24 relative overflow-hidden"
        style="background: linear-gradient(135deg, #007A3D 0%, #004d27 50%, #007A3D 100%);">
        <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-10"
            style="background: #C8102E; transform: translate(30%, -30%);"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full opacity-10"
            style="background: white; transform: translate(-30%, 30%);"></div>
        <div class="max-w-3xl mx-auto px-6 text-center relative z-10">
            <img src="{{ asset('images/logo-ccibf.png') }}"
                alt="CCI-BF" class="w-16 h-16 object-contain mx-auto mb-8 opacity-90">
            <h2 class="text-5xl font-extrabold text-white mb-6 leading-tight">
                Rejoignez la plateforme
            </h2>
            <p class="text-white/60 text-xl mb-10 leading-relaxed">
                Gérez vos rencontres B2B de manière professionnelle,
                efficace et entièrement digitalisée.
            </p>
            <div class="flex items-center justify-center gap-5 flex-wrap">
                @auth
                <a href="{{ url('/dashboard') }}"
                    class="px-10 py-4 rounded-xl bg-white font-bold text-base transition hover:bg-gray-50 flex items-center gap-3 shadow-xl"
                    style="color: #007A3D;">
                    <i class="fa-solid fa-gauge"></i> Mon espace
                </a>
                @else
                <a href="{{ route('login') }}" class="btn-primary shadow-2xl">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Se connecter
                </a>
                <a href="{{ route('inscription.participant') }}"
                    class="px-10 py-4 rounded-xl bg-white font-bold text-base transition hover:bg-gray-50 flex items-center gap-3 shadow-xl"
                    style="color: #007A3D;">
                    <i class="fa-solid fa-user-plus"></i>
                    S'inscrire
                </a>
                @endauth
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer style="background-color: #0d1f16;" class="py-10 text-white/40">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/logo-ccibf.png') }}"
                        alt="CCI-BF" class="w-10 h-10 object-contain opacity-80">
                    <div>
                        <p class="font-bold text-white text-sm">GesB2B</p>
                        <p class="text-xs">Chambre de Commerce et d'Industrie du Burkina Faso</p>
                    </div>
                </div>
                <p class="text-sm text-center">© {{ date('Y') }} CCI-BF — Tous droits réservés</p>
                <div class="flex items-center gap-2 text-xs">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                    <span>Plateforme opérationnelle</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>