<div>

    {{-- Messages --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Notifications</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium"
                style="background-color: #007A3D;">
                {{ $notifications->count() }} notification(s)
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Nouvelle notification
        </button>
    </div>

    {{-- Recherche --}}
    <div class="mb-5">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher une notification..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Contenu</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Type</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Date d'envoi</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifications as $notification)
                <tr class="border-b hover:bg-gray-50 transition">

                    {{-- Contenu --}}
                    <td class="px-6 py-4">
                        <p class="text-gray-800 text-sm">{{ $notification->contenu }}</p>
                    </td>

                    {{-- Type --}}
                    <td class="px-6 py-4">
                        @if($notification->type == 'alerte')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-red-600">
                                <i class="fa-solid fa-triangle-exclamation mr-1"></i> Alerte
                            </span>
                        @elseif($notification->type == 'rappel')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">
                                <i class="fa-solid fa-clock mr-1"></i> Rappel
                            </span>
                        @elseif($notification->type == 'confirmation')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-circle-check mr-1"></i> Confirmation
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-600">
                                <i class="fa-solid fa-circle-info mr-1"></i> Info
                            </span>
                        @endif
                    </td>

                    {{-- Date --}}
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        <i class="fa-regular fa-clock text-gray-400 mr-1"></i>
                        {{ $notification->date_envoie ?? $notification->created_at->format('d/m/Y H:i') }}
                    </td>

                    {{-- Actions --}}
                    <td class="px-6 py-4">
                        <button wire:click="supprimer({{ $notification->id }})"
                            wire:confirm="Voulez-vous vraiment supprimer cette notification ?"
                            class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                            <i class="fa-solid fa-trash"></i> Supprimer
                        </button>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-bell text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucune notification envoyée</p>
                        <button wire:click="openModal"
                            class="mt-3 px-5 py-2 rounded-xl text-white text-sm font-medium"
                            style="background-color: #C8102E;">
                            Envoyer la première notification
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

            {{-- Header --}}
            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-bell"></i>
                    Nouvelle notification
                </h3>
                <button wire:click="closeModal"
                    class="text-white/70 hover:text-white text-2xl transition">
                    &times;
                </button>
            </div>

            {{-- Body --}}
            <div class="p-8">
                <div class="grid grid-cols-1 gap-5">

                   {{-- Type --}}
<div>
    <label class="block text-gray-600 text-sm font-medium mb-2">Type *</label>
    <div class="flex gap-3 flex-wrap">
        <button type="button"
            wire:click="$set('type', 'systeme')"
            class="px-4 py-2.5 rounded-xl border text-sm font-medium transition flex items-center gap-2
                {{ $type === 'systeme'
                    ? 'border-red-400 bg-red-50 text-red-700'
                    : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
            <i class="fa-solid fa-bell text-blue-500"></i> Système
        </button>
        <button type="button"
            wire:click="$set('type', 'email')"
            class="px-4 py-2.5 rounded-xl border text-sm font-medium transition flex items-center gap-2
                {{ $type === 'email'
                    ? 'border-red-400 bg-red-50 text-red-700'
                    : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
            <i class="fa-solid fa-envelope text-green-500"></i> Email
        </button>
        <button type="button"
            wire:click="$set('type', 'sms')"
            class="px-4 py-2.5 rounded-xl border text-sm font-medium transition flex items-center gap-2
                {{ $type === 'sms'
                    ? 'border-red-400 bg-red-50 text-red-700'
                    : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
            <i class="fa-solid fa-message text-yellow-500"></i> SMS
        </button>
    </div>
</div>
                    {{-- Destinataires --}}
<div>
    <label class="block text-gray-600 text-sm font-medium mb-2">Destinataires *</label>
    <div class="flex gap-3">
        <button type="button"
            wire:click="$set('destinataire', 'tous')"
            class="px-4 py-2.5 rounded-xl border text-sm font-medium transition
                {{ $destinataire === 'tous'
                    ? 'border-red-400 bg-red-50 text-red-700'
                    : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
            Tous
        </button>
        <button type="button"
            wire:click="$set('destinataire', 'participants')"
            class="px-4 py-2.5 rounded-xl border text-sm font-medium transition
                {{ $destinataire === 'participants'
                    ? 'border-red-400 bg-red-50 text-red-700'
                    : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
            Participants
        </button>
        <button type="button"
            wire:click="$set('destinataire', 'entreprises')"
            class="px-4 py-2.5 rounded-xl border text-sm font-medium transition
                {{ $destinataire === 'entreprises'
                    ? 'border-red-400 bg-red-50 text-red-700'
                    : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
            Entreprises
        </button>
    </div>
</div>
                    {{-- Événement --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Événement concerné
                            <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <select wire:model="id_evenement"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                            <option value="">-- Tous les événements --</option>
                            @foreach($evenements as $evenement)
                            <option value="{{ $evenement->id }}">{{ $evenement->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Contenu --}}
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Message *
                        </label>
                        <textarea wire:model="contenu" rows="4"
                            class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm resize-none"
                            placeholder="Tapez votre message ici..."></textarea>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ strlen($contenu) }}/1000 caractères
                        </p>
                        @error('contenu')
                            <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                </div>

                {{-- Boutons --}}
                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="closeModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="envoyer"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-paper-plane mr-1"></i> Envoyer
                    </button>
                </div>
            </div>

        </div>
    </div>
    @endif

</div>