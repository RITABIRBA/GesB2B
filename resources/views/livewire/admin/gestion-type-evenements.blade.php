<div class="flex h-screen bg-gray-100">

    <aside class="w-64 text-white flex flex-col" style="background-color: #007A3D;">
        <div class="p-6 text-center border-b border-green-800">
            <h1 class="text-xl font-bold text-white"> CCI-BF</h1>
            <p class="text-sm mt-1 text-green-200">GesB2B — Administration</p>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Dashboard
            </a>
            <a href="{{ route('admin.evenements') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Événements
            </a>
            <a href="{{ route('admin.type-evenements') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white font-medium" style="background-color: #C8102E;">
                 Types d'événements
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Entreprises
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Participants
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Stands
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Rendez-vous
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Badges
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Traducteurs
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Notifications
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Utilisateurs
            </a>
        </nav>

        <div class="p-4 border-t border-green-800">
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:bg-red-700 hover:text-white transition">
                    🚪 Déconnexion
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <header class="bg-white shadow px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <span class="text-2xl font-bold" style="color: #C8102E;">CCI-BF</span>
                <span class="text-gray-400">|</span>
                <h2 class="text-lg font-semibold text-gray-700">Types d'événements</h2>
            </div>
            <span class="text-gray-500"> {{ auth()->user()->name }}</span>
        </header>

        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-700">Liste des types d'événements</h3>
                <button wire:click="openModal" class="px-6 py-3 rounded-lg text-white font-medium" style="background-color: #C8102E;">
                    + Nouveau type
                </button>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="pb-3 text-gray-500 font-medium">Nom</th>
                            <th class="pb-3 text-gray-500 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $type)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3">{{ $type->nom }}</td>
                            <td class="py-3 flex gap-2">
                                <button wire:click="modifier({{ $type->id }})" class="px-3 py-1 rounded text-white text-sm" style="background-color: #007A3D;">
                                     Modifier
                                </button>
                                <button wire:click="supprimer({{ $type->id }})" wire:confirm="Voulez-vous vraiment supprimer ce type ?" class="px-3 py-1 rounded text-white text-sm bg-red-600">
                                     Supprimer
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="py-8 text-center text-gray-400">
                                Aucun type d'événement
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md">
            <h3 class="text-xl font-bold text-gray-700 mb-6">
                {{ $isEditing ? ' Modifier le type' : ' Nouveau type' }}
            </h3>

            <div>
                <label class="block text-gray-600 text-sm mb-1">Nom du type</label>
                <input wire:model="nom" type="text" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                @error('nom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <button wire:click="closeModal" class="px-6 py-2 rounded-lg border text-gray-600 hover:bg-gray-100">
                    Annuler
                </button>
                <button wire:click="sauvegarder" class="px-6 py-2 rounded-lg text-white font-medium" style="background-color: #C8102E;">
                    {{ $isEditing ? 'Modifier' : 'Enregistrer' }}
                </button>
            </div>
        </div>
    </div>
    @endif

</div>