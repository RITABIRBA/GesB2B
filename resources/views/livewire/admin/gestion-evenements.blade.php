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
            <h3 class="text-xl font-bold text-gray-700">Liste des événements</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #007A3D;">
                {{ $evenements->count() }} événement(s)
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Nouvel événement
        </button>
    </div>

    {{-- Recherche --}}
    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher par nom ou ville..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left" style="min-width: 1100px;">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Nom & Type</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Lieu</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Dates</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Inscriptions</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Paiement</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Salle RDV</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Stands</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Souhaits</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">RDV</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evenements as $evenement)
                @php $estB2B = ($evenement->type_evenement ?? 'avec_b2b') === 'avec_b2b'; @endphp
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-4 py-4">
                        <p class="font-bold text-gray-800 text-sm">{{ $evenement->nom }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium text-white mt-1 inline-block"
                            style="background-color: #007A3D;">
                            {{ $evenement->typeEvenement->nom ?? '-' }}
                        </span>
                        @if($estB2B)
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold text-white mt-1 inline-block bg-blue-600">
                            <i class="fa-solid fa-handshake mr-1"></i> B2B
                        </span>
                        @else
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold text-white mt-1 inline-block bg-purple-600">
                            <i class="fa-solid fa-calendar-star mr-1"></i> Sans B2B
                        </span>
                        @endif
                        <p class="text-xs text-gray-400 mt-0.5">{{ $evenement->annee }}</p>
                    </td>
                    <td class="px-4 py-4 text-sm">
                        <p class="text-gray-700 font-medium">
                            <i class="fa-solid fa-location-dot text-gray-400 mr-1"></i>
                            {{ $evenement->ville }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $evenement->lieu }}</p>
                    </td>
                    <td class="px-4 py-4 text-xs">
                        <p class="text-gray-600">
                            <i class="fa-solid fa-calendar-plus text-green-500 mr-1"></i>
                            {{ $evenement->date_debut }}
                        </p>
                        <p class="text-gray-600 mt-0.5">
                            <i class="fa-solid fa-calendar-minus text-red-400 mr-1"></i>
                            {{ $evenement->date_fin }}
                        </p>
                        <p class="text-gray-400 mt-0.5">
                            <i class="fa-solid fa-clock text-gray-300 mr-1"></i>
                            {{ $evenement->heure_debut }} - {{ $evenement->heure_fin }}
                        </p>
                        @if($evenement->date_limite_rdv && $estB2B)
                        <p class="text-orange-500 mt-0.5">
                            <i class="fa-solid fa-heart-crack mr-1"></i>
                            Limite souhaits : {{ $evenement->date_limite_rdv }}
                        </p>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-xs">
                        @if($evenement->date_ouverture_inscriptions)
                        <p class="text-green-600">
                            <i class="fa-solid fa-door-open mr-1"></i>
                            {{ $evenement->date_ouverture_inscriptions }}
                        </p>
                        @endif
                        @if($evenement->date_cloture_inscriptions)
                        <p class="text-red-500 mt-0.5">
                            <i class="fa-solid fa-door-closed mr-1"></i>
                            {{ $evenement->date_cloture_inscriptions }}
                        </p>
                        @endif
                        @php
                            $today   = now()->toDateString();
                            $ouvert  = !$evenement->date_ouverture_inscriptions || $today >= $evenement->date_ouverture_inscriptions;
                            $nonClos = !$evenement->date_cloture_inscriptions || $today <= $evenement->date_cloture_inscriptions;
                        @endphp
                        @if($ouvert && $nonClos)
                        <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium mt-1 inline-block">
                            <i class="fa-solid fa-circle-check mr-1"></i> Ouvertes
                        </span>
                        @elseif(!$ouvert)
                        <span class="px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 font-medium mt-1 inline-block">
                            <i class="fa-solid fa-clock mr-1"></i> Pas encore
                        </span>
                        @else
                        <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-medium mt-1 inline-block">
                            <i class="fa-solid fa-lock mr-1"></i> Fermées
                        </span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        @if($evenement->type_paiement == 'gratuit')
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium text-white bg-green-500 block w-fit">
                            <i class="fa-solid fa-gift mr-1"></i> Gratuit
                        </span>
                        @else
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium text-white bg-purple-600 block w-fit">
                            <i class="fa-solid fa-money-bill-wave mr-1"></i> Payant
                        </span>
                        @endif
                        @if($evenement->type_paiement != 'gratuit')
                        <p class="text-sm font-bold mt-1" style="color: #007A3D;">
                            {{ number_format($evenement->montant_inscription, 0, ',', ' ') }} FCFA
                        </p>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-xs">
                        @if($evenement->nom_salle)
                        <p class="text-gray-700 font-medium">
                            <i class="fa-solid fa-door-open text-blue-500 mr-1"></i>
                            {{ $evenement->nom_salle }}
                        </p>
                        <p class="text-gray-500 mt-0.5">
                            <i class="fa-solid fa-table text-gray-400 mr-1"></i>
                            {{ $evenement->nombre_tables }} table(s)
                        </p>
                        @else
                        <span class="text-gray-400 italic">Non définie</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-xs">
                        @if($evenement->nombre_stands)
                        <p class="text-gray-700 font-medium">
                            <i class="fa-solid fa-store text-green-600 mr-1"></i>
                            {{ $evenement->nombre_stands }} stand(s)
                        </p>
                        @else
                        <span class="text-gray-400 italic">Aucun</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-xs">
                        @if($estB2B)
                        <div class="flex items-center gap-1">
                            <span class="px-2 py-1 rounded-lg bg-orange-100 text-orange-700 font-bold">
                                min {{ $evenement->min_souhaits ?? 5 }}
                            </span>
                            <span class="text-gray-400">—</span>
                            <span class="px-2 py-1 rounded-lg bg-blue-100 text-blue-700 font-bold">
                                max {{ $evenement->max_souhaits ?? 20 }}
                            </span>
                        </div>
                        @else
                        <span class="text-gray-400 italic">Sans B2B</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-xs">
                        @if($estB2B)
                        <span class="px-2 py-1 rounded-lg bg-purple-100 text-purple-700 font-bold block w-fit">
                            {{ $evenement->duree_rdv ?? 20 }} min / RDV
                        </span>
                        @else
                        <span class="text-gray-400 italic">Sans B2B</span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex gap-2">
                            <button wire:click="modifier({{ $evenement->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="supprimer({{ $evenement->id }})"
                                wire:confirm="Voulez-vous vraiment supprimer cet événement ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-calendar-xmark text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun événement pour le moment</p>
                        <button wire:click="openModal"
                            class="mt-3 px-5 py-2 rounded-xl text-white text-sm font-medium"
                            style="background-color: #C8102E;">
                            Créer le premier événement
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL PRINCIPAL --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b sticky top-0 z-10"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-plus' }}"></i>
                    {{ $isEditing ? "Modifier l'événement" : 'Nouvel événement' }}
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl transition">
                    &times;
                </button>
            </div>

            <div class="p-8 space-y-5">

                {{-- Format B2B --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-star" style="color: #007A3D;"></i>
                        Format de l'événement *
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="type_evenement" value="avec_b2b" class="hidden peer">
                            <div class="p-4 border-2 rounded-xl transition text-center
                                peer-checked:border-green-500 peer-checked:bg-green-50
                                hover:bg-gray-50 border-gray-200">
                                <i class="fa-solid fa-handshake text-2xl mb-2 block" style="color: #007A3D;"></i>
                                <p class="font-bold text-gray-800 text-sm">Avec B2B</p>
                                <p class="text-xs text-gray-400 mt-1">Souhaits, RDV, planning automatique</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="type_evenement" value="sans_b2b" class="hidden peer">
                            <div class="p-4 border-2 rounded-xl transition text-center
                                peer-checked:border-purple-500 peer-checked:bg-purple-50
                                hover:bg-gray-50 border-gray-200">
                                <i class="fa-solid fa-calendar-check text-2xl mb-2 block text-purple-500"></i>
                                <p class="font-bold text-gray-800 text-sm">Sans B2B</p>
                                <p class="text-xs text-gray-400 mt-1">Inscription simple, ouvert à tous</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Informations générales --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info" style="color: #C8102E;"></i>
                        Informations générales
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Nom de l'événement *</label>
                            <input wire:model="nom" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                                placeholder="Ex: Africallia 2026">
                            @error('nom') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-2">Catégorie d'événement *</label>
                            <div class="flex gap-6 mb-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="utiliser_nouveau_type" value="" class="accent-green-700">
                                    <span class="text-sm text-gray-600">Catégorie existante</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="utiliser_nouveau_type" value="1" class="accent-red-600">
                                    <span class="text-sm text-gray-600">Nouvelle catégorie</span>
                                </label>
                            </div>
                            @if($utiliser_nouveau_type !== '1')
                            <select wire:model="id_type_evenement"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                                <option value="">-- Choisir --</option>
                                @foreach($typeEvenements as $type)
                                <option value="{{ $type->id }}">{{ $type->nom }}</option>
                                @endforeach
                            </select>
                            @error('id_type_evenement') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            @else
                            <input wire:model="nouveau_type" type="text"
                                placeholder="Ex: Foire internationale..."
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                            @error('nouveau_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            @endif
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Année *</label>
                            <input wire:model="annee" type="number" min="2000" max="2100"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                                placeholder="2026">
                            @error('annee') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Ville *</label>
                            <input wire:model="ville" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                                placeholder="Ex: Ouagadougou">
                            @error('ville') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Lieu *</label>
                            <input wire:model="lieu" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white"
                                placeholder="Ex: Palais des Sports de Ouaga 2000">
                            @error('lieu') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Dates événement --}}
                <div class="bg-blue-50 rounded-xl p-5 border border-blue-200">
                    <h4 class="font-bold text-blue-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-calendar"></i>
                        Dates de l'événement
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1">Date début *</label>
                            <input wire:model="date_debut" type="date"
                                class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm bg-white">
                            @error('date_debut') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1">Date fin *</label>
                            <input wire:model="date_fin" type="date"
                                class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm bg-white">
                            @error('date_fin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1">Heure début *</label>
                            <input wire:model="heure_debut" type="time"
                                class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm bg-white">
                            @error('heure_debut') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1">Heure fin *</label>
                            <input wire:model="heure_fin" type="time"
                                class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm bg-white">
                            @error('heure_fin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Dates inscriptions --}}
                <div class="bg-green-50 rounded-xl p-5 border border-green-200">
                    <h4 class="font-bold text-green-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-user-plus"></i>
                        Dates des inscriptions
                        <span class="text-green-500 font-normal text-xs">(optionnel)</span>
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1">
                                <i class="fa-solid fa-door-open text-green-500 mr-1"></i>
                                Ouverture des inscriptions
                            </label>
                            <input wire:model="date_ouverture_inscriptions" type="date"
                                class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm bg-white">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1">
                                <i class="fa-solid fa-door-closed text-red-500 mr-1"></i>
                                Clôture des inscriptions
                            </label>
                            <input wire:model="date_cloture_inscriptions" type="date"
                                class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm bg-white">
                        </div>
                    </div>
                    <p class="text-xs text-green-600 mt-2">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Si non renseignées, les inscriptions seront toujours ouvertes.
                    </p>
                </div>

                {{-- Salle RDV --}}
                <div class="bg-blue-50 rounded-xl p-5 border border-blue-200">
                    <h4 class="font-bold text-blue-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-door-open"></i>
                        Salle des rendez-vous
                        <span class="text-blue-500 font-normal text-xs">(optionnel)</span>
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1">Nom de la salle</label>
                            <input wire:model="nom_salle" type="text"
                                class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm bg-white"
                                placeholder="Ex: Salle B2B">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1">Nombre de tables</label>
                            <input wire:model="nombre_tables" type="number" min="1" max="500"
                                class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm bg-white"
                                placeholder="Ex: 20">
                        </div>
                    </div>
                </div>

                {{-- Config B2B --}}
                @if($type_evenement === 'avec_b2b')
                <div class="bg-orange-50 rounded-xl p-5 border border-orange-200">
                    <h4 class="font-bold text-orange-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-handshake"></i>
                        Configuration B2B
                    </h4>
                    <div class="mb-4">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            <i class="fa-solid fa-heart-crack text-red-400 mr-1"></i>
                            Date limite de prise de RDV
                            <span class="text-gray-400 font-normal">(optionnelle)</span>
                        </label>
                        <input wire:model="date_limite_rdv" type="date"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white">
                        <p class="text-xs text-orange-600 mt-1">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            Passé cette date, les souhaits de RDV seront désactivés automatiquement.
                        </p>
                        @error('date_limite_rdv') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1">Minimum de souhaits *</label>
                            <input wire:model="min_souhaits" type="number" min="1" max="50"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: 5">
                            @error('min_souhaits') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1">Maximum de souhaits *</label>
                            <input wire:model="max_souhaits" type="number" min="1" max="100"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: 20">
                            @error('max_souhaits') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1">Durée d'un RDV (minutes) *</label>
                            <input wire:model="duree_rdv" type="number" min="5" max="120"
                                class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm bg-white"
                                placeholder="Ex: 20">
                            @error('duree_rdv') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex items-end">
                            @if($duree_rdv && $heure_debut && $heure_fin)
                            @php
                                try {
                                    $debut   = \Carbon\Carbon::createFromFormat('H:i', substr($heure_debut, 0, 5));
                                    $fin     = \Carbon\Carbon::createFromFormat('H:i', substr($heure_fin, 0, 5));
                                    $total   = $debut->diffInMinutes($fin);
                                    $nbRdv   = $duree_rdv > 0 ? floor($total / $duree_rdv) : 0;
                                    $nbTotal = $nbRdv * (int)($nombre_tables ?: 1);
                                } catch (\Exception $e) { $nbRdv = 0; $nbTotal = 0; }
                            @endphp
                            @if($nbRdv > 0)
                            <div class="bg-white rounded-xl p-3 border border-orange-200 text-xs text-orange-700 w-full">
                                <i class="fa-solid fa-circle-check text-orange-500 mr-1"></i>
                                <strong>{{ $nbRdv }}</strong> créneaux/table
                                · <strong>{{ $nbTotal }}</strong> RDV au total
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Paiement --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-money-bill" style="color: #007A3D;"></i>
                        Paiement des inscriptions *
                    </h4>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="type_paiement" value="gratuit" class="hidden peer">
                            <div class="p-4 border-2 rounded-xl text-center transition
                                peer-checked:border-green-500 peer-checked:bg-green-50
                                hover:bg-gray-50 border-gray-200">
                                <i class="fa-solid fa-gift text-2xl text-green-500 mb-1 block"></i>
                                <span class="text-sm font-semibold text-gray-700">Gratuit</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="type_paiement" value="payant" class="hidden peer">
                            <div class="p-4 border-2 rounded-xl text-center transition
                                peer-checked:border-red-500 peer-checked:bg-red-50
                                hover:bg-gray-50 border-gray-200">
                                <i class="fa-solid fa-money-bill-wave text-2xl text-red-500 mb-1 block"></i>
                                <span class="text-sm font-semibold text-gray-700">Payant</span>
                            </div>
                        </label>
                    </div>
                    @if($type_paiement !== 'gratuit')
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Montant de l'inscription (FCFA) *</label>
                        <input wire:model="montant_inscription" type="number" min="0"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm bg-white"
                            placeholder="Ex: 50000">
                        @error('montant_inscription') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    @endif
                </div>

                {{-- Types de stands --}}
                <div class="bg-yellow-50 rounded-xl p-5 border border-yellow-200">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-bold text-yellow-700 flex items-center gap-2">
                            <i class="fa-solid fa-store"></i>
                            Types de stands
                            <span class="text-yellow-600 font-normal text-xs">(optionnel)</span>
                        </h4>
                        <button type="button" wire:click="ouvrirAjoutTypeStand"
                            class="px-3 py-1.5 rounded-lg text-white text-xs font-medium flex items-center gap-1"
                            style="background-color: #C8102E;">
                            <i class="fa-solid fa-plus"></i> Ajouter un type
                        </button>
                    </div>

                    @if(empty($types_stands))
                    <div class="text-center py-4 text-gray-400 text-sm">
                        <i class="fa-solid fa-store text-3xl mb-2 block text-gray-300"></i>
                        Aucun type de stand défini. Cliquez sur "Ajouter un type" pour en créer.
                    </div>
                    @else
                    <div class="space-y-2">
                        @foreach($types_stands as $i => $ts)
                        <div class="bg-white rounded-xl p-4 border border-yellow-200 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                    style="background-color: #007A3D;">
                                    {{ strtoupper(substr($ts['standing'], 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm capitalize">{{ $ts['standing'] }}</p>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 mt-0.5 flex-wrap">
                                        <span class="font-semibold text-blue-600">
                                            <i class="fa-solid fa-layer-group mr-1"></i>
                                            {{ $ts['quantite'] ?? 1 }} stand(s)
                                        </span>
                                        @if($ts['superficie'])
                                        <span><i class="fa-solid fa-ruler-combined mr-1"></i>{{ $ts['superficie'] }}</span>
                                        @endif
                                        @if($ts['est_gratuit'])
                                        <span class="text-green-600 font-semibold">
                                            <i class="fa-solid fa-gift mr-1"></i>Gratuit
                                        </span>
                                        @else
                                        <span class="font-semibold" style="color: #C8102E;">
                                            <i class="fa-solid fa-money-bill mr-1"></i>
                                            {{ number_format($ts['montant'], 0, ',', ' ') }} FCFA/stand
                                        </span>
                                        @endif
                                        @if(!empty($ts['composants']))
                                        <span class="text-purple-600">
                                            <i class="fa-solid fa-box mr-1"></i>
                                            {{ count($ts['composants']) }} élément(s)
                                        </span>
                                        @endif
                                    </div>
                                    @if(!empty($ts['composants']))
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($ts['composants'] as $comp)
                                        <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">
                                            {{ $comp['qte'] }}x {{ $comp['nom'] }}
                                        </span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-2 flex-shrink-0">
                                <button type="button" wire:click="ouvrirModifierTypeStand({{ $i }})"
                                    class="px-2.5 py-1.5 rounded-lg text-white text-xs bg-blue-600 hover:bg-blue-700 transition">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="button" wire:click="supprimerTypeStand({{ $i }})"
                                    class="px-2.5 py-1.5 rounded-lg text-white text-xs bg-red-600 hover:bg-red-700 transition">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3 bg-white rounded-xl p-3 border border-yellow-300 flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700">
                            <i class="fa-solid fa-calculator mr-1 text-yellow-600"></i>
                            Total de stands (calculé automatiquement)
                        </span>
                        <span class="text-lg font-bold" style="color: #007A3D;">
                            {{ collect($types_stands)->sum(fn($t) => (int)($t['quantite'] ?? 0)) }} stand(s)
                        </span>
                    </div>
                    @endif
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-8 py-4 bg-gray-50 border-t flex justify-end gap-3 rounded-b-2xl">
                <button type="button" wire:click="closeModal"
                    class="px-5 py-2 rounded-xl text-gray-500 hover:bg-gray-100 transition text-sm font-medium">
                    Annuler
                </button>
                <button type="button" wire:click="sauvegarder"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-not-allowed"
                    class="px-6 py-2 rounded-xl text-white font-medium text-sm transition hover:opacity-90 shadow"
                    style="background-color: #007A3D;">
                    <span wire:loading.remove>
                        <i class="fa-solid fa-floppy-disk mr-1"></i>
                        {{ $isEditing ? 'Enregistrer les modifications' : "Créer l'événement" }}
                    </span>
                    <span wire:loading>
                        <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                        Enregistrement...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL TYPE DE STAND — avec liste déroulante pour le standing ✅ --}}
    @if($showTypeStandModal)
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center px-6 py-4 border-b sticky top-0 z-10"
                style="background: linear-gradient(135deg, #C8102E, #a00d25);">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-store"></i>
                    {{ $typeStandIndex === -1 ? 'Ajouter un type de stand' : 'Modifier le type de stand' }}
                </h3>
                <button wire:click="$set('showTypeStandModal', false)"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-6 space-y-4">

                {{-- ✅ CORRECTION : Liste déroulante au lieu d'input texte --}}
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Standing *</label>
                    <select wire:model="ts_standing"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-white">
                        <option value="">-- Choisir le standing --</option>
                        <option value="standard">Standard</option>
                        <option value="premium">Premium</option>
                        <option value="vip">VIP</option>
                    </select>
                    @error('ts_standing') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">
                        Nombre de stands de ce standing *
                    </label>
                    <input wire:model="ts_quantite" type="number" min="1" max="200"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                        placeholder="Ex: 10">
                    <p class="text-xs text-gray-400 mt-1">
                        Combien de stands de ce type seront générés.
                    </p>
                    @error('ts_quantite') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">
                        Superficie <span class="text-gray-400 font-normal">(optionnel)</span>
                    </label>
                    <input wire:model="ts_superficie" type="text"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                        placeholder="Ex: 9m², 18m²...">
                </div>

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-2">Tarif</label>
                    <div class="flex gap-4 mb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model.live="ts_est_gratuit" class="w-4 h-4">
                            <span class="text-sm text-gray-600">Stand gratuit</span>
                        </label>
                    </div>
                    @if(!$ts_est_gratuit)
                    <input wire:model="ts_montant" type="number" min="0"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none text-sm"
                        placeholder="Ex: 150000">
                    <p class="text-xs text-gray-400 mt-1">Montant en FCFA (par stand)</p>
                    @else
                    <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-xs text-green-700">
                        <i class="fa-solid fa-gift mr-1"></i>
                        Ce type de stand sera gratuit pour les participants assignés.
                    </div>
                    @endif
                </div>

                {{-- Composants --}}
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-2">
                        Composition du stand
                        <span class="text-gray-400 font-normal">(éléments inclus)</span>
                    </label>
                    @if(!empty($ts_composants))
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($ts_composants as $i => $comp)
                        <span class="flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-1.5 rounded-xl text-xs font-medium">
                            <span>{{ $comp['qte'] }}x {{ $comp['nom'] }}</span>
                            <button type="button" wire:click="supprimerComposant({{ $i }})"
                                class="text-blue-400 hover:text-red-500 transition">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </span>
                        @endforeach
                    </div>
                    @endif
                    <div class="flex gap-2">
                        <input wire:model="ts_composant_nom" type="text"
                            class="flex-1 border rounded-xl px-3 py-2 text-sm focus:outline-none"
                            placeholder="Ex: Table, Chaise, Prise électrique..."
                            wire:keydown.enter.prevent="ajouterComposant">
                        <input wire:model="ts_composant_qte" type="number" min="1"
                            class="w-16 border rounded-xl px-3 py-2 text-sm focus:outline-none text-center"
                            placeholder="Qté">
                        <button type="button" wire:click="ajouterComposant"
                            class="px-4 py-2 rounded-xl text-white text-sm font-medium"
                            style="background-color: #007A3D;">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Appuyez sur Entrée ou cliquez + pour ajouter</p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showTypeStandModal', false)"
                        class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 text-sm">
                        Annuler
                    </button>
                    <button type="button" wire:click="sauvegarderTypeStand"
                        class="px-5 py-2.5 rounded-xl text-white font-medium text-sm"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-floppy-disk mr-1"></i>
                        {{ $typeStandIndex === -1 ? 'Ajouter' : 'Modifier' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>