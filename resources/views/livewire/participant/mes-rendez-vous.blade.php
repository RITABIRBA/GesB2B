<div>
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Mes Rendez-vous</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $rendezVous->count() }} RDV
            </span>
        </div>
        <select wire:model.live="filtre_statut"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Tous les statuts</option>
            <option value="planifie">Planifié</option>
            <option value="confirme">Confirmé</option>
            <option value="annule">Annulé</option>
            <option value="termine">Terminé</option>
        </select>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @forelse($rendezVous as $rdv)
        <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg"
                        style="background-color: #007A3D;">
                        {{ $rdv->stand->numero_stand ?? '-' }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">
                            {{ $rdv->participant1->nom ?? '-' }} {{ $rdv->participant1->prenom ?? '' }}
                            <span class="text-gray-400 mx-2">↔</span>
                            {{ $rdv->participant2->nom ?? '-' }} {{ $rdv->participant2->prenom ?? '' }}
                        </p>
                        <div class="flex items-center gap-4 mt-1 text-sm text-gray-500">
                            <span><i class="fa-solid fa-calendar mr-1"></i>{{ $rdv->date }}</span>
                            <span><i class="fa-solid fa-clock mr-1"></i>{{ $rdv->heure_debut }} - {{ $rdv->heure_fin }}</span>
                            @if($rdv->traducteur)
                            <span><i class="fa-solid fa-language mr-1"></i>{{ $rdv->traducteur->nom }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                    @if($rdv->statut == 'planifie')
                        <span class="px-4 py-2 rounded-full text-sm text-white bg-blue-600">Planifié</span>
                    @elseif($rdv->statut == 'confirme')
                        <span class="px-4 py-2 rounded-full text-sm text-white" style="background-color: #007A3D;">Confirmé</span>
                    @elseif($rdv->statut == 'annule')
                        <span class="px-4 py-2 rounded-full text-sm text-white bg-red-600">Annulé</span>
                    @else
                        <span class="px-4 py-2 rounded-full text-sm text-white bg-gray-500">Terminé</span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow p-16 text-center text-gray-400">
            <i class="fa-solid fa-handshake text-5xl mb-3 block text-gray-300"></i>
            <p class="text-lg font-medium">Aucun rendez-vous planifié</p>
            <p class="text-sm text-gray-400 mt-1">Émettez des souhaits pour obtenir des rendez-vous</p>
        </div>
        @endforelse
    </div>
</div>