<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\GestionEvenements;
use App\Livewire\Superviseur\Dashboard as SuperviseurDashboard;
use App\Livewire\Cdd\Dashboard as CddDashboard;
use App\Livewire\Entreprise\Dashboard as EntrepriseDashboard;
use App\Livewire\Participant\Dashboard as ParticipantDashboard;
use App\Livewire\Traducteur\Dashboard as TraducteurDashboard;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('admin')) return redirect('/admin/dashboard');
        if ($user->hasRole('superviseur')) return redirect('/superviseur/dashboard');
        if ($user->hasRole('cdd')) return redirect('/cdd/dashboard');
        if ($user->hasRole('entreprise')) return redirect('/entreprise/dashboard');
        if ($user->hasRole('participant')) return redirect('/participant/dashboard');
        if ($user->hasRole('traducteur')) return redirect('/traducteur/dashboard');
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    // Routes Admin
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('admin.dashboard');
        Route::get('/evenements', GestionEvenements::class)->name('admin.evenements');
        Route::get('/type-evenements', \App\Livewire\Admin\GestionTypeEvenements::class)->name('admin.type-evenements');
        Route::get('/entreprises', \App\Livewire\Admin\GestionEntreprises::class)->name('admin.entreprises');
        Route::get('/participants', \App\Livewire\Admin\GestionParticipants::class)->name('admin.participants');
    });

    // Routes Superviseur
    Route::middleware(['role:superviseur'])->prefix('superviseur')->group(function () {
        Route::get('/dashboard', SuperviseurDashboard::class)->name('superviseur.dashboard');
    });

    // Routes CDD
    Route::middleware(['role:cdd'])->prefix('cdd')->group(function () {
        Route::get('/dashboard', CddDashboard::class)->name('cdd.dashboard');
    });

    // Routes Entreprise
    Route::middleware(['role:entreprise'])->prefix('entreprise')->group(function () {
        Route::get('/dashboard', EntrepriseDashboard::class)->name('entreprise.dashboard');
    });

    // Routes Participant
    Route::middleware(['role:participant'])->prefix('participant')->group(function () {
        Route::get('/dashboard', ParticipantDashboard::class)->name('participant.dashboard');
    });

    // Routes Traducteur
    Route::middleware(['role:traducteur'])->prefix('traducteur')->group(function () {
        Route::get('/dashboard', TraducteurDashboard::class)->name('traducteur.dashboard');
    });

});

require __DIR__.'/auth.php';