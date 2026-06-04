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
    <div class="flex justify-between items-center mb-6 no-print">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Planning des Rendez-vous</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $rendezVous->count() }} RDV
            </span>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()"
                class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow no-print"
                style="background-color: #2d5a8e;">
                <i class="fa-solid fa-print"></i>
                Imprimer
            </button>
            <button wire:click="openGenerateModal"
                class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow no-print"
                style="background-color: #C8102E;">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                Générer le planning
            </button>
        </div>
    </div>

    {{-- FILTRES --}}
    <div class="flex gap-4 mb-5 no-print">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher un participant..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
        <select wire:model.live="filtre_statut"
            class="border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
            <option value="">Tous les statuts</option>
            <option value="planifie">Planifié</option>
            <option value="confirme">Confirmé</option>
            <option value="annule">Annulé</option>
            <option value="termine">Terminé</option>
        </select>
    </div>

    {{-- STATISTIQUES GLOBALES --}}
    <div class="grid grid-cols-4 gap-4 mb-6 no-print">
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
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-gray-400">
            <i class="fa-solid fa-flag-checkered text-gray-400 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">Terminés</p>
                <p class="font-bold text-gray-800 text-lg">{{ $rendezVous->where('statut', 'termine')->count() }}</p>
            </div>
        </div>
    </div>

    {{-- PLANNING PAR ÉVÉNEMENT --}}
    @if($rendezVous->isEmpty())
    <div class="bg-white rounded-xl shadow p-16 text-center text-gray-400">
        <i class="fa-solid fa-handshake text-5xl mb-3 block text-gray-300"></i>
        <p class="text-lg font-medium">Aucun rendez-vous planifié</p>
        <button wire:click="openGenerateModal"
            class="mt-3 px-5 py-2 rounded-xl text-white text-sm font-medium"
            style="background-color: #C8102E;">
            Générer le planning
        </button>
    </div>
    @else

    {{-- Groupe par événement --}}
    @php
        $rdvParEvenement = $rendezVous->groupBy(function($rdv) {
            return $rdv->participant1->id_evenement ?? $rdv->participant2->id_evenement ?? 0;
        });
    @endphp

    @foreach($rdvParEvenement as $id_evenement => $rdvs)
    @php
        $evenement = $evenements->find($id_evenement);
    @endphp

    <div class="mb-8">
        {{-- Header événement --}}
        <div class="flex items-center justify-between mb-4 p-4 rounded-xl text-white"
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
                        <i class="fa-solid fa-location-dot mr-1"></i>
                        {{ $evenement->ville }}
                        <span class="mx-2">•</span>
                        <i class="fa-solid fa-calendar mr-1"></i>
                        {{ $evenement->date_debut }}
                        <span class="mx-2">•</span>
                        <i class="fa-solid fa-clock mr-1"></i>
                        {{ $evenement->heure_debut }} - {{ $evenement->heure_fin }}
                    </p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1.5 rounded-xl bg-white/20 text-sm font-bold">
                    {{ $rdvs->count() }} RDV
                </span>
                <span class="px-3 py-1.5 rounded-xl bg-white/20 text-sm">
                    {{ $rdvs->where('statut', 'planifie')->count() }} planifiés
                </span>
                <span class="px-3 py-1.5 rounded-xl bg-white/20 text-sm">
                    {{ $rdvs->where('statut', 'confirme')->count() }} confirmés
                </span>
            </div>
        </div>

        {{-- Tableau des RDV de cet événement --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full text-left">
                <thead style="background-color: #f8f9fa;">
                    <tr class="border-b">
                        <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Participant 1</th>
                        <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Participant 2</th>
                        <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Date</th>
                        <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Horaire</th>
                        <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Stand</th>
                        <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Traducteur</th>
                        <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Statut</th>
                        <th class="px-6 py-4 text-gray-500 font-semibold text-sm no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rdvs as $rdv)
                    <tr class="border-b hover:bg-gray-50 transition
                        {{ $rdv->statut == 'annule' ? 'bg-red-50' : '' }}">

                        {{-- Participant 1 --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                    style="background-color: {{ $rdv->absent_participant_id == $rdv->id_participant1 ? '#ef4444' : '#C8102E' }}">
                                    {{ strtoupper(substr($rdv->participant1->prenom ?? 'X', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-sm
                                        {{ $rdv->absent_participant_id == $rdv->id_participant1 ? 'line-through text-red-400' : 'text-gray-800' }}">
                                        {{ $rdv->participant1->nom ?? '-' }}
                                        {{ $rdv->participant1->prenom ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $rdv->participant1->entreprise->nom ?? 'Indépendant' }}
                                    </p>
                                    @if($rdv->absent_participant_id == $rdv->id_participant1)
                                    <span class="text-xs text-red-500 font-medium flex items-center gap-1">
                                        <i class="fa-solid fa-user-slash"></i> Absent
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Participant 2 --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                    style="background-color: {{ $rdv->absent_participant_id == $rdv->id_participant2 ? '#ef4444' : '#007A3D' }}">
                                    {{ strtoupper(substr($rdv->participant2->prenom ?? 'X', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-sm
                                        {{ $rdv->absent_participant_id == $rdv->id_participant2 ? 'line-through text-red-400' : 'text-gray-800' }}">
                                        {{ $rdv->participant2->nom ?? '-' }}
                                        {{ $rdv->participant2->prenom ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $rdv->participant2->entreprise->nom ?? 'Indépendant' }}
                                    </p>
                                    @if($rdv->absent_participant_id == $rdv->id_participant2)
                                    <span class="text-xs text-red-500 font-medium flex items-center gap-1">
                                        <i class="fa-solid fa-user-slash"></i> Absent
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Date --}}
                        <td class="px-6 py-4 text-gray-600 text-sm">
                            <i class="fa-solid fa-calendar text-gray-400 mr-1"></i>
                            {{ $rdv->date }}
                        </td>

                        {{-- Horaire --}}
                        <td class="px-6 py-4 text-gray-600 text-sm">
                            <i class="fa-solid fa-clock text-gray-400 mr-1"></i>
                            {{ $rdv->heure_debut }} - {{ $rdv->heure_fin }}
                        </td>

                        {{-- Stand --}}
                        <td class="px-6 py-4">
                            <span class="text-xs px-2 py-1 rounded-lg bg-gray-100 text-gray-700 font-medium">
                                Stand {{ $rdv->stand->numero_stand ?? '-' }}
                            </span>
                        </td>

                        {{-- Traducteur --}}
                        <td class="px-6 py-4 text-sm">
                            @if($rdv->traducteur)
                                <span class="text-xs px-2 py-1 rounded-lg bg-purple-100 text-purple-700 font-medium">
                                    <i class="fa-solid fa-language mr-1"></i>
                                    {{ $rdv->traducteur->nom }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">Aucun</span>
                            @endif
                        </td>

                        {{-- Statut --}}
                        <td class="px-6 py-4">
                            @if($rdv->statut == 'planifie')
                                <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                                    <i class="fa-solid fa-calendar-check mr-1"></i> Planifié
                                </span>
                            @elseif($rdv->statut == 'confirme')
                                <span class="px-3 py-1 rounded-full text-xs text-white font-medium"
                                    style="background-color: #007A3D;">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Confirmé
                                </span>
                            @elseif($rdv->statut == 'annule')
                                <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-red-600 block text-center">
                                    <i class="fa-solid fa-circle-xmark mr-1"></i> Annulé
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-gray-500">
                                    <i class="fa-solid fa-flag-checkered mr-1"></i> Terminé
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 no-print">
                            <div class="flex gap-1.5 flex-wrap">
                                <button wire:click="ouvrirModalTraducteur({{ $rdv->id }})"
                                    class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium bg-purple-600 transition hover:bg-purple-700"
                                    title="Traducteur">
                                    <i class="fa-solid fa-language"></i>
                                </button>

                                @if($rdv->statut == 'annule')
                                <button wire:click="ouvrirRematch({{ $rdv->id }})"
                                    class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium bg-orange-500 transition hover:bg-orange-600"
                                    title="Re-match">
                                    <i class="fa-solid fa-rotate"></i>
                                </button>
                                @endif

                                @if($rdv->statut == 'planifie')
                                <button wire:click="confirmer({{ $rdv->id }})"
                                    class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90"
                                    style="background-color: #007A3D;" title="Confirmer">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                                <button wire:click="annuler({{ $rdv->id }})"
                                    wire:confirm="Voulez-vous annuler ce rendez-vous ?"
                                    class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium bg-orange-500 transition hover:bg-orange-600"
                                    title="Annuler">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                                @elseif($rdv->statut == 'confirme')
                                <button wire:click="terminer({{ $rdv->id }})"
                                    class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium bg-gray-500 transition hover:bg-gray-600"
                                    title="Terminer">
                                    <i class="fa-solid fa-flag-checkered"></i>
                                </button>
                                @endif

                                <button wire:click="supprimer({{ $rdv->id }})"
                                    wire:confirm="Supprimer ce rendez-vous ?"
                                    class="px-2.5 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700"
                                    title="Supprimer">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    @endif

    {{-- MODAL — GÉNÉRATION DU PLANNING --}}
    @if($showGenerateModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-screen overflow-y-auto">

            <div class="flex justify-between items-center px-8 py-5 border-b sticky top-0 z-10"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    Générer le planning
                </h3>
                <button wire:click="closeGenerateModal"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8">
                <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 mb-5 text-sm text-blue-700 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    Les heures sont prises automatiquement depuis l'événement choisi.
                </div>

                <div class="grid grid-cols-1 gap-5">

                    {{-- Événement --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Événement *</label>
                        <select wire:model.live="id_evenement"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            <option value="">-- Choisir un événement --</option>
                            @foreach($evenements as $evenement)
                            <option value="{{ $evenement->id }}">{{ $evenement->nom }}</option>
                            @endforeach
                        </select>
                        @error('id_evenement')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($evenement_selectionne)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <p class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-calendar" style="color: #007A3D;"></i>
                            {{ $evenement_selectionne->nom }}
                        </p>
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span>
                                <i class="fa-solid fa-clock text-gray-400 mr-1"></i>
                                {{ $evenement_selectionne->heure_debut }} → {{ $evenement_selectionne->heure_fin }}
                            </span>
                            <span>
                                <i class="fa-solid fa-location-dot text-gray-400 mr-1"></i>
                                {{ $evenement_selectionne->ville }}
                            </span>
                        </div>
                    </div>
                    @endif

                    {{-- Durée RDV --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">Durée d'un RDV *</label>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach([15, 20, 30, 45, 60] as $duree)
                            <button type="button"
                                wire:click="$set('duree_rdv', {{ $duree }})"
                                class="border rounded-xl p-2 text-center transition text-sm font-medium
                                    {{ $duree_rdv == $duree
                                        ? 'border-red-400 bg-red-50 text-red-700'
                                        : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                {{ $duree }}min
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- PAUSES --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-3">
                            <i class="fa-solid fa-coffee mr-1 text-orange-400"></i>
                            Pauses (optionnel)
                        </label>
                        <div class="space-y-2">

                            <div class="flex items-center gap-3 p-3 border rounded-xl transition
                                {{ $pause_cafe_matin ? 'border-orange-300 bg-orange-50' : 'border-gray-200' }}">
                                <input type="checkbox" wire:model.live="pause_cafe_matin"
                                    class="rounded border-gray-300 text-orange-500 w-4 h-4">
                                <span class="text-sm font-medium text-gray-700 flex-1">
                                    <i class="fa-solid fa-mug-hot text-orange-400 mr-1"></i>
                                    Pause café matin
                                </span>
                                @if($pause_cafe_matin)
                                <div class="flex items-center gap-2">
                                    <input wire:model.live="pause_cafe_matin_debut" type="time"
                                        class="border rounded-lg px-2 py-1 text-xs focus:outline-none">
                                    <span class="text-xs text-gray-400">→</span>
                                    <input wire:model.live="pause_cafe_matin_fin" type="time"
                                        class="border rounded-lg px-2 py-1 text-xs focus:outline-none">
                                </div>
                                @else
                                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-lg">10:00 → 10:15</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-3 p-3 border rounded-xl transition
                                {{ $pause_dejeuner ? 'border-green-300 bg-green-50' : 'border-gray-200' }}">
                                <input type="checkbox" wire:model.live="pause_dejeuner"
                                    class="rounded border-gray-300 text-green-500 w-4 h-4">
                                <span class="text-sm font-medium text-gray-700 flex-1">
                                    <i class="fa-solid fa-utensils text-green-400 mr-1"></i>
                                    Pause déjeuner
                                </span>
                                @if($pause_dejeuner)
                                <div class="flex items-center gap-2">
                                    <input wire:model.live="pause_dejeuner_debut" type="time"
                                        class="border rounded-lg px-2 py-1 text-xs focus:outline-none">
                                    <span class="text-xs text-gray-400">→</span>
                                    <input wire:model.live="pause_dejeuner_fin" type="time"
                                        class="border rounded-lg px-2 py-1 text-xs focus:outline-none">
                                </div>
                                @else
                                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-lg">12:00 → 14:00</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-3 p-3 border rounded-xl transition
                                {{ $pause_cafe_aprem ? 'border-orange-300 bg-orange-50' : 'border-gray-200' }}">
                                <input type="checkbox" wire:model.live="pause_cafe_aprem"
                                    class="rounded border-gray-300 text-orange-500 w-4 h-4">
                                <span class="text-sm font-medium text-gray-700 flex-1">
                                    <i class="fa-solid fa-mug-hot text-orange-400 mr-1"></i>
                                    Pause café après-midi
                                </span>
                                @if($pause_cafe_aprem)
                                <div class="flex items-center gap-2">
                                    <input wire:model.live="pause_cafe_aprem_debut" type="time"
                                        class="border rounded-lg px-2 py-1 text-xs focus:outline-none">
                                    <span class="text-xs text-gray-400">→</span>
                                    <input wire:model.live="pause_cafe_aprem_fin" type="time"
                                        class="border rounded-lg px-2 py-1 text-xs focus:outline-none">
                                </div>
                                @else
                                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-lg">15:30 → 15:45</span>
                                @endif
                            </div>

                        </div>
                    </div>

                    {{-- Résumé --}}
                    @if($evenement_selectionne && $nb_creneaux > 0)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                        <p class="text-sm font-semibold text-green-700 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-circle-check"></i>
                            Résumé du planning
                        </p>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="text-center bg-white rounded-xl p-3 shadow-sm">
                                <p class="text-2xl font-bold" style="color: #007A3D;">{{ $nb_creneaux }}</p>
                                <p class="text-xs text-gray-500">Créneaux</p>
                            </div>
                            <div class="text-center bg-white rounded-xl p-3 shadow-sm">
                                <p class="text-2xl font-bold" style="color: #C8102E;">{{ $nb_stands }}</p>
                                <p class="text-xs text-gray-500">Stands</p>
                            </div>
                            <div class="text-center bg-white rounded-xl p-3 shadow-sm">
                                <p class="text-2xl font-bold text-blue-600">{{ $nb_paires }}</p>
                                <p class="text-xs text-gray-500">Paires RDV</p>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>

                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="closeGenerateModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="genererPlanning"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2"
                        style="background-color: #C8102E;">
                        <span wire:loading.remove>
                            <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Générer
                        </span>
                        <span wire:loading>
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i> Génération...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL — TRADUCTEUR --}}
    @if($showTraducteurModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-language"></i>
                    Assigner un traducteur
                </h3>
                <button wire:click="fermerModalTraducteur"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8">
                @if($rdv_courant)
                <div class="bg-gray-50 rounded-xl p-4 mb-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                style="background-color: #C8102E;">
                                {{ strtoupper(substr($rdv_courant->participant1->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ $rdv_courant->participant1->nom ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $rdv_courant->participant1->entreprise->nom ?? 'Indépendant' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <i class="fa-solid fa-arrows-left-right text-gray-400"></i>
                            <span class="text-xs text-gray-500 bg-gray-200 px-2 py-0.5 rounded-full">
                                {{ $rdv_courant->heure_debut }} - {{ $rdv_courant->heure_fin }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                style="background-color: #007A3D;">
                                {{ strtoupper(substr($rdv_courant->participant2->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ $rdv_courant->participant2->nom ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $rdv_courant->participant2->entreprise->nom ?? 'Indépendant' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-3">
                        Choisir un traducteur
                        <span class="text-gray-400 font-normal ml-1">(vert = disponible)</span>
                    </label>

                    @if($traducteurs->isEmpty())
                    <div class="text-center py-6 text-gray-400">
                        <i class="fa-solid fa-language text-3xl mb-2 block text-gray-300"></i>
                        Aucun traducteur enregistré
                    </div>
                    @else
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($traducteurs as $traducteur)
                        <label class="{{ !$traducteur->disponible ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                            <input type="radio"
                                wire:model="id_traducteur"
                                value="{{ $traducteur->id }}"
                                class="hidden peer"
                                {{ !$traducteur->disponible ? 'disabled' : '' }}>
                            <div class="flex items-center justify-between p-3 border rounded-xl transition
                                {{ $traducteur->disponible ? 'hover:bg-gray-50 border-gray-200' : 'border-gray-100 bg-gray-50' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                        style="background-color: {{ $traducteur->disponible ? '#007A3D' : '#9ca3af' }}">
                                        {{ strtoupper(substr($traducteur->prenom, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $traducteur->nom }} {{ $traducteur->prenom }}
                                        </p>
                                        <span class="text-xs px-2 py-0.5 rounded-full text-white"
                                            style="background-color: #007A3D;">
                                            {{ $traducteur->langue }}
                                        </span>
                                    </div>
                                </div>
                                @if($traducteur->disponible)
                                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                                    Disponible
                                </span>
                                @else
                                <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-600 font-medium">
                                    Occupé
                                </span>
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

    {{-- MODAL — RE-MATCH --}}
    @if($showRematchModal && $rematch_rdv)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-rotate"></i>
                    Re-match — Reprogrammer le RDV
                </h3>
                <button wire:click="fermerRematch"
                    class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div class="bg-red-100 rounded-xl p-3 text-center">
                            <p class="text-xs text-red-500 font-medium mb-1">
                                <i class="fa-solid fa-user-slash mr-1"></i> Absent
                            </p>
                            <p class="font-bold text-red-700 text-sm">
                                @if($rematch_rdv->absent_participant_id == $rematch_rdv->id_participant1)
                                    {{ $rematch_rdv->participant1->nom ?? '-' }}
                                    {{ $rematch_rdv->participant1->prenom ?? '' }}
                                @else
                                    {{ $rematch_rdv->participant2->nom ?? '-' }}
                                    {{ $rematch_rdv->participant2->prenom ?? '' }}
                                @endif
                            </p>
                        </div>
                        <div class="bg-green-100 rounded-xl p-3 text-center">
                            <p class="text-xs text-green-600 font-medium mb-1">
                                <i class="fa-solid fa-user-check mr-1"></i> Présent
                            </p>
                            <p class="font-bold text-green-700 text-sm">
                                @if($rematch_rdv->absent_participant_id == $rematch_rdv->id_participant1)
                                    {{ $rematch_rdv->participant2->nom ?? '-' }}
                                    {{ $rematch_rdv->participant2->prenom ?? '' }}
                                @else
                                    {{ $rematch_rdv->participant1->nom ?? '-' }}
                                    {{ $rematch_rdv->participant1->prenom ?? '' }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <span><i class="fa-solid fa-calendar mr-1"></i>{{ $rematch_rdv->date }}</span>
                        <span><i class="fa-solid fa-clock mr-1"></i>{{ $rematch_rdv->heure_debut }} - {{ $rematch_rdv->heure_fin }}</span>
                        <span><i class="fa-solid fa-store mr-1"></i>Stand {{ $rematch_rdv->stand->numero_stand ?? '-' }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-2">
                        Choisir le remplaçant *
                    </label>
                    <select wire:model="nouveau_participant"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-300 text-sm">
                        <option value="">-- Choisir un participant --</option>
                        @foreach($participantsDisponibles as $participant)
                        <option value="{{ $participant->id }}">
                            {{ $participant->nom }} {{ $participant->prenom }}
                            {{ $participant->entreprise ? '('.$participant->entreprise->nom.')' : '(Indépendant)' }}
                        </option>
                        @endforeach
                    </select>
                    @error('nouveau_participant')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="fermerRematch"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="effectuerRematch"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm"
                        style="background-color: #8b5cf6;">
                        <i class="fa-solid fa-rotate mr-1"></i> Effectuer le re-match
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>