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
            <a href="{{ route('admin.entreprises') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:text-white hover:bg-green-800 transition">
                 Entreprises
            </a>
            <a href="{{ route('admin.participants') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white font-medium" style="background-color: #C8102E;">
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
                <h2 class="text-lg font-semibold text-gray-700">Gestion des Participants</h2>
            </div>
            <span class="text-gray-500"> {{ auth()->user()->name }}</span>
        </header>

        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-700">Liste des participants</h3>
                <button wire:click="openModal" class="px-6 py-3 rounded-lg text-white font-medium" style="background-color: #C8102E;">
                    + Nouveau participant
                </button>
            </div>

            <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="pb-3 text-gray-500 font-medium">Nom</th>
                            <th class="pb-3 text-gray-500 font-medium">Prénom</th>
                            <th class="pb-3 text-gray-500 font-medium">Téléphone</th>
                            <th class="pb-3 text-gray-500 font-medium">Email</th>
                            <th class="pb-3 text-gray-500 font-medium">Entreprise</th>
                            <th class="pb-3 text-gray-500 font-medium">Rôle</th>
                            <th class="pb-3 text-gray-500 font-medium">Code</th>
                            <th class="pb-3 text-gray-500 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($participants as $participant)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 font-medium">{{ $participant->nom }}</td>
                            <td class="py-3">{{ $participant->prenom }}</td>
                            <td class="py-3">{{ $participant->telephone }}</td>
                            <td class="py-3">{{ $participant->email ?? '-' }}</td>
                            <td class="py-3">{{ $participant->entreprise->nom ?? 'Indépendant' }}</td>
                            <td class="py-3">
                                <span class="px-3 py-1 rounded-full text-sm text-white" style="background-color: #007A3D;">
                                    {{ ucfirst($participant->role) }}
                                </span>
                            </td>
                            <td class="py-3 font-mono text-sm">{{ $participant->code_acces }}</td>
                            <td class="py-3">
                                <div class="flex gap-2">
                                    <button wire:click="modifier({{ $participant->id }})" class="px-3 py-1 rounded text-white text-sm bg-blue-600">
                                         Modifier
                                    </button>
                                    <button wire:click="supprimer({{ $participant->id }})" wire:confirm="Voulez-vous vraiment supprimer ce participant ?" class="px-3 py-1 rounded text-white text-sm bg-red-600">
                                         Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400">
                                Aucun participant pour le moment
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
                {{ $isEditing ? ' Modifier le participant' : ' Nouveau participant' }}
            </h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-600 text-sm mb-1">Nom *</label>
                    <input wire:model="nom" type="text" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                    @error('nom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm mb-1">Prénom *</label>
                    <input wire:model="prenom" type="text" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                    @error('prenom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm mb-1">Téléphone *</label>
                    <input wire:model="telephone" type="text" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                    @error('telephone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm mb-1">Email <span class="text-gray-400">(optionnel)</span></label>
                    <input wire:model="email" type="email" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm mb-1">Secteur d'activité *</label>
                    <input wire:model="secteur_activite" type="text" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                    @error('secteur_activite') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm mb-1">Rôle *</label>
                    <select wire:model="role" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                        @foreach($roles as $r)
                        <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-600 text-sm mb-1">Événement *</label>
                    <select wire:model="id_evenement" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                        <option value="">-- Choisir --</option>
                        @foreach($evenements as $evenement)
                        <option value="{{ $evenement->id }}">{{ $evenement->nom }}</option>
                        @endforeach
                    </select>
                    @error('id_evenement') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-gray-600 text-sm mb-1">Entreprise <span class="text-gray-400">(optionnel)</span></label>
                    <select wire:model="id_entreprise" class="w-full border rounded-lg px-4 py-2 focus:outline-none">
                        <option value="">-- Indépendant --</option>
                        @foreach($entreprises as $entreprise)
                        <option value="{{ $entreprise->id }}">{{ $entreprise->nom }}</option>
                        @endforeach
                    </select>
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