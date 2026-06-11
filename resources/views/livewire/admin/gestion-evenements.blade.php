<div>
    {{-- Message succès --}}
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
        <table class="w-full text-left" style="min-width: 900px;">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Nom & Type</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Lieu</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Dates</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Inscriptions</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Paiement</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Salle RDV</th>
                    {{-- ← Nouvelle colonne souhaits --}}
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Souhaits</th>
                    <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evenements as $evenement)
                <tr class="border-b hover:bg-gray-50 transition">

                    <td class="px-4 py-4">
                        <p class="font-bold text-gray-800 text-sm">{{ $evenement->nom }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium text-white mt-1 inline-block"
                            style="background-color: #007A3D;">
                            {{ $evenement->typeEvenement->nom ?? '-' }}
                        </span>
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
                            $today = now()->toDateString();
                            $ouvert = !$evenement->date_ouverture_inscriptions
                                || $today >= $evenement->date_ouverture_inscriptions;
                            $nonClos = !$evenement->date_cloture_inscriptions
                                || $today <= $evenement->date_cloture_inscriptions;
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
                        @elseif($evenement->type_paiement == 'par_participant')
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium text-white bg-blue-600 block w-fit">
                            <i class="fa-solid fa-user mr-1"></i> Par participant
                        </span>
                        @else
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium text-white bg-purple-600 block w-fit">
                            <i class="fa-solid fa-building mr-1"></i> Par entreprise
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

                    {{-- ← Colonne souhaits --}}
                    <td class="px-4 py-4 text-xs">
                        <div class="flex items-center gap-1">
                            <span class="px-2 py-1 rounded-lg bg-orange-100 text-orange-700 font-bold">
                                min {{ $evenement->min_souhaits ?? 5 }}
                            </span>
                            <span class="text-gray-400">—</span>
                            <span class="px-2 py-1 rounded-lg bg-blue-100 text-blue-700 font-bold">
                                max {{ $evenement->max_souhaits ?? 20 }}
                            </span>
                        </div>
                    </td>

                    <td class="px-4 py-4">
                        <div class="flex gap-2">
                            <button wire:click="modifier({{ $evenement->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90 flex items-center gap-1"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="supprimer({{ $evenement->id }})"
                                wire:confirm="Voulez-vous vraiment supprimer cet événement ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-16 text-center text-gray-400">
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

    {{-- MODAL --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-plus' }}"></i>
                    {{ $isEditing ? 'Modifier l\'événement' : 'Nouvel événement' }}
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl transition">
                    &times;
                </button>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-2 gap-5">

                    {{-- Nom --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Nom de l'événement *
                        </label>
                        <input wire:model="nom" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: Africallia 2026">
                        @error('nom') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Type événement --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-2">
                            Type d'événement *
                        </label>
                        <div class="flex gap-6 mb-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model.live="utiliser_nouveau_type" value="" class="accent-green-700">
                                <span class="text-sm text-gray-600">Type existant</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model.live="utiliser_nouveau_type" value="1" class="accent-red-600">
                                <span class="text-sm text-gray-600">Nouveau type</span>
                            </label>
                        </div>
                        @if($utiliser_nouveau_type !== '1')
                        <select wire:model="id_type_evenement"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            <option value="">-- Choisir un type --</option>
                            @foreach($typeEvenements as $type)
                            <option value="{{ $type->id }}">{{ $type->nom }}</option>
                            @endforeach
                        </select>
                        @error('id_type_evenement') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        @else
                        <input wire:model="nouveau_type" type="text"
                            placeholder="Ex: Foire internationale..."
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                        @error('nouveau_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        @endif
                    </div>

                    {{-- Année --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Année *</label>
                        <input wire:model="annee" type="number" min="2000" max="2100"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="2026">
                        @error('annee') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Ville --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Ville *</label>
                        <input wire:model="ville" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: Ouagadougou">
                        @error('ville') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Dates événement --}}
                    <div class="col-span-2">
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <p class="text-xs font-bold text-blue-700 mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-calendar"></i>
                                Dates de l'événement
                            </p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-gray-600 text-xs font-medium mb-1">Date début *</label>
                                    <input wire:model="date_debut" type="date"
                                        class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm">
                                    @error('date_debut') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-600 text-xs font-medium mb-1">Date fin *</label>
                                    <input wire:model="date_fin" type="date"
                                        class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm">
                                    @error('date_fin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-600 text-xs font-medium mb-1">Heure début *</label>
                                    <input wire:model="heure_debut" type="time"
                                        class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm">
                                    @error('heure_debut') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-600 text-xs font-medium mb-1">Heure fin *</label>
                                    <input wire:model="heure_fin" type="time"
                                        class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm">
                                    @error('heure_fin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Dates inscriptions --}}
                    <div class="col-span-2">
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <p class="text-xs font-bold text-green-700 mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-user-plus"></i>
                                Dates des inscriptions
                                <span class="text-green-500 font-normal">(optionnel)</span>
                            </p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-gray-600 text-xs font-medium mb-1">
                                        <i class="fa-solid fa-door-open text-green-500 mr-1"></i>
                                        Ouverture
                                    </label>
                                    <input wire:model="date_ouverture_inscriptions" type="date"
                                        class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm">
                                </div>
                                <div>
                                    <label class="block text-gray-600 text-xs font-medium mb-1">
                                        <i class="fa-solid fa-door-closed text-red-500 mr-1"></i>
                                        Clôture
                                    </label>
                                    <input wire:model="date_cloture_inscriptions" type="date"
                                        class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm">
                                </div>
                            </div>
                            <p class="text-xs text-green-600 mt-2">
                                <i class="fa-solid fa-circle-info mr-1"></i>
                                Si non renseignées, les inscriptions seront toujours ouvertes.
                            </p>
                        </div>
                    </div>

                    {{-- Lieu --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Lieu *</label>
                        <input wire:model="lieu" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: Palais des Sports de Ouaga 2000">
                        @error('lieu') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Salle RDV --}}
                    <div class="col-span-2">
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <p class="text-xs font-bold text-blue-700 mb-1 flex items-center gap-2">
                                <i class="fa-solid fa-door-open"></i>
                                Salle des rendez-vous
                                <span class="text-blue-500 font-normal">(optionnel)</span>
                            </p>
                            <p class="text-xs text-blue-600 mb-3">
                                <i class="fa-solid fa-circle-info mr-1"></i>
                                La salle et le numéro de table seront communiqués aux participants lors de leurs RDV.
                            </p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-gray-600 text-xs font-medium mb-1">Nom de la salle</label>
                                    <input wire:model="nom_salle" type="text"
                                        class="w-full border rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm"
                                        placeholder="Ex: Salle B2B...">
                                    @error('nom_salle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-600 text-xs font-medium mb-1">Nombre de tables</label>
                                    <input wire:model="nombre_tables" type="number" min="1" max="500"
                                        class="w-full border rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm"
                                        placeholder="Ex: 20">
                                    @error('nombre_tables') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            @if($nom_salle && $nombre_tables)
                            <div class="mt-3 bg-white rounded-xl p-3 border border-blue-200 text-xs text-blue-700 flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-blue-500"></i>
                                Les RDV se tiendront dans <strong>{{ $nom_salle }}</strong>
                                sur <strong>{{ (int) $nombre_tables }} tables</strong>
                                numérotées de 1 à {{ (int) $nombre_tables }}.
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- ← BLOC MIN/MAX SOUHAITS --}}
                    <div class="col-span-2">
                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                            <p class="text-xs font-bold text-orange-700 mb-1 flex items-center gap-2">
                                <i class="fa-solid fa-heart"></i>
                                Limites des souhaits de RDV *
                            </p>
                            <p class="text-xs text-orange-600 mb-3">
                                <i class="fa-solid fa-circle-info mr-1"></i>
                                Définissez le nombre minimum et maximum de souhaits
                                qu'un participant peut émettre pour cet événement.
                            </p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-600 text-xs font-medium mb-1">
                                        <i class="fa-solid fa-arrow-down text-orange-500 mr-1"></i>
                                        Minimum de souhaits *
                                    </label>
                                    <input wire:model="min_souhaits" type="number"
                                        min="1" max="50"
                                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-300 text-sm bg-white"
                                        placeholder="Ex: 5">
                                    @error('min_souhaits')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-600 text-xs font-medium mb-1">
                                        <i class="fa-solid fa-arrow-up text-blue-500 mr-1"></i>
                                        Maximum de souhaits *
                                    </label>
                                    <input wire:model="max_souhaits" type="number"
                                        min="1" max="100"
                                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm bg-white"
                                        placeholder="Ex: 20">
                                    @error('max_souhaits')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @if($min_souhaits && $max_souhaits)
                            <div class="mt-3 bg-white rounded-xl p-3 border border-orange-200 text-xs text-orange-700 flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-orange-500"></i>
                                Chaque participant devra émettre entre
                                <strong>{{ $min_souhaits }}</strong> et
                                <strong>{{ $max_souhaits }}</strong> souhaits.
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- TYPE DE PAIEMENT --}}
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-3">
                            <i class="fa-solid fa-money-bill mr-1" style="color: #007A3D;"></i>
                            Type de paiement *
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button"
                                wire:click="$set('type_paiement', 'gratuit')"
                                class="border-2 rounded-xl p-4 text-left transition
                                    {{ $type_paiement === 'gratuit' ? 'border-green-400 bg-green-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fa-solid fa-gift text-green-500"></i>
                                    <p class="font-semibold text-sm text-gray-800">Gratuit</p>
                                </div>
                                <p class="text-xs text-gray-400">Aucun frais d'inscription</p>
                            </button>
                            <button type="button"
                                wire:click="$set('type_paiement', 'par_participant')"
                                class="border-2 rounded-xl p-4 text-left transition
                                    {{ $type_paiement === 'par_participant' ? 'border-blue-400 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fa-solid fa-user text-blue-500"></i>
                                    <p class="font-semibold text-sm text-gray-800">Par participant</p>
                                </div>
                                <p class="text-xs text-gray-400">Chaque participant paie individuellement</p>
                            </button>
                            <button type="button"
                                wire:click="$set('type_paiement', 'par_entreprise')"
                                class="border-2 rounded-xl p-4 text-left transition
                                    {{ $type_paiement === 'par_entreprise' ? 'border-purple-400 bg-purple-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fa-solid fa-building text-purple-500"></i>
                                    <p class="font-semibold text-sm text-gray-800">Par entreprise</p>
                                </div>
                                <p class="text-xs text-gray-400">L'entreprise paie un montant global</p>
                            </button>
                        </div>
                    </div>

                    {{-- Montant --}}
                    @if($type_paiement !== 'gratuit')
                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            @if($type_paiement === 'par_participant')
                                Montant par participant (FCFA) *
                            @else
                                Montant global par entreprise (FCFA) *
                            @endif
                        </label>
                        <input wire:model="montant_inscription" type="number" min="1"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: 50000">
                        @error('montant_inscription') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    @else
                    <div class="col-span-2">
                        <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-700 flex items-center gap-2">
                            <i class="fa-solid fa-circle-check"></i>
                            Événement gratuit — Les participants seront validés directement.
                        </div>
                    </div>
                    @endif

                    {{-- PRIX DES STANDS --}}
                    <div class="col-span-2">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                            <p class="text-xs font-bold text-yellow-700 mb-1 flex items-center gap-2">
                                <i class="fa-solid fa-store"></i>
                                Prix des stands d'exposition
                                <span class="text-yellow-500 font-normal">(optionnel)</span>
                            </p>
                            <p class="text-xs text-yellow-600 mb-3">
                                <i class="fa-solid fa-circle-info mr-1"></i>
                                Laissez à 0 si les stands sont gratuits.
                            </p>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-white rounded-xl border border-gray-200 p-3">
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="fa-solid fa-store text-green-600"></i>
                                        <p class="text-xs font-bold text-gray-700">Standard</p>
                                    </div>
                                    <div class="relative">
                                        <input wire:model="prix_stand_standard" type="number" min="0"
                                            class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm pr-14"
                                            placeholder="0">
                                        <span class="absolute right-3 top-2 text-xs text-gray-400">FCFA</span>
                                    </div>
                                </div>
                                <div class="bg-white rounded-xl border border-blue-200 p-3">
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="fa-solid fa-gem text-blue-600"></i>
                                        <p class="text-xs font-bold text-gray-700">Premium</p>
                                    </div>
                                    <div class="relative">
                                        <input wire:model="prix_stand_premium" type="number" min="0"
                                            class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm pr-14"
                                            placeholder="0">
                                        <span class="absolute right-3 top-2 text-xs text-gray-400">FCFA</span>
                                    </div>
                                </div>
                                <div class="bg-white rounded-xl border border-yellow-200 p-3">
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="fa-solid fa-star text-yellow-500"></i>
                                        <p class="text-xs font-bold text-gray-700">VIP</p>
                                    </div>
                                    <div class="relative">
                                        <input wire:model="prix_stand_vip" type="number" min="0"
                                            class="w-full border rounded-xl px-3 py-2 focus:outline-none text-sm pr-14"
                                            placeholder="0">
                                        <span class="absolute right-3 top-2 text-xs text-gray-400">FCFA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Boutons --}}
                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="closeModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="sauvegarder"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2"
                        style="background-color: #C8102E;">
                        <span wire:loading.remove>
                            <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-floppy-disk' }} mr-1"></i>
                            {{ $isEditing ? 'Modifier' : 'Enregistrer' }}
                        </span>
                        <span wire:loading>
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                            Enregistrement...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>