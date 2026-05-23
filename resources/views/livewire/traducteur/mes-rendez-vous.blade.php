<div>
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Mes Rendez-vous</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $rendezVous->count() }} RDV
            </span>
        </div>
        <div class="flex gap-3">
            <select wire:model.live="filtre_statut"
                class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
                <option value="">Tous les statuts</option>
                <option value="planifie">Planifié</option>
                <option value="confirme">Confirmé</option>
                <option value="termine">Terminé</option>
            </select>
            <input wire:model.live="filtre_date" type="date"
                class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @forelse($rendezVous as $rdv)
        <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    {{-- Stand --}}
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white font-bold text-xl flex-shrink-0"
                        style="background-color: #007A3D;">
                        {{ $rdv->stand->numero_stand ?? '-' }}
                    </div>

                    <div>
                        {{-- Participants --}}
                        <div class="flex items-center gap-3 mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                    style="background-color: #C8102E;">
                                    {{ strtoupper(substr($rdv->participant1->prenom ?? 'X', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">
                                        {{ $rdv->participant1->nom ?? '-' }} {{ $rdv->participant1->prenom ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $rdv->participant1->entreprise->nom ?? 'Indépendant' }}
                                    </p>
                                </div>
                            </div>
                            <i class="fa-solid fa-arrows-left-right text-gray-400 mx-2"></i>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                    style="background-color: #007A3D;">
                                    {{ strtoupper(substr($rdv->participant2->prenom ?? 'X', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">
                                        {{ $rdv->participant2->nom ?? '-' }} {{ $rdv->participant2->prenom ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $rdv->participant2->entreprise->nom ?? 'Indépendant' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Horaire --}}
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span>
                                <i class="fa-solid fa-calendar text-gray-400 mr-1"></i>
                                {{ $rdv->date }}
                            </span>
                            <span>
                                <i class="fa-solid fa-clock text-gray-400 mr-1"></i>
                                {{ $rdv->heure_debut }} - {{ $rdv->heure_fin }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Statut --}}
                <div>
                    @if($rdv->statut == 'planifie')
                        <span class="px-4 py-2 rounded-full text-sm text-white bg-blue-600">
                            <i class="fa-solid fa-calendar-check mr-1"></i> Planifié
                        </span>
                    @elseif($rdv->statut == 'confirme')
                        <span class="px-4 py-2 rounded-full text-sm text-white" style="background-color: #007A3D;">
                            <i class="fa-solid fa-circle-check mr-1"></i> Confirmé
                        </span>
                    @elseif($rdv->statut == 'annule')
                        <span class="px-4 py-2 rounded-full text-sm text-white bg-red-600">
                            <i class="fa-solid fa-circle-xmark mr-1"></i> Annulé
                        </span>
                    @else
                        <span class="px-4 py-2 rounded-full text-sm text-white bg-gray-500">
                            <i class="fa-solid fa-flag-checkered mr-1"></i> Terminé
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow p-16 text-center text-gray-400">
            <i class="fa-solid fa-handshake text-5xl mb-3 block text-gray-300"></i>
            <p class="text-lg font-medium">Aucun rendez-vous assigné</p>
            <p class="text-sm text-gray-400 mt-1">
                L'administrateur vous assignera des rendez-vous selon vos disponibilités.
            </p>
        </div>
        @endforelse
    </div>
</div>