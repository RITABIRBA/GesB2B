<div class="relative" wire:poll.5s="loadNotifications">

    {{-- Bouton cloche --}}
    <button wire:click="toggleDropdown"
        class="relative w-10 h-10 rounded-xl flex items-center justify-center transition hover:bg-gray-100">
        <i class="fa-solid fa-bell text-gray-500 text-lg {{ $count > 0 ? 'animate-bounce' : '' }}"></i>
        @if($count > 0)
        <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full text-white text-xs font-bold flex items-center justify-center"
            style="background-color: #C8102E;">
            {{ $count > 9 ? '9+' : $count }}
        </span>
        @endif
    </button>

    {{-- Dropdown notifications --}}
    @if($showDropdown)
    <div class="absolute right-0 top-12 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b"
            style="background: linear-gradient(135deg, #007A3D, #005a2d);">
            <h4 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-bell"></i> Notifications
            </h4>
            <button wire:click="toggleDropdown" class="text-white/70 hover:text-white text-lg">&times;</button>
        </div>

        {{-- Liste --}}
        <div class="max-h-72 overflow-y-auto">
            @forelse($notifications as $notif)
            <div class="px-4 py-3 border-b last:border-0 hover:bg-gray-50 transition">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5
                        {{ str_contains($notif->contenu, 'mutuel') || str_contains($notif->contenu, '🎉') ? 'bg-green-100' : 'bg-blue-100' }}">
                        @if(str_contains($notif->contenu, 'mutuel') || str_contains($notif->contenu, '🎉'))
                        <i class="fa-solid fa-handshake text-green-600 text-xs"></i>
                        @elseif(str_contains($notif->contenu, 'absent') || str_contains($notif->contenu, 'absence'))
                        <i class="fa-solid fa-user-slash text-red-500 text-xs"></i>
                        @else
                        <i class="fa-solid fa-bell text-blue-500 text-xs"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-700 leading-relaxed">{{ $notif->contenu }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $notif->created_at ? $notif->created_at->diffForHumans() : $notif->date_envoie }}
                        </p>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">
                <i class="fa-solid fa-bell-slash text-3xl mb-2 block text-gray-300"></i>
                <p class="text-sm">Aucune notification</p>
            </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if($notifications->isNotEmpty())
        <div class="px-4 py-2 border-t bg-gray-50 text-center">
            <p class="text-xs text-gray-400">{{ $notifications->count() }} notification(s)</p>
        </div>
        @endif
    </div>

    {{-- Overlay pour fermer --}}
    <div wire:click="toggleDropdown" class="fixed inset-0 z-40"></div>
    @endif

</div>