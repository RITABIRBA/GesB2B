<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
           $table->foreignId('id_entreprise')->nullable()->constrained('entreprises')->onDelete('set null');
            $table->foreignId('id_evenement')->constrained('evenements')->onDelete('cascade');
            $table->string('nom');
            $table->string('prenom');
            $table->string('secteur_activite');
            $table->string('email')->unique();
            $table->string('code_acces')->unique();
            $table->enum('role', ['exposant', 'visiteur', 'organisateur']);
            $table->enum('statut_historique', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};