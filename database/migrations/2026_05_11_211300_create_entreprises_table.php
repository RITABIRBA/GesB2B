<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entreprises', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('secteur_activite');
            $table->string('sous_secteur');
            $table->string('pays');
            $table->string('ville');
            $table->string('contact');
            $table->enum('statut_validation', ['en_attente', 'valide', 'rejete'])->default('en_attente');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('entreprises');
    }
};