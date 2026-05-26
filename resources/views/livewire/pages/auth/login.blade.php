<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Participant;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;
    public $onglet = 'email';
    public $code_acces = '';
    public $erreur_code = '';

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    public function loginParCode(): void
    {
        $this->erreur_code = '';

        if (empty($this->code_acces)) {
            $this->erreur_code = 'Veuillez saisir votre code d\'accès.';
            return;
        }

        $participant = Participant::where('code_acces', strtoupper(trim($this->code_acces)))->first();

        if (!$participant) {
            $this->erreur_code = 'Code d\'accès invalide. Vérifiez votre code.';
            return;
        }

        $user = \App\Models\User::where('email', $participant->email)->first();

        if (!$user) {
            $this->erreur_code = 'Aucun compte associé à ce code. Contactez votre CDD.';
            return;
        }

        Auth::login($user);
        Session::regenerate();
        $this->redirect(route('dashboard'));
    }
}; ?>

<div class="min-h-screen flex" style="background-color: #f8f9fa;">

    {{-- PARTIE GAUCHE — Visuel CCI-BF --}}
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12 text-white"
        style="background: linear-gradient(135deg, #006B34 0%, #007A3D 50%, #005a2d 100%);">

        {{-- Logo --}}
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo-ccibf.png') }}"
                alt="CCI-BF" class="w-12 h-12 object-contain rounded-xl">
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

        <div class="text-green-300 text-sm">
            © {{ date('Y') }} CCI-BF — Tous droits réservés
        </div>

    </div>

    {{-- PARTIE DROITE — Formulaire --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md">

            {{-- Logo mobile --}}
            <div class="lg:hidden flex items-center gap-3 mb-8 justify-center">
                <img src="{{ asset('images/logo-ccibf.png') }}"
                    alt="CCI-BF" class="w-12 h-12 object-contain rounded-xl">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">GesB2B</h1>
                    <p class="text-gray-400 text-sm">CCI-BF Platform</p>
                </div>
            </div>

            {{-- Titre --}}
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Connexion</h2>
                <p class="text-gray-500 mt-2">Bienvenue sur GesB2B CCI-BF</p>
            </div>

            {{-- Message statut --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{-- FORMULAIRE EMAIL --}}
            <form wire:submit="login" class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        <i class="fa-solid fa-envelope text-gray-400 mr-1"></i>
                        Adresse email
                    </label>
                    <input wire:model="form.email"
                        id="email" type="email" name="email"
                        required autofocus autocomplete="username"
                        placeholder="votre@email.com"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                    @if($errors->get('form.email'))
                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('form.email') }}</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        <i class="fa-solid fa-lock text-gray-400 mr-1"></i>
                        Mot de passe
                    </label>
                    <input wire:model="form.password"
                        id="password" type="password" name="password"
                        required autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                    @if($errors->get('form.password'))
                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('form.password') }}</p>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="form.remember" type="checkbox"
                            class="rounded border-gray-300">
                        <span class="text-sm text-gray-600">Se souvenir de moi</span>
                    </label>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate
                        class="text-sm hover:underline" style="color: #007A3D;">
                        Mot de passe oublié ?
                    </a>
                    @endif
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90 shadow-lg flex items-center justify-center gap-2"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Se connecter
                </button>

            </form>

            {{-- Séparateur --}}
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-sm text-gray-400 font-medium">ou</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            {{-- CONNEXION PAR CODE --}}
            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5">
                <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-key" style="color: #007A3D;"></i>
                    Connexion par code d'accès
                </p>
                <p class="text-xs text-gray-500 mb-3">
                    Pour les participants ayant reçu un code d'accès de leur CDD.
                </p>
                <div class="flex gap-2">
                    <input wire:model="code_acces"
                        type="text"
                        placeholder="Ex: TIN5273"
                        class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-mono uppercase tracking-widest text-center"
                        style="letter-spacing: 0.2em;">
                    <button wire:click="loginParCode"
                        class="px-4 py-2.5 rounded-xl text-white font-medium text-sm transition hover:opacity-90 flex-shrink-0"
                        style="background-color: #007A3D;">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
                @if($erreur_code)
                <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $erreur_code }}
                </p>
                @endif
            </div>

            {{-- Séparateur --}}
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-sm text-gray-400 font-medium">Nouveau sur GesB2B ?</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            {{-- Liens inscription --}}
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('inscription.participant') }}"
                    class="py-3 rounded-xl text-white text-sm font-medium transition hover:opacity-90 text-center flex items-center justify-center gap-2"
                    style="background-color: #007A3D;">
                    <i class="fa-solid fa-user"></i>
                    Je suis participant
                </a>
                <a href="{{ route('inscription.entreprise') }}"
                    class="py-3 rounded-xl text-white text-sm font-medium transition hover:opacity-90 text-center flex items-center justify-center gap-2"
                    style="background-color: #C8102E;">
                    <i class="fa-solid fa-building"></i>
                    Je suis une entreprise
                </a>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                © {{ date('Y') }} CCI-BF — GesB2B Platform
            </p>

        </div>
    </div>

</div>