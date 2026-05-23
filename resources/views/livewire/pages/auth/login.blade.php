<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen flex" style="background-color: #f8f9fa;">

    {{-- 
        PARTIE GAUCHE — Visuel CCI-BF
     --}}
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12 text-white"
        style="background: linear-gradient(135deg, #006B34 0%, #007A3D 50%, #005a2d 100%);">

        {{-- Logo --}}
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-xl"
                style="background-color: #C8102E;">B</div>
            <div>
                <h1 class="text-2xl font-bold">GesB2B</h1>
                <p class="text-green-300 text-sm">CCI-BF Platform</p>
            </div>
        </div>

        {{-- Contenu central --}}
        <div>
            <h2 class="text-4xl font-bold mb-4 leading-tight">
                Plateforme de gestion des rencontres B2B
            </h2>
            <p class="text-green-200 text-lg mb-8">
                Chambre de Commerce et d'Industrie du Burkina Faso
            </p>

            {{-- Features --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(200, 16, 46, 0.3);">
                        <i class="fa-solid fa-handshake text-white text-sm"></i>
                    </div>
                    <span class="text-green-100">Gestion des rendez-vous B2B</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(200, 16, 46, 0.3);">
                        <i class="fa-solid fa-calendar text-white text-sm"></i>
                    </div>
                    <span class="text-green-100">Organisation des forums économiques</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(200, 16, 46, 0.3);">
                        <i class="fa-solid fa-building text-white text-sm"></i>
                    </div>
                    <span class="text-green-100">Mise en relation des entreprises</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(200, 16, 46, 0.3);">
                        <i class="fa-solid fa-id-badge text-white text-sm"></i>
                    </div>
                    <span class="text-green-100">Gestion des badges et inscriptions</span>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-green-300 text-sm">
            © {{ date('Y') }} CCI-BF — Tous droits réservés
        </div>

    </div>

    {{-- 
        PARTIE DROITE — Formulaire de connexion
     --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md">

            {{-- Logo mobile --}}
            <div class="lg:hidden flex items-center gap-3 mb-8 justify-center">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-xl"
                    style="background-color: #C8102E;">B</div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">GesB2B</h1>
                    <p class="text-gray-400 text-sm">CCI-BF Platform</p>
                </div>
            </div>

            {{-- Titre --}}
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Connexion</h2>
                <p class="text-gray-500 mt-2">Connectez-vous à votre espace personnel</p>
            </div>

            {{-- Message de statut --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{-- Formulaire --}}
            <form wire:submit="login" class="space-y-5">

                {{-- Email --}}
                <div>
                    <label for="email"
                        class="block text-sm font-medium text-gray-700 mb-1.5">
                        <i class="fa-solid fa-envelope text-gray-400 mr-1"></i>
                        Adresse email
                    </label>
                    <input wire:model="form.email"
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="votre@email.com"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:border-transparent text-sm transition"
                        style="--tw-ring-color: #007A3D;">
                    @if($errors->get('form.email'))
                    <p class="text-red-500 text-xs mt-1">
                        {{ $errors->first('form.email') }}
                    </p>
                    @endif
                </div>

                {{-- Mot de passe --}}
                <div>
                    <label for="password"
                        class="block text-sm font-medium text-gray-700 mb-1.5">
                        <i class="fa-solid fa-lock text-gray-400 mr-1"></i>
                        Mot de passe
                    </label>
                    <input wire:model="form.password"
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:border-transparent text-sm transition"
                        style="--tw-ring-color: #007A3D;">
                    @if($errors->get('form.password'))
                    <p class="text-red-500 text-xs mt-1">
                        {{ $errors->first('form.password') }}
                    </p>
                    @endif
                </div>

                {{-- Se souvenir + Mot de passe oublié --}}
                <div class="flex items-center justify-between">
                    <label for="remember" class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="form.remember"
                            id="remember"
                            type="checkbox"
                            class="rounded border-gray-300 text-green-700"
                            name="remember">
                        <span class="text-sm text-gray-600">Se souvenir de moi</span>
                    </label>

                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        wire:navigate
                        class="text-sm hover:underline transition"
                        style="color: #007A3D;">
                        Mot de passe oublié ?
                    </a>
                    @endif
                </div>

                {{-- Bouton connexion --}}
                <button type="submit"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow-lg flex items-center justify-center gap-2"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Se connecter
                </button>

            </form>

            {{-- Rôles disponibles --}}
<div class="mt-8 p-4 bg-gray-50 rounded-xl border border-gray-200">
    <p class="text-xs text-gray-500 font-medium mb-3 text-center">
        <i class="fa-solid fa-users mr-1"></i>
        Espaces disponibles
    </p>
    <div class="grid grid-cols-3 gap-2">
        @php
        $roles = [
            'Admin', 'Superviseur', 'CDD',
            'Entreprise', 'Participant', 'Traducteur',
        ];
        @endphp
        @foreach($roles as $role)
        <div class="text-center px-2 py-1.5 rounded-lg text-xs text-white font-medium"
            style="background-color: #007A3D;">
            {{ $role }}
        </div>
        @endforeach
    </div>
</div>

            {{-- Footer --}}
            <p class="text-center text-xs text-gray-400 mt-6">
                © {{ date('Y') }} CCI-BF — GesB2B Platform
            </p>

        </div>
    </div>

</div>