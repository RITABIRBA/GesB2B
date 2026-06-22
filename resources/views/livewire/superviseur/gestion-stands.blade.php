<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-300 text-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <div class="flex items-center gap-4 flex-wrap">
            <h3 class="text-xl font-bold text-gray-700">Liste des stands</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #007A3D;">
                {{ $stands->count() }} stand(s)
            </span>
            <span class="text-sm px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                <i class="fa-solid fa-circle-dot mr-1"></i>
                {{ $stands->whereNull('id_entreprise')->whereNull('id_participant')->count() }} disponible(s)
            </span>
            <span class="text-sm px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                <i class="fa-solid fa-building mr-1"></i>
                {{ $stands->where(fn($s) => $s->id_entreprise || $s->id_participant)->count() }} occupé(s)
            </span>
        </div>
        <div class="flex gap-3">
            <button wire:click="ouvrirCreateModal"
                class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
                style="background-color: #C8102E;">
                <i class="fa-solid fa-plus"></i>
                Créer un stand
            </button>
            <button wire:click="openGenerateModal"
                class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
                style="background-color: #007A3D;">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                Générer automatiquement
            </button>
        </div>
    </div>

    {{-- Recherche --}}
    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher par entreprise, participant ou événement..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left" style="min-width: 1000px;">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">N° Stand</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Événement</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Attribué à</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Réservation</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Superficie</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Standing</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stands as $stand)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white text-sm"
                            style="background-color: {{ ($stand->entreprise || $stand->participant) ? '#2d5a8e' : '#007A3D' }}">
                            {{ $stand->numero_stand }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-700 font-medium">
                        <i class="fa-solid fa-calendar text-gray-400 mr-1"></i>
                        {{ $stand->evenement->nom ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($stand->participant)
                        <span class="text-xs px-3 py-1 rounded-full bg-purple-100 text-purple-700 font-medium block w-fit">
                            <i class="fa-solid fa-user mr-1"></i>
                            {{ $stand->participant->nom }} {{ $stand->participant->prenom }}
                        </span>
                        @if($stand->est_gratuit)
                        <span class="text-xs text-green-600 block mt-1">
                            <i class="fa-solid fa-gift mr-1"></i> Gratuit
                            @if($stand->motif_gratuite)
                            — {{ Str::limit($stand->motif_gratuite, 30) }}
                            @endif
                        </span>
                        @endif
                        @elseif($stand->entreprise)
                        <span class="text-xs px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                            <i class="fa-solid fa-building mr-1"></i>
                            {{ $stand->entreprise->nom }}
                        </span>
                        @else
                        <span class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                            <i class="fa-solid fa-circle-dot mr-1"></i>
                            Disponible
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if(!$stand->id_entreprise && !$stand->id_participant)
                        <span class="text-xs text-gray-300">—</span>
                        @elseif($stand->statut_reservation == 'en_attente')
                        <div class="flex flex-col gap-1">
                            <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium block w-fit">
                                <i class="fa-solid fa-clock mr-1"></i> En attente
                            </span>
                            <div class="flex gap-1 mt-1">
                                <button wire:click="validerReservation({{ $stand->id }})"
                                    wire:confirm="Valider la réservation du Stand N°{{ $stand->numero_stand }} ?"
                                    class="px-2 py-1 rounded-lg text-white text-xs font-medium transition hover:opacity-90"
                                    style="background-color: #007A3D;">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                                <button wire:click="rejeterReservation({{ $stand->id }})"
                                    wire:confirm="Rejeter la réservation du Stand N°{{ $stand->numero_stand }} ?"
                                    class="px-2 py-1 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                        @elseif($stand->statut_reservation == 'valide')
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium block w-fit">
                            <i class="fa-solid fa-circle-check mr-1"></i> Validée
                        </span>
                        @if($stand->statut_paiement_stand == 'paye')
                        <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-medium block w-fit mt-1">
                            <i class="fa-solid fa-money-bill mr-1"></i> Payé
                        </span>
                        @else
                        <span class="text-xs px-2 py-1 rounded-full bg-orange-100 text-orange-700 font-medium block w-fit mt-1">
                            <i class="fa-solid fa-hourglass-half mr-1"></i> Paiement attendu
                        </span>
                        @endif
                        @else
                        <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <i class="fa-solid fa-ruler-combined text-gray-400 mr-1"></i>
                        {{ $stand->superficie }}
                    </td>
                    <td class="px-6 py-4">
                        @if($stand->standing == 'vip')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">
                                <i class="fa-solid fa-star mr-1"></i> VIP
                            </span>
                        @elseif($stand->standing == 'premium')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                                <i class="fa-solid fa-gem mr-1"></i> Premium
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium" style="background-color: #007A3D;">
                                <i class="fa-solid fa-store mr-1"></i> {{ ucfirst($stand->standing ?? 'Standard') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button wire:click="modifier({{ $stand->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700 flex items-center gap-1">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="supprimer({{ $stand->id }})"
                                wire:confirm="Voulez-vous vraiment supprimer ce stand ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-store text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun stand pour le moment</p>
                        <div class="flex gap-3 justify-center mt-3">
                            <button wire:click="ouvrirCreateModal"
                                class="px-5 py-2 rounded-xl text-white text-sm font-medium"
                                style="background-color: #C8102E;">
                                <i class="fa-solid fa-plus mr-1"></i> Créer un stand
                            </button>
                            <button wire:click="openGenerateModal"
                                class="px-5 py-2 rounded-xl text-white text-sm font-medium"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-wand-magic-sparkles mr-1"></i>
                                Générer automatiquement
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL CRÉATION MANUELLE --}}
    @if($showCreateModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b sticky top-0 z-10"
                style="background: linear-gradient(135deg, #C8102E, #a00d25);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-store"></i>
                    Créer un stand manuellement
                </h3>
                <button wire:click="fermerCreateModal" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8 space-y-5">

                {{-- Événement --}}
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Événement *</label>
                    <select wire:model.live="create_id_evenement"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                        <option value="">-- Choisir un événement --</option>
                        @foreach($evenements as $evenement)
                        <option value="{{ $evenement->id }}">{{ $evenement->nom }}</option>
                        @endforeach
                    </select>
                    @error('create_id_evenement') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                @if($create_id_evenement)

                {{-- Type de stand --}}
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-2">Type de stand *</label>
                    @if($typesStandsEvenement->isEmpty())
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-xs text-yellow-700">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        Aucun type de stand n'est défini pour cet événement.
                        Allez dans "Gestion des Événements" pour en ajouter.
                    </div>
                    @else
                    <div class="space-y-2">
                        @foreach($typesStandsEvenement as $ts)
                        <label class="cursor-pointer block">
                            <input type="radio" wire:model.live="create_id_type_stand" value="{{ $ts->id }}" class="hidden peer">
                            <div class="p-3 border-2 rounded-xl transition flex items-center justify-between
                                peer-checked:border-red-400 peer-checked:bg-red-50 hover:bg-gray-50 border-gray-200">
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">{{ $ts->standing }}</p>
                                    <div class="flex gap-3 text-xs text-gray-500 mt-0.5">
                                        @if($ts->superficie)
                                        <span><i class="fa-solid fa-ruler-combined mr-1"></i>{{ $ts->superficie }}</span>
                                        @endif
                                        @if($ts->est_gratuit)
                                        <span class="text-green-600 font-semibold"><i class="fa-solid fa-gift mr-1"></i>Gratuit</span>
                                        @else
                                        <span class="font-semibold" style="color: #C8102E;">
                                            {{ number_format($ts->montant, 0, ',', ' ') }} FCFA
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endif
                    @error('create_id_type_stand') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Numéro de stand --}}
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Numéro de stand *</label>
                    <input wire:model="create_numero_stand" type="number" min="1"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                        placeholder="Ex: 15">
                    @error('create_numero_stand') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Assignation à un participant --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <p class="text-sm font-bold text-blue-700 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-user"></i>
                        Assigner à un participant
                        <span class="text-blue-500 font-normal text-xs">(optionnel)</span>
                    </p>

                    @if($create_id_participant)
                    <div class="flex items-center justify-between bg-white rounded-xl p-3 border border-blue-200">
                        <span class="text-sm font-semibold text-gray-800">
                            <i class="fa-solid fa-circle-check text-green-500 mr-1"></i>
                            {{ $create_recherche_participant }}
                        </span>
                        <button type="button" wire:click="viderParticipant"
                            class="text-red-500 text-xs hover:underline">Changer</button>
                    </div>
                    @else
                    <input wire:model.live.debounce.400ms="create_recherche_participant"
                        type="text"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm bg-white"
                        placeholder="Rechercher un participant par nom...">

                    @if($participantsRecherche->isNotEmpty())
                    <div class="mt-2 space-y-1 max-h-48 overflow-y-auto">
                        @foreach($participantsRecherche as $p)
                        <button type="button" wire:click="selectionnerParticipant({{ $p->id }})"
                            class="w-full text-left p-2.5 rounded-lg hover:bg-blue-100 transition flex items-center gap-2 bg-white border border-gray-100">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                style="background-color: #007A3D;">
                                {{ strtoupper(substr($p->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $p->nom }} {{ $p->prenom }}</p>
                                <p class="text-xs text-gray-400">{{ $p->entreprise->nom ?? 'Indépendant' }}</p>
                            </div>
                        </button>
                        @endforeach
                    </div>
                    @elseif(strlen($create_recherche_participant) >= 2)
                    <p class="text-xs text-gray-400 mt-2">Aucun participant trouvé.</p>
                    @endif
                    @endif
                </div>

                {{-- Motif si gratuit + assigné --}}
                @if($create_est_gratuit && $create_id_participant)
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">
                        Motif de la gratuité *
                    </label>
                    <textarea wire:model="create_motif_gratuite" rows="2"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                        placeholder="Ex: Participant sponsor de l'événement..."></textarea>
                    @error('create_motif_gratuite') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                @endif

                @endif

                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="fermerCreateModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        Annuler
                    </button>
                    <button wire:click="creerStandManuel"
                        wire:loading.attr="disabled"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-check mr-1"></i> Créer le stand
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL GÉNÉRATION AUTOMATIQUE --}}
    @if($showGenerateModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    Générer les stands automatiquement
                </h3>
                <button wire:click="closeGenerateModal"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8">

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-5 text-xs text-blue-700 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    Les stands existants de l'événement choisi seront
                    <strong>supprimés et remplacés</strong> par les nouveaux stands générés.
                </div>

                <div class="space-y-4">

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Événement *
                        </label>
                        <select wire:model="id_evenement_generate"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
                            <option value="">-- Choisir un événement --</option>
                            @foreach($evenements as $evenement)
                            <option value="{{ $evenement->id }}">{{ $evenement->nom }}</option>
                            @endforeach
                        </select>
                        @error('id_evenement_generate')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Nombre de stands *
                        </label>
                        <input wire:model.live="nombre_stands"
                            type="number" min="1" max="100"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                            placeholder="Ex: 20">
                        @error('nombre_stands')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                        @if($nombre_stands > 0)
                        <p class="text-xs text-green-600 mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check"></i>
                            {{ $nombre_stands }} stands seront créés,
                            numérotés de 1 à {{ $nombre_stands }}
                        </p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Superficie par défaut (m²) *
                        </label>
                        <input wire:model="superficie_default"
                            type="number" step="0.1" min="1"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                            placeholder="Ex: 9">
                        @error('superficie_default')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Standing par défaut *
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach($standings as $s)
                            <button type="button"
                                wire:click="$set('standing_default', '{{ $s }}')"
                                class="border-2 rounded-xl p-3 text-center transition
                                    {{ $standing_default === $s
                                        ? 'border-green-400 bg-green-50'
                                        : 'border-gray-200 hover:bg-gray-50' }}">
                                @if($s === 'vip')
                                    <i class="fa-solid fa-star text-yellow-500 text-lg mb-1 block"></i>
                                @elseif($s === 'premium')
                                    <i class="fa-solid fa-gem text-blue-500 text-lg mb-1 block"></i>
                                @else
                                    <i class="fa-solid fa-store text-green-600 text-lg mb-1 block"></i>
                                @endif
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst($s) }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="closeGenerateModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="genererStands"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm flex items-center gap-2"
                        style="background-color: #007A3D;">
                        <span wire:loading.remove>
                            <i class="fa-solid fa-wand-magic-sparkles mr-1"></i>
                            Générer {{ $nombre_stands > 0 ? $nombre_stands : '...' }} stands
                        </span>
                        <span wire:loading>
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                            Génération...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL MODIFICATION STAND --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-pen"></i>
                    Modifier le stand
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl transition">
                    &times;
                </button>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-2 gap-5">

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Numéro du stand</label>
                        <input wire:model="numero_stand" type="number"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-gray-50"
                            readonly>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Superficie (m²) *</label>
                        <input wire:model="superficie" type="text"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                            placeholder="Ex: 25">
                        @error('superficie') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Événement</label>
                        <select wire:model="id_evenement"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm bg-gray-50"
                            disabled>
                            <option value="">-- Choisir un événement --</option>
                            @foreach($evenements as $evenement)
                            <option value="{{ $evenement->id }}">{{ $evenement->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Entreprise
                            <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <select wire:model="id_entreprise"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            <option value="">-- Disponible --</option>
                            @foreach($entreprises as $entreprise)
                            <option value="{{ $entreprise->id }}">{{ $entreprise->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Standing *</label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach($standings as $s)
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="standing" value="{{ $s }}" class="hidden peer">
                                <div class="border rounded-xl p-3 text-center transition hover:bg-gray-50
                                    {{ $standing === $s ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                                    @if($s === 'vip')
                                        <i class="fa-solid fa-star text-yellow-500 text-lg mb-1 block"></i>
                                    @elseif($s === 'premium')
                                        <i class="fa-solid fa-gem text-blue-500 text-lg mb-1 block"></i>
                                    @else
                                        <i class="fa-solid fa-store text-green-600 text-lg mb-1 block"></i>
                                    @endif
                                    <span class="text-sm font-medium text-gray-700">{{ ucfirst($s) }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                </div>

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
                            <i class="fa-solid fa-pen mr-1"></i>
                            Modifier
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