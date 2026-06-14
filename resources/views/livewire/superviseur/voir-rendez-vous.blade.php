<div>

    {{-- MESSAGES --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Planning des Rendez-vous</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #6b2d6b;">
                {{ $rendezVous->count() }} RDV
            </span>
        </div>
        <button wire:click="ouvrirMatchManuel"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #8b5cf6;">
            <i class="fa-solid fa-handshake-angle"></i>
            Match manuel
        </button>
    </div>

    <div class="flex gap-4 mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher un participant..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none text-sm">
        </div>
        <select wire:model.live="filtre_statut"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Tous les statuts</option>
            <option value="a_planifier">À planifier</option>
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
                        @if($rdv->absent_participant_id == $rdv->id_participant1)
                        <span class="text-xs text-red-500 ml-1"><i class="fa-solid fa-user-slash"></i></span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        {{ $rdv->participant2->nom ?? '-' }} {{ $rdv->participant2->prenom ?? '' }}
                        @if($rdv->absent_participant_id == $rdv->id_participant2)
                        <span class="text-xs text-red-500 ml-1"><i class="fa-solid fa-user-slash"></i></span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">{{ $rdv->date ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        @if($rdv->heure_debut)
                        {{ $rdv->heure_debut }} - {{ $rdv->heure_fin }}
                        @else
                        <span class="text-gray-400 italic">Non planifié</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-1 rounded-lg bg-gray-100 text-gray-700">
                            Stand {{ $rdv->stand->numero_stand ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($rdv->statut == 'a_planifier')
                            <span class="px-3 py-1 rounded-full text-xs text-white bg-gray-500">
                                <i class="fa-solid fa-hourglass-half mr-1"></i> À planifier
                            </span>
                        @elseif($rdv->statut == 'planifie')
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

    {{-- MODAL MATCH MANUEL (CAS 4) --}}
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
                    Sélectionnez 2 participants compatibles. Le rendez-vous sera créé avec le
                    statut "À planifier" et les deux participants seront notifiés.
                </div>

                <div class="space-y-4">

                    {{-- Événement --}}
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
                    {{-- Participant 1 --}}
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

                    {{-- Participant 2 --}}
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

                    {{-- Compatibilité live --}}
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
                            <span class="text-sm font-medium text-gray-600 ml-2">
                                {{ $match_compatibilite }} / 3
                            </span>
                        </div>

                        @if($match_compatibilite == 0)
                        <p class="text-xs text-red-500">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                            Aucun critère de compatibilité ne correspond.
                        </p>
                        @elseif($match_compatibilite == 3)
                        <p class="text-xs text-green-600">
                            <i class="fa-solid fa-circle-check mr-1"></i>
                            Profils très compatibles !
                        </p>
                        @else
                        <p class="text-xs text-blue-600">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            Compatibilité partielle.
                        </p>
                        @endif

                        @if(!$match_disponibilite_ok)
                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-2 mt-3 text-xs text-orange-700 flex items-start gap-2">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                            Ces deux participants n'ont pas de disponibilité commune déclarée.
                            Le RDV pourra être ajusté manuellement plus tard.
                        </div>
                        @else
                        <div class="bg-green-50 border border-green-200 rounded-xl p-2 mt-3 text-xs text-green-700 flex items-center gap-2">
                            <i class="fa-solid fa-circle-check"></i>
                            Disponibilités compatibles.
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
                        <span wire:loading.remove>
                            <i class="fa-solid fa-handshake-angle mr-1"></i> Créer le match
                        </span>
                        <span wire:loading>
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i> Création...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>