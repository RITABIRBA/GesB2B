<div>
    {{-- Bouton flottant --}}
    <button wire:click="ouvrir"
        class="fixed bottom-6 right-6 z-40 w-14 h-14 rounded-full shadow-2xl text-white flex items-center justify-center text-2xl transition hover:scale-110"
        style="background-color: #C8102E;"
        title="Besoin d'aide ?">
        <i class="fa-solid fa-circle-question"></i>
    </button>

    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex justify-between items-center px-6 py-5 border-b"
                style="background: linear-gradient(135deg, #C8102E, #a00d25);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-circle-question"></i> Besoin d'aide ?
                </h3>
                <button wire:click="fermer" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-6">

                @if($alertSuccess)
                <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-green-500"></i>
                    {{ $alertSuccess }}
                </div>
                @endif

                @if($alertError)
                <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark text-red-500"></i>
                    {{ $alertError }}
                </div>
                @endif

                <p class="text-sm text-gray-500 mb-4">
                    Un problème avec votre inscription ou vos rendez-vous ?
                    Décrivez votre besoin ci-dessous, votre Chef de Délégation
                    (ou l'administration) sera notifié.
                </p>

                <div class="mb-4">
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Sujet *</label>
                    <select wire:model="sujet"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                        <option value="inscription">Mon inscription</option>
                        <option value="rendez_vous">Mes souhaits / rendez-vous</option>
                        <option value="autre">Autre</option>
                    </select>
                    @error('sujet') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Votre message *</label>
                    <textarea wire:model="message" rows="4" maxlength="500"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                        placeholder="Décrivez votre problème ou votre question..."></textarea>
                    @error('message') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                @if($mesDemandes->count() > 0)
                <div class="mb-4">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Vos demandes récentes</p>
                    <div class="space-y-2 max-h-32 overflow-y-auto">
                        @foreach($mesDemandes as $demande)
                        <div class="bg-gray-50 rounded-lg p-2 text-xs flex items-center justify-between gap-2">
                            <span class="text-gray-600 truncate flex-1">{{ Str::limit($demande->message, 40) }}</span>
                            @if($demande->statut == 'traite')
                            <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium flex-shrink-0">Traité</span>
                            @else
                            <span class="px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 font-medium flex-shrink-0">En attente</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="flex justify-end gap-3">
                    <button wire:click="fermer"
                        class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        Fermer
                    </button>
                    <button wire:click="envoyer"
                        wire:loading.attr="disabled"
                        class="px-5 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow flex items-center gap-2"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-paper-plane"></i>
                        Envoyer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>