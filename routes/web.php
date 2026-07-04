<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Superviseur\Dashboard as SuperviseurDashboard;
use App\Livewire\Cdd\Dashboard as CddDashboard;
use App\Livewire\Entreprise\Dashboard as EntrepriseDashboard;
use App\Livewire\Participant\Dashboard as ParticipantDashboard;
use App\Livewire\Traducteur\Dashboard as TraducteurDashboard;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\RecuController;

Route::get('/', function () {
    return view('welcome');
});

// ============================================================
// ROUTES PUBLIQUES (sans authentification)
// ============================================================
Route::get('/inscription-participant', \App\Livewire\Auth\InscriptionParticipant::class)->name('inscription.participant');
Route::get('/inscription-entreprise',  \App\Livewire\Auth\InscriptionEntreprise::class)->name('inscription.entreprise');

// Page publique du profil — lien QR code (accessible sans connexion)
Route::get('/participant/{id}/profil-public', [BadgeController::class, 'profilPublic'])
    ->name('participant.profil-public');

// Ancien lien QR code (compatibilité)
Route::get('/badge/{qr_code}', [App\Http\Controllers\BadgePublicController::class, 'show'])
    ->name('badge.public');

// ============================================================
// ROUTES AUTHENTIFIÉES
// ============================================================
Route::middleware(['auth'])->group(function () {

    // ── REDIRECTION SELON LE RÔLE ──────────────────────────────
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('admin'))       return redirect('/admin/dashboard');
        if ($user->hasRole('superviseur')) return redirect('/superviseur/dashboard');
        if ($user->hasRole('cdd'))         return redirect('/cdd/dashboard');
        if ($user->hasRole('entreprise'))  return redirect('/entreprise/dashboard');
        if ($user->hasRole('participant')) return redirect('/participant/dashboard');
        if ($user->hasRole('traducteur'))  return redirect('/traducteur/dashboard');
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    // ============================================================
    // ROUTES ADMIN
    // ============================================================
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard',         AdminDashboard::class)->name('admin.dashboard');
        Route::get('/evenements',        \App\Livewire\Admin\GestionEvenements::class)->name('admin.evenements');
        Route::get('/type-evenements',   \App\Livewire\Admin\GestionTypeEvenements::class)->name('admin.type-evenements');
        Route::get('/entreprises',       \App\Livewire\Admin\GestionEntreprises::class)->name('admin.entreprises');
        Route::get('/participants',      \App\Livewire\Admin\GestionParticipants::class)->name('admin.participants');
        Route::get('/inscriptions',      \App\Livewire\Admin\GestionInscriptions::class)->name('admin.inscriptions');
        Route::get('/paiements',         \App\Livewire\Admin\GestionPaiements::class)->name('admin.paiements');
        Route::get('/stands',            \App\Livewire\Admin\GestionStands::class)->name('admin.stands');
        Route::get('/traducteurs',       \App\Livewire\Admin\GestionTraducteurs::class)->name('admin.traducteurs');
        Route::get('/souhaits',          \App\Livewire\Admin\GestionSouhaits::class)->name('admin.souhaits');
        Route::get('/rendez-vous',       \App\Livewire\Admin\GestionRendezVous::class)->name('admin.rendez-vous');
        Route::get('/notifications',     \App\Livewire\Admin\GestionNotifications::class)->name('admin.notifications');
        Route::get('/utilisateurs',      \App\Livewire\Admin\GestionUtilisateurs::class)->name('admin.utilisateurs');
        Route::get('/chefs-delegation',  \App\Livewire\Admin\GestionChefsDelegation::class)->name('admin.chefs-delegation');
        Route::get('/demandes-aide',     \App\Livewire\Admin\DemandesAide::class)->name('admin.demandes-aide');
        Route::get('/participants/{id}', \App\Livewire\Admin\FicheParticipant::class)->name('admin.fiche-participant');
        Route::get('/entreprises/{id}',  \App\Livewire\Admin\FicheEntreprise::class)->name('admin.fiche-entreprise');
        Route::get('/remises',           \App\Livewire\Admin\GestionRemises::class)->name('admin.remises');
        Route::get('/admin/sponsors', \App\Livewire\Admin\GestionSponsors::class)->name('admin.sponsors');


        // ── BADGES ──
        Route::get('/badges',                    \App\Livewire\Admin\GestionBadges::class)->name('admin.badges');
        Route::get('/badge/{id}/telecharger',    [BadgeController::class, 'telecharger'])->name('admin.badge.telecharger');
        Route::get('/evenement/{id}/badges/tous',[BadgeController::class, 'tousLesBadges'])->name('admin.badges.tous');
    });

    // ============================================================
    // ROUTES SUPERVISEUR
    // ============================================================
    Route::middleware(['role:superviseur'])->prefix('superviseur')->group(function () {
        Route::get('/dashboard',             SuperviseurDashboard::class)->name('superviseur.dashboard');
        Route::get('/evenements',            \App\Livewire\Superviseur\GestionEvenements::class)->name('superviseur.evenements');
        Route::get('/entreprises',           \App\Livewire\Superviseur\VoirEntreprises::class)->name('superviseur.entreprises');
        Route::get('/gestion-entreprises',   \App\Livewire\Superviseur\GestionEntreprises::class)->name('superviseur.gestion-entreprises');
        Route::get('/participants',          \App\Livewire\Superviseur\VoirParticipants::class)->name('superviseur.participants');
        Route::get('/gestion-participants',  \App\Livewire\Superviseur\GestionParticipants::class)->name('superviseur.gestion-participants');
        Route::get('/inscriptions',          \App\Livewire\Superviseur\GestionInscriptions::class)->name('superviseur.inscriptions');
        Route::get('/paiements',             \App\Livewire\Superviseur\GestionPaiements::class)->name('superviseur.paiements');
        Route::get('/rendez-vous',           \App\Livewire\Superviseur\GestionRendezVous::class)->name('superviseur.rendez-vous');
        Route::get('/souhaits',              \App\Livewire\Superviseur\GestionSouhaits::class)->name('superviseur.souhaits');
        Route::get('/stands',                \App\Livewire\Superviseur\GestionStands::class)->name('superviseur.stands');
        Route::get('/remises',               \App\Livewire\Admin\GestionRemises::class)->name('superviseur.remises');
        Route::get('/chefs-delegation',      \App\Livewire\Admin\GestionChefsDelegation::class)->name('superviseur.chefs-delegation');
        Route::get('/gestion-acces',         \App\Livewire\Superviseur\GestionCdd::class)->name('superviseur.gestion-acces');
        Route::get('/demandes-aide',         \App\Livewire\Superviseur\DemandesAide::class)->name('superviseur.demandes-aide');
        Route::get('/participants/{id}',     \App\Livewire\Superviseur\FicheParticipant::class)->name('superviseur.fiche-participant');
        Route::get('/entreprises/{id}',      \App\Livewire\Superviseur\FicheEntreprise::class)->name('superviseur.fiche-entreprise');
        
Route::get('/superviseur/sponsors', \App\Livewire\Superviseur\GestionSponsors::class)->name('superviseur.sponsors');

        // ── BADGES ──
        Route::get('/badges',                    \App\Livewire\Superviseur\VoirBadges::class)->name('superviseur.badges');
        Route::get('/badge/{id}/telecharger',    [BadgeController::class, 'telecharger'])->name('superviseur.badge.telecharger');
        Route::get('/evenement/{id}/badges/tous',[BadgeController::class, 'tousLesBadges'])->name('superviseur.badges.tous');
    });

    // ============================================================
    // ROUTES CDD
    // ============================================================
    Route::middleware(['role:cdd'])->prefix('cdd')->group(function () {
        Route::get('/dashboard',             CddDashboard::class)->name('cdd.dashboard');
        Route::get('/entreprises',           \App\Livewire\Cdd\GestionEntreprises::class)->name('cdd.entreprises');
        Route::get('/participants',          \App\Livewire\Cdd\GestionParticipants::class)->name('cdd.participants');
        Route::get('/inscriptions',          \App\Livewire\Cdd\GestionInscriptions::class)->name('cdd.inscriptions');
        Route::get('/souhaits',              \App\Livewire\Cdd\GestionSouhaits::class)->name('cdd.souhaits');
        Route::get('/catalogue',             \App\Livewire\Cdd\Catalogue::class)->name('cdd.catalogue');
        Route::get('/statistiques-souhaits', \App\Livewire\Cdd\StatistiquesSouhaits::class)->name('cdd.statistiques-souhaits');
        Route::get('/demandes-aide',         \App\Livewire\Cdd\DemandesAide::class)->name('cdd.demandes-aide');
        Route::get('/inscriptions-liste',    \App\Livewire\Cdd\Inscriptions::class)->name('cdd.inscriptions-liste');
    });

    // ============================================================
    // ROUTES ENTREPRISE
    // ============================================================
    Route::middleware(['role:entreprise'])->prefix('entreprise')->group(function () {
        Route::get('/dashboard',            EntrepriseDashboard::class)->name('entreprise.dashboard');
        Route::get('/profil',               \App\Livewire\Entreprise\MonProfil::class)->name('entreprise.profil');
        Route::get('/participants',         \App\Livewire\Entreprise\MesParticipants::class)->name('entreprise.participants');
        Route::get('/stands',               \App\Livewire\Entreprise\MesStands::class)->name('entreprise.stands');
        Route::get('/souhaits',             \App\Livewire\Entreprise\MesSouhaits::class)->name('entreprise.souhaits');
        Route::get('/rendez-vous',          \App\Livewire\Entreprise\MesRendezVous::class)->name('entreprise.rendez-vous');
        Route::get('/catalogue',            \App\Livewire\Entreprise\Catalogue::class)->name('entreprise.catalogue');
        Route::get('/completer-profil-b2b', \App\Livewire\Entreprise\CompleterProfilB2B::class)->name('entreprise.completer-profil-b2b');
        Route::get('/inscription/{evenement}', \App\Livewire\Participant\InscriptionWizard::class)->name('entreprise.inscription.wizard');

        // ── REÇU & BADGE ──
        Route::get('/recu/{id}/telecharger',  [RecuController::class, 'telecharger'])->name('entreprise.recu.telecharger');
        Route::get('/badge/{id}/telecharger', [BadgeController::class, 'telecharger'])->name('entreprise.badge.telecharger');
    });

    // ============================================================
    // ROUTES PARTICIPANT
    // ============================================================
    Route::middleware(['role:participant'])->prefix('participant')->group(function () {
        Route::get('/dashboard',            ParticipantDashboard::class)->name('participant.dashboard');
        Route::get('/profil',               \App\Livewire\Participant\MonProfil::class)->name('participant.profil');
        Route::get('/inscription',          \App\Livewire\Participant\MonInscription::class)->name('participant.inscription');
        Route::get('/souhaits',             \App\Livewire\Participant\MesSouhaits::class)->name('participant.souhaits');
        Route::get('/rendez-vous',          \App\Livewire\Participant\MesRendezVous::class)->name('participant.rendez-vous');
        Route::get('/badge',                \App\Livewire\Participant\MonBadge::class)->name('participant.badge');
        Route::get('/catalogue',            \App\Livewire\Participant\Catalogue::class)->name('participant.catalogue');
        Route::get('/planning',             \App\Livewire\Participant\MonPlanning::class)->name('participant.planning');
        Route::get('/completer-profil-b2b', \App\Livewire\Participant\CompleterProfilB2B::class)->name('participant.completer-profil-b2b');
        Route::get('/stands',               \App\Livewire\Participant\MesStands::class)->name('participant.stands');
        Route::get('/inscription/{evenement}', \App\Livewire\Participant\InscriptionWizard::class)->name('participant.inscription.wizard');

        // ── REÇU & BADGE ──
        Route::get('/recu/{id}/telecharger',  [RecuController::class, 'telecharger'])->name('participant.recu.telecharger');
        Route::get('/badge/{id}/telecharger', [BadgeController::class, 'telecharger'])->name('participant.badge.telecharger');
    });

    // ============================================================
    // ROUTES TRADUCTEUR
    // ============================================================
    Route::middleware(['role:traducteur'])->prefix('traducteur')->group(function () {
        Route::get('/dashboard',   TraducteurDashboard::class)->name('traducteur.dashboard');
        Route::get('/profil',      \App\Livewire\Traducteur\MonProfil::class)->name('traducteur.profil');
        Route::get('/planning',    \App\Livewire\Traducteur\MonPlanning::class)->name('traducteur.planning');
        Route::get('/rendez-vous', \App\Livewire\Traducteur\MesRendezVous::class)->name('traducteur.rendez-vous');
    });

}); // Fermeture du groupe auth

