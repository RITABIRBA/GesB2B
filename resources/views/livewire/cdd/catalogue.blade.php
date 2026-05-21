<div>
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Catalogue des Entreprises</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #2d5a8e;">
                {{ $entreprises->count() }} entreprise(s)
            </span>
        </div>
    </div>

    {{-- Recherche --}}
    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher une entreprise..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none text-sm">
        </div>
    </div>

    {{-- Grille des entreprises --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($entreprises as $entreprise)
        <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg"
                    style="background-color: #2d5a8e;">
                    {{ strtoupper(substr($entreprise->nom, 0, 1)) }}
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">{{ $entreprise->nom }}</h4>
                    <p class="text-xs text-gray-400">{{ $entreprise->pays }}</p>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fa-solid fa-industry text-gray-400"></i>
                    {{ $entreprise->secteur_activite }}
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fa-solid fa-location-dot text-gray-400"></i>
                    {{ $entreprise->ville }}
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fa-solid fa-users text-gray-400"></i>
                    {{ $entreprise->participants->count() }} participant(s)
                </div>
            </div>
            <div class="mt-4 pt-4 border-t">
                <span class="text-xs px-3 py-1 rounded-full font-medium text-white"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-circle-check mr-1"></i> Validée
                </span>
            </div>
        </div>
        @empty
        <div class="col-span-3 py-16 text-center text-gray-400">
            <i class="fa-solid fa-book-open text-5xl mb-3 block text-gray-300"></i>
            <p class="text-lg font-medium">Aucune entreprise dans le catalogue</p>
        </div>
        @endforelse
    </div>
</div>