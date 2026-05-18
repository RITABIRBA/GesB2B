<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_participant1')->constrained('participants')->onDelete('cascade');
            $table->foreignId('id_participant2')->constrained('participants')->onDelete('cascade');
            $table->foreignId('id_traducteur')->nullable()->constrained('traducteurs')->onDelete('set null');
            $table->foreignId('id_stand')->nullable()->constrained('stands')->onDelete('set null');
            $table->date('date');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->enum('statut', ['planifie', 'confirme', 'annule', 'termine'])->default('planifie');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};