// ============================================================
// ROUTES DE TEST EMAILS — À SUPPRIMER EN PRODUCTION
// ============================================================
Route::get('/test-email/preinscription-recue', function () {
    $participant = \App\Models\Participant::first();
    $evenement   = \App\Models\Evenement::first();
    return new \App\Mail\PreinscriptionRecue($participant, $evenement->nom);
});

Route::get('/test-email/preinscription-validee', function () {
    $participant = \App\Models\Participant::first();
    return new \App\Mail\PreinscriptionValidee($participant, 'MotDePasse123');
});

Route::get('/test-email/preinscription-rejetee', function () {
    $participant = \App\Models\Participant::first();
    $evenement   = \App\Models\Evenement::first();
    return new \App\Mail\PreinscriptionRejetee($participant, $evenement->nom, 'Dossier incomplet');
});

Route::get('/test-email/match-mutuel', function () {
    $participant1 = \App\Models\Participant::skip(0)->first();
    $participant2 = \App\Models\Participant::skip(1)->first();
    $evenement    = \App\Models\Evenement::first();
    return new \App\Mail\MatchMutuelNotification($participant1, $participant2, $evenement->nom);
});

Route::get('/test-email/planning', function () {
    $participant = \App\Models\Participant::first();
    $evenement   = \App\Models\Evenement::first();
    $rdvs        = \App\Models\RendezVous::with(['participant1', 'participant2'])
        ->where('id_participant1', $participant->id)
        ->orWhere('id_participant2', $participant->id)
        ->get();
    return new \App\Mail\PlanningGenere(
        $participant,
        $evenement->nom,
        \Carbon\Carbon::parse($evenement->date_debut)->format('d/m/Y'),
        $rdvs
    );
});

