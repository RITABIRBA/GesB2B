<div>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-700">Souhaits de Rendez-vous</h3>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Priorité</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Veut rencontrer</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Type</th>
                </tr>
            </thead>
            <tbody>
                @forelse($souhaits as $souhait)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white text-sm"
                            style="background-color: {{ $souhait->priorite <= 3 ? '#C8102E' : ($souhait->priorite <= 10 ? '#007A3D' : '#6b7280') }}">
                            {{ $souhait->priorite }}
                        </div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-800">
                        {{ $souhait->participant->nom ?? '-' }} {{ $souhait->participant->prenom ?? '' }}
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ $souhait->participantCible->nom ?? '-' }} {{ $souhait->participantCible->prenom ?? '' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($souhait->type == 'mutuel')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium"
                                style="background-color: #C8102E;">
                                <i class="fa-solid fa-arrows-left-right mr-1"></i> Mutuel
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                                <i class="fa-solid fa-arrow-right mr-1"></i> Envoyé
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-heart text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun souhait de rendez-vous</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>