<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Créer la table chefs_delegation
        // (les CDD sont dans users avec rôle 'cdd'
        //  cette table stocke les infos supplémentaires)
        Schema::create('chefs_delegation', function (Blueprint $table) {
            $table->id();

            // Lien vers l'utilisateur (obligatoire)
            $table->foreignId('id_user')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Pays ou zone couverte
            // ex: "Burkina Faso", "Europe", "Afrique de l'Ouest"
            $table->string('pays_zone')->nullable();

            // Si "Autre" → zone saisie manuellement
            $table->string('zone_personnalisee')->nullable();

            // Lié à un événement (optionnel)
            $table->foreignId('id_evenement')
                  ->nullable()
                  ->constrained('evenements')
                  ->nullOnDelete();

            // Téléphone
            $table->string('telephone')->nullable();

            // Notes / observations
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chefs_delegation');
    }
};