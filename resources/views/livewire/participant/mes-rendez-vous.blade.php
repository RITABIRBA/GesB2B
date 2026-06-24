<div>

    @if($alertSuccess)
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ $alertSuccess }}
    </div>
    @endif

    @if($alertError)
    <div class="bg-red-100 border border-red-300 text-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
        {{ $alertError }}
    </div>
    @endif

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Mes Rendez-vous</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #2d5a8e;">
                {{ $rendezVous->count() }} RDV
            </span>
        </div>
        <select wire:model.live="filtre_statut" class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Tous les statuts</option>
            <option value="a_planifier">À planifier</option>
            <option value="planifie">Planifié</option>
            <option value="confirme">Confirmé</option>
            <option value="annule">Annulé</option>
            <option value="termine">Terminé</option>
        </select>
    </div>

    {{-- Info absences --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-6 py-4 mb-6 text-sm text-yellow-700 flex items-start gap-2">
        <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
        <div>
            <p class="font-semibold mb-1">Absence à un ou plusieurs rendez-vous ?</p>
            Utilisez le bloc <strong>"Signaler une absence pour toute une journée"</strong>
            ci-dessous. Les partenaires seront notifiés et des remplaçants compatibles leur seront proposés automatiquement.
        </div>
    </div>

    {{-- Signaler absence journée --}}
    @if($datesAvecRdv->isNotEmpty())
    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <h4 class="font-bold text-gray-700 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-calendar-xmark" style="color: #C8102E;"></i>
            Signaler une absence pour toute une journée
        </h4>
        <p class="text-sm text-gray-500 mb-4">
            Signalez votre absence pour une journée entière plutôt que d'annuler chaque RDV individuellement.
        </p>
        <div class="flex flex-wrap gap-3">
            @foreach($datesAvecRdv as $date)
            <button wire:click="signalerAbsenceJournee('{{ $date }}')"
                wire:confirm="Signaler votre absence pour le {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} ? Tous vos rendez-vous de cette journée seront annulés."
                class="px-4 py-2.5 rounded-xl border-2 border-red-200 text-red-600 font-medium text-sm hover:bg-red-50 transition flex items-center gap-2">
                <i class="fa-solid fa-calendar-day"></i>
                {{ \Carbon\Carbon::parse($date)->locale('fr')->translatedFormat('l d F Y') }}
            </button>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Statistiques --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-gray-400">
            <i class="fa-solid fa-hourglass-half text-gray-400 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">À planifier</p>
                <p class="font-bold text-gray-800 text-lg">{{ $rendezVous->where('statut', 'a_planifier')->count() }}</p>
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
    </div>

    {{-- Liste des RDV --}}
    <div class="grid grid-cols-1 gap-4">
        @forelse($rendezVous as $rdv)
        @php
            $estParticipant1 = $rdv->id_participant1 == $participant->id;
            $partenaire      = $estParticipant1 ? $rdv->participant2 : $rdv->participant1;

            $estMutuel = \App\Models\Souhait::where('id_participant', $rdv->id_participant1)
                ->where('id_participant_cible', $rdv->id_participant2)->exists()
                && \App\Models\Souhait::where('id_participant', $rdv->id_participant2)
                ->where('id_participant_cible', $rdv->id_participant1)->exists();

            $candidatsRemplacement = $remplacants[$rdv->id] ?? collect();
        @endphp

        <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition
            {{ $rdv->statut == 'annule' ? 'opacity-90 border-l-4 border-red-400' :
               ($rdv->statut == 'confirme' ? 'border-l-4 border-green-400' :
               ($rdv->statut == 'a_planifier' ? 'border-l-4 border-gray-300' : 'border-l-4 border-blue-400')) }}">

            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-start gap-4 flex-1">

                    <div class="w-14 h-14 rounded-xl flex flex-col items-center justify-center text-white font-bold flex-shrink-0"
                        style="background-color: {{ $rdv->statut == 'annule' ? '#9ca3af' : '#2d5a8e' }}">
                        @if($rdv->numero_table)
                        <span class="text-xs font-normal">Table</span>
                        <span class="text-xl">{{ $rdv->numero_table }}</span>
                        @else
                        <i class="fa-solid fa-handshake text-xl"></i>
                        @endif
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            {{-- ✅ Nom sans M./Mme --}}
                            <p class="font-bold text-gray-800 text-lg">
                                {{ $partenaire->nom ?? '-' }} {{ $partenaire->prenom ?? '' }}
                            </p>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                                {{ $partenaire->entreprise->nom ?? 'Indépendant' }}
                            </span>
                            @if($estMutuel)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">
                                <i class="fa-solid fa-arrows-left-right mr-1"></i> Mutuel
                            </span>
                            @else
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                <i class="fa-solid fa-arrow-right mr-1"></i> Unilatéral
                            </span>
                            @endif
                        </div>

                        @if($partenaire && $partenaire->fonction)
                        <p class="text-xs text-gray-400 mb-2">
                            <i class="fa-solid fa-briefcase mr-1"></i>{{ $partenaire->fonction }}
                            @if($partenaire->secteur_activite) · {{ $partenaire->secteur_activite }} @endif
                        </p>
                        @endif

                        <div class="flex items-center gap-4 text-sm text-gray-500 flex-wrap">
                            @if($rdv->date)
                            <span><i class="fa-solid fa-calendar text-gray-400 mr-1"></i>{{ \Carbon\Carbon::parse($rdv->date)->format('d/m/Y') }}</span>
                            @endif
                            @if($rdv->heure_debut && $rdv->heure_fin)
                            <span><i class="fa-solid fa-clock text-gray-400 mr-1"></i>{{ $rdv->heure_debut }} → {{ $rdv->heure_fin }}</span>
                            @endif
                            @if(!$rdv->date && !$rdv->heure_debut)
                            <span class="text-gray-400 italic"><i class="fa-solid fa-clock mr-1"></i>Horaire non encore planifié</span>
                            @endif
                            @if($rdv->salle)
                            <span class="text-blue-600">
                                <i class="fa-solid fa-door-open mr-1"></i>{{ $rdv->salle }}
                                @if($rdv->numero_table) — Table {{ $rdv->numero_table }} @endif
                            </span>
                            @endif
                            @if($rdv->traducteur)
                            <span class="text-purple-600"><i class="fa-solid fa-language mr-1"></i>{{ $rdv->traducteur->nom }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Statut + actions --}}
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    @if($rdv->statut == 'a_planifier')
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-gray-500">
                        <i class="fa-solid fa-hourglass-half mr-1"></i> À planifier
                    </span>
                    @elseif($rdv->statut == 'planifie')
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                        <i class="fa-solid fa-calendar-check mr-1"></i> Planifié
                    </span>
                    @elseif($rdv->statut == 'confirme')
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium" style="background-color: #007A3D;">
                        <i class="fa-solid fa-circle-check mr-1"></i> Confirmé
                    </span>
                    @elseif($rdv->statut == 'annule')
                        @if($rdv->absent_participant_id == $participant->id)
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-red-600">
                            <i class="fa-solid fa-user-slash mr-1"></i> Vous êtes absent(e)
                        </span>
                        <button wire:click="annulerAbsence({{ $rdv->id }})"
                            wire:confirm="Rétablir votre présence ?"
                            class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700 flex items-center gap-1">
                            <i class="fa-solid fa-rotate-left mr-1"></i> Rétablir la présence
                        </button>
                        @else
                        <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-orange-500">
                            <i class="fa-solid fa-user-slash mr-1"></i> Partenaire absent
                        </span>
                        <p class="text-xs text-gray-400">Choisissez un remplaçant ci-dessous</p>
                        @endif
                    @else
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-gray-500">
                        <i class="fa-solid fa-flag-checkered mr-1"></i> Terminé
                    </span>
                    @endif
                </div>
            </div>

            {{-- Candidats remplacement --}}
            @if($rdv->statut == 'annule')
            <div class="mt-4 pt-4 border-t">
                @if($rdv->absent_participant_id == $participant->id)
                <div class="bg-red-50 rounded-xl p-3 text-xs text-red-600 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i>
                    Votre absence a été signalée. {{ $partenaire->prenom ?? '' }} {{ $partenaire->nom ?? '' }} a été notifié(e) et reçoit une liste de remplaçants compatibles.
                </div>
                @else
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                    <p class="text-sm font-semibold text-orange-700 mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info"></i>
                        {{ $partenaire->prenom ?? '' }} {{ $partenaire->nom ?? '' }} a signalé son absence.
                    </p>
                    <p class="text-xs text-orange-600 mb-3">Voici des participants compatibles qui pourraient le/la remplacer :</p>

                    @if($candidatsRemplacement->isEmpty())
                    <p class="text-xs text-gray-400 italic">Aucun remplaçant compatible disponible pour le moment.</p>
                    @else
                    <div class="space-y-2">
                        @foreach($candidatsRemplacement as $cand)
                        <div class="bg-white rounded-xl p-3 flex items-center justify-between gap-3 flex-wrap shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                    style="background-color: {{ $cand->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                                    {{ strtoupper(substr($cand->prenom ?? 'X', 0, 1)) }}
                                </div>
                                <div>
                                    {{-- ✅ Nom sans M./Mme --}}
                                    <p class="font-semibold text-gray-800 text-sm">{{ $cand->nom }} {{ $cand->prenom }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $cand->entreprise->nom ?? 'Indépendant' }}
                                        @if($cand->secteur_activite) · {{ $cand->secteur_activite }} @endif
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        <i class="fa-solid fa-briefcase mr-1"></i>{{ $cand->fonction ?: 'Non renseigné' }}
                                        <span class="mx-1">•</span>
                                        <i class="fa-solid fa-phone mr-1"></i>{{ $cand->telephone ?: 'Non renseigné' }}
                                    </p>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    {{ $cand->score_compatibilite >= 4 ? 'bg-green-100 text-green-700' :
                                       ($cand->score_compatibilite >= 2 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500') }}">
                                    {{ str_repeat('⭐', min($cand->score_compatibilite, 3)) }}
                                </span>
                            </div>
                            <button wire:click="choisirRemplacant({{ $rdv->id }}, {{ $cand->id }})"
                                wire:confirm="Créer un rendez-vous avec {{ $cand->nom }} {{ $cand->prenom }} ?"
                                class="px-4 py-2 rounded-xl text-white text-xs font-bold transition hover:opacity-90 flex items-center gap-2 shadow"
                                style="background-color: #2d5a8e;">
                                <i class="fa-solid fa-heart"></i> Choisir
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endif

        </div>
        @empty
        <div class="bg-white rounded-xl shadow p-16 text-center text-gray-400">
            <i class="fa-solid fa-handshake text-5xl mb-3 block text-gray-300"></i>
            <p class="text-lg font-medium">Aucun rendez-vous</p>
            <p class="text-sm text-gray-400 mt-1">Émettez des souhaits pour obtenir des rendez-vous</p>
        </div>
        @endforelse
    </div>

</div>