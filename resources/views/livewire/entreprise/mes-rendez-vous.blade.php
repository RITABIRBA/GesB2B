<div>
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Mes Rendez-vous</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $rendezVous->count() }} RDV
            </span>
        </div>
    </div>

    <div class="flex gap-4 mb-5">
        <select wire:model.live="filtre_statut"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Tous les statuts</option>
            <option value="planifie">Planifié</option>
            <option value="confirme">Confirmé</option>
            <option value="annule">Annulé</option>
            <option value="termine">Terminé</option>
        </select>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Participant 1</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Participant 2</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Date</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Horaire</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Stand</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rendezVous as $rdv)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-800 text-sm">
                        {{ $rdv->participant1->nom ?? '-' }} {{ $rdv->participant1->prenom ?? '' }}
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        {{ $rdv->participant2->nom ?? '-' }} {{ $rdv->participant2->prenom ?? '' }}
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">{{ $rdv->date }}</td>
                    <td class="px-6 py-4 text-gray-600 text-sm">{{ $rdv->heure_debut }} - {{ $rdv->heure_fin }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-1 rounded-lg bg-gray-100 text-gray-700">
                            Stand {{ $rdv->stand->numero_stand ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($rdv->statut == 'planifie')
                            <span class="px-3 py-1 rounded-full text-xs text-white bg-blue-600">Planifié</span>
                        @elseif($rdv->statut == 'confirme')
                            <span class="px-3 py-1 rounded-full text-xs text-white" style="background-color: #007A3D;">Confirmé</span>
                        @elseif($rdv->statut == 'annule')
                            <span class="px-3 py-1 rounded-full text-xs text-white bg-red-600">Annulé</span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs text-white bg-gray-500">Terminé</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-handshake text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun rendez-vous</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>