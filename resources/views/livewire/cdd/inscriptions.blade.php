<div>
    <div class="flex items-center gap-4 mb-6">
        <h3 class="text-xl font-bold text-gray-700">Inscriptions de ma délégation</h3>
        <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #2d5a8e;">
            {{ $inscriptions->count() }} inscription(s)
        </span>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Membre</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Événement</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Montant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Statut paiement</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inscriptions as $i)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-800 text-sm">
                            {{ $i->participant->nom ?? '-' }} {{ $i->participant->prenom ?? '' }}
                        </p>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $i->evenement->nom ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                        {{ number_format($i->montant_paye, 0, ',', ' ') }} FCFA
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $colors = ['paye' => 'bg-green-100 text-green-700', 'en_attente' => 'bg-yellow-100 text-yellow-700', 'annule' => 'bg-red-100 text-red-700'];
                        @endphp
                        <span class="text-xs px-3 py-1 rounded-full font-medium {{ $colors[$i->statut_paiement] ?? 'bg-gray-100 text-gray-500' }}">
                            {{ ucfirst(str_replace('_', ' ', $i->statut_paiement)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-400">{{ $i->date_inscription }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-clipboard-list text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucune inscription pour le moment</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>