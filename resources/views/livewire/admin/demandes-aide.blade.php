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

    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Demandes d'aide</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #C8102E;">
                {{ $demandes->where('statut', 'en_attente')->count() }} en attente
            </span>
        </div>
        <select wire:model.live="filtre_statut" class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Tous les statuts</option>
            <option value="en_attente">En attente</option>
            <option value="traite">Traitées</option>
        </select>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @forelse($demandes as $d)
        <div class="bg-white rounded-xl shadow p-5 {{ $d->statut == 'en_attente' ? 'border-l-4 border-yellow-400' : 'border-l-4 border-green-400 opacity-75' }}">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <p class="font-bold text-gray-800">
                            {{ $d->participant->nom ?? '-' }} {{ $d->participant->prenom ?? '' }}
                        </p>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                            {{ $d->participant->entreprise->nom ?? 'Indépendant' }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                            {{ ['inscription' => 'Inscription', 'rendez_vous' => 'Souhaits / RDV', 'autre' => 'Autre'][$d->sujet] ?? 'Autre' }}
                        </span>
                        @if($d->evenement)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">
                            {{ $d->evenement->nom }}
                        </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mt-2">{{ $d->message }}</p>
                    <p class="text-xs text-gray-400 mt-2">
                        <i class="fa-solid fa-clock mr-1"></i>{{ $d->created_at->format('d/m/Y H:i') }}
                    </p>

                    @if($d->statut == 'traite' && $d->reponse)
                    <div class="mt-3 bg-green-50 border border-green-200 rounded-xl p-3 text-xs text-green-700 flex items-start gap-2">
                        <i class="fa-solid fa-reply mt-0.5"></i>
                        <div>
                            <span class="font-semibold">Réponse envoyée :</span>
                            {{ $d->reponse }}
                            @if($d->traite_le)
                            <span class="text-green-500 block mt-0.5">
                                Traitée le {{ \Carbon\Carbon::parse($d->traite_le)->format('d/m/Y H:i') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    @if($d->statut == 'traite')
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium" style="background-color: #007A3D;">
                        <i class="fa-solid fa-circle-check mr-1"></i> Traitée
                    </span>
                    @else
                    <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">
                        <i class="fa-solid fa-clock mr-1"></i> En attente
                    </span>
                    <button wire:click="ouvrirTraiter({{ $d->id }})"
                        class="px-4 py-2 rounded-xl text-white text-xs font-bold transition hover:opacity-90 flex items-center gap-2 shadow"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-check"></i> Traiter
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow p-16 text-center text-gray-400">
            <i class="fa-solid fa-circle-question text-5xl mb-3 block text-gray-300"></i>
            <p class="text-lg font-medium">Aucune demande d'aide</p>
        </div>
        @endforelse
    </div>

    {{-- MODAL TRAITER LA DEMANDE --}}
    @if($showTraiterModal && $demande_courante)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center px-8 py-5 border-b sticky top-0 z-10"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    Traiter la demande
                </h3>
                <button wire:click="fermerTraiter" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">

                <div class="bg-gray-50 rounded-xl p-4 mb-5 border border-gray-200">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <p class="font-bold text-gray-800">
                            {{ $demande_courante->participant->nom ?? '-' }} {{ $demande_courante->participant->prenom ?? '' }}
                        </p>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                            {{ $demande_courante->participant->entreprise->nom ?? 'Indépendant' }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                            {{ ['inscription' => 'Inscription', 'rendez_vous' => 'Souhaits / RDV', 'autre' => 'Autre'][$demande_courante->sujet] ?? 'Autre' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">{{ $demande_courante->message }}</p>
                </div>

                <div class="mb-5">
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">
                        Quelle action avez-vous effectuée ? *
                    </label>
                    <p class="text-xs text-gray-400 mb-2">
                        Ce texte sera envoyé au participant dans ses notifications
                        (ex : "Votre inscription a été validée", "Un nouveau rendez-vous
                        vous a été attribué avec...", etc.).
                    </p>
                    <textarea wire:model="reponse_texte" rows="4"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                        placeholder="Ex: Votre inscription a été validée et votre stand confirmé."></textarea>
                    @error('reponse_texte') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button wire:click="fermerTraiter"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        Annuler
                    </button>
                    <button wire:click="confirmerTraiter"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2"
                        style="background-color: #007A3D;">
                        <span wire:loading.remove><i class="fa-solid fa-check mr-1"></i> Confirmer le traitement</span>
                        <span wire:loading><i class="fa-solid fa-spinner fa-spin mr-1"></i> Envoi...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>