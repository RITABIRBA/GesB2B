<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;
use App\Models\Participant;

class NotificationBell extends Component
{
    public int  $count        = 0;
    public bool $showDropdown = false;
    public $notifications;

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function loadNotifications(): void
    {
        $participant = Participant::where('email', auth()->user()->email)->first();

        if (!$participant) {
            $this->count         = 0;
            $this->notifications = collect();
            return;
        }

        $this->notifications = Notification::where('id_participant', $participant->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // ✅ Pas de colonne "lu" — on compte les notifications du jour
        $this->count = Notification::where('id_participant', $participant->id)
            ->whereDate('created_at', today())
            ->count();
    }

    public function toggleDropdown(): void
    {
        $this->showDropdown = !$this->showDropdown;
        if ($this->showDropdown) {
            $this->count = 0; // Reset le compteur quand on ouvre
            $this->loadNotifications();
        }
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}