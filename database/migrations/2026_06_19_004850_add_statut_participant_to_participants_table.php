<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Statut : classique, sponsor, partenaire
            $table->enum('statut_participant', [
                'classique',
                'sponsor',
                'partenaire',
            ])->default('classique')->after('role');

            // Statut préinscription
            $table->enum('statut_preinscription', [
                'en_attente',
                'valide',
                'rejete',
            ])->default('en_attente')->after('statut_participant');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn([
                'statut_participant',
                'statut_preinscription',
            ]);
        });
    }
};