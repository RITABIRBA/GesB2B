<div>

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Catalogue des Entreprises</h3>
            @if($catalogueDisponible)
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $entreprises->count() }} entreprise(s)
            </span>
            @endif
        </div>

        {{-- Filtre événement --}}
        <select wire:model.live="id_evenement"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">-- Choisir un événement --</option>
            @foreach($evenements as $ev)
            <option value="{{ $ev->id }}">{{ $ev->nom }}</option>
            @endforeach
        </select>
    </div>

    {{-- Info événement sélectionné --}}
    @if($evenement)
    <div class="bg-white rounded-xl shadow p-4 mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold"
                style="background-color: #007A3D;">
                <i class="fa-solid fa-calendar"></i>
            </div>
            <div>
                <p class="font-bold text-gray-800">{{ $evenement->nom }}</p>
                <p class="text-xs text-gray-400">
                    Du {{ $evenement->date_debut }} au {{ $evenement->date_fin }}
                </p>
            </div>
        </div>
        <div>
            @if($catalogueDisponible)
                <span class="px-3 py-1 rounded-full text-xs text-white font-medium"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-circle-check mr-1"></i> Catalogue ouvert
                </span>
            @else
                <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">
                    <i class="fa-solid fa-clock mr-1"></i>
                    Disponible après le {{ $evenement->date_fin }}
                </span>
            @endif
        </div>
    </div>
    @endif

    {{-- Catalogue pas encore disponible --}}
    @if(!$catalogueDisponible)
    <div class="bg-white rounded-xl shadow p-16 text-center">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4"
            style="background-color: #fef3c7;">
            <i class="fa-solid fa-lock text-4xl text-yellow-500"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">
            Catalogue non disponible
        </h3>
        @if($evenement)
        <p class="text-gray-500 mb-2">
            Le catalogue sera accessible après la clôture des inscriptions.
        </p>
        <p class="text-sm text-gray-400">
            <i class="fa-solid fa-calendar mr-1"></i>
            Date de clôture : <strong>{{ $evenement->date_fin }}</strong>
        </p>
        @else
        <p class="text-gray-500">
            Veuillez sélectionner un événement pour voir le catalogue.
        </p>
        @endif
    </div>

    {{-- Catalogue disponible --}}
    @else

    {{-- Filtres --}}
    <div class="flex gap-4 mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher une entreprise..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none text-sm">
        </div>
        <select wire:model.live="secteur_filtre"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Tous les secteurs</option>
            @foreach($secteurs as $secteur)
            <option value="{{ $secteur }}">{{ $secteur }}</option>
            @endforeach
        </select>
    </div>

    {{-- Liste entreprises --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($entreprises as $entreprise)
        <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0"
                    style="background-color: #007A3D;">
                    {{ strtoupper(substr($entreprise->nom, 0, 1)) }}
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">{{ $entreprise->nom }}</h4>
                    <p class="text-xs text-gray-400">{{ $entreprise->pays }}</p>
                </div>
            </div>
            <div class="space-y-2 mb-4">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fa-solid fa-industry text-gray-400"></i>
                    {{ $entreprise->secteur_activite }}
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fa-solid fa-location-dot text-gray-400"></i>
                    {{ $entreprise->ville }}
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fa-solid fa-phone text-gray-400"></i>
                    {{ $entreprise->contact }}
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fa-solid fa-users text-gray-400"></i>
                    {{ $entreprise->participants->count() }} participant(s)
                </div>
            </div>
            <div class="pt-3 border-t flex items-center justify-between">
                <span class="text-xs px-3 py-1 rounded-full font-medium text-white"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-circle-check mr-1"></i> Validée
                </span>
                <span class="text-xs text-gray-400">{{ $entreprise->contact }}</span>
            </div>
        </div>
        @empty
        <div class="col-span-3 py-16 text-center text-gray-400">
            <i class="fa-solid fa-book-open text-5xl mb-3 block text-gray-300"></i>
            <p class="text-lg font-medium">Aucune entreprise dans le catalogue</p>
        </div>
        @endforelse
    </div>

    @endif

</div>