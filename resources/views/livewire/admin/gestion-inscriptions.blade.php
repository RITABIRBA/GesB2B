<div>
    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- En-tête --}}
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-700">Gestion des Inscriptions</h3>
    </div>

    {{-- ONGLETS --}}
    <div class="flex gap-2 mb-6 bg-white rounded-xl shadow p-1.5 w-fit">
        <button wire:click="changerOnglet('preinscrits')"
            class="px-5 py-2.5 rounded-lg text-sm font-semibold transition flex items-center gap-2
                {{ $onglet === 'preinscrits' ? 'text-white shadow' : 'text-gray-500 hover:bg-gray-50' }}"
            style="{{ $onglet === 'preinscrits' ? 'background-color: #C8102E;' : '' }}">
            <i class="fa-solid fa-clock"></i>
            Préinscrits
            @if($nbPreinscrits > 0)
            <span class="px-2 py-0.5 rounded-full text-xs font-bold
                {{ $onglet === 'preinscrits' ? 'bg-white/20 text-white' : 'bg-red-100 text-red-600' }}">
                {{ $nbPreinscrits }}
            </span>
            @endif
        </button>
        <button wire:click="changerOnglet('inscrits')"
            class="px-5 py-2.5 rounded-lg text-sm font-semibold transition flex items-center gap-2
                {{ $onglet === 'inscrits' ? 'text-white shadow' : 'text-gray-500 hover:bg-gray-50' }}"
            style="{{ $onglet === 'inscrits' ? 'background-color: #007A3D;' : '' }}">
            <i class="fa-solid fa-circle-check"></i>
            Inscrits
            <span class="px-2 py-0.5 rounded-full text-xs font-bold
                {{ $onglet === 'inscrits' ? 'bg-white/20 text-white' : 'bg-green-100 text-green-700' }}">
                {{ $nbInscrits }}
            </span>
        </button>
    </div>

    {{-- Recherche commune --}}
    <div class="flex gap-4 mb-5 flex-wrap">
        <div class="relative w-full md:w-1/3">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input wire:model.live="search" type="text"
                placeholder="Rechercher un participant..."
                class="w-full border rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
        </div>
        <select wire:model.live="filtre_evenement"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Tous les événements</option>
            @foreach($evenements as $evenement)
            <option value="{{ $evenement->id }}">{{ $evenement->nom }}</option>
            @endforeach
        </select>
        @if($onglet === 'inscrits')
        <select wire:model.live="filtre_statut"
            class="border rounded-xl px-4 py-2.5 focus:outline-none text-sm">
            <option value="">Tous les statuts</option>
            <option value="en_attente">En attente</option>
            <option value="paye">Payé</option>
            <option value="annule">Annulé</option>
        </select>
        @endif
    </div>

    {{-- ════════════════════════════════════════════════════ --}}
    {{-- ONGLET PRÉINSCRITS --}}
    {{-- ════════════════════════════════════════════════════ --}}
    @if($onglet === 'preinscrits')

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-5 text-sm text-blue-700 flex items-center gap-2">
        <i class="fa-solid fa-circle-info"></i>
        Ces dossiers ont été soumis via le formulaire public ou créés sans validation.
        Examinez-les et validez ou rejetez chaque demande.
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Contact</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Entreprise & Événement</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Soumis le</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($preinscrits as $p)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                style="background-color: {{ $p->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                                {{ strtoupper(substr($p->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ $p->nom }} {{ $p->prenom }}</p>
                                @if($p->fonction)
                                <p class="text-xs text-gray-400">{{ $p->fonction }}</p>
                                @endif
                                @if($p->filiere)
                                <p class="text-xs text-blue-500">
                                    <i class="fa-solid fa-graduation-cap mr-0.5"></i>{{ $p->filiere }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-xs">
                        @if($p->email)
                        <p class="text-gray-600"><i class="fa-solid fa-envelope text-gray-400 mr-1"></i>{{ $p->email }}</p>
                        @else
                        <p class="text-orange-400"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Pas d'email</p>
                        @endif
                        <p class="text-gray-600 mt-0.5"><i class="fa-solid fa-phone text-gray-400 mr-1"></i>{{ $p->telephone }}</p>
                    </td>
                    <td class="px-6 py-4 text-xs">
                        <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium block w-fit mb-1">
                            <i class="fa-solid fa-building mr-0.5"></i>{{ $p->entreprise->nom ?? 'Indépendant' }}
                        </span>
                        <p class="text-gray-500">
                            <i class="fa-solid fa-calendar text-gray-400 mr-0.5"></i>{{ $p->evenement->nom ?? '-' }}
                        </p>
                        @if($p->evenement && $p->evenement->type_paiement == 'gratuit')
                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-500 text-white font-medium inline-block mt-1">
                            <i class="fa-solid fa-gift mr-0.5"></i> Gratuit
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-500">
                        {{ $p->created_at?->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button wire:click="ouvrirValidationPreinscription({{ $p->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-check mr-1"></i> Valider
                            </button>
                            <button wire:click="ouvrirRejetPreinscription({{ $p->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700">
                                <i class="fa-solid fa-xmark mr-1"></i> Rejeter
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-inbox text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucune préinscription en attente</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════ --}}
    {{-- ONGLET INSCRITS --}}
    {{-- ════════════════════════════════════════════════════ --}}
    @if($onglet === 'inscrits')

    {{-- Statistiques --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-yellow-500">
            <i class="fa-solid fa-clock text-yellow-500 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">En attente paiement</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $inscriptions->where('statut_paiement', 'en_attente')->count() }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4" style="border-color: #007A3D;">
            <i class="fa-solid fa-circle-check text-xl" style="color: #007A3D;"></i>
            <div>
                <p class="text-xs text-gray-500">Payées</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $inscriptions->filter(fn($i) => $i->statut_paiement == 'paye' && (!$i->evenement || $i->evenement->type_paiement != 'gratuit'))->count() }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-blue-500">
            <i class="fa-solid fa-gift text-blue-500 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">Gratuites</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $inscriptions->filter(fn($i) => $i->evenement && $i->evenement->type_paiement == 'gratuit')->count() }}
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3 border-l-4 border-red-500">
            <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
            <div>
                <p class="text-xs text-gray-500">Annulées</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $inscriptions->where('statut_paiement', 'annule')->count() }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Participant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Événement</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Date</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Montant</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Paiement</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Présence</th>
                    <th class="px-6 py-4 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inscriptions as $inscription)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                style="background-color: #C8102E;">
                                {{ strtoupper(substr($inscription->participant->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{ $inscription->participant->nom ?? '-' }}
                                    {{ $inscription->participant->prenom ?? '' }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $inscription->participant->entreprise->nom ?? 'Indépendant' }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        {{ $inscription->evenement->nom ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        {{ $inscription->date_inscription }}
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-800">
                        @if($inscription->evenement && $inscription->evenement->type_paiement == 'gratuit')
                            <span class="text-gray-400 italic text-sm">Gratuit</span>
                        @else
                            {{ number_format($inscription->montant_paye, 0, ',', ' ') }} FCFA
                        @endif
                    </td>
                    {{-- ✅ COLONNE PAIEMENT CORRIGÉE --}}
                    <td class="px-6 py-4">
                        @if($inscription->evenement && $inscription->evenement->type_paiement == 'gratuit')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-blue-500">
                                <i class="fa-solid fa-gift mr-1"></i> Gratuit
                            </span>
                        @elseif($inscription->statut_paiement == 'paye')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium" style="background-color: #007A3D;">
                                <i class="fa-solid fa-circle-check mr-1"></i> Payé
                            </span>
                        @elseif($inscription->statut_paiement == 'annule')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-red-600">
                                <i class="fa-solid fa-circle-xmark mr-1"></i> Annulé
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">
                                <i class="fa-solid fa-clock mr-1"></i> En attente
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($inscription->statut_presence == 'present')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium" style="background-color: #007A3D;">
                                Présent
                            </span>
                        @elseif($inscription->statut_presence == 'excuse')
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-yellow-500">
                                Excusé
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs text-white font-medium bg-gray-400">
                                Absent
                            </span>
                        @endif
                    </td>
                    {{-- ✅ ACTIONS CORRIGÉES — pas de bouton "Valider" si gratuit --}}
                    <td class="px-6 py-4">
                        <div class="flex gap-1.5 flex-wrap">
                            @if($inscription->statut_paiement == 'en_attente' && (!$inscription->evenement || $inscription->evenement->type_paiement != 'gratuit'))
                            <button wire:click="validerPaiement({{ $inscription->id }})"
                                wire:confirm="Voulez-vous vraiment valider ce paiement ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium transition hover:opacity-90 flex items-center gap-1"
                                style="background-color: #007A3D;">
                                <i class="fa-solid fa-check"></i> Valider
                            </button>
                            <button wire:click="annuler({{ $inscription->id }})"
                                wire:confirm="Annuler cette inscription ?"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-red-600 transition hover:bg-red-700 flex items-center gap-1">
                                <i class="fa-solid fa-xmark"></i> Annuler
                            </button>
                            @endif
                            @if($inscription->statut_presence == 'absent')
                            <button wire:click="marquerPresent({{ $inscription->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-blue-600 transition hover:bg-blue-700 flex items-center gap-1">
                                <i class="fa-solid fa-user-check"></i>
                            </button>
                            @else
                            <button wire:click="marquerAbsent({{ $inscription->id }})"
                                class="px-3 py-1.5 rounded-lg text-white text-xs font-medium bg-gray-500 transition hover:bg-gray-600 flex items-center gap-1">
                                <i class="fa-solid fa-user-xmark"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-16 text-center text-gray-400">
                        <i class="fa-solid fa-clipboard-list text-5xl mb-3 block text-gray-300"></i>
                        <p class="text-lg font-medium">Aucune inscription</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    {{-- MODAL VALIDATION PRÉINSCRIPTION --}}
    @if($showModalPreinscription && $preinscription_courante)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> Valider la préinscription
                </h3>
                <button wire:click="fermerValidationPreinscription" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                <div class="bg-gray-50 rounded-xl p-4 mb-5 border border-gray-200">
                    <p class="font-bold text-gray-800">{{ $preinscription_courante->nom }} {{ $preinscription_courante->prenom }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $preinscription_courante->email ?: 'Pas d\'email' }}</p>
                    <p class="text-xs text-gray-500">{{ $preinscription_courante->telephone }}</p>
                    @if($preinscription_courante->entreprise)
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fa-solid fa-building mr-1"></i>{{ $preinscription_courante->entreprise->nom }}
                    </p>
                    @endif
                    @if($preinscription_courante->evenement)
                    <p class="text-xs text-blue-600 mt-1">
                        <i class="fa-solid fa-calendar mr-1"></i>{{ $preinscription_courante->evenement->nom }}
                        @if($preinscription_courante->evenement->type_paiement == 'gratuit')
                        <span class="text-blue-500">(Gratuit)</span>
                        @endif
                    </p>
                    @endif
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-5 text-xs text-blue-700">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    @if($preinscription_courante->email)
                    Un compte sera automatiquement créé avec un mot de passe temporaire.
                    @else
                    Le participant pourra se connecter uniquement avec son code d'accès.
                    @endif
                </div>
                <div class="flex justify-end gap-3">
                    <button wire:click="fermerValidationPreinscription"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        Annuler
                    </button>
                    <button wire:click="validerPreinscription"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-check mr-1"></i> Confirmer la validation
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL REJET --}}
    @if($showModalRejet && $preinscription_courante)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex justify-between items-center px-8 py-5 border-b"
                style="background: linear-gradient(135deg, #C8102E, #a00d25);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark"></i> Rejeter la préinscription
                </h3>
                <button wire:click="fermerRejetPreinscription" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                <div class="bg-gray-50 rounded-xl p-4 mb-5 border border-gray-200">
                    <p class="font-bold text-gray-800">{{ $preinscription_courante->nom }} {{ $preinscription_courante->prenom }}</p>
                </div>
                <div class="mb-5">
                    <label class="block text-gray-600 text-sm font-medium mb-1.5">Motif du rejet *</label>
                    <textarea wire:model="motif_rejet" rows="3"
                        class="w-full border rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm"
                        placeholder="Expliquez la raison du rejet..."></textarea>
                    @error('motif_rejet')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex justify-end gap-3">
                    <button wire:click="fermerRejetPreinscription"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        Annuler
                    </button>
                    <button wire:click="rejeterPreinscription"
                        class="px-6 py-2.5 rounded-xl text-white font-medium transition hover:opacity-90 text-sm shadow"
                        style="background-color: #C8102E;">
                        <i class="fa-solid fa-xmark mr-1"></i> Rejeter
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL COMPTE CRÉÉ --}}
    @if($showModalCompte)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-8 py-6 rounded-t-2xl text-white text-center"
                style="background: linear-gradient(135deg, #007A3D, #005a2d);">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3"
                    style="background-color: rgba(255,255,255,0.2);">
                    <i class="fa-solid fa-user-check text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold">Compte créé !</h3>
                <p class="text-green-200 text-sm mt-1">
                    @if($compte_has_email) Compte email + code d'accès
                    @else Accès par code uniquement @endif
                </p>
            </div>
            <div class="p-8">
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-4 text-xs text-yellow-700 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                    Transmettez ces informations au participant.
                </div>
                <div class="space-y-3">
                    @if($compte_has_email)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-500 mb-1"><i class="fa-solid fa-envelope mr-1"></i> Email</p>
                        <p class="font-semibold text-gray-800">{{ $compte_email }}</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <p class="text-xs text-blue-500 mb-1"><i class="fa-solid fa-lock mr-1"></i> Mot de passe</p>
                        @if($compte_password)
                        <p class="font-mono font-bold text-xl text-blue-700 tracking-widest">{{ $compte_password }}</p>
                        @endif
                    </div>
                    @endif
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                        <p class="text-xs text-red-500 mb-1"><i class="fa-solid fa-key mr-1"></i> Code d'accès</p>
                        <p class="font-mono font-bold text-2xl text-red-700 tracking-widest">{{ $compte_code_acces }}</p>
                    </div>
                </div>
                <button wire:click="closeModalCompte"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow mt-6"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-check mr-1"></i> Fermer
                </button>
            </div>
        </div>
    </div>
    @endif

</div>