Route::get('/test-email/paiement', function () {
    $paiement    = \App\Models\Paiement::with(['inscription.participant', 'inscription.evenement'])->first();
    $participant = $paiement->inscription->participant;
    $evenement   = $paiement->inscription->evenement;
    return new \App\Mail\PaiementConfirme($participant, $paiement, $evenement->nom);
});

Route::get('/test-email/stand', function () {
    $stand       = \App\Models\Stand::with('typeStand')->first();
    $participant = \App\Models\Participant::first();
    $evenement   = \App\Models\Evenement::first();
    return new \App\Mail\StandAssigne($participant, $stand, $evenement->nom);
});

Route::get('/test-email/rappel', function () {
    $participant = \App\Models\Participant::first();
    $evenement   = \App\Models\Evenement::first();
    return new \App\Mail\RappelEvenement(
        $participant,
        $evenement->nom,
        \Carbon\Carbon::parse($evenement->date_debut)->format('d/m/Y'),
        $evenement->lieu ?? 'Salle principale',
        1
    );
});

Route::get('/test-email/envoyer-reel', function () {
    $participant = \App\Models\Participant::whereNotNull('email')->first();
    $evenement   = \App\Models\Evenement::first();
    \Illuminate\Support\Facades\Mail::to($participant->email)->send(
        new \App\Mail\PreinscriptionRecue($participant, $evenement->nom)
    );
    return 'Email envoyé à : ' . $participant->email . ' — Vérifiez Mailpit !';
});

require __DIR__.'/auth.php';