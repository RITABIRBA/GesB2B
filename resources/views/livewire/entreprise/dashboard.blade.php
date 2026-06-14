<div>

    {{-- ============================================================
         MESSAGES INLINE
    ============================================================ --}}
    @if($alertSuccess)
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ $alertSuccess }}
    </div>
    @endif

    @if($alertError)
    <div class="bg-red-100 border border-red-300 text-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
        {{ $alertError }}
    </div>
    @endif

    {{-- ============================================================
         INFO ENTREPRISE
    ============================================================ --}}
    @if($entreprise)
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl font-bold flex-shrink-0"
                style="background-color: #007A3D;">
                {{ strtoupper(substr($entreprise->nom, 0, 1)) }}
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-800">{{ $entreprise->nom }}</h2>
                <div class="flex items-center gap-4 mt-1 flex-wrap">
                    <span class="text-sm text-gray-500">
                        <i class="fa-solid fa-industry text-gray-400 mr-1"></i>
                        {{ $entreprise->secteur_activite }}
                        @if($entreprise->sous_secteur)
                        / {{ $entreprise->sous_secteur }}
                        @endif
                    </span>
                    <span class="text-sm text-gray-500">
                        <i class="fa-solid fa-location-dot text-gray-400 mr-1"></i>
                        {{ $entreprise->ville }}, {{ $entreprise->pays }}
                    </span>
                    @if($entreprise->ifu)
                    <span class="text-sm font-mono text-gray-400">
                        IFU: {{ $entreprise->ifu }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="flex-shrink-0">
                @if($entreprise->statut_validation == 'valide')
                <span class="px-4 py-2 rounded-full text-sm text-white font-medium"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-circle-check mr-1"></i> Validée
                </span>
                @elseif($entreprise->statut_validation == 'en_attente')
                <span class="px-4 py-2 rounded-full text-sm text-white font-medium bg-yellow-500">
                    <i class="fa-solid fa-clock mr-1"></i> En attente
                </span>
                @else
                <span class="px-4 py-2 rounded-full text-sm text-white font-medium bg-red-600">
                    <i class="fa-solid fa-circle-xmark mr-1"></i> Rejetée
                </span>
                @endif
            </div>
        </div>

        @if($representant)
        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                style="background-color: #C8102E;">
                {{ strtoupper(substr($representant->prenom ?? 'R', 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-700">
                    {{ $representant->nom }} {{ $representant->prenom }}
                    <span class="text-xs text-gray-400 ml-1">— {{ $representant->fonction }}</span>
                </p>
                <p class="text-xs text-gray-400">
                    Représentant principal
                    · Code : <span class="font-mono font-bold">{{ $representant->code_acces }}</span>
                </p>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ============================================================
         NOTIFICATION PAIEMENT LIGDICASH
         → Affiché quand l'entreprise est validée
           et qu'aucun paiement n'a encore été soumis
    ============================================================ --}}
    @if($paiementEnAttente)
    <div class="rounded-xl shadow p-6 mb-6 border-l-4"
        style="background: linear-gradient(135deg, #007A3D, #005a2d); border-color: #C8102E;">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center bg-white/20 flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-white text-2xl"></i>
                </div>
                <div>
                    <p class="font-bold text-white text-lg">
                        Votre entreprise est validée !
                    </p>
                    <p class="text-green-200 text-sm mt-0.5">
                        Procédez au paiement de votre inscription via LigdiCash
                        pour finaliser votre participation.
                    </p>
                    @if($montantPaiement > 0)
                    <p class="text-white font-bold text-xl mt-1">
                        {{ number_format($montantPaiement, 0, ',', ' ') }} FCFA
                    </p>
                    @endif
                </div>
            </div>
            <button wire:click="openModalPaiement"
                class="px-6 py-3 rounded-xl font-bold text-sm transition hover:opacity-90 shadow-xl flex items-center gap-2 flex-shrink-0"
                style="background-color: #C8102E; color: white;">
                <i class="fa-solid fa-credit-card"></i>
                Payer maintenant
            </button>
        </div>
    </div>
    @endif

    {{-- ============================================================
         REÇU DE PAIEMENT
         → Affiché quand le paiement a déjà été soumis
    ============================================================ --}}
    @if(isset($recuPaiement) && $recuPaiement)
    <div class="bg-white rounded-xl shadow p-6 mb-6 border-l-4"
        style="border-color: #007A3D;">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background-color: #e6f4ed;">
                    <i class="fa-solid fa-receipt text-2xl" style="color: #007A3D;"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-800 text-lg">Paiement soumis</p>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Numéro de reçu :
                        <span class="font-mono font-bold text-gray-700">
                            REC-{{ str_pad($recuPaiement->id, 6, '0', STR_PAD_LEFT) }}
                        </span>
                        · {{ $recuPaiement->date }}
                    </p>
                    <p class="text-lg font-bold mt-1" style="color: #007A3D;">
                        {{ number_format($recuPaiement->montant, 0, ',', ' ') }} FCFA
                    </p>

                    {{-- ← Statut paiement --}}
                    @if($statutPaiement == 'valide')
                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                        <i class="fa-solid fa-circle-check mr-1"></i>
                        Paiement confirmé ✅
                    </span>
                    @elseif($statutPaiement == 'rejete')
                    <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 font-medium">
                        <i class="fa-solid fa-circle-xmark mr-1"></i>
                        Paiement rejeté
                    </span>
                    @else
                    <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium">
                        <i class="fa-solid fa-clock mr-1"></i>
                        En attente de confirmation
                    </span>
                    @endif

                </div>
            </div>
            <button onclick="imprimerRecu()"
                class="px-5 py-2.5 rounded-xl text-white text-sm font-medium transition hover:opacity-90 flex items-center gap-2 flex-shrink-0"
                style="background-color: #007A3D;">
                <i class="fa-solid fa-print"></i>
                Imprimer le reçu
            </button>
        </div>
    </div>

    {{-- Zone d'impression cachée --}}
    <div id="zone-recu" style="display:none;">
        <div style="max-width:500px; margin:auto; font-family:Arial,sans-serif; padding:40px;">
            <div style="text-align:center; margin-bottom:30px;">
                <h2 style="color:#007A3D; font-size:24px; margin:0;">GesB2B — CCI-BF</h2>
                <p style="color:#666; margin:5px 0;">Chambre de Commerce et d'Industrie du Burkina Faso</p>
                <p style="color:#999; font-size:12px; margin:5px 0;">Reçu de paiement officiel</p>
            </div>
            <div style="background:#f8f9fa; border-radius:12px; padding:20px; margin-bottom:20px; text-align:center;">
                <p style="color:#999; font-size:12px; margin:0 0 5px;">Numéro de reçu</p>
                <p style="font-family:monospace; font-size:22px; font-weight:bold; margin:0; color:#333;">
                    REC-{{ str_pad($recuPaiement->id, 6, '0', STR_PAD_LEFT) }}
                </p>
            </div>
            <table style="width:100%; border-collapse:collapse;">
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:12px 0; color:#666; font-size:14px;">Entreprise</td>
                    <td style="padding:12px 0; font-weight:bold; text-align:right; color:#333;">
                        {{ $entreprise->nom }}
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:12px 0; color:#666; font-size:14px;">Représentant</td>
                    <td style="padding:12px 0; font-weight:bold; text-align:right; color:#333;">
                        {{ $representant->nom }} {{ $representant->prenom }}
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:12px 0; color:#666; font-size:14px;">Mode de paiement</td>
                    <td style="padding:12px 0; font-weight:bold; text-align:right; color:#333;">
                        LigdiCash
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:12px 0; color:#666; font-size:14px;">Statut</td>
                    <td style="padding:12px 0; font-weight:bold; text-align:right; color:#333;">
                        @if($statutPaiement == 'valide') Confirmé ✅
                        @elseif($statutPaiement == 'rejete') Rejeté ❌
                        @else En attente ⏳
                        @endif
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:12px 0; color:#666; font-size:14px;">Date</td>
                    <td style="padding:12px 0; font-weight:bold; text-align:right; color:#333;">
                        {{ $recuPaiement->date }}
                    </td>
                </tr>
                <tr style="background:#e6f4ed;">
                    <td style="padding:16px 12px; font-weight:bold; font-size:16px; color:#333;">
                        Montant payé
                    </td>
                    <td style="padding:16px 12px; font-weight:bold; font-size:22px; text-align:right; color:#007A3D;">
                        {{ number_format($recuPaiement->montant, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            </table>
            <div style="text-align:center; margin-top:30px; padding-top:20px; border-top:1px solid #eee; color:#999; font-size:12px;">
                <p style="margin:0;">Reçu officiel CCI-BF — GesB2B Platform</p>
                <p style="margin:5px 0 0;">Ce reçu est généré automatiquement.</p>
            </div>
        </div>
    </div>

    <script>
    function imprimerRecu() {
        var contenu = document.getElementById('zone-recu').innerHTML;
        var fenetre = window.open('', '_blank');
        fenetre.document.write('<html><head><title>Reçu GesB2B — CCI-BF</title></head><body>' + contenu + '</body></html>');
        fenetre.document.close();
        fenetre.focus();
        fenetre.print();
        fenetre.close();
    }
    </script>
    @endif

    {{-- ============================================================
         NOTIFICATIONS SYSTÈME
    ============================================================ --}}
    @if($notifications->count() > 0)
    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-bell" style="color: #C8102E;"></i>
            Notifications
        </h3>
        <div class="space-y-2">
            @foreach($notifications as $notif)
            <div class="bg-gray-50 rounded-xl p-3 flex items-start gap-3 border border-gray-100">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                    style="background-color: #e6f4ed;">
                    <i class="fa-solid fa-bell text-sm" style="color: #007A3D;"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-700">{{ $notif->contenu }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ \Carbon\Carbon::parse($notif->date_envoie)->locale('fr')->translatedFormat('d/m/Y') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ============================================================
         DEMANDES D'ADHÉSION EN ATTENTE
    ============================================================ --}}
    @if($demandesEnAttente > 0)
    <div class="bg-white rounded-xl shadow p-5 mb-6 border-l-4" style="border-color: #C8102E;">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background-color: #fde8ec;">
                    <i class="fa-solid fa-user-clock text-xl" style="color: #C8102E;"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-800">
                        {{ $demandesEnAttente }} demande(s) d'adhésion en attente
                    </p>
                    <p class="text-sm text-gray-400 mt-0.5">
                        Des personnes souhaitent rejoindre votre entreprise.
                    </p>
                </div>
            </div>
            <a href="{{ route('entreprise.participants') }}"
                class="px-4 py-2 rounded-xl text-white text-sm font-medium transition hover:opacity-90 flex-shrink-0 flex items-center gap-2"
                style="background-color: #C8102E;">
                <i class="fa-solid fa-users"></i>
                Voir et valider
            </a>
        </div>
    </div>
    @endif

    {{-- ============================================================
         STATISTIQUES
    ============================================================ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #007A3D;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #e6f4ed;">
                <i class="fa-solid fa-users" style="color: #007A3D;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Mes Membres</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalMembres }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #C8102E;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #fde8ec;">
                <i class="fa-solid fa-store" style="color: #C8102E;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Mes Stands</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalStands }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4 border-l-4 hover:shadow-lg transition"
            style="border-color: #2d5a8e;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl"
                style="background-color: #e8f0fb;">
                <i class="fa-solid fa-handshake" style="color: #2d5a8e;"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Mes Rendez-vous</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalRdv }}</p>
            </div>
        </div>

    </div>

    {{-- ============================================================
         ÉVÉNEMENTS DISPONIBLES
    ============================================================ --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-calendar-star" style="color: #C8102E;"></i>
                Événements disponibles
            </h3>
            <span class="text-xs text-gray-400">
                {{ $evenementsDisponibles->count() }} événement(s) ouvert(s)
            </span>
        </div>

        @forelse($evenementsDisponibles as $evenement)
        <div class="border border-gray-200 rounded-xl p-5 mb-4 hover:border-green-300 hover:shadow-md transition last:mb-0">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white flex-shrink-0"
                            style="background-color: #007A3D;">
                            <i class="fa-solid fa-calendar text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">{{ $evenement->nom }}</h4>
                            <span class="text-xs px-2 py-0.5 rounded-full text-white font-medium"
                                style="background-color: #007A3D;">
                                {{ $evenement->typeEvenement->nom ?? '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 mt-3">
                        <span>
                            <i class="fa-solid fa-calendar mr-1 text-gray-400"></i>
                            {{ \Carbon\Carbon::parse($evenement->date_debut)->locale('fr')->translatedFormat('d/m/Y') }}
                            @if($evenement->date_debut != $evenement->date_fin)
                            → {{ \Carbon\Carbon::parse($evenement->date_fin)->locale('fr')->translatedFormat('d/m/Y') }}
                            @endif
                        </span>
                        <span>
                            <i class="fa-solid fa-clock mr-1 text-gray-400"></i>
                            {{ $evenement->heure_debut }} - {{ $evenement->heure_fin }}
                        </span>
                        <span>
                            <i class="fa-solid fa-location-dot mr-1 text-gray-400"></i>
                            {{ $evenement->ville }}
                        </span>
                        <span>
                            <i class="fa-solid fa-map-pin mr-1 text-gray-400"></i>
                            {{ $evenement->lieu }}
                        </span>
                    </div>
                    <div class="mt-2">
                        @if($evenement->type_paiement == 'gratuit')
                        <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">
                            <i class="fa-solid fa-gift mr-1"></i> Gratuit
                        </span>
                        @elseif($evenement->type_paiement == 'par_participant')
                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">
                            <i class="fa-solid fa-user mr-1"></i>
                            {{ number_format($evenement->montant_inscription, 0, ',', ' ') }} FCFA / participant
                        </span>
                        @else
                        <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-medium">
                            <i class="fa-solid fa-building mr-1"></i>
                            {{ number_format($evenement->montant_inscription, 0, ',', ' ') }} FCFA / entreprise
                        </span>
                        @endif
                    </div>
                </div>
                <div class="flex-shrink-0 text-right">
                    @if($evenement->deja_inscrit)
                    <span class="px-4 py-2 rounded-xl text-xs font-medium text-white flex items-center gap-1"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-circle-check"></i> Inscrit
                    </span>
                    @elseif($representant)
                    <a href="{{ route('entreprise.inscription.wizard', $evenement->id) }}"
                        class="px-4 py-2 rounded-xl text-white text-sm font-medium transition hover:opacity-90 flex items-center gap-1 shadow"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-user-plus"></i>
                        S'inscrire
                    </a>
                    @else
                    <span class="text-xs text-orange-500 italic flex items-center gap-1">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Profil incomplet
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-400">
            <i class="fa-solid fa-calendar-xmark text-3xl mb-2 block text-gray-300"></i>
            <p class="text-sm">Aucun événement disponible pour le moment</p>
        </div>
        @endforelse
    </div>

    {{-- ============================================================
         DERNIERS MEMBRES
    ============================================================ --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-users" style="color: #007A3D;"></i>
                Mes Membres
            </h3>
            <a href="{{ route('entreprise.participants') }}"
                class="text-sm px-4 py-2 rounded-lg text-white transition hover:opacity-90"
                style="background-color: #007A3D;">
                Voir tous
            </a>
        </div>

        @forelse($derniersMembres as $membre)
        <div class="flex items-center justify-between py-3 border-b last:border-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold"
                    style="background-color: {{ $membre->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                    {{ strtoupper(substr($membre->prenom ?? 'X', 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">
                        {{ $membre->nom }} {{ $membre->prenom }}
                    </p>
                    <p class="text-xs text-gray-400">{{ $membre->fonction ?? '-' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded-lg">
                    {{ $membre->code_acces }}
                </span>
                @php
                    $profilComplet = $membre->secteur_recherche
                        && $membre->zone_geographique
                        && $membre->type_partenaire;
                @endphp
                @if($profilComplet)
                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
                @else
                <span class="text-xs px-2 py-1 rounded-full bg-orange-100 text-orange-700">
                    <i class="fa-solid fa-clock"></i>
                </span>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-6 text-gray-400">
            <i class="fa-solid fa-users text-3xl mb-2 block text-gray-300"></i>
            <p class="text-sm">Aucun membre ajouté</p>
            <a href="{{ route('entreprise.participants') }}"
                class="mt-3 inline-block px-4 py-2 rounded-xl text-white text-sm font-medium"
                style="background-color: #C8102E;">
                <i class="fa-solid fa-user-plus mr-1"></i>
                Ajouter un membre
            </a>
        </div>
        @endforelse
    </div>

    {{-- ============================================================
         MODAL PAIEMENT LIGDICASH
    ============================================================ --}}
    @if($showModalPaiement)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="fa-solid fa-credit-card text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Paiement LigdiCash</h3>
                        <p class="text-green-200 text-xs">Paiement mobile sécurisé</p>
                    </div>
                </div>
                <button wire:click="closeModalPaiement"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8">

                {{-- Montant --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-6 text-center border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">Montant à payer</p>
                    <p class="text-3xl font-bold" style="color: #007A3D;">
                        {{ number_format($montant_paiement, 0, ',', ' ') }} FCFA
                    </p>
                </div>

                {{-- Notice simulation --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-5 text-xs text-yellow-700 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="font-bold mb-1">Mode simulation</p>
                        L'intégration LigdiCash sera activée dès réception des clés API.
                    </div>
                </div>

                @if($etape_paiement == 1)
                <p class="text-sm font-semibold text-gray-700 mb-4">
                    Choisissez votre opérateur :
                </p>
                <div class="space-y-3 mb-5">
                    <button type="button" wire:click="$set('mode_paiement', 'orange_money')"
                        class="w-full border-2 rounded-xl p-4 transition flex items-center gap-4
                            {{ $mode_paiement === 'orange_money' ? 'border-orange-400 bg-orange-50' : 'border-gray-200 hover:bg-orange-50' }}">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                            style="background-color: #FF6600;">OM</div>
                        <div class="text-left">
                            <p class="font-bold text-gray-800">Orange Money</p>
                            <p class="text-xs text-gray-400">Composez *144*4*6# pour votre OTP</p>
                        </div>
                        @if($mode_paiement === 'orange_money')
                        <i class="fa-solid fa-circle-check ml-auto text-orange-500"></i>
                        @else
                        <i class="fa-solid fa-chevron-right ml-auto text-gray-400"></i>
                        @endif
                    </button>

                    <button type="button" wire:click="$set('mode_paiement', 'moov_money')"
                        class="w-full border-2 rounded-xl p-4 transition flex items-center gap-4
                            {{ $mode_paiement === 'moov_money' ? 'border-blue-400 bg-blue-50' : 'border-gray-200 hover:bg-blue-50' }}">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                            style="background-color: #0066CC;">MM</div>
                        <div class="text-left">
                            <p class="font-bold text-gray-800">Moov Africa</p>
                            <p class="text-xs text-gray-400">Paiement via Moov Money</p>
                        </div>
                        @if($mode_paiement === 'moov_money')
                        <i class="fa-solid fa-circle-check ml-auto text-blue-500"></i>
                        @else
                        <i class="fa-solid fa-chevron-right ml-auto text-gray-400"></i>
                        @endif
                    </button>
                </div>

                <div class="mb-5">
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">
                        Votre numéro de téléphone *
                    </label>
                    <input wire:model="telephone_paiement" type="text"
                        class="w-full border rounded-xl px-4 py-3 focus:outline-none text-lg text-center font-mono"
                        placeholder="{{ $mode_paiement == 'orange_money' ? '07XXXXXXXX' : '01XXXXXXXX' }}">
                    @error('telephone_paiement')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                @if($mode_paiement == 'orange_money')
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-3 mb-5 text-xs text-orange-700 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5 flex-shrink-0"></i>
                    Composez <strong>*144*4*6#</strong> sur votre téléphone Orange
                    pour générer votre OTP avant de continuer.
                </div>
                @endif

                <button wire:click="envoyerOtp"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow-lg flex items-center justify-center gap-2"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-paper-plane"></i>
                    Continuer et recevoir mon OTP
                </button>

                @elseif($etape_paiement == 2)
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3"
                        style="background-color: #e6f4ed;">
                        <i class="fa-solid fa-shield-halved text-3xl" style="color: #007A3D;"></i>
                    </div>
                    <p class="font-bold text-gray-800">Vérification OTP</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Code envoyé au <strong>{{ $telephone_paiement }}</strong>
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">
                        Code OTP reçu *
                    </label>
                    <input wire:model="otp_saisi" type="text" maxlength="6"
                        class="w-full border rounded-xl px-4 py-3 text-2xl text-center font-mono tracking-widest"
                        placeholder="_ _ _ _ _ _">
                    @error('otp_saisi')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-center text-xs text-yellow-700 mb-5">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                    <strong>Simulation :</strong> Votre code OTP est
                    <span class="font-mono font-bold text-red-600 text-lg ml-1">{{ $otp_code }}</span>
                </div>

                <div class="flex gap-3">
                    <button wire:click="$set('etape_paiement', 1)"
                        class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Retour
                    </button>
                    <button wire:click="confirmerOtp"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="flex-1 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm flex items-center justify-center gap-2"
                        style="background-color: #007A3D;">
                        <span wire:loading.remove wire:target="confirmerOtp">
                            <i class="fa-solid fa-check mr-1"></i>
                            Confirmer le paiement
                        </span>
                        <span wire:loading wire:target="confirmerOtp">
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                            Traitement...
                        </span>
                    </button>
                </div>
                @endif

            </div>
        </div>
    </div>
    @endif

</div>