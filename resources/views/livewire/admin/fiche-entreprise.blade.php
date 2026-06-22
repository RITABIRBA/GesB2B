<div>
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.entreprises') }}"
            class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
            <i class="fa-solid fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    {{-- CARTE PROFIL --}}
    <div class="bg-white rounded-2xl shadow p-8 mb-6">
        <div class="flex items-start gap-6 flex-wrap">
            <div class="w-20 h-20 rounded-xl flex items-center justify-center text-white text-3xl font-bold flex-shrink-0"
                style="background-color: #007A3D;">
                {{ strtoupper(substr($entreprise->nom, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-[280px]">
                <h2 class="text-2xl font-bold text-gray-800">{{ $entreprise->nom }}</h2>
                <div class="flex items-center gap-3 mt-2 flex-wrap">
                    @if($entreprise->statut_validation == 'valide')
                    <span class="text-sm px-3 py-1 rounded-full text-white font-medium" style="background-color: #007A3D;">
                        <i class="fa-solid fa-circle-check mr-1"></i> Validée
                    </span>
                    @else
                    <span class="text-sm px-3 py-1 rounded-full text-white font-medium bg-yellow-500">
                        <i class="fa-solid fa-clock mr-1"></i> En attente
                    </span>
                    @endif
                    @if($entreprise->ifu)
                    <span class="text-sm font-mono bg-gray-100 px-3 py-1 rounded-full text-gray-600">IFU: {{ $entreprise->ifu }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-100">
            <div>
                <p class="text-xs text-gray-400 mb-1">Secteur d'activité</p>
                <p class="text-sm text-gray-700">
                    <i class="fa-solid fa-industry text-gray-400 mr-1"></i>{{ $entreprise->secteur_activite ?? '-' }}
                    @if($entreprise->sous_secteur) / {{ $entreprise->sous_secteur }} @endif
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Localisation</p>
                <p class="text-sm text-gray-700"><i class="fa-solid fa-location-dot text-gray-400 mr-1"></i>{{ $entreprise->ville ?? '-' }}, {{ $entreprise->pays ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Contact</p>
                <p class="text-sm text-gray-700"><i class="fa-solid fa-phone text-gray-400 mr-1"></i>{{ $entreprise->contact ?? '-' }}</p>
                @if($entreprise->email_responsable)
                <p class="text-sm text-gray-700 mt-1"><i class="fa-solid fa-envelope text-gray-400 mr-1"></i>{{ $entreprise->email_responsable }}</p>
                @endif
            </div>
            @if($entreprise->nom_responsable)
            <div class="md:col-span-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-400 mb-1">Responsable déclaré</p>
                <p class="text-sm text-gray-700">{{ $entreprise->nom_responsable }} {{ $entreprise->prenom_responsable }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $membres->count() }}</p>
            <p class="text-xs text-gray-400">Membres</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $stands->count() }}</p>
            <p class="text-xs text-gray-400">Stands</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $totalRdv }}</p>
            <p class="text-xs text-gray-400">Rendez-vous</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $paiements->where('statut', 'valide')->count() }}</p>
            <p class="text-xs text-gray-400">Paiements validés</p>
        </div>
    </div>

    {{-- MEMBRES --}}
    <div class="bg-white rounded-xl shadow mb-6">
        <div class="px-6 py-4 border-b" style="background-color: #f8f9fa;">
            <h4 class="font-bold text-gray-700"><i class="fa-solid fa-users mr-2" style="color: #C8102E;"></i>Membres ({{ $membres->count() }})</h4>
        </div>
        <table class="w-full text-left">
            <thead style="background-color: #f8f9fa;">
                <tr class="border-b">
                    <th class="px-6 py-3 text-gray-500 font-semibold text-sm">Membre</th>
                    <th class="px-6 py-3 text-gray-500 font-semibold text-sm">Fonction</th>
                    <th class="px-6 py-3 text-gray-500 font-semibold text-sm">Contact</th>
                    <th class="px-6 py-3 text-gray-500 font-semibold text-sm">Rôle</th>
                    <th class="px-6 py-3 text-gray-500 font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($membres as $m)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                style="background-color: {{ $m->genre == 'femme' ? '#C8102E' : '#007A3D' }}">
                                {{ strtoupper(substr($m->prenom ?? 'X', 0, 1)) }}
                            </div>
                            <span class="font-semibold text-gray-800 text-sm">{{ $m->nom }} {{ $m->prenom }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $m->fonction ?? '-' }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $m->telephone ?? '-' }}</td>
                    <td class="px-6 py-3">
                        <span class="text-xs px-2 py-1 rounded-full text-white font-medium"
                            style="background-color: {{ $m->role == 'representant' ? '#C8102E' : '#007A3D' }}">
                            {{ ucfirst($m->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-3">
                        <a href="{{ route('admin.fiche-participant', $m->id) }}"
                            class="text-xs px-3 py-1.5 rounded-lg text-white font-medium bg-blue-600 hover:bg-blue-700 transition">
                            <i class="fa-solid fa-eye mr-1"></i> Voir
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-8 text-gray-400">Aucun membre</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- STANDS --}}
    @if($stands->isNotEmpty())
    <div class="bg-white rounded-xl shadow mb-6">
        <div class="px-6 py-4 border-b" style="background-color: #f8f9fa;">
            <h4 class="font-bold text-gray-700"><i class="fa-solid fa-store mr-2" style="color: #007A3D;"></i>Stands</h4>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($stands as $s)
            <div class="border border-gray-200 rounded-xl p-4 flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">Stand N°{{ $s->numero_stand }} — {{ $s->standing }}</p>
                    <p class="text-xs text-gray-400">{{ $s->superficie }}</p>
                </div>
                @if($s->est_gratuit)
                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">Gratuit</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- PAIEMENTS --}}
    <div class="bg-white rounded-xl shadow">
        <div class="px-6 py-4 border-b" style="background-color: #f8f9fa;">
            <h4 class="font-bold text-gray-700"><i class="fa-solid fa-money-bill mr-2" style="color: #007A3D;"></i>Paiements</h4>
        </div>
        <div class="p-6">
            @forelse($paiements as $p)
            <div class="flex items-center justify-between py-3 border-b last:border-0">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">
                        {{ $p->inscription->participant->nom ?? '-' }} — {{ number_format($p->montant, 0, ',', ' ') }} FCFA
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ ucfirst(str_replace('_', ' ', $p->mode_paiement)) }}
                        @if($p->numero_cheque) · N° {{ $p->numero_cheque }} @endif
                        · {{ $p->date_paiement }}
                    </p>
                </div>
                @php
                    $colors = ['valide' => 'bg-green-100 text-green-700', 'en_attente' => 'bg-yellow-100 text-yellow-700', 'rejete' => 'bg-red-100 text-red-700'];
                @endphp
                <span class="text-xs px-3 py-1 rounded-full font-medium {{ $colors[$p->statut] ?? 'bg-gray-100 text-gray-500' }}">
                    {{ ucfirst($p->statut) }}
                </span>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Aucun paiement</p>
            @endforelse
        </div>
    </div>

</div>