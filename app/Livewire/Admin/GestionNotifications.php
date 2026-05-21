<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Notification;
use App\Models\Participant;
use App\Models\Evenement;

class GestionNotifications extends Component
{
    public $notification_id;
    public $contenu = '';
    public $type = 'systeme';
    public $destinataire = 'tous';
    public $id_evenement = '';
    public $id_participant = '';
    public $showModal = false;
    public $search = '';

    // ✅ Types selon la migration
    public $types = ['email', 'sms', 'systeme'];

    public function openModal()
    {
        $this->resetFields();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->notification_id = null;
        $this->contenu         = '';
        $this->type            = 'systeme';
        $this->destinataire    = 'tous';
        $this->id_evenement    = '';
        $this->id_participant  = '';
        $this->resetErrorBag();
    }

    public function envoyer()
    {
        $this->validate([
            'contenu' => 'required|string|max:1000',
            'type'    => 'required|in:email,sms,systeme',
        ]);

        // Détermine les participants selon le destinataire
        $participants = collect();

        if ($this->destinataire === 'tous') {
            $participants = Participant::all();
        } elseif ($this->destinataire === 'participants') {
            $participants = Participant::whereIn('role', ['visiteur', 'exposant'])->get();
        } elseif ($this->destinataire === 'entreprises') {
            $participants = Participant::where('role', 'exposant')->get();
        } elseif ($this->destinataire === 'un_participant' && $this->id_participant) {
            $participants = Participant::where('id', $this->id_participant)->get();
        }

        // Crée une notification pour chaque participant
        foreach ($participants as $participant) {
            Notification::create([
                'id_participant' => $participant->id,
                'contenu'        => $this->contenu,
                'type'           => $this->type,
                'date_envoie'    => now()->toDateString(),
            ]);
        }

        session()->flash('success', count($participants) . ' notification(s) envoyée(s) avec succès.');
        $this->closeModal();
    }

    public function supprimer($id)
    {
        Notification::findOrFail($id)->delete();
        session()->flash('success', 'Notification supprimée.');
    }

    public function render()
    {
        return view('livewire.admin.gestion-notifications', [
            'notifications' => Notification::with('participant')
                ->when($this->search, fn($q) =>
                    $q->where('contenu', 'like', '%'.$this->search.'%')
                      ->orWhere('type', 'like', '%'.$this->search.'%')
                )
                ->latest()
                ->get(),
            'evenements'   => Evenement::orderBy('nom')->get(),
            'participants' => Participant::orderBy('nom')->get(),
        ])->layout('layouts.admin', ['title' => 'Gestion des Notifications']);
    }
}