<div>
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Paiements</h3>
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
    <div class="flex gap-4 mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher un participant..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none text-sm">
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
                </tr>
            </thead>
            <tbody>
                @forelse($paiements as $paiement)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                style="background-color: #007A3D;">
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
                                'especes'      => ['icon' => 'fa-money-bill',       'color' => '#007A3D', 'label' => 'Espèces'],
                                'virement'     => ['icon' => 'fa-building-columns', 'color' => '#2d5a8e', 'label' => 'Virement'],
                                'mobile_money' => ['icon' => 'fa-mobile',           'color' => '#f59e0b', 'label' => 'Mobile Money'],
                                'carte'        => ['icon' => 'fa-credit-card',      'color' => '#8b5cf6', 'label' => 'Carte'],
                            ];
                            $mode = $modeIcons[$paiement->mode_paiement] ?? ['icon' => 'fa-money-bill', 'color' => '#6b7280', 'label' => $paiement->mode_paiement];
                        @endphp
                        <span class="text-xs px-2 py-1 rounded-full text-white font-medium flex items-center gap-1 w-fit"
                            style="background-color: {{ $mode['color'] }}">
                            <i class="fa-solid {{ $mode['icon'] }}"></i>
                            {{ $mode['label'] }}
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
                                <i class="fa-solid fa-receipt mr-1"></i> Disponible
                            </span>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-money-bill text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun paiement</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>