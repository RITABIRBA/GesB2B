<div>
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Liste des Badges</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #6b2d6b;">
                {{ $badges->count() }} badge(s)
            </span>
        </div>
    </div>

    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher un badge..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none text-sm">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Entreprise</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Type</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">QR Code</th>
                </tr>
            </thead>
            <tbody>
                @forelse($badges as $badge)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                style="background-color: #6b2d6b;">
                                {{ strtoupper(substr($badge->participant->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <p class="font-semibold text-gray-800">
                                {{ $badge->participant->nom ?? '-' }}
                                {{ $badge->participant->prenom ?? '' }}
                            </p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                            {{ $badge->participant->entreprise->nom ?? 'Indépendant' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-3 py-1 rounded-full text-white font-medium"
                            style="background-color: #6b2d6b;">
                            {{ $badge->typeBadge->libelle ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-mono text-xs bg-gray-100 px-3 py-1 rounded-lg text-gray-700">
                            {{ $badge->qr_code }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-id-badge text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun badge</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>