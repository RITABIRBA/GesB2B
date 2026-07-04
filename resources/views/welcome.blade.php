<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Business Forum — CCI-BF</title>
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
        .value-card { transition: all 0.3s ease; }
        .value-card:hover { transform: translateY(-4px); }
        .event-card {
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e5e7eb;
        }
        .event-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0,122,61,0.12);
            border-color: #007A3D;
        }

        /* Modal S'inscrire */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-box {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body class="antialiased bg-white">

    {{-- ══════════════════════════════════════════════
         MODAL S'INSCRIRE
    ══════════════════════════════════════════════ --}}
    <div id="modalInscription" class="modal-overlay" style="display:none;">
        <div class="modal-box">
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo-ccibf.png') }}"
                    alt="CCI-BF" class="w-14 h-14 object-contain mx-auto mb-4">
                <h2 class="text-2xl font-bold text-gray-900">Vous souhaitez vous inscrire ?</h2>
                <p class="text-gray-400 text-sm mt-2">Choisissez votre type de compte</p>
            </div>

            <div class="space-y-4 mb-6">
                {{-- Participant --}}
                <a href="{{ route('inscription.participant') }}"
                    class="flex items-center gap-5 p-5 rounded-2xl border-2 border-gray-100 hover:border-green-400 hover:bg-green-50 transition group">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0"
                        style="background-color: #e6f4ed;">
                        <i class="fa-solid fa-user text-xl" style="color: #007A3D;"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-800 group-hover:text-green-700">Participant individuel</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                           Vous participez à titre personnel en tant que professionnel ou représentant
                        </p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-300 group-hover:text-green-500"></i>
                </a>

                {{-- Entreprise --}}
                <a href="{{ route('inscription.entreprise') }}"
                    class="flex items-center gap-5 p-5 rounded-2xl border-2 border-gray-100 hover:border-red-400 hover:bg-red-50 transition group">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0"
                        style="background-color: #fde8ec;">
                        <i class="fa-solid fa-building text-xl" style="color: #C8102E;"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-800 group-hover:text-red-700">Entreprise</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Vous représentez une entreprise et souhaitez inscrire votre délégation
                        </p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-300 group-hover:text-red-500"></i>
                </a>
            </div>

            <button onclick="document.getElementById('modalInscription').style.display='none'"
                class="w-full py-3 rounded-xl border border-gray-200 text-gray-500 text-sm font-medium hover:bg-gray-50 transition">
                <i class="fa-solid fa-xmark mr-2"></i> Annuler
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         NAVBAR
    ══════════════════════════════════════════════ --}}
    <nav class="fixed top-0 left-0 right-0 z-50 border-b border-gray-100 shadow-sm"
        style="background: rgba(255,255,255,0.98); backdrop-filter: blur(20px);">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-ccibf.png') }}"
                    alt="CCI-BF" class="w-11 h-11 object-contain">
                <div>
                    <h1 class="text-lg font-bold text-gray-900 leading-none">Business Forum</h1>
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
                {{-- ✅ Un seul bouton S'inscrire qui ouvre le modal --}}
                <button onclick="document.getElementById('modalInscription').style.display='flex'"
                    class="hidden md:flex px-5 py-2.5 rounded-xl font-semibold text-sm transition items-center gap-2 border-2"
                    style="color: #007A3D; border-color: #007A3D;">
                    <i class="fa-solid fa-user-plus text-xs"></i> S'inscrire
                </button>
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

    {{-- ══════════════════════════════════════════════
         HERO
    ══════════════════════════════════════════════ --}}
    <section class="hero flex items-center pt-20">
        <div class="max-w-7xl mx-auto px-6 w-full py-24 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div class="text-white fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold mb-8 uppercase tracking-wider"
                        style="background: rgba(200,16,46,0.25); border: 1px solid rgba(200,16,46,0.4);">
                        <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                        Plateforme officielle CCI-BF
                    </div>
                    <h1 class="text-6xl font-extrabold leading-none mb-6 tracking-tight">
                        Business<br>
                        <span style="color: rgba(255,255,255,0.6);">Forum</span>
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
                        <a href="#evenements" class="btn-outline">
                            Nos événements <i class="fa-solid fa-arrow-down"></i>
                        </a>
                        @endauth
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5 fade-up delay-1">
                    <div class="glass rounded-2xl p-7 value-card float">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4"
                            style="background: rgba(200,16,46,0.2);">
                            <i class="fa-solid fa-shield-halved text-white text-xl"></i>
                        </div>
                        <p class="text-white font-bold text-lg mb-1">Sécurisé</p>
                        <p class="text-white/50 text-sm leading-relaxed">Accès sécurisé avec gestion des rôles et permissions</p>
                    </div>
                    <div class="glass rounded-2xl p-7 value-card"
                        style="animation: float 4s ease-in-out 1s infinite;">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4"
                            style="background: rgba(200,16,46,0.2);">
                            <i class="fa-solid fa-wand-magic-sparkles text-white text-xl"></i>
                        </div>
                        <p class="text-white font-bold text-lg mb-1">Intelligent</p>
                        <p class="text-white/50 text-sm leading-relaxed">Match-making automatique et optimisé des rendez-vous</p>
                    </div>
                    <div class="glass rounded-2xl p-7 value-card"
                        style="animation: float 4s ease-in-out 0.5s infinite;">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4"
                            style="background: rgba(200,16,46,0.2);">
                            <i class="fa-solid fa-mobile-screen text-white text-xl"></i>
                        </div>
                        <p class="text-white font-bold text-lg mb-1">Accessible</p>
                        <p class="text-white/50 text-sm leading-relaxed">Disponible sur tous les appareils, partout et à tout moment</p>
                    </div>
                    <div class="glass rounded-2xl p-7 value-card"
                        style="animation: float 4s ease-in-out 1.5s infinite;">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4"
                            style="background: rgba(200,16,46,0.2);">
                            <i class="fa-solid fa-bolt text-white text-xl"></i>
                        </div>
                        <p class="text-white font-bold text-lg mb-1">Efficace</p>
                        <p class="text-white/50 text-sm leading-relaxed">Gestion complète des forums de A à Z en temps réel</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white/40 text-xs flex flex-col items-center gap-2">
            <span>Défiler</span>
            <i class="fa-solid fa-chevron-down animate-bounce"></i>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════
         ÉVÉNEMENTS À VENIR
    ══════════════════════════════════════════════ --}}
    @php
        $today = now()->toDateString();
        $evenementsAVenir = \App\Models\Evenement::where('date_fin', '>=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('date_ouverture_inscriptions')
                  ->orWhere('date_ouverture_inscriptions', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('date_cloture_inscriptions')
                  ->orWhere('date_cloture_inscriptions', '>=', $today);
            })
            ->orderBy('date_debut', 'asc')
            ->get();
    @endphp

    @if($evenementsAVenir->isNotEmpty())
    <section id="evenements" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <div class="section-tag text-white" style="background-color: #007A3D;">Événements</div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Vos prochaines opportunités</h2>
                <div class="divider mx-auto mb-4"></div>
                <p class="text-gray-500 text-lg max-w-xl mx-auto">
                    Découvrez les événements à venir et inscrivez-vous dès aujourd'hui
                    pour développer votre réseau professionnel.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
                @foreach($evenementsAVenir as $evt)
                @php
                    $gratuit   = ($evt->type_paiement ?? 'payant') === 'gratuit';
                    $estB2B    = ($evt->type_evenement ?? 'avec_b2b') === 'avec_b2b';
                    $nbJours   = (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($evt->date_debut), false);
                    $dateDebut = \Carbon\Carbon::parse($evt->date_debut)->locale('fr')->translatedFormat('d M Y');
                    $dateFin   = $evt->date_fin && $evt->date_fin !== $evt->date_debut
                        ? \Carbon\Carbon::parse($evt->date_fin)->locale('fr')->translatedFormat('d M Y')
                        : null;
                @endphp
                <div class="event-card bg-white rounded-2xl overflow-hidden flex flex-col h-full">
                    <div class="h-2 w-full"
                        style="background: linear-gradient(90deg, {{ $estB2B ? '#007A3D' : '#2d5a8e' }}, #C8102E);"></div>
                    <div class="p-6 flex flex-col flex-1">
                        <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                            <div class="flex items-center gap-2">
                                @if($estB2B)
                                <span class="text-xs px-2.5 py-1 rounded-full font-bold text-white" style="background-color: #007A3D;">
                                    <i class="fa-solid fa-handshake mr-1"></i> B2B
                                </span>
                                @else
                                <span class="text-xs px-2.5 py-1 rounded-full font-bold text-white" style="background-color: #2d5a8e;">
                                    <i class="fa-solid fa-calendar-star mr-1"></i> Événement
                                </span>
                                @endif
                                <span class="text-xs px-2.5 py-1 rounded-full font-semibold text-white"
                                    style="background-color: {{ $gratuit ? '#059669' : '#C8102E' }}">
                                    <i class="fa-solid {{ $gratuit ? 'fa-gift' : 'fa-ticket' }} mr-1"></i>
                                    {{ $gratuit ? 'Gratuit' : 'Payant' }}
                                </span>
                            </div>
                            @if($nbJours >= 0)
                            <span class="text-xs font-semibold text-gray-400">
                                <i class="fa-solid fa-clock mr-1"></i> dans {{ $nbJours }} jour(s)
                            </span>
                            @endif
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-3 leading-snug">{{ $evt->nom }}</h3>
                        <div class="space-y-2 text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-calendar w-4" style="color: #007A3D;"></i>
                                <span>{{ $dateDebut }} @if($dateFin) → {{ $dateFin }} @endif</span>
                            </div>
                            @if($evt->heure_debut)
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-clock w-4" style="color: #007A3D;"></i>
                                <span>{{ \Carbon\Carbon::parse($evt->heure_debut)->format('H\hi') }}
                                    @if($evt->heure_fin) → {{ \Carbon\Carbon::parse($evt->heure_fin)->format('H\hi') }} @endif
                                </span>
                            </div>
                            @endif
                            @if($evt->ville)
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-location-dot w-4" style="color: #C8102E;"></i>
                                <span>{{ $evt->ville }}</span>
                            </div>
                            @endif
                        </div>
                        @if($evt->date_ouverture_inscriptions || $evt->date_cloture_inscriptions)
                        <div class="mt-3 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 space-y-1">
                            @if($evt->date_ouverture_inscriptions)
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <i class="fa-solid fa-door-open w-3.5 text-green-500"></i>
                                Ouverture : <strong class="text-gray-700">{{ \Carbon\Carbon::parse($evt->date_ouverture_inscriptions)->locale('fr')->translatedFormat('d M Y') }}</strong>
                            </div>
                            @endif
                            @if($evt->date_cloture_inscriptions)
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <i class="fa-solid fa-door-closed w-3.5 text-red-500"></i>
                                Clôture : <strong class="text-gray-700">{{ \Carbon\Carbon::parse($evt->date_cloture_inscriptions)->locale('fr')->translatedFormat('d M Y') }}</strong>
                            </div>
                            @endif
                        </div>
                        @endif
                        @if($estB2B)
                        <div class="mt-3 bg-green-50 border border-green-200 rounded-xl px-3 py-2 text-xs text-green-700 flex items-center gap-2">
                            <i class="fa-solid fa-circle-check"></i> Inclut des rendez-vous d'affaires B2B
                        </div>
                        @else
                        <div class="mt-3 bg-blue-50 border border-blue-200 rounded-xl px-3 py-2 text-xs text-blue-700 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info"></i> Événement sans rendez-vous B2B
                        </div>
                        @endif
                        <div class="border-t border-gray-100 mt-auto pt-4">
                            @if($evt->type_paiement === 'par_entreprise')
                            <p class="text-xs text-gray-400 flex items-center gap-1">
                                <i class="fa-solid fa-building" style="color: #007A3D;"></i>
                                Paiement pris en charge par l'entreprise
                            </p>
                            @elseif(!$gratuit && $evt->montant_inscription)
                            <p class="text-xs font-bold" style="color: #C8102E;">
                                <i class="fa-solid fa-money-bill mr-1"></i>
                                {{ number_format($evt->montant_inscription, 0, ',', ' ') }} FCFA
                            </p>
                            @else
                            <p class="text-xs text-green-600 font-semibold">
                                <i class="fa-solid fa-circle-check mr-1"></i> Inscription gratuite
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ══════════════════════════════════════════════
         PROCESSUS
    ══════════════════════════════════════════════ --}}
    <section class="py-24" style="background-color: #f8faf9;">
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
                <div class="hidden lg:block absolute top-10 left-0 right-0 h-0.5
                    bg-gradient-to-r from-transparent via-gray-200 to-transparent mx-32"></div>
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                    @php
                    $etapes = [
                        ['num' => '01', 'icon' => 'fa-building',       'color' => '#007A3D', 'label' => 'Inscription',     'desc' => "Participants et entreprises s'inscrivent et renseignent leur profil complet"],
                        ['num' => '02', 'icon' => 'fa-user-check',     'color' => '#2d5a8e', 'label' => 'Validation',      'desc' => "L'administration valide les dossiers d'inscription"],
                        ['num' => '03', 'icon' => 'fa-credit-card',    'color' => '#C8102E', 'label' => 'Paiement',        'desc' => 'Paiement sécurisé via Mobile Money, carte bancaire ou chèque'],
                        ['num' => '04', 'icon' => 'fa-heart',          'color' => '#8b5cf6', 'label' => 'Souhaits',        'desc' => 'Les participants émettent leurs souhaits de rencontre par priorité (événements B2B)'],
                        ['num' => '05', 'icon' => 'fa-calendar-check', 'color' => '#007A3D', 'label' => 'Planning généré', 'desc' => 'Le système génère automatiquement le planning optimisé des rendez-vous'],
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

    {{-- ══════════════════════════════════════════════
         FONCTIONNALITÉS
    ══════════════════════════════════════════════ --}}
    <section id="fonctionnalites" class="py-24 bg-white">
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
                    ['icon' => 'fa-mobile-screen',       'color' => '#C8102E', 'title' => 'Paiement flexible',         'desc' => 'Paiement Orange Money, Moov Money, carte bancaire ou chèque. Individuel ou par entreprise.'],
                    ['icon' => 'fa-id-badge',            'color' => '#2d5a8e', 'title' => 'Badge électronique',        'desc' => 'Génération automatique de badges électroniques avec QR code scannable après validation.'],
                    ['icon' => 'fa-rotate',              'color' => '#8b5cf6', 'title' => 'Gestion des absences',      'desc' => 'Signalement des absences et re-match automatique pour remplacer les participants absents.'],
                    ['icon' => 'fa-calendar-star',       'color' => '#007A3D', 'title' => 'Multi-types d\'événements', 'desc' => "Événements avec ou sans B2B. Ouvert à tous : entreprises, particuliers, étudiants."],
                    ['icon' => 'fa-shield-halved',       'color' => '#C8102E', 'title' => 'Multi-rôles sécurisé',      'desc' => "Plusieurs rôles distincts avec des droits d'accès adaptés à chaque acteur du forum."],
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

    {{-- ══════════════════════════════════════════════
         CTA FINAL — un seul bouton S'inscrire
    ══════════════════════════════════════════════ --}}
    <section class="py-24 relative overflow-hidden"
        style="background: linear-gradient(135deg, #007A3D 0%, #004d27 50%, #007A3D 100%);">
        <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-10"
            style="background: #C8102E; transform: translate(30%, -30%);"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full opacity-10"
            style="background: white; transform: translate(-30%, 30%);"></div>
        <div class="max-w-3xl mx-auto px-6 text-center relative z-10">
            <img src="{{ asset('images/logo-ccibf.png') }}"
                alt="CCI-BF" class="w-16 h-16 object-contain mx-auto mb-8 opacity-90">
            <h2 class="text-5xl font-extrabold text-white mb-6 leading-tight">Business Forum</h2>
            <p class="text-white/60 text-xl mb-10 leading-relaxed">
                Gérez vos événements et rencontres de manière professionnelle,
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
                {{-- ✅ Un seul bouton S'inscrire --}}
                <button onclick="document.getElementById('modalInscription').style.display='flex'"
                    class="px-10 py-4 rounded-xl bg-white font-bold text-base transition hover:bg-gray-50 flex items-center gap-3 shadow-xl"
                    style="color: #C8102E;">
                    <i class="fa-solid fa-user-plus"></i> S'inscrire
                </button>
                <a href="{{ route('login') }}" class="btn-primary shadow-2xl">
                    <i class="fa-solid fa-right-to-bracket"></i> Se connecter
                </a>
                @endauth
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════
         FOOTER — propre sans boutons
    ══════════════════════════════════════════════ --}}
    <footer style="background-color: #0d1f16;" class="py-10 text-white/40">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/logo-ccibf.png') }}"
                        alt="CCI-BF" class="w-10 h-10 object-contain opacity-80">
                    <div>
                        <p class="font-bold text-white text-sm">Business Forum</p>
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

    {{-- Fermer modal en cliquant en dehors --}}
    <script>
        document.getElementById('modalInscription').addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });
    </script>

</body>
</html>