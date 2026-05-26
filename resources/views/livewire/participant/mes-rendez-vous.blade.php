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
            <option value="annule">Annulé (absence)</option>
            <option value="termine">Terminé</option>
        </select>
    </div>

    {{-- Info absences --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-6 py-4 mb-6 text-sm text-yellow-700 flex items-start gap-2">
        <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
        <div>
            Si vous ne pouvez pas assister à un rendez-vous, signalez votre absence
            <strong>avant le début de l'événement</strong>. Le système en tiendra compte
            lors du match-making.
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-blue-500">
            <i class="fa-solid fa-calendar-check text-blue-500 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">Planifiés</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $rendezVous->where('statut', 'planifie')->count() }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4"
            style="border-color: #007A3D;">
            <i class="fa-solid fa-circle-check text-xl" style="color: #007A3D;"></i>
            <div>
                <p class="text-xs text-gray-500">Confirmés</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $rendezVous->where('statut', 'confirme')->count() }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-red-500">
            <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">Absences signalées</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $rendezVous->where('statut', 'annule')->count() }}
                </p>
            </div>
        </div>
    </div>

    {{-- Liste des RDV --}}
    <div class="grid grid-cols-1 gap-4">
        @forelse($rendezVous as $rdv)
        @php
            $estParticipant1 = $rdv->id_participant1 == $participant->id;
            $partenaire = $estParticipant1 ? $rdv->participant2 : $rdv->participant1;
        @endphp
        <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition
            {{ $rdv->statut == 'annule' ? 'opacity-75 border-l-4 border-red-400' : '' }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">

                    {{-- Stand --}}
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white font-bold text-xl flex-shrink-0"
                        style="background-color: {{ $rdv->statut == 'annule' ? '#9ca3af' : '#007A3D' }}">
                        {{ $rdv->stand->numero_stand ?? '-' }}
                    </div>

                    <div>
                        {{-- Partenaire --}}
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-bold text-gray-800">
                                {{ $partenaire->nom ?? '-' }} {{ $partenaire->prenom ?? '' }}
                            </p>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                                {{ $partenaire->entreprise->nom ?? 'Indépendant' }}
                            </span>
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
                            @if($rdv->traducteur)
                            <span>
                                <i class="fa-solid fa-language text-gray-400 mr-1"></i>
                                {{ $rdv->traducteur->nom }} ({{ $rdv->traducteur->langue }})
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions + Statut --}}
                <div class="flex items-center gap-3">

                    {{-- Statut --}}
                    @if($rdv->statut == 'planifie')
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                            <i class="fa-solid fa-calendar-check mr-1"></i> Planifié
                        </span>
                        {{-- Bouton signaler absence --}}
                        <button wire:click="signalerAbsence({{ $rdv->id }})"
                            wire:confirm="Voulez-vous signaler votre absence pour ce rendez-vous ?"
                            class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-orange-500 transition hover:bg-orange-600 flex items-center gap-1">
                            <i class="fa-solid fa-user-slash"></i> Signaler absence
                        </button>

                    @elseif($rdv->statut == 'confirme')
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium"
                            style="background-color: #007A3D;">
                            <i class="fa-solid fa-circle-check mr-1"></i> Confirmé
                        </span>

                    @elseif($rdv->statut == 'annule')
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-red-600">
                            <i class="fa-solid fa-user-slash mr-1"></i> Absent
                        </span>
                        {{-- Bouton annuler absence --}}
                        <button wire:click="annulerAbsence({{ $rdv->id }})"
                            wire:confirm="Voulez-vous rétablir ce rendez-vous ?"
                            class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700 flex items-center gap-1">
                            <i class="fa-solid fa-rotate-left"></i> Rétablir
                        </button>

                    @else
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-gray-500">
                            <i class="fa-solid fa-flag-checkered mr-1"></i> Terminé
                        </span>
                    @endif

                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow p-16 text-center text-gray-400">
            <i class="fa-solid fa-handshake text-5xl mb-3 block text-gray-300"></i>
            <p class="text-lg font-medium">Aucun rendez-vous planifié</p>
            <p class="text-sm text-gray-400 mt-1">
                Émettez des souhaits pour obtenir des rendez-vous
            </p>
        </div>
        @endforelse
    </div>

</div>