<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Gestion des Paiements</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $paiements->count() }} paiement(s)
            </span>
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-yellow-500">
            <i class="fa-solid fa-clock text-yellow-500 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">En attente</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $paiements->where('statut', 'en_attente')->count() }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4"
            style="border-color: #007A3D;">
            <i class="fa-solid fa-circle-check text-xl" style="color: #007A3D;"></i>
            <div>
                <p class="text-xs text-gray-500">Validés</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $paiements->where('statut', 'valide')->count() }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-red-500">
            <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">Rejetés</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $paiements->where('statut', 'rejete')->count() }}
                </p>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="flex gap-4 mb-5 flex-wrap">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher un participant..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
        <select wire:model.live="filtre_statut"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Tous les statuts</option>
            <option value="en_attente">En attente</option>
            <option value="valide">Validé</option>
            <option value="rejete">Rejeté</option>
        </select>
        <select wire:model.live="filtre_mode"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Tous les modes</option>
            <option value="especes">Espèces</option>
            <option value="virement">Virement</option>
            <option value="mobile_money">Mobile Money</option>
            <option value="carte">Carte</option>
            <option value="cheque">Chèque</option>
        </select>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Événement</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Montant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Mode</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Date</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Statut</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Reçu</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paiements as $paiement)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                style="background-color: #C8102E;">
                                {{ strtoupper(substr($paiement->inscription->participant->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{ $paiement->inscription->participant->nom ?? '-' }}
                                    {{ $paiement->inscription->participant->prenom ?? '' }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $paiement->inscription->participant->entreprise->nom ?? 'Indépendant' }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        {{ $paiement->inscription->evenement->nom ?? '-' }}
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-800">
                        {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $modeIcons = [
                                'especes'      => ['icon' => 'fa-money-bill', 'color' => '#007A3D', 'label' => 'Espèces'],
                                'virement'     => ['icon' => 'fa-building-columns', 'color' => '#2d5a8e', 'label' => 'Virement'],
                                'mobile_money' => ['icon' => 'fa-mobile', 'color' => '#f59e0b', 'label' => 'Mobile Money'],
                                'carte'        => ['icon' => 'fa-credit-card', 'color' => '#8b5cf6', 'label' => 'Carte'],
                                'cheque'       => ['icon' => 'fa-money-check', 'color' => '#C8102E', 'label' => 'Chèque'],
                            ];
                            $mode = $modeIcons[$paiement->mode_paiement] ?? ['icon' => 'fa-money-bill', 'color' => '#6b7280', 'label' => $paiement->mode_paiement];
                        @endphp
                        <span class="text-xs px-2 py-1 rounded-full text-white font-medium flex items-center gap-1 w-fit cursor-pointer"
                            style="background-color: {{ $mode['color'] }}"
                            @if($paiement->mode_paiement === 'cheque') wire:click="voirCheque({{ $paiement->id }})" @endif>
                            <i class="fa-solid {{ $mode['icon'] }}"></i>
                            {{ $mode['label'] }}
                            @if($paiement->mode_paiement === 'cheque' && $paiement->numero_cheque)
                            <i class="fa-solid fa-eye ml-1"></i>
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">{{ $paiement->date_paiement }}</td>
                    <td class="px-6 py-4">
                        @if($paiement->statut == 'valide')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-circle-check mr-1"></i> Validé
                            </span>
                        @elseif($paiement->statut == 'rejete')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-red-600">
                                <i class="fa-solid fa-circle-xmark mr-1"></i> Rejeté
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">
                                <i class="fa-solid fa-clock mr-1"></i> En attente
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($paiement->recu)
                            <span class="text-xs px-2 py-1 rounded-lg bg-green-100 text-green-700 font-medium">
                                <i class="fa-solid fa-receipt mr-1"></i> Généré
                            </span>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($paiement->statut == 'en_attente')
                        <div class="flex gap-2">
                            @if($paiement->mode_paiement === 'cheque')
                            <button wire:click="voirCheque({{ $paiement->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90 flex items-center gap-1"
                                style="background-color: #C8102E;">
                                <i class="fa-solid fa-eye"></i> Vérifier
                            </button>
                            @else
                            <button wire:click="valider({{ $paiement->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90 flex items-center gap-1"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-check"></i> Valider
                            </button>
                            <button wire:click="rejeter({{ $paiement->id }})"
                                wire:confirm="Rejeter ce paiement ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                                <i class="fa-solid fa-xmark"></i> Rejeter
                            </button>
                            @endif
                        </div>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-money-bill text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun paiement</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ✅ MODAL DÉTAIL CHÈQUE --}}
    @if($showChequeModal && $paiement_cheque)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #C8102E, #a00d25);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-money-check"></i> Paiement par chèque
                </h3>
                <button wire:click="fermerChequeModal" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                <div class="bg-gray-50 rounded-xl p-4 mb-5 border border-gray-200">
                    <p class="font-bold text-gray-800">
                        {{ $paiement_cheque->inscription->participant->nom ?? '-' }}
                        {{ $paiement_cheque->inscription->participant->prenom ?? '' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $paiement_cheque->inscription->evenement->nom ?? '-' }}
                    </p>
                </div>

                <div class="bg-red-50 border-2 border-red-200 rounded-xl p-4 text-center mb-5">
                    <p class="text-xs text-red-500 font-medium mb-1">
                        <i class="fa-solid fa-hashtag mr-1"></i> Numéro de chèque
                    </p>
                    <p class="font-mono font-bold text-2xl text-red-700 tracking-widest">
                        {{ $paiement_cheque->numero_cheque ?? '—' }}
                    </p>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 mb-5">
                    <p class="text-xs text-gray-500 mb-1">Montant</p>
                    <p class="font-bold text-gray-800 text-xl">
                        {{ number_format($paiement_cheque->montant, 0, ',', ' ') }} FCFA
                    </p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-5 text-xs text-blue-700">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    Vérifiez physiquement que le chèque a bien été reçu avant de valider ce paiement.
                </div>

                <div class="flex justify-end gap-3">
                    <button wire:click="rejeter({{ $paiement_cheque->id }})"
                        wire:confirm="Rejeter ce paiement par chèque ?"
                        class="px-5 py-2.5 rounded-xl text-white font-medium text-sm bg-red-600 hover:bg-red-700">
                        <i class="fa-solid fa-xmark mr-1"></i> Rejeter
                    </button>
                    <button wire:click="valider({{ $paiement_cheque->id }})"
                        class="px-5 py-2.5 rounded-xl text-white font-medium text-sm transition hover:opacity-90"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-check mr-1"></i> Confirmer la réception et valider
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>