<div>
    {{-- En-tête --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.participants') }}"
            class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
            <i class="fa-solid fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    {{-- CARTE PROFIL --}}
    <div class="bg-white rounded-2xl shadow p-8 mb-6">
        <div class="flex items-start gap-6 flex-wrap">
            <div class="w-20 h-20 rounded-full flex items-center justify-center text-white text-3xl font-bold flex-shrink-0"
                style="background-color: {{ $participant->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                {{ strtoupper(substr($participant->prenom ?? 'X', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-[280px]">
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ $participant->nom }} {{ $participant->prenom }}
                    @if($participant->genre == 'femme')
                    <span class="text-base text-gray-400">(Mme)</span>
                    @elseif($participant->genre == 'homme')
                    <span class="text-base text-gray-400">(M.)</span>
                    @endif
                </h2>
                <div class="flex items-center gap-3 mt-2 flex-wrap">
                    <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                        style="background-color: {{ $participant->role == 'representant' ? '#C8102E' : '#007A3D' }}">
                        {{ ucfirst(str_replace('_', ' ', $participant->role)) }}
                    </span>
                    @if($participant->statut_preinscription)
                    @php
                        $statutColors = ['en_attente' => 'bg-yellow-500', 'valide' => 'bg-green-600', 'rejete' => 'bg-red-600'];
                    @endphp
                    <span class="text-sm px-3 py-1 rounded-full text-white font-medium {{ $statutColors[$participant->statut_preinscription] ?? 'bg-gray-400' }}">
                        Préinscription : {{ ucfirst($participant->statut_preinscription) }}
                    </span>
                    @endif
                    @if($participant->statut_participant && $participant->statut_participant !== 'classique')
                    <span class="text-sm px-3 py-1 rounded-full font-medium
                        {{ $participant->statut_participant === 'sponsor' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ ucfirst($participant->statut_participant) }}
                    </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-3">
                    <i class="fa-solid fa-key text-gray-400 mr-1"></i>
                    Code d'accès : <span class="font-mono font-bold">{{ $participant->code_acces }}</span>
                </p>
            </div>
            @if($compteUser)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center flex-shrink-0">
                <i class="fa-solid fa-circle-check text-green-500 text-2xl mb-1 block"></i>
                <p class="text-xs text-green-700 font-medium">Compte actif</p>
            </div>
            @else
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-center flex-shrink-0">
                <i class="fa-solid fa-circle-xmark text-gray-400 text-2xl mb-1 block"></i>
                <p class="text-xs text-gray-500 font-medium">Pas de compte</p>
            </div>
            @endif
        </div>

        {{-- Infos détaillées --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-100">
            <div>
                <p class="text-xs text-gray-400 mb-1">Contact</p>
                <p class="text-sm text-gray-700"><i class="fa-solid fa-phone text-gray-400 mr-1"></i>{{ $participant->telephone ?? '-' }}</p>
                @if($participant->email)
                <p class="text-sm text-gray-700 mt-1"><i class="fa-solid fa-envelope text-gray-400 mr-1"></i>{{ $participant->email }}</p>
                @endif
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Localisation</p>
                <p class="text-sm text-gray-700"><i class="fa-solid fa-location-dot text-gray-400 mr-1"></i>{{ $participant->ville ?? '-' }}, {{ $participant->pays ?? '-' }}</p>
                @if($participant->date_naissance)
                <p class="text-sm text-gray-700 mt-1"><i class="fa-solid fa-cake-candles text-gray-400 mr-1"></i>{{ \Carbon\Carbon::parse($participant->date_naissance)->format('d/m/Y') }}</p>
                @endif
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Profession</p>
                <p class="text-sm text-gray-700"><i class="fa-solid fa-briefcase text-gray-400 mr-1"></i>{{ $participant->fonction ?? '-' }}</p>
                @if($participant->filiere)
                <p class="text-sm text-blue-600 mt-1"><i class="fa-solid fa-graduation-cap mr-1"></i>{{ $participant->filiere }} — {{ $participant->universite }}</p>
                @endif
            </div>
            @if($participant->entreprise)
            <div class="md:col-span-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-400 mb-1">Entreprise</p>
                <a href="{{ route('admin.fiche-entreprise', $participant->entreprise->id) }}"
                    class="text-sm font-semibold hover:underline" style="color: #007A3D;">
                    <i class="fa-solid fa-building mr-1"></i>{{ $participant->entreprise->nom }}
                </a>
            </div>
            @endif
            @if($participant->evenement)
            <div class="md:col-span-3">
                <p class="text-xs text-gray-400 mb-1">Événement d'inscription initial</p>
                <p class="text-sm text-gray-700"><i class="fa-solid fa-calendar text-gray-400 mr-1"></i>{{ $participant->evenement->nom }}</p>
            </div>
            @endif
        </div>

        {{-- Profil B2B --}}
        @php
            $secteursRecherche = is_array($participant->secteurs_recherche) ? $participant->secteurs_recherche : (json_decode($participant->secteurs_recherche ?? '[]', true) ?: []);
            $typesPartenariat  = is_array($participant->types_partenariat) ? $participant->types_partenariat : (json_decode($participant->types_partenariat ?? '[]', true) ?: []);
        @endphp
        @if($participant->zone_geographique || !empty($secteursRecherche) || !empty($typesPartenariat))
        <div class="mt-6 pt-6 border-t border-gray-100">
            <p class="text-xs font-bold text-gray-500 mb-3">PROFIL B2B</p>
            <div class="flex flex-wrap gap-2">
                @if($participant->zone_geographique)
                <span class="text-xs px-3 py-1.5 rounded-full bg-blue-50 text-blue-700">
                    <i class="fa-solid fa-globe mr-1"></i>{{ $participant->zone_geographique }}
                </span>
                @endif
                @foreach($secteursRecherche as $s)
                <span class="text-xs px-3 py-1.5 rounded-full bg-purple-50 text-purple-700">{{ $s }}</span>
                @endforeach
                @foreach($typesPartenariat as $t)
                <span class="text-xs px-3 py-1.5 rounded-full bg-green-50 text-green-700">{{ $t }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- STATISTIQUES RAPIDES --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $inscriptions->count() }}</p>
            <p class="text-xs text-gray-400">Inscriptions</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $souhaitsEmis->count() }}</p>
            <p class="text-xs text-gray-400">Souhaits émis</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $rendezVous->count() }}</p>
            <p class="text-xs text-gray-400">Rendez-vous</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $paiements->where('statut', 'valide')->count() }}</p>
            <p class="text-xs text-gray-400">Paiements validés</p>
        </div>
    </div>

    {{-- INSCRIPTIONS --}}
    <div class="bg-white rounded-xl shadow mb-6">
        <div class="px-6 py-4 border-b" style="background-color: #f8f9fa;">
            <h4 class="font-bold text-gray-700"><i class="fa-solid fa-clipboard-list mr-2" style="color: #C8102E;"></i>Inscriptions</h4>
        </div>
        <div class="p-6">
            @forelse($inscriptions as $i)
            <div class="flex items-center justify-between py-3 border-b last:border-0">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $i->evenement->nom ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $i->date_inscription }} · {{ number_format($i->montant_paye, 0, ',', ' ') }} FCFA</p>
                </div>
                @php
                    $colors = ['paye' => 'bg-green-100 text-green-700', 'en_attente' => 'bg-yellow-100 text-yellow-700', 'annule' => 'bg-red-100 text-red-700'];
                @endphp
                <span class="text-xs px-3 py-1 rounded-full font-medium {{ $colors[$i->statut_paiement] ?? 'bg-gray-100 text-gray-500' }}">
                    {{ ucfirst(str_replace('_', ' ', $i->statut_paiement)) }}
                </span>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Aucune inscription</p>
            @endforelse
        </div>
    </div>

    {{-- PAIEMENTS --}}
    <div class="bg-white rounded-xl shadow mb-6">
        <div class="px-6 py-4 border-b" style="background-color: #f8f9fa;">
            <h4 class="font-bold text-gray-700"><i class="fa-solid fa-money-bill mr-2" style="color: #007A3D;"></i>Paiements</h4>
        </div>
        <div class="p-6">
            @forelse($paiements as $p)
            <div class="flex items-center justify-between py-3 border-b last:border-0">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ number_format($p->montant, 0, ',', ' ') }} FCFA</p>
                    <p class="text-xs text-gray-400">
                        {{ ucfirst(str_replace('_', ' ', $p->mode_paiement)) }}
                        @if($p->numero_cheque) · N° {{ $p->numero_cheque }} @endif
                        · {{ $p->date_paiement }}
                    </p>
                </div>
                @php
                    $colors = ['valide' => 'bg-green-100 text-green-700', 'en_attente' => 'bg-yellow-100 text-yellow-700', 'rejete' => 'bg-red-100 text-red-700'];
                @endphp
                <span class="text-xs px-3 py-1 rounded-full font-medium {{ $colors[$p->statut] ?? 'bg-gray-100 text-gray-500' }}">
                    {{ ucfirst($p->statut) }}
                </span>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Aucun paiement</p>
            @endforelse
        </div>
    </div>

    {{-- STAND --}}
    @if($stand)
    <div class="bg-white rounded-xl shadow mb-6">
        <div class="px-6 py-4 border-b" style="background-color: #f8f9fa;">
            <h4 class="font-bold text-gray-700"><i class="fa-solid fa-store mr-2" style="color: #C8102E;"></i>Stand assigné</h4>
        </div>
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="font-semibold text-gray-800">Stand N°{{ $stand->numero_stand }} — {{ $stand->standing }}</p>
                <p class="text-xs text-gray-400">{{ $stand->superficie }}</p>
            </div>
            @if($stand->est_gratuit)
            <span class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">Gratuit</span>
            @endif
        </div>
    </div>
    @endif

    {{-- SOUHAITS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow">
            <div class="px-6 py-4 border-b" style="background-color: #f8f9fa;">
                <h4 class="font-bold text-gray-700 text-sm">Souhaits émis ({{ $souhaitsEmis->count() }})</h4>
            </div>
            <div class="p-4 max-h-72 overflow-y-auto">
                @forelse($souhaitsEmis as $s)
                <div class="flex items-center justify-between py-2 border-b last:border-0 text-sm">
                    <span>{{ $s->participantCible->nom ?? '-' }} {{ $s->participantCible->prenom ?? '' }}</span>
                    @if($s->type == 'mutuel')
                    <span class="text-xs px-2 py-0.5 rounded-full text-white" style="background-color: #C8102E;">Mutuel</span>
                    @endif
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-3">Aucun souhait</p>
                @endforelse
            </div>
        </div>
        <div class="bg-white rounded-xl shadow">
            <div class="px-6 py-4 border-b" style="background-color: #f8f9fa;">
                <h4 class="font-bold text-gray-700 text-sm">Souhaits reçus ({{ $souhaitsRecus->count() }})</h4>
            </div>
            <div class="p-4 max-h-72 overflow-y-auto">
                @forelse($souhaitsRecus as $s)
                <div class="flex items-center justify-between py-2 border-b last:border-0 text-sm">
                    <span>{{ $s->participant->nom ?? '-' }} {{ $s->participant->prenom ?? '' }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-3">Aucun souhait reçu</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- RENDEZ-VOUS --}}
    <div class="bg-white rounded-xl shadow mb-6">
        <div class="px-6 py-4 border-b" style="background-color: #f8f9fa;">
            <h4 class="font-bold text-gray-700"><i class="fa-solid fa-handshake mr-2" style="color: #2d5a8e;"></i>Rendez-vous</h4>
        </div>
        <div class="p-6">
            @forelse($rendezVous as $rdv)
            <div class="flex items-center justify-between py-3 border-b last:border-0">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">
                        {{ $rdv->participant1->nom ?? '-' }} ↔ {{ $rdv->participant2->nom ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-400">{{ $rdv->date }} · {{ $rdv->heure_debut }} - {{ $rdv->heure_fin }}</p>
                </div>
                <span class="text-xs px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                    {{ ucfirst($rdv->statut) }}
                </span>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Aucun rendez-vous</p>
            @endforelse
        </div>
    </div>

    {{-- NOTIFICATIONS --}}
    <div class="bg-white rounded-xl shadow">
        <div class="px-6 py-4 border-b" style="background-color: #f8f9fa;">
            <h4 class="font-bold text-gray-700"><i class="fa-solid fa-bell mr-2" style="color: #f59e0b;"></i>Notifications envoyées</h4>
        </div>
        <div class="p-6 max-h-80 overflow-y-auto">
            @forelse($notifications as $n)
            <div class="py-2.5 border-b last:border-0 text-sm">
                <p class="text-gray-700">{{ $n->contenu }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $n->date_envoie }}</p>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Aucune notification</p>
            @endforelse
        </div>
    </div>

</div>