<div>

    {{-- MESSAGES --}}
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

    {{-- EN-TÊTE --}}
    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Planning des Rendez-vous</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $rendezVous->count() }} RDV
            </span>
        </div>
        <div class="flex gap-3 flex-wrap">
            <button wire:click="ouvrirMatchManuel"
                class="px-4 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
                style="background-color: #8b5cf6;">
                <i class="fa-solid fa-handshake-angle"></i>
                Match manuel
            </button>
        </div>
    </div>

    {{-- FILTRES --}}
    <div class="flex gap-4 mb-5 flex-wrap">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher un participant..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
        <select wire:model.live="filtre_statut"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Tous les statuts</option>
            <option value="planifie">Planifié</option>
            <option value="confirme">Confirmé</option>
            <option value="annule">Annulé</option>
            <option value="termine">Terminé</option>
        </select>
        <select wire:model.live="filtre_evenement"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Tous les événements</option>
            @foreach($evenements as $ev)
            <option value="{{ $ev->id }}">{{ $ev->nom }}</option>
            @endforeach
        </select>
    </div>

    {{-- STATISTIQUES --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-gray-400">
            <i class="fa-solid fa-list text-gray-400 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">Total</p>
                <p class="font-bold text-gray-800 text-lg">{{ $rendezVous->count() }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-blue-500">
            <i class="fa-solid fa-calendar-check text-blue-500 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">Planifiés</p>
                <p class="font-bold text-gray-800 text-lg">{{ $rendezVous->where('statut', 'planifie')->count() }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4" style="border-color: #007A3D;">
            <i class="fa-solid fa-circle-check text-xl" style="color: #007A3D;"></i>
            <div>
                <p class="text-xs text-gray-500">Confirmés</p>
                <p class="font-bold text-gray-800 text-lg">{{ $rendezVous->where('statut', 'confirme')->count() }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-red-500">
            <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">Annulés</p>
                <p class="font-bold text-gray-800 text-lg">{{ $rendezVous->where('statut', 'annule')->count() }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-emerald-500">
            <i class="fa-solid fa-calendar-days text-emerald-500 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">Restants</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $rendezVous->count() - $rendezVous->where('statut', 'annule')->count() }}
                </p>
            </div>
        </div>
    </div>

    {{-- TABLEAU PAR ÉVÉNEMENT --}}
    @if($rendezVous->isEmpty())
    <div class="bg-white rounded-xl shadow p-16 text-center text-gray-400">
        <i class="fa-solid fa-handshake text-5xl mb-3 block text-gray-300"></i>
        <p class="text-lg font-medium">Aucun rendez-vous planifié</p>
        <button wire:click="ouvrirMatchManuel"
            class="mt-3 px-5 py-2 rounded-xl text-white text-sm font-medium"
            style="background-color: #8b5cf6;">
            Créer un match manuel
        </button>
    </div>
    @else

    @foreach($rdvGroupesPagines as $id_evenement => $groupe)
    @php
        $evenement = $evenements->find($id_evenement);
        $rdvsTous  = $groupe['tous'];
        $rdvsPage  = $groupe['page'];
    @endphp

    <div class="mb-8">
        {{-- Header événement --}}
        <div class="flex items-center justify-between mb-4 p-4 rounded-xl text-white flex-wrap gap-3"
            style="background: linear-gradient(135deg, #007A3D, #005a2d);">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/20">
                    <i class="fa-solid fa-calendar-star text-white"></i>
                </div>
                <div>
                    <h4 class="font-bold text-lg">
                        {{ $evenement->nom ?? 'Événement inconnu' }}
                    </h4>
                    @if($evenement)
                    <p class="text-green-200 text-xs">
                        <i class="fa-solid fa-calendar mr-1"></i>{{ $evenement->date_debut }}
                        @if($evenement->nom_salle)
                        <span class="mx-2">•</span>
                        <i class="fa-solid fa-door-open mr-1"></i>{{ $evenement->nom_salle }}
                        @endif
                    </p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-3 py-1.5 rounded-xl bg-white/20 text-sm font-bold">{{ $rdvsTous->count() }} RDV</span>
                <span class="px-3 py-1.5 rounded-xl bg-white/20 text-sm">{{ $rdvsTous->where('statut', 'planifie')->count() }} planifiés</span>
                <span class="px-3 py-1.5 rounded-xl bg-white/20 text-sm">{{ $rdvsTous->where('statut', 'annule')->count() }} annulés</span>
                <span class="px-3 py-1.5 rounded-xl bg-white/20 text-sm">
                    {{ $rdvsTous->count() - $rdvsTous->where('statut', 'annule')->count() }} restants
                </span>
            </div>
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <table class="w-full text-left" style="min-width: 900px;">
                <thead style="background-color: #f8f9fa;">
                    <tr class="border-b">
                        <th class="px-4 py-4 text-gray-500 font-semibold text-sm cursor-pointer"
                            wire:click="sortBy('nom1')">
                            Participant 1
                            @if($sort_field === 'nom1')
                                <i class="fa-solid fa-sort-{{ $sort_direction === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fa-solid fa-sort ml-1 text-gray-300"></i>
                            @endif
                        </th>
                        <th class="px-4 py-4 text-gray-500 font-semibold text-sm cursor-pointer"
                            wire:click="sortBy('nom2')">
                            Participant 2
                            @if($sort_field === 'nom2')
                                <i class="fa-solid fa-sort-{{ $sort_direction === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fa-solid fa-sort ml-1 text-gray-300"></i>
                            @endif
                        </th>
                        <th class="px-4 py-4 text-gray-500 font-semibold text-sm cursor-pointer"
                            wire:click="sortBy('date')">
                            Date & Horaire
                            @if($sort_field === 'date')
                                <i class="fa-solid fa-sort-{{ $sort_direction === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fa-solid fa-sort ml-1 text-gray-300"></i>
                            @endif
                        </th>
                        <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Salle & Table</th>
                        <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Traducteur</th>
                        <th class="px-4 py-4 text-gray-500 font-semibold text-sm cursor-pointer"
                            wire:click="sortBy('statut')">
                            Statut
                            @if($sort_field === 'statut')
                                <i class="fa-solid fa-sort-{{ $sort_direction === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fa-solid fa-sort ml-1 text-gray-300"></i>
                            @endif
                        </th>
                        <th class="px-4 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rdvsPage as $rdv)
                    <tr class="border-b hover:bg-gray-50 transition
                        {{ $rdv->statut == 'annule' ? 'bg-red-50' : '' }}">

                        {{-- Participant 1 --}}
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                    style="background-color: {{ $rdv->absent_participant_id == $rdv->id_participant1 ? '#ef4444' : '#C8102E' }}">
                                    {{ strtoupper(substr($rdv->participant1->prenom ?? 'X', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-sm {{ $rdv->absent_participant_id == $rdv->id_participant1 ? 'line-through text-red-400' : 'text-gray-800' }}">
                                        {{ $rdv->participant1->nom ?? '-' }} {{ $rdv->participant1->prenom ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $rdv->participant1->entreprise->nom ?? 'Indépendant' }}</p>
                                    @if($rdv->absent_participant_id == $rdv->id_participant1)
                                    <span class="text-xs text-red-500 font-medium">
                                        <i class="fa-solid fa-user-slash mr-0.5"></i> Absent
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Participant 2 --}}
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                    style="background-color: {{ $rdv->absent_participant_id == $rdv->id_participant2 ? '#ef4444' : '#007A3D' }}">
                                    {{ strtoupper(substr($rdv->participant2->prenom ?? 'X', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-sm {{ $rdv->absent_participant_id == $rdv->id_participant2 ? 'line-through text-red-400' : 'text-gray-800' }}">
                                        {{ $rdv->participant2->nom ?? '-' }} {{ $rdv->participant2->prenom ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $rdv->participant2->entreprise->nom ?? 'Indépendant' }}</p>
                                    @if($rdv->absent_participant_id == $rdv->id_participant2)
                                    <span class="text-xs text-red-500 font-medium">
                                        <i class="fa-solid fa-user-slash mr-0.5"></i> Absent
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Date & Horaire --}}
                        <td class="px-4 py-4 text-xs">
                            @if($rdv->date)
                            <p class="text-gray-600">
                                <i class="fa-solid fa-calendar text-gray-400 mr-1"></i>{{ $rdv->date }}
                            </p>
                            @endif
                            @if($rdv->heure_debut)
                            <p class="text-gray-600 mt-0.5">
                                <i class="fa-solid fa-clock text-gray-400 mr-1"></i>{{ $rdv->heure_debut }} - {{ $rdv->heure_fin }}
                            </p>
                            @endif
                            @if(!$rdv->date && !$rdv->heure_debut)
                            <span class="text-gray-400 italic">Non planifié</span>
                            @endif
                        </td>

                        {{-- Salle & Table --}}
                        <td class="px-4 py-4 text-xs">
                            @if($rdv->salle)
                            <p class="text-gray-700 font-medium">
                                <i class="fa-solid fa-door-open text-blue-500 mr-1"></i>{{ $rdv->salle }}
                            </p>
                            <p class="text-gray-500 mt-0.5">
                                <i class="fa-solid fa-hashtag text-gray-400 mr-0.5"></i>Table {{ $rdv->numero_table }}
                            </p>
                            @else
                            <span class="text-gray-400 italic">Non assigné</span>
                            @endif
                        </td>

                        {{-- Traducteur --}}
                        <td class="px-4 py-4 text-sm">
                            @if($rdv->traducteur)
                                <span class="text-xs px-2 py-1 rounded-lg bg-purple-100 text-purple-700 font-medium">
                                    <i class="fa-solid fa-language mr-1"></i>{{ $rdv->traducteur->nom }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">Aucun</span>
                            @endif
                        </td>

                        {{-- Statut --}}
                        <td class="px-4 py-4">
                            @if($rdv->statut == 'planifie')
                                <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                                    <i class="fa-solid fa-calendar-check mr-1"></i> Planifié
                                </span>
                            @elseif($rdv->statut == 'confirme')
                                <span class="px-3 py-1 rounded-full text-xs text-white font-medium" style="background-color: #007A3D;">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Confirmé
                                </span>
                            @elseif($rdv->statut == 'annule')
                                <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-red-600">
                                    <i class="fa-solid fa-circle-xmark mr-1"></i> Annulé
                                </span>
                                @if($rdv->participantAbsent)
                                <p class="text-xs text-red-500 mt-1">
                                    {{ $rdv->participantAbsent->nom }} {{ $rdv->participantAbsent->prenom }} absent(e)
                                </p>
                                @endif
                            @elseif($rdv->statut == 'termine')
                                <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-gray-500">
                                    <i class="fa-solid fa-flag-checkered mr-1"></i> Terminé
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-gray-400">
                                    {{ $rdv->statut }}
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-4">
                            <div class="flex gap-1.5 flex-wrap">
                                {{-- Traducteur --}}
                                <button wire:click="ouvrirModalTraducteur({{ $rdv->id }})"
                                    class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium bg-purple-600 transition hover:bg-purple-700"
                                    title="Traducteur">
                                    <i class="fa-solid fa-language"></i>
                                </button>

                                {{-- Re-match si annulé --}}
                                @if($rdv->statut == 'annule')
                                <button wire:click="ouvrirRematch({{ $rdv->id }})"
                                    class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium bg-orange-500 transition hover:bg-orange-600"
                                    title="Re-match">
                                    <i class="fa-solid fa-rotate"></i>
                                </button>
                                @endif

                                {{-- Annuler si planifié ou confirmé --}}
                                @if(in_array($rdv->statut, ['planifie', 'confirme']))
                                <button wire:click="ouvrirModalAnnuler({{ $rdv->id }})"
                                    class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium bg-orange-500 transition hover:bg-orange-600"
                                    title="Signaler absence">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($rdvsPage->hasPages())
        <div class="mt-3">
            {{ $rdvsPage->links() }}
        </div>
        @endif
    </div>
    @endforeach
    @endif

    {{-- MODAL MATCH MANUEL --}}
    @if($showMatchManuelModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center px-8 py-5 border-b sticky top-0 z-10"
                style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-handshake-angle"></i>
                    Organiser un match manuel
                </h3>
                <button wire:click="fermerMatchManuel" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                <div class="bg-purple-50 border border-purple-200 rounded-xl px-4 py-3 mb-5 text-sm text-purple-700 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    Sélectionnez 2 participants. Le système trouvera automatiquement
                    un créneau libre et notifiera les deux participants.
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Événement *</label>
                        <select wire:model.live="match_id_evenement"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-300 text-sm">
                            <option value="">-- Choisir un événement --</option>
                            @foreach($evenements as $evenement)
                            <option value="{{ $evenement->id }}">{{ $evenement->nom }}</option>
                            @endforeach
                        </select>
                        @error('match_id_evenement')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($match_id_evenement)
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Participant 1 *</label>
                        <select wire:model.live="match_participant1"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-300 text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($participantsMatchManuel as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->nom }} {{ $p->prenom }}
                                {{ $p->entreprise ? '('.$p->entreprise->nom.')' : '(Indépendant)' }}
                            </option>
                            @endforeach
                        </select>
                        @error('match_participant1')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Participant 2 *</label>
                        <select wire:model.live="match_participant2"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-300 text-sm">
                            <option value="">-- Choisir --</option>
                            @foreach($participantsMatchManuel as $p)
                            @if($p->id != $match_participant1)
                            <option value="{{ $p->id }}">
                                {{ $p->nom }} {{ $p->prenom }}
                                {{ $p->entreprise ? '('.$p->entreprise->nom.')' : '(Indépendant)' }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                        @error('match_participant2')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    @if(!is_null($match_compatibilite))
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <p class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-gauge-high" style="color: #8b5cf6;"></i>
                            Compatibilité des profils
                        </p>
                        <div class="flex items-center gap-2 mb-2">
                            @for($i = 1; $i <= 3; $i++)
                                <i class="fa-solid fa-star text-xl {{ $i <= $match_compatibilite ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                            @endfor
                            <span class="text-sm font-medium text-gray-600 ml-2">{{ $match_compatibilite }} / 3</span>
                        </div>
                        @if($match_compatibilite == 0)
                        <p class="text-xs text-red-500"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Aucun critère de compatibilité.</p>
                        @elseif($match_compatibilite == 3)
                        <p class="text-xs text-green-600"><i class="fa-solid fa-circle-check mr-1"></i>Profils très compatibles !</p>
                        @else
                        <p class="text-xs text-blue-600"><i class="fa-solid fa-circle-info mr-1"></i>Compatibilité partielle.</p>
                        @endif
                        @if(!$match_disponibilite_ok)
                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-2 mt-3 text-xs text-orange-700 flex items-start gap-2">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                            Aucune disponibilité commune déclarée.
                        </div>
                        @else
                        <div class="bg-green-50 border border-green-200 rounded-xl p-2 mt-3 text-xs text-green-700 flex items-center gap-2">
                            <i class="fa-solid fa-circle-check"></i>Disponibilités compatibles.
                        </div>
                        @endif
                    </div>
                    @endif
                    @endif
                </div>

                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="fermerMatchManuel"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="creerMatchManuel"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2"
                        style="background-color: #8b5cf6;">
                        <span wire:loading.remove><i class="fa-solid fa-handshake-angle mr-1"></i> Créer le match</span>
                        <span wire:loading><i class="fa-solid fa-spinner fa-spin mr-1"></i> Création...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL ANNULATION --}}
    @if($showAnnulerModal && $annuler_rdv)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #C8102E, #a00d25);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-ban"></i> Signaler une absence
                </h3>
                <button wire:click="fermerModalAnnuler" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                <div class="bg-gray-50 rounded-xl p-4 mb-5 border border-gray-200 text-xs text-gray-600">
                    <p class="font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-calendar mr-1"></i>
                        {{ $annuler_rdv->date }} — {{ $annuler_rdv->heure_debut }} à {{ $annuler_rdv->heure_fin }}
                        @if($annuler_rdv->salle)
                        — {{ $annuler_rdv->salle }}, Table {{ $annuler_rdv->numero_table }}
                        @endif
                    </p>
                    <div class="flex items-center gap-3">
                        <span class="font-medium text-gray-800">
                            {{ $annuler_rdv->participant1->nom ?? '-' }} {{ $annuler_rdv->participant1->prenom ?? '' }}
                        </span>
                        <i class="fa-solid fa-arrows-left-right text-gray-400"></i>
                        <span class="font-medium text-gray-800">
                            {{ $annuler_rdv->participant2->nom ?? '-' }} {{ $annuler_rdv->participant2->prenom ?? '' }}
                        </span>
                    </div>
                </div>

                <div class="mb-5">
                    <p class="text-sm font-semibold text-gray-700 mb-3">
                        <i class="fa-solid fa-user-slash text-red-500 mr-1"></i> Qui est absent ? *
                    </p>
                    <div class="space-y-2">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="absent_id"
                                value="{{ $annuler_rdv->id_participant1 }}" class="hidden peer">
                            <div class="flex items-center gap-3 p-4 border-2 rounded-xl transition
                                peer-checked:border-red-400 peer-checked:bg-red-50 hover:bg-gray-50 border-gray-200">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                    style="background-color: #C8102E;">
                                    {{ strtoupper(substr($annuler_rdv->participant1->prenom ?? 'X', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">
                                        {{ $annuler_rdv->participant1->nom ?? '-' }} {{ $annuler_rdv->participant1->prenom ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $annuler_rdv->participant1->entreprise->nom ?? 'Indépendant' }}</p>
                                </div>
                                @if($absent_id == $annuler_rdv->id_participant1)
                                <i class="fa-solid fa-circle-check text-red-500 text-xl ml-auto"></i>
                                @endif
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="absent_id"
                                value="{{ $annuler_rdv->id_participant2 }}" class="hidden peer">
                            <div class="flex items-center gap-3 p-4 border-2 rounded-xl transition
                                peer-checked:border-red-400 peer-checked:bg-red-50 hover:bg-gray-50 border-gray-200">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                    style="background-color: #007A3D;">
                                    {{ strtoupper(substr($annuler_rdv->participant2->prenom ?? 'X', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">
                                        {{ $annuler_rdv->participant2->nom ?? '-' }} {{ $annuler_rdv->participant2->prenom ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $annuler_rdv->participant2->entreprise->nom ?? 'Indépendant' }}</p>
                                </div>
                                @if($absent_id == $annuler_rdv->id_participant2)
                                <i class="fa-solid fa-circle-check text-red-500 text-xl ml-auto"></i>
                                @endif
                            </div>
                        </label>
                    </div>
                    @error('absent_id')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button wire:click="fermerModalAnnuler"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        <i class="fa-solid fa-xmark mr-1"></i> Fermer
                    </button>
                    <button wire:click="confirmerAnnulation"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-ban mr-1"></i> Confirmer l'annulation
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL RE-MATCH --}}
    @if($showRematchModal && $rematch_rdv)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center px-8 py-5 border-b sticky top-0 z-10"
                style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-rotate"></i> Re-match
                </h3>
                <button wire:click="fermerRematch" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div class="bg-red-100 rounded-xl p-3 text-center">
                            <p class="text-xs text-red-500 font-medium mb-1"><i class="fa-solid fa-user-slash mr-1"></i> Absent</p>
                            <p class="font-bold text-red-700 text-sm">
                                @if($rematch_rdv->absent_participant_id == $rematch_rdv->id_participant1)
                                    {{ $rematch_rdv->participant1->nom ?? '-' }} {{ $rematch_rdv->participant1->prenom ?? '' }}
                                @else
                                    {{ $rematch_rdv->participant2->nom ?? '-' }} {{ $rematch_rdv->participant2->prenom ?? '' }}
                                @endif
                            </p>
                        </div>
                        <div class="bg-green-100 rounded-xl p-3 text-center">
                            <p class="text-xs text-green-600 font-medium mb-1"><i class="fa-solid fa-user-check mr-1"></i> Présent</p>
                            <p class="font-bold text-green-700 text-sm">
                                @if($rematch_rdv->absent_participant_id == $rematch_rdv->id_participant1)
                                    {{ $rematch_rdv->participant2->nom ?? '-' }} {{ $rematch_rdv->participant2->prenom ?? '' }}
                                @else
                                    {{ $rematch_rdv->participant1->nom ?? '-' }} {{ $rematch_rdv->participant1->prenom ?? '' }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <span><i class="fa-solid fa-calendar mr-1"></i>{{ $rematch_rdv->date }}</span>
                        <span><i class="fa-solid fa-clock mr-1"></i>{{ $rematch_rdv->heure_debut }} - {{ $rematch_rdv->heure_fin }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-2">Choisir le remplaçant *</label>
                    @if($participantsDisponibles->isEmpty())
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-xs text-yellow-700 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Aucun participant disponible sur ce créneau !
                    </div>
                    @else
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($participantsDisponibles as $participant)
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="nouveau_participant"
                                value="{{ $participant->id }}" class="hidden peer">
                            <div class="flex items-center justify-between p-3 border-2 rounded-xl transition
                                peer-checked:border-purple-400 peer-checked:bg-purple-50
                                hover:bg-gray-50 border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                        style="background-color: #8b5cf6;">
                                        {{ strtoupper(substr($participant->prenom ?? 'X', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $participant->nom }} {{ $participant->prenom }}
                                        </p>
                                        <p class="text-xs text-gray-400">{{ $participant->entreprise->nom ?? 'Indépendant' }}</p>
                                    </div>
                                </div>
                                @if($nouveau_participant == $participant->id)
                                <i class="fa-solid fa-circle-check text-purple-500 text-xl"></i>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('nouveau_participant')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                    @if($erreur_rematch)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-3 mt-3 text-xs text-red-600 flex items-start gap-2">
                        <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                        {{ $erreur_rematch }}
                    </div>
                    @endif
                    @endif
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="fermerRematch"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    @if(!$participantsDisponibles->isEmpty())
                    <button wire:click="effectuerRematch"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm"
                        style="background-color: #8b5cf6;">
                        <i class="fa-solid fa-rotate mr-1"></i> Effectuer le re-match
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL TRADUCTEUR --}}
    @if($showTraducteurModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-language"></i> Assigner un traducteur
                </h3>
                <button wire:click="fermerModalTraducteur" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                @if($rdv_courant)
                <div class="bg-gray-50 rounded-xl p-4 mb-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold" style="background-color: #C8102E;">
                                {{ strtoupper(substr($rdv_courant->participant1->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <p class="text-sm font-semibold text-gray-800">{{ $rdv_courant->participant1->nom ?? '-' }}</p>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <i class="fa-solid fa-arrows-left-right text-gray-400"></i>
                            <span class="text-xs text-gray-500 bg-gray-200 px-2 py-0.5 rounded-full">
                                {{ $rdv_courant->heure_debut }} - {{ $rdv_courant->heure_fin }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold" style="background-color: #007A3D;">
                                {{ strtoupper(substr($rdv_courant->participant2->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <p class="text-sm font-semibold text-gray-800">{{ $rdv_courant->participant2->nom ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-3">Choisir un traducteur</label>
                    @if($traducteurs->isEmpty())
                    <div class="text-center py-6 text-gray-400">
                        <i class="fa-solid fa-language text-3xl mb-2 block text-gray-300"></i>
                        Aucun traducteur enregistré
                    </div>
                    @else
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($traducteurs as $traducteur)
                        <label class="{{ !$traducteur->disponible ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                            <input type="radio" wire:model="id_traducteur" value="{{ $traducteur->id }}"
                                class="hidden" {{ !$traducteur->disponible ? 'disabled' : '' }}>
                            <div class="flex items-center justify-between p-3 border rounded-xl transition {{ $traducteur->disponible ? 'hover:bg-gray-50 border-gray-200' : 'border-gray-100 bg-gray-50' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                        style="background-color: {{ $traducteur->disponible ? '#007A3D' : '#9ca3af' }}">
                                        {{ strtoupper(substr($traducteur->prenom, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">{{ $traducteur->nom }} {{ $traducteur->prenom }}</p>
                                        <span class="text-xs px-2 py-0.5 rounded-full text-white" style="background-color: #007A3D;">{{ $traducteur->langue }}</span>
                                    </div>
                                </div>
                                @if($traducteur->disponible)
                                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">Disponible</span>
                                @else
                                <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-600 font-medium">Occupé</span>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="fermerModalTraducteur"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="assignerTraducteur"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-check mr-1"></i> Assigner
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>