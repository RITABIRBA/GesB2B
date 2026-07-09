<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email'    => 'Veuillez saisir une adresse email valide.',
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');
        session()->flash('status', __($status));
    }
}; ?>

<div>
    <div class="mb-6 text-sm text-gray-600 leading-relaxed">
        Mot de passe oublié ? Pas de problème. Saisissez votre adresse email
        et nous vous enverrons un lien de réinitialisation.
    </div>

    {{-- Message de succès --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink">
        <div>
            <x-input-label for="email" value="Adresse email" />
            <x-text-input
                wire:model="email"
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                required
                autofocus
                placeholder="votre@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Envoyer le lien de réinitialisation
            </x-primary-button>
        </div>
    </form>
</div>