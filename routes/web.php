<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Superviseur\Dashboard as SuperviseurDashboard;
use App\Livewire\Cdd\Dashboard as CddDashboard;
use App\Livewire\Entreprise\Dashboard as EntrepriseDashboard;
use App\Livewire\Participant\Dashboard as ParticipantDashboard;
use App\Livewire\Traducteur\Dashboard as TraducteurDashboard;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {


    // REDIRECTION SELON LE RÔLE
    
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('admin'))        return redirect('/admin/dashboard');
        if ($user->hasRole('superviseur'))  return redirect('/superviseur/dashboard');
        if ($user->hasRole('cdd'))          return redirect('/cdd/dashboard');
        if ($user->hasRole('entreprise'))   return redirect('/entreprise/dashboard');
        if ($user->hasRole('participant'))  return redirect('/participant/dashboard');
        if ($user->hasRole('traducteur'))   return redirect('/traducteur/dashboard');
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    
    // ROUTES ADMIN
    
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard',       AdminDashboard::class)->name('admin.dashboard');
        Route::get('/evenements',      \App\Livewire\Admin\GestionEvenements::class)->name('admin.evenements');
        Route::get('/type-evenements', \App\Livewire\Admin\GestionTypeEvenements::class)->name('admin.type-evenements');
        Route::get('/entreprises',     \App\Livewire\Admin\GestionEntreprises::class)->name('admin.entreprises');
        Route::get('/participants',    \App\Livewire\Admin\GestionParticipants::class)->name('admin.participants');
        Route::get('/inscriptions',    \App\Livewire\Admin\GestionInscriptions::class)->name('admin.inscriptions');
        Route::get('/paiements',       \App\Livewire\Admin\GestionPaiements::class)->name('admin.paiements');
        Route::get('/stands',          \App\Livewire\Admin\GestionStands::class)->name('admin.stands');
        Route::get('/traducteurs',     \App\Livewire\Admin\GestionTraducteurs::class)->name('admin.traducteurs');
        Route::get('/souhaits',        \App\Livewire\Admin\GestionSouhaits::class)->name('admin.souhaits');
        Route::get('/rendez-vous',     \App\Livewire\Admin\GestionRendezVous::class)->name('admin.rendez-vous');
        Route::get('/badges',          \App\Livewire\Admin\GestionBadges::class)->name('admin.badges');
        Route::get('/notifications',   \App\Livewire\Admin\GestionNotifications::class)->name('admin.notifications');
        Route::get('/utilisateurs',    \App\Livewire\Admin\GestionUtilisateurs::class)->name('admin.utilisateurs');
    });

    
    // ROUTES SUPERVISEUR
    
    Route::middleware(['role:superviseur'])->prefix('superviseur')->group(function () {
        Route::get('/dashboard',     SuperviseurDashboard::class)->name('superviseur.dashboard');
        Route::get('/evenements',    \App\Livewire\Superviseur\VoirEvenements::class)->name('superviseur.evenements');
        Route::get('/entreprises',   \App\Livewire\Superviseur\VoirEntreprises::class)->name('superviseur.entreprises');
        Route::get('/participants',  \App\Livewire\Superviseur\VoirParticipants::class)->name('superviseur.participants');
        Route::get('/inscriptions',  \App\Livewire\Superviseur\VoirInscriptions::class)->name('superviseur.inscriptions');
        Route::get('/paiements',     \App\Livewire\Superviseur\VoirPaiements::class)->name('superviseur.paiements');
        Route::get('/rendez-vous',   \App\Livewire\Superviseur\VoirRendezVous::class)->name('superviseur.rendez-vous');
        Route::get('/badges',        \App\Livewire\Superviseur\VoirBadges::class)->name('superviseur.badges');
        Route::get('/gestion-acces', \App\Livewire\Superviseur\GestionCdd::class)->name('superviseur.gestion-acces');
    });

    
    // ROUTES CDD
    
    Route::middleware(['role:cdd'])->prefix('cdd')->group(function () {
        Route::get('/dashboard',    CddDashboard::class)->name('cdd.dashboard');
        Route::get('/entreprises',  \App\Livewire\Cdd\GestionEntreprises::class)->name('cdd.entreprises');
        Route::get('/participants', \App\Livewire\Cdd\GestionParticipants::class)->name('cdd.participants');
        Route::get('/inscriptions', \App\Livewire\Cdd\GestionInscriptions::class)->name('cdd.inscriptions');
        Route::get('/souhaits',     \App\Livewire\Cdd\GestionSouhaits::class)->name('cdd.souhaits');
        Route::get('/catalogue',    \App\Livewire\Cdd\Catalogue::class)->name('cdd.catalogue');
    });

    
    // ROUTES ENTREPRISE
    
    Route::middleware(['role:entreprise'])->prefix('entreprise')->group(function () {
        Route::get('/dashboard',    EntrepriseDashboard::class)->name('entreprise.dashboard');
        Route::get('/profil',       \App\Livewire\Entreprise\MonProfil::class)->name('entreprise.profil');
        Route::get('/participants', \App\Livewire\Entreprise\MesParticipants::class)->name('entreprise.participants');
        Route::get('/stands',       \App\Livewire\Entreprise\MesStands::class)->name('entreprise.stands');
        Route::get('/souhaits',     \App\Livewire\Entreprise\MesSouhaits::class)->name('entreprise.souhaits');
        Route::get('/rendez-vous',  \App\Livewire\Entreprise\MesRendezVous::class)->name('entreprise.rendez-vous');
        Route::get('/catalogue',    \App\Livewire\Entreprise\Catalogue::class)->name('entreprise.catalogue');
    });

    
    // ROUTES PARTICIPANT
    
    Route::middleware(['role:participant'])->prefix('participant')->group(function () {
        Route::get('/dashboard',    ParticipantDashboard::class)->name('participant.dashboard');
        Route::get('/profil',       \App\Livewire\Participant\MonProfil::class)->name('participant.profil');
        Route::get('/inscription',  \App\Livewire\Participant\MonInscription::class)->name('participant.inscription');
        Route::get('/souhaits',     \App\Livewire\Participant\MesSouhaits::class)->name('participant.souhaits');
        Route::get('/rendez-vous',  \App\Livewire\Participant\MesRendezVous::class)->name('participant.rendez-vous');
        Route::get('/badge',        \App\Livewire\Participant\MonBadge::class)->name('participant.badge');
        Route::get('/catalogue',    \App\Livewire\Participant\Catalogue::class)->name('participant.catalogue');
    });

    
    // ROUTES TRADUCTEUR
    
    Route::middleware(['role:traducteur'])->prefix('traducteur')->group(function () {
        Route::get('/dashboard',   TraducteurDashboard::class)->name('traducteur.dashboard');
        Route::get('/profil',      \App\Livewire\Traducteur\MonProfil::class)->name('traducteur.profil');
        Route::get('/rendez-vous', \App\Livewire\Traducteur\MesRendezVous::class)->name('traducteur.rendez-vous');
    });

});

require __DIR__.'/auth.php';