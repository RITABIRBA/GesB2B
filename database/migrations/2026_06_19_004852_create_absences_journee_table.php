<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absences_journee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_participant')
                  ->constrained('participants')
                  ->onDelete('cascade');
            $table->foreignId('id_evenement')
                  ->constrained('evenements')
                  ->onDelete('cascade');

            // Date de l'absence
            $table->date('date');

            // Motif obligatoire
            $table->text('motif');

            // Liste des RDV annulés (JSON)
            $table->json('rdv_annules')->nullable();

            // Qui a signalé l'absence
            $table->string('signale_par')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absences_journee');
    }
};