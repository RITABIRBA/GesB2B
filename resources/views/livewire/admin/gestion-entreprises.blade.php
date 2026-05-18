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
            <a href="{{ route('admin.entreprises') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white font-medium" style="background-color: #C8102E;">
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
                <h2 class="text-lg font-semibold text-gray-700">Gestion des Entreprises</h2>
            </div>
            <span class="text-gray-500"> {{ auth()->user()->name }}</span>
        </header>

        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-700">Liste des entreprises</h3>
                <button wire:click="openModal" class="px-6 py-3 rounded-lg text-white font-medium" style="background-color: #C8102E;">
                    + Nouvelle entreprise
                </button>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="pb-3 text-gray-500 font-medium">Nom</th>
                            <th class="pb-3 text-gray-500 font-medium">Secteur</th>
                            <th class="pb-3 text-gray-500 font-medium">Pays</th>
                            <th class="pb-3 text-gray-500 font-medium">Ville</th>
                            <th class="pb-3 text-gray-500 font-medium">Statut</th>
                            <th class="pb-3 text-gray-500 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entreprises as $entreprise)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 font-medium">{{ $entreprise->nom }}</td>
                            <td class="py-3">{{ $entreprise->secteur_activite }}</td>
                            <td class="py-3">{{ $entreprise->pays }}</td>
                            <td class="py-3">{{ $entreprise->ville }}</td>
                            <td class="py-3">
                                @if($entreprise->statut_validation == 'valide')
                                    <span class="px-3 py-1 rounded-full text-sm text-white" style="background-color: #007A3D;">✅ Validé</span>
                                @elseif($entreprise->statut_validation == 'rejete')
                                    <span class="px-3 py-1 rounded-full text-sm text-white bg-red-600">❌ Rejeté</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-sm text-white bg-yellow-500">⏳ En attente</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="flex gap-2 flex-wrap">
                                    @if($entreprise->statut_validation == 'en_attente')
                                    <button wire:click="valider({{ $entreprise->id }})" class="px-3 py-1 rounded text-white text-sm" style="background-color: #007A3D;">
                                         Valider
                                    </button>
                                    <button wire:click="rejeter({{ $entreprise->id }})" class="px-3 py-1 rounded text-white text-sm bg-red-600">
                                         Rejeter
                                    </button>
                                    @endif
                                    <button wire:click="modifier({{ $entreprise->id }})" class="px-3 py-1 rounded text-white text-sm bg-blue-600">
                                         Modifier
                                    </button>
                                    <button wire:click="supprimer({{ $entreprise->id }})" wire:confirm="Voulez-vous vraiment supprimer cette entreprise ?" class="px-3 py-1 rounded text-white text-sm bg-gray-600">
                                         Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                Aucune entreprise pour le moment
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
        <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-2xl overflow-y-auto max-h-screen">
            <h3 class="text-xl font-bold text-gray-700 mb-6">
                {{ $isEditing ? ' Modifier l\'entreprise' : ' Nouvelle entreprise' }}
            </h3>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-gray-600 text-sm mb-1">Nom de l'entreprise *</label>
                    <input wire:model="nom" type="text" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                    @error('nom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm mb-1">Secteur d'activité *</label>
                    <select wire:model="secteur_activite" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                        <option value="">-- Choisir --</option>
                        @foreach($secteurs as $secteur)
                        <option value="{{ $secteur }}">{{ $secteur }}</option>
                        @endforeach
                    </select>
                    @error('secteur_activite') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm mb-1">Sous-secteur <span class="text-gray-400">(optionnel)</span></label>
                    <input wire:model="sous_secteur" type="text" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                </div>

                <div>
                    <label class="block text-gray-600 text-sm mb-1">Pays *</label>
                    <select wire:model="pays" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                        <option value="">-- Choisir --</option>
                        @foreach($pays_liste as $p)
                        <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                    @error('pays') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm mb-1">Ville *</label>
                    <input wire:model="ville" type="text" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                    @error('ville') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm mb-1">Téléphone *</label>
                    <input wire:model="telephone" type="text" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                    @error('telephone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm mb-1">Email *</label>
                    <input wire:model="email" type="email" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
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