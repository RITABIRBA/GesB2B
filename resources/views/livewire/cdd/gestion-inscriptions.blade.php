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

    {{-- Explication du flux --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-4 mb-6 text-sm text-blue-700">
        <p class="font-semibold mb-2">
            <i class="fa-solid fa-circle-info mr-1"></i>
            Flux d'inscription en 2 étapes :
        </p>
        <div class="flex items-center gap-3 flex-wrap">
            <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium">
                1. Préinscription
            </span>
            <i class="fa-solid fa-arrow-right text-gray-400"></i>
            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                2. Validation CDD
            </span>
            <i class="fa-solid fa-arrow-right text-gray-400"></i>
            <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                3. Paiement participant
            </span>
            <i class="fa-solid fa-arrow-right text-gray-400"></i>
            <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">
                4. Confirmation CDD
            </span>
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
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4"
            style="border-color: #007A3D;">
            <i class="fa-solid fa-circle-check text-xl" style="color: #007A3D;"></i>
            <div>
                <p class="text-xs text-gray-500">Payées</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $inscriptions->where('statut_paiement', 'paye')->count() }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-red-500">
            <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">Annulées</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $inscriptions->where('statut_paiement', 'annule')->count() }}
                </p>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="flex gap-4 mb-5">
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

                    {{-- Participant --}}
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

                    {{-- Événement --}}
                    <td class="px-5 py-4 text-gray-600 text-sm">
                        {{ $inscription->evenement->nom ?? '-' }}
                    </td>

                    {{-- Montant --}}
                    <td class="px-5 py-4 font-bold text-gray-800">
                        {{ number_format($inscription->montant_paye, 0, ',', ' ') }} FCFA
                    </td>

                    {{-- Étape --}}
                    <td class="px-5 py-4">
                        @if($inscription->statut_paiement == 'annule')
                            <span class="px-2 py-1 rounded-full text-xs text-white font-medium bg-red-600">
                                <i class="fa-solid fa-circle-xmark mr-1"></i> Rejetée
                            </span>
                        @elseif($inscription->statut_paiement == 'paye')
                            <span class="px-2 py-1 rounded-full text-xs text-white font-medium"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-circle-check mr-1"></i> Complète
                            </span>
                        @elseif($inscription->statut_presence == 'present')
                            <span class="px-2 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                                <i class="fa-solid fa-check mr-1"></i> Validée — Attente paiement
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">
                                <i class="fa-solid fa-clock mr-1"></i> Préinscription
                            </span>
                        @endif
                    </td>

                    {{-- Paiement --}}
                    <td class="px-5 py-4">
                        @if($inscription->paiement)
                            @if($inscription->paiement->statut == 'valide')
                                <span class="text-xs px-2 py-1 rounded-lg bg-green-100 text-green-700 font-medium">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Validé
                                </span>
                            @elseif($inscription->paiement->statut == 'rejete')
                                <span class="text-xs px-2 py-1 rounded-lg bg-red-100 text-red-700 font-medium">
                                    <i class="fa-solid fa-circle-xmark mr-1"></i> Rejeté
                                </span>
                            @else
                                <span class="text-xs px-2 py-1 rounded-lg bg-yellow-100 text-yellow-700 font-medium">
                                    <i class="fa-solid fa-clock mr-1"></i>
                                    {{ number_format($inscription->paiement->montant, 0, ',', ' ') }} FCFA
                                </span>
                            @endif
                        @else
                            <span class="text-xs text-gray-400">Aucun paiement</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-4">
                        <div class="flex gap-1.5 flex-wrap">

                            {{-- ÉTAPE 1 : Valider/Rejeter la préinscription --}}
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

                            {{-- ÉTAPE 2 : Valider/Rejeter le paiement --}}
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

                            {{-- Reçu disponible --}}
                            @if($inscription->paiement && $inscription->paiement->recu)
                            <span class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium bg-purple-600 flex items-center gap-1">
                                <i class="fa-solid fa-receipt"></i> Reçu ✓
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
</div>