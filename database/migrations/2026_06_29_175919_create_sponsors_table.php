<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();

            // Lien événement
            $table->foreignId('id_evenement')
                ->constrained('evenements')
                ->onDelete('cascade');

            // Type : entreprise ou personne
            $table->enum('type_entite', ['entreprise', 'personne'])->default('entreprise');

            // Infos générales
            $table->string('nom');
            $table->string('nom_contact')->nullable();       // Personne de contact
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('site_web')->nullable();
            $table->string('logo')->nullable();              // Chemin vers le logo
            $table->text('description')->nullable();

            // Niveau de sponsoring
            $table->enum('niveau', ['principal', 'associe', 'partenaire', 'supporter'])
                ->default('partenaire');

            // Avantages accordés
            $table->integer('nb_stands_gratuits')->default(0);
            $table->integer('nb_badges_vip')->default(0);
            $table->decimal('remise_inscription', 5, 2)->default(0); // % de remise
            $table->text('autres_avantages')->nullable();   // Texte libre

            // Lien avec une entreprise déjà inscrite (optionnel)
            $table->foreignId('id_entreprise')
                ->nullable()
                ->constrained('entreprises')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsors');
    }
};