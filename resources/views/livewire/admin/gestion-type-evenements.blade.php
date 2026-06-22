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
            <h3 class="text-xl font-bold text-gray-700">Types d'événements</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #007A3D;">
                {{ $types->count() }} type(s)
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i>
            Nouveau type d'événement
        </button>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 text-sm text-blue-700 flex items-start gap-2">
        <i class="fa-solid fa-circle-info mt-0.5"></i>
        Les types d'événements permettent de classer vos événements
        (ex : Foire internationale, Forum économique, Salon professionnel...).
        Ils sont sélectionnables lors de la création d'un événement.
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Type d'événement</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Événements rattachés</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($types as $type)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-tag text-sm"></i>
                            </div>
                            <span class="font-semibold text-gray-800">{{ $type->nom }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                            {{ $type->evenements()->count() }} événement(s)
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button wire:click="modifier({{ $type->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </button>
                            <button wire:click="supprimer({{ $type->id }})"
                                wire:confirm="Voulez-vous vraiment supprimer ce type d'événement ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700">
                                <i class="fa-solid fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-tags text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun type d'événement</p>
                        <button wire:click="openModal"
                            class="mt-3 px-5 py-2 rounded-xl text-white text-sm font-medium"
                            style="background-color: #C8102E;">
                            Créer le premier type
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
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-plus' }}"></i>
                    {{ $isEditing ? "Modifier le type d'événement" : "Nouveau type d'événement" }}
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">
                        Nom du type d'événement *
                    </label>
                    <input wire:model="nom" type="text"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                        placeholder="Ex: Forum économique, Foire internationale...">
                    @error('nom') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 mt-7">
                    <button wire:click="closeModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm font-medium">
                        <i class="fa-solid fa-xmark mr-1"></i> Annuler
                    </button>
                    <button wire:click="sauvegarder"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #C8102E;">
                        <span wire:loading.remove>
                            <i class="fa-solid {{ $isEditing ? 'fa-pen' : 'fa-floppy-disk' }} mr-1"></i>
                            {{ $isEditing ? 'Modifier' : 'Enregistrer' }}
                        </span>
                        <span wire:loading>
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i> Enregistrement...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>