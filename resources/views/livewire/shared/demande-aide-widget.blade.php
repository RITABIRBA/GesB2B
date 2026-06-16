<div>
    {{-- Bouton flottant --}}
    <button wire:click="ouvrir"
        class="fixed bottom-6 right-6 z-40 px-5 py-3 rounded-full text-white font-medium shadow-lg flex items-center gap-2 transition hover:opacity-90"
        style="background-color: #C8102E;">
        <i class="fa-solid fa-circle-question"></i>
        Besoin d'aide ?
    </button>

    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center px-8 py-5 border-b sticky top-0 z-10"
                style="background: linear-gradient(135deg, #C8102E, #a00d25);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-circle-question"></i>
                    Demander de l'aide
                </h3>
                <button wire:click="fermer" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">

                @if($alertSuccess)
                <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-circle-check text-green-500"></i>
                    {{ $alertSuccess }}
                </div>
                @endif

                @if($alertError)
                <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-circle-xmark text-red-500"></i>
                    {{ $alertError }}
                </div>
                @endif

                <p class="text-sm text-gray-500 mb-4">
                    Votre Chef de Délégation (ou l'administration s'il n'y en a pas)
                    examinera votre demande et vous indiquera l'action effectuée.
                    Vous serez notifié(e) dans vos notifications.
                </p>

                <div class="mb-4">
                    <label class="block text-gray-600 text-sm font-medium mb-2">Sujet *</label>
                    <div class="grid grid-cols-1 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="sujet" value="inscription" class="hidden peer">
                            <div class="flex items-center gap-3 p-3 border-2 rounded-xl transition peer-checked:border-red-400 peer-checked:bg-red-50 hover:bg-gray-50 border-gray-200">
                                <i class="fa-solid fa-clipboard-list text-gray-400"></i>
                                <span class="text-sm text-gray-700">Mon inscription</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="sujet" value="rendez_vous" class="hidden peer">
                            <div class="flex items-center gap-3 p-3 border-2 rounded-xl transition peer-checked:border-red-400 peer-checked:bg-red-50 hover:bg-gray-50 border-gray-200">
                                <i class="fa-solid fa-handshake text-gray-400"></i>
                                <span class="text-sm text-gray-700">Mes souhaits / rendez-vous</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="sujet" value="autre" class="hidden peer">
                            <div class="flex items-center gap-3 p-3 border-2 rounded-xl transition peer-checked:border-red-400 peer-checked:bg-red-50 hover:bg-gray-50 border-gray-200">
                                <i class="fa-solid fa-circle-question text-gray-400"></i>
                                <span class="text-sm text-gray-700">Autre</span>
                            </div>
                        </label>
                    </div>
                    @error('sujet') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Votre message *</label>
                    <textarea wire:model="message" rows="4"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                        placeholder="Expliquez votre problème ou votre question..."></textarea>
                    @error('message') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 mb-6">
                    <button wire:click="fermer"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        Fermer
                    </button>
                    <button wire:click="envoyer"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2"
                        style="background-color: #C8102E;">
                        <span wire:loading.remove><i class="fa-solid fa-paper-plane mr-1"></i> Envoyer</span>
                        <span wire:loading><i class="fa-solid fa-spinner fa-spin mr-1"></i> Envoi...</span>
                    </button>
                </div>

                @if($mesDemandes->isNotEmpty())
                <div class="border-t pt-4">
                    <p class="text-xs font-semibold text-gray-500 mb-2 uppercase">Mes demandes récentes</p>
                    <div class="space-y-2">
                        @foreach($mesDemandes as $d)
                        <div class="bg-gray-50 rounded-xl p-3 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-700">
                                        {{ ['inscription' => 'Inscription', 'rendez_vous' => 'Souhaits / RDV', 'autre' => 'Autre'][$d->sujet] ?? 'Autre' }}
                                    </p>
                                    <p class="text-gray-400 mt-0.5">{{ Str::limit($d->message, 60) }}</p>
                                </div>
                                @if($d->statut == 'traite')
                                <span class="px-2 py-1 rounded-full text-white font-medium flex-shrink-0" style="background-color: #007A3D;">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Traitée
                                </span>
                                @else
                                <span class="px-2 py-1 rounded-full text-white font-medium bg-yellow-500 flex-shrink-0">
                                    <i class="fa-solid fa-clock mr-1"></i> En attente
                                </span>
                                @endif
                            </div>

                            @if($d->statut == 'traite' && $d->reponse)
                            <div class="mt-2 pt-2 border-t border-gray-200 flex items-start gap-2">
                                <i class="fa-solid fa-reply mt-0.5" style="color: #007A3D;"></i>
                                <p class="text-gray-600">
                                    <span class="font-semibold" style="color: #007A3D;">Réponse :</span>
                                    {{ $d->reponse }}
                                </p>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
    @endif
</div>