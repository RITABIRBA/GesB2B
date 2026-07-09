<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'token'    => ['required'],
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.required'             => 'L\'adresse email est obligatoire.',
            'email.email'                => 'Veuillez saisir une adresse email valide.',
            'password.required'          => 'Le mot de passe est obligatoire.',
            'password.confirmed'         => 'La confirmation du mot de passe ne correspond pas.',
            'password_confirmation.required' => 'Veuillez confirmer votre mot de passe.',
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password'       => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));
            return;
        }

        Session::flash('status', __($status));
        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div>
    <div class="mb-6 text-sm text-gray-600 leading-relaxed">
        Choisissez un nouveau mot de passe sécurisé pour votre compte.
    </div>

    <form wire:submit="resetPassword">

        {{-- Email --}}
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
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Nouveau mot de passe --}}
        <div class="mt-4">
            <x-input-label for="password" value="Nouveau mot de passe" />
            <x-text-input
                wire:model="password"
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Minimum 8 caractères" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirmer le mot de passe --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
            <x-text-input
                wire:model="password_confirmation"
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Répétez le mot de passe" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                Réinitialiser mon mot de passe
            </x-primary-button>
        </div>

    </form>
</div>