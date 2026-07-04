<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-xl font-bold text-gray-700">Sponsors & Partenaires</h3>
            <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #007A3D;">
                {{ $sponsors->count() }} sponsor(s)
            </span>
        </div>
        <button wire:click="openModal"
            class="px-5 py-2.5 rounded-xl text-white font-medium flex items-center gap-2 transition hover:opacity-90 shadow"
            style="background-color: #C8102E;">
            <i class="fa-solid fa-plus"></i> Ajouter un sponsor
        </button>
    </div>

    {{-- Filtres --}}
    <div class="flex gap-3 mb-5 flex-wrap">
        <div class="relative flex-1 min-w-48">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text" placeholder="Rechercher..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none text-sm">
        </div>
        <select wire:model.live="filtre_niveau"
            class="border rounded-xl px-4 py-2.5 text-sm focus:outline-none bg-white">
            <option value="">Tous les niveaux</option>
            <option value="principal">Principal</option>
            <option value="associe">Associé</option>
            <option value="partenaire">Partenaire</option>
            <option value="supporter">Supporter</option>
        </select>
        <select wire:model.live="filtre_evenement"
            class="border rounded-xl px-4 py-2.5 text-sm focus:outline-none bg-white">
            <option value="">Tous les événements</option>
            @foreach($evenements as $ev)
            <option value="{{ $ev->id }}">{{ $ev->nom }}</option>
            @endforeach
        </select>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left" style="min-width: 900px;">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-5 py-4 text-gray-500 font-semibold text-sm">Sponsor / Partenaire</th>
                    <th class="px-5 py-4 text-gray-500 font-semibold text-sm">Niveau</th>
                    <th class="px-5 py-4 text-gray-500 font-semibold text-sm">Événement</th>
                    <th class="px-5 py-4 text-gray-500 font-semibold text-sm">Contact</th>
                    <th class="px-5 py-4 text-gray-500 font-semibold text-sm">Avantages</th>
                    <th class="px-5 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sponsors as $sponsor)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0"
                                style="background-color: {{ $sponsor->niveau_couleur }};">
                                {{ strtoupper(substr($sponsor->nom, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">{{ $sponsor->nom }}</p>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $sponsor->type_entite === 'entreprise' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    <i class="fa-solid {{ $sponsor->type_entite === 'entreprise' ? 'fa-building' : 'fa-user' }} mr-1"></i>
                                    {{ $sponsor->type_entite === 'entreprise' ? 'Entreprise' : 'Personne' }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold text-white"
                            style="background-color: {{ $sponsor->niveau_couleur }};">
                            {{ $sponsor->niveau_label }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-600">
                        <p class="font-medium">{{ $sponsor->evenement->nom ?? '-' }}</p>
                        <p class="text-xs text-gray-400">{{ $sponsor->evenement->annee ?? '' }}</p>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-500">
                        @if($sponsor->nom_contact)
                        <p class="font-medium text-gray-700">{{ $sponsor->nom_contact }}</p>
                        @endif
                        @if($sponsor->email)
                        <p><i class="fa-solid fa-envelope mr-1 text-gray-400"></i>{{ $sponsor->email }}</p>
                        @endif
                        @if($sponsor->telephone)
                        <p><i class="fa-solid fa-phone mr-1 text-gray-400"></i>{{ $sponsor->telephone }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex flex-wrap gap-1">
                            @if($sponsor->nb_stands_gratuits > 0)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">
                                <i class="fa-solid fa-store mr-1"></i>{{ $sponsor->nb_stands_gratuits }} stand(s)
                            </span>
                            @endif
                            @if($sponsor->nb_badges_vip > 0)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 font-medium">
                                <i class="fa-solid fa-id-badge mr-1"></i>{{ $sponsor->nb_badges_vip }} VIP
                            </span>
                            @endif
                            @if($sponsor->remise_inscription > 0)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">
                                <i class="fa-solid fa-percent mr-1"></i>{{ $sponsor->remise_inscription }}% remise
                            </span>
                            @endif
                            @if($sponsor->autres_avantages)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-medium">
                                <i class="fa-solid fa-gift mr-1"></i>Autres
                            </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex gap-2">
                            <button wire:click="voirDetail({{ $sponsor->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 hover:bg-blue-700 transition">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <button wire:click="modifier({{ $sponsor->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="supprimer({{ $sponsor->id }})"
                                wire:confirm="Voulez-vous vraiment supprimer ce sponsor ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 hover:bg-red-700 transition">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-handshake text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucun sponsor enregistré</p>
                        <button wire:click="openModal"
                            class="mt-3 px-5 py-2 rounded-xl text-white text-sm font-medium"
                            style="background-color: #C8102E;">
                            Ajouter le premier sponsor
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL FORMULAIRE --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b sticky top-0 z-10"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-handshake"></i>
                    {{ $isEditing ? 'Modifier le sponsor' : 'Nouveau sponsor / partenaire' }}
                </h3>
                <button wire:click="closeModal" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-8 space-y-5">

                {{-- Type d'entité --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <h4 class="font-bold text-gray-700 mb-3">Type d'entité *</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="type_entite" value="entreprise" class="hidden peer">
                            <div class="p-4 border-2 rounded-xl text-center transition
                                peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 border-gray-200">
                                <i class="fa-solid fa-building text-2xl mb-2 block text-blue-600"></i>
                                <p class="font-bold text-sm text-gray-800">Entreprise</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="type_entite" value="personne" class="hidden peer">
                            <div class="p-4 border-2 rounded-xl text-center transition
                                peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:bg-gray-50 border-gray-200">
                                <i class="fa-solid fa-user text-2xl mb-2 block text-purple-600"></i>
                                <p class="font-bold text-sm text-gray-800">Personne</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Informations générales --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 space-y-4">
                    <h4 class="font-bold text-gray-700">Informations générales</h4>

                    {{-- Lien entreprise existante --}}
                    @if($type_entite === 'entreprise')
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1.5">
                            Lier à une entreprise existante
                            <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <select wire:model.live="id_entreprise"
                            class="w-full border rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none">
                            <option value="">-- Nouvelle entreprise externe --</option>
                            @foreach($entreprises as $ent)
                            <option value="{{ $ent->id }}">{{ $ent->nom }} ({{ $ent->ifu }})</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">
                                {{ $type_entite === 'entreprise' ? 'Nom de l\'entreprise' : 'Nom complet' }} *
                            </label>
                            <input wire:model="nom" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-300"
                                placeholder="{{ $type_entite === 'entreprise' ? 'Ex: Total Energies BF' : 'Ex: Jean Dupont' }}">
                            @error('nom') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">
                                Personne de contact
                            </label>
                            <input wire:model="nom_contact" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none"
                                placeholder="Ex: Marie Kaboré">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Téléphone</label>
                            <input wire:model="telephone" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none"
                                placeholder="Ex: +226 70 00 00 00">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Email</label>
                            <input wire:model="email" type="email"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none"
                                placeholder="contact@sponsor.com">
                            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Site web</label>
                            <input wire:model="site_web" type="text"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none"
                                placeholder="https://www.sponsor.com">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Description</label>
                            <textarea wire:model="description" rows="2"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none resize-none"
                                placeholder="Courte description du sponsor..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Événement + Niveau --}}
                <div class="bg-blue-50 rounded-xl p-5 border border-blue-200 space-y-4">
                    <h4 class="font-bold text-blue-700">Événement & Niveau</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-1.5">Événement *</label>
                            <select wire:model="id_evenement"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-300">
                                <option value="">-- Choisir un événement --</option>
                                @foreach($evenements as $ev)
                                <option value="{{ $ev->id }}">{{ $ev->nom }} ({{ $ev->annee }})</option>
                                @endforeach
                            </select>
                            @error('id_evenement') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-2">Niveau de sponsoring *</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(['principal' => ['Principal', '#FFD700'], 'associe' => ['Associé', '#C0C0C0'], 'partenaire' => ['Partenaire', '#CD7F32'], 'supporter' => ['Supporter', '#6b7280']] as $val => [$label, $color])
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="niveau" value="{{ $val }}" class="hidden peer">
                                <div class="p-3 border-2 rounded-xl text-center transition
                                    peer-checked:border-gray-400 peer-checked:bg-gray-50 hover:bg-gray-50 border-gray-200">
                                    <div class="w-6 h-6 rounded-full mx-auto mb-1" style="background-color: {{ $color }};"></div>
                                    <p class="font-bold text-sm text-gray-800">{{ $label }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Avantages --}}
                <div class="bg-orange-50 rounded-xl p-5 border border-orange-200 space-y-4">
                    <h4 class="font-bold text-orange-700 flex items-center gap-2">
                        <i class="fa-solid fa-gift"></i> Avantages accordés
                    </h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1.5">
                                <i class="fa-solid fa-store text-green-600 mr-1"></i>
                                Stands gratuits
                            </label>
                            <input wire:model="nb_stands_gratuits" type="number" min="0" max="50"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none text-center font-bold"
                                placeholder="0">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1.5">
                                <i class="fa-solid fa-id-badge text-yellow-600 mr-1"></i>
                                Badges VIP
                            </label>
                            <input wire:model="nb_badges_vip" type="number" min="0" max="50"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none text-center font-bold"
                                placeholder="0">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-xs font-medium mb-1.5">
                                <i class="fa-solid fa-percent text-blue-600 mr-1"></i>
                                Remise inscription (%)
                            </label>
                            <input wire:model="remise_inscription" type="number" min="0" max="100" step="5"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none text-center font-bold"
                                placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs font-medium mb-1.5">
                            <i class="fa-solid fa-star text-purple-600 mr-1"></i>
                            Autres avantages
                            <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <textarea wire:model="autres_avantages" rows="2"
                            class="w-full border rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none resize-none"
                            placeholder="Ex: Logo sur les supports de communication, mention dans le programme..."></textarea>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-8 py-4 bg-gray-50 border-t flex justify-end gap-3 rounded-b-2xl">
                <button type="button" wire:click="closeModal"
                    class="px-5 py-2 rounded-xl text-gray-500 hover:bg-gray-100 transition text-sm font-medium">
                    Annuler
                </button>
                <button type="button" wire:click="sauvegarder"
                    wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed"
                    class="px-6 py-2 rounded-xl text-white font-medium text-sm transition hover:opacity-90 shadow"
                    style="background-color: #007A3D;">
                    <span wire:loading.remove>
                        <i class="fa-solid fa-floppy-disk mr-1"></i>
                        {{ $isEditing ? 'Enregistrer' : 'Ajouter le sponsor' }}
                    </span>
                    <span wire:loading>
                        <i class="fa-solid fa-spinner fa-spin mr-1"></i> Enregistrement...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL DÉTAIL --}}
    @if($showModalDetail && $sponsor_courant)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background-color: {{ $sponsor_courant->niveau_couleur }};">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-handshake"></i>
                    {{ $sponsor_courant->nom }}
                </h3>
                <button wire:click="fermerDetail" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <div class="p-6 space-y-4">

                {{-- Niveau --}}
                <div class="text-center py-3">
                    <span class="px-6 py-2 rounded-full text-white font-bold text-sm"
                        style="background-color: {{ $sponsor_courant->niveau_couleur }};">
                        {{ $sponsor_courant->niveau_label }}
                    </span>
                </div>

                {{-- Infos --}}
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-sm text-gray-500">Type</span>
                        <span class="font-semibold text-sm text-gray-800">
                            {{ $sponsor_courant->type_entite === 'entreprise' ? 'Entreprise' : 'Personne' }}
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-sm text-gray-500">Événement</span>
                        <span class="font-semibold text-sm text-gray-800">
                            {{ $sponsor_courant->evenement->nom ?? '-' }}
                        </span>
                    </div>
                    @if($sponsor_courant->nom_contact)
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-sm text-gray-500">Contact</span>
                        <span class="font-semibold text-sm text-gray-800">{{ $sponsor_courant->nom_contact }}</span>
                    </div>
                    @endif
                    @if($sponsor_courant->email)
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-sm text-gray-500">Email</span>
                        <span class="font-semibold text-sm text-gray-800">{{ $sponsor_courant->email }}</span>
                    </div>
                    @endif
                    @if($sponsor_courant->telephone)
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-sm text-gray-500">Téléphone</span>
                        <span class="font-semibold text-sm text-gray-800">{{ $sponsor_courant->telephone }}</span>
                    </div>
                    @endif
                    @if($sponsor_courant->site_web)
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-sm text-gray-500">Site web</span>
                        <a href="{{ $sponsor_courant->site_web }}" target="_blank"
                            class="font-semibold text-sm text-blue-600 hover:underline">
                            {{ $sponsor_courant->site_web }}
                        </a>
                    </div>
                    @endif
                </div>

                {{-- Avantages --}}
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                    <h4 class="font-bold text-orange-700 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-gift"></i> Avantages accordés
                    </h4>
                    @if(count($sponsor_courant->total_avantages) > 0)
                    <div class="space-y-2">
                        @foreach($sponsor_courant->total_avantages as $avantage)
                        <div class="flex items-center gap-2 text-sm text-orange-700">
                            <i class="fa-solid fa-check-circle text-green-500"></i>
                            {{ $avantage }}
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-400 italic">Aucun avantage défini</p>
                    @endif
                </div>

                @if($sponsor_courant->description)
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-400 mb-1">Description</p>
                    <p class="text-sm text-gray-700">{{ $sponsor_courant->description }}</p>
                </div>
                @endif

            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex justify-between gap-3 rounded-b-2xl">
                <button wire:click="fermerDetail"
                    class="px-5 py-2 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                    Fermer
                </button>
                <button wire:click="modifier({{ $sponsor_courant->id }}); fermerDetail()"
                    class="px-5 py-2 rounded-xl text-white font-medium text-sm transition hover:opacity-90"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-pen mr-1"></i> Modifier
                </button>
            </div>
        </div>
    </div>
    @endif

</div>