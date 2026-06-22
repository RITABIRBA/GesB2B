<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('remises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_evenement')->constrained('evenements')->cascadeOnDelete();
            $table->string('libelle'); // Ex: "Remise groupe entreprise"
            // Type de remise : nb_participants / age / genre
            $table->enum('type', ['nb_participants', 'age', 'genre']);
            // Pour type=nb_participants : seuil minimum de participants
            $table->integer('seuil_min')->nullable();
            // Pour type=age : tranche d'âge
            $table->integer('age_min')->nullable();
            $table->integer('age_max')->nullable();
            // Pour type=genre : 'homme' ou 'femme'
            $table->string('genre')->nullable();
            // Pourcentage de remise (0 à 100)
            $table->decimal('pourcentage', 5, 2);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remises');
    }
};