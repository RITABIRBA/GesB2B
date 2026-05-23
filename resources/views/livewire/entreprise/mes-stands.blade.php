<div>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-700">Mes Stands</h3>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">N° Stand</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Événement</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Superficie</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Standing</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stands as $stand)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white text-sm"
                            style="background-color: #007A3D;">
                            {{ $stand->numero_stand }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-700 font-medium">{{ $stand->evenement->nom ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $stand->superficie }} m²</td>
                    <td class="px-6 py-4">
                        @if($stand->standing == 'vip')
                            <span class="px-3 py-1 rounded-full text-xs text-white bg-yellow-500">VIP</span>
                        @elseif($stand->standing == 'premium')
                            <span class="px-3 py-1 rounded-full text-xs text-white bg-blue-600">Premium</span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs text-white" style="background-color: #007A3D;">Standard</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-store text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun stand réservé</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>