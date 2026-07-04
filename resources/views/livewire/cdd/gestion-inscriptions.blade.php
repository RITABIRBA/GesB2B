<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-300 text-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Gestion des Inscriptions</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $inscriptions->count() }} inscription(s)
            </span>
        </div>
    </div>

    {{-- Flux --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-4 mb-6 text-sm text-blue-700">
        <p class="font-semibold mb-2">
            <i class="fa-solid fa-circle-info mr-1"></i>
            Flux d'inscription en 2 étapes :
        </p>
        <div class="flex items-center gap-3 flex-wrap">
            <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium">1. Préinscription</span>
            <i class="fa-solid fa-arrow-right text-gray-400"></i>
            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">2. Validation CDD</span>
            <i class="fa-solid fa-arrow-right text-gray-400"></i>
            <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">3. Paiement participant</span>
            <i class="fa-solid fa-arrow-right text-gray-400"></i>
            <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">4. Confirmation CDD</span>
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-yellow-500">
            <i class="fa-solid fa-clock text-yellow-500 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">Préinscriptions</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $inscriptions->where('statut_presence', 'absent')->where('statut_paiement', 'en_attente')->count() }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-blue-500">
            <i class="fa-solid fa-check text-blue-500 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">Validées / En attente paiement</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $inscriptions->where('statut_presence', 'present')->where('statut_paiement', 'en_attente')->count() }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4" style="border-color: #007A3D;">
            <i class="fa-solid fa-circle-check text-xl" style="color: #007A3D;"></i>
            <div>
                <p class="text-xs text-gray-500">Payées</p>
                <p class="font-bold text-gray-800 text-lg">{{ $inscriptions->where('statut_paiement', 'paye')->count() }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-red-500">
            <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">Annulées</p>
                <p class="font-bold text-gray-800 text-lg">{{ $inscriptions->where('statut_paiement', 'annule')->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="flex gap-4 mb-5 flex-wrap">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher un participant..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none text-sm">
        </div>
        <select wire:model.live="filtre_statut"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Toutes les présences</option>
            <option value="absent">Préinscription (non validée)</option>
            <option value="present">Validée par CDD</option>
        </select>
        <select wire:model.live="filtre_paiement"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Tous les paiements</option>
            <option value="en_attente">En attente</option>
            <option value="paye">Payé</option>
            <option value="annule">Annulé</option>
        </select>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-5 py-4 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-5 py-4 text-gray-500 font-semibold text-sm">Événement</th>
                    <th class="px-5 py-4 text-gray-500 font-semibold text-sm">Montant</th>
                    <th class="px-5 py-4 text-gray-500 font-semibold text-sm">Étape</th>
                    <th class="px-5 py-4 text-gray-500 font-semibold text-sm">Paiement</th>
                    <th class="px-5 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inscriptions as $inscription)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                style="background-color: #007A3D;">
                                {{ strtoupper(substr($inscription->participant->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{ $inscription->participant->nom ?? '-' }}
                                    {{ $inscription->participant->prenom ?? '' }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $inscription->participant->entreprise->nom ?? 'Indépendant' }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-600 text-sm">{{ $inscription->evenement->nom ?? '-' }}</td>
                    <td class="px-5 py-4 font-bold text-gray-800">
                        {{ number_format($inscription->montant_paye, 0, ',', ' ') }} FCFA
                    </td>
                    <td class="px-5 py-4">
                        @if($inscription->statut_paiement == 'annule')
                        <span class="px-2 py-1 rounded-full text-xs text-white font-medium bg-red-600">Rejetée</span>
                        @elseif($inscription->statut_paiement == 'paye')
                        <span class="px-2 py-1 rounded-full text-xs text-white font-medium" style="background-color: #007A3D;">Complète</span>
                        @elseif($inscription->statut_presence == 'present')
                        <span class="px-2 py-1 rounded-full text-xs text-white font-medium bg-blue-600">Validée — Attente paiement</span>
                        @else
                        <span class="px-2 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">Préinscription</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($inscription->paiement)
                            @if($inscription->paiement->statut == 'valide')
                            <span class="text-xs px-2 py-1 rounded-lg bg-green-100 text-green-700 font-medium">Validé</span>
                            @elseif($inscription->paiement->statut == 'rejete')
                            <span class="text-xs px-2 py-1 rounded-lg bg-red-100 text-red-700 font-medium">Rejeté</span>
                            @else
                            <span class="text-xs px-2 py-1 rounded-lg bg-yellow-100 text-yellow-700 font-medium">
                                {{ number_format($inscription->paiement->montant, 0, ',', ' ') }} FCFA
                            </span>
                            @endif
                        @else
                        <span class="text-xs text-gray-400">Aucun paiement</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex gap-1.5 flex-wrap">

                            {{-- ✅ Voir le dossier avant de valider --}}
                            <button wire:click="ouvrirDossier({{ $inscription->id }})"
                                class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90 flex items-center gap-1"
                                style="background-color: #2d5a8e;">
                                <i class="fa-solid fa-eye"></i> Dossier
                            </button>

                            {{-- Valider/Rejeter la préinscription --}}
                            @if($inscription->statut_presence == 'absent' && $inscription->statut_paiement == 'en_attente')
                            <button wire:click="validerInscription({{ $inscription->id }})"
                                class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90 flex items-center gap-1"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-check"></i> Valider
                            </button>
                            <button wire:click="rejeterInscription({{ $inscription->id }})"
                                wire:confirm="Rejeter cette inscription ?"
                                class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                                <i class="fa-solid fa-xmark"></i> Rejeter
                            </button>
                            @endif

                            {{-- Valider/Rejeter le paiement --}}
                            @if($inscription->statut_presence == 'present'
                                && $inscription->paiement
                                && $inscription->paiement->statut == 'en_attente')
                            <button wire:click="validerPaiement({{ $inscription->id }})"
                                class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700 flex items-center gap-1">
                                <i class="fa-solid fa-money-bill"></i> Valider paiement
                            </button>
                            <button wire:click="rejeterPaiement({{ $inscription->id }})"
                                wire:confirm="Rejeter ce paiement ?"
                                class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium bg-orange-500 transition hover:bg-orange-600 flex items-center gap-1">
                                <i class="fa-solid fa-xmark"></i> Rejeter
                            </button>
                            @endif

                            @if($inscription->paiement && $inscription->paiement->recu)
                            <span class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium bg-purple-600 flex items-center gap-1">
                                <i class="fa-solid fa-receipt"></i> Recu
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-clipboard-list text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucune inscription</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ════ MODAL DOSSIER COMPLET ════ --}}
    @if($showModalDossier && $dossier_courant)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #2d5a8e, #1e3f63);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-folder-open"></i> Dossier du participant
                </h3>
                <button wire:click="fermerDossier" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                @php $p = $dossier_courant->participant; @endphp

                {{-- Identité --}}
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white text-2xl font-bold flex-shrink-0"
                        style="background-color: {{ ($p->genre ?? 'homme') == 'femme' ? '#C8102E' : '#007A3D' }}">
                        {{ strtoupper(substr($p->prenom ?? 'X', 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-gray-800">{{ $p->nom }} {{ $p->prenom }}</h4>
                        <p class="text-gray-500 text-sm">{{ $p->fonction ?? 'Fonction non renseignée' }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full text-white font-medium mt-1 inline-block"
                            style="background-color: {{ ($p->genre ?? 'homme') == 'femme' ? '#C8102E' : '#007A3D' }}">
                            {{ ($p->genre ?? 'homme') == 'femme' ? 'Femme' : 'Homme' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5">
                    {{-- Contact --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-bold text-gray-400 uppercase mb-3">Contact</p>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-envelope w-4" style="color: #007A3D;"></i>
                                <span>{{ $p->email ?: 'Pas d\'email' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-phone w-4" style="color: #007A3D;"></i>
                                <span>{{ $p->telephone ?: '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Entreprise --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-bold text-gray-400 uppercase mb-3">Entreprise</p>
                        @if($p->entreprise)
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-building w-4" style="color: #007A3D;"></i>
                                <span class="font-semibold">{{ $p->entreprise->nom }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-industry w-4 text-gray-400"></i>
                                <span>{{ $p->entreprise->secteur_activite ?? '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-location-dot w-4 text-gray-400"></i>
                                <span>{{ $p->entreprise->pays ?? '-' }}</span>
                            </div>
                        </div>
                        @else
                        <p class="text-sm text-gray-400 italic">Participant indépendant</p>
                        @endif
                    </div>

                    {{-- Événement --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <p class="text-xs font-bold text-blue-400 uppercase mb-3">Événement</p>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-calendar w-4 text-blue-500"></i>
                                <span class="font-semibold">{{ $dossier_courant->evenement->nom ?? '-' }}</span>
                            </div>
                            @if($dossier_courant->evenement)
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-tag w-4 text-blue-400"></i>
                                <span>{{ $dossier_courant->evenement->type_paiement == 'gratuit' ? 'Gratuit' : number_format($dossier_courant->montant_paye, 0, ',', ' ') . ' FCFA' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-handshake w-4 text-blue-400"></i>
                                <span>{{ ($dossier_courant->evenement->type_evenement ?? 'avec_b2b') == 'avec_b2b' ? 'Avec rendez-vous B2B' : 'Sans B2B' }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Profil --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-bold text-gray-400 uppercase mb-3">Profil professionnel</p>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-briefcase w-4 text-gray-400"></i>
                                <span>{{ $p->secteur_activite ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-location-dot w-4 text-gray-400"></i>
                                <span>{{ $p->zone_geographique ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-handshake w-4 text-gray-400"></i>
                                <span>{{ $p->participation_rdv ? 'Participe aux rendez-vous B2B' : 'Sans rendez-vous B2B' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Objectif de participation --}}
                @if($p->objectif_participation)
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5">
                    <p class="text-xs font-bold text-green-600 uppercase mb-2">
                        <i class="fa-solid fa-bullseye mr-1"></i> Objectif de participation
                    </p>
                    @php
                        $objectifs = is_array($p->objectif_participation)
                            ? $p->objectif_participation
                            : (json_decode($p->objectif_participation, true) ?: [$p->objectif_participation]);
                    @endphp
                    <div class="flex flex-wrap gap-2">
                        @foreach($objectifs as $obj)
                        <span class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">{{ $obj }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Boutons action --}}
                <div class="flex justify-end gap-3 mt-4">
                    <button wire:click="fermerDossier"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        Fermer
                    </button>
                    @if($dossier_courant->statut_presence == 'absent' && $dossier_courant->statut_paiement == 'en_attente')
                    <button wire:click="rejeterInscription({{ $dossier_courant->id }})"
                        wire:confirm="Rejeter cette inscription ?"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow bg-red-600">
                        <i class="fa-solid fa-xmark mr-1"></i> Rejeter
                    </button>
                    <button wire:click="validerInscription({{ $dossier_courant->id }})"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-check mr-1"></i> Valider l'inscription
                    </button>
                    @endif
                    @if($dossier_courant->statut_presence == 'present'
                        && $dossier_courant->paiement
                        && $dossier_courant->paiement->statut == 'en_attente')
                    <button wire:click="validerPaiement({{ $dossier_courant->id }})"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow bg-blue-600">
                        <i class="fa-solid fa-money-bill mr-1"></i> Valider le paiement
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>