<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('souhaits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_participant')->constrained('participants')->onDelete('cascade');
            $table->foreignId('id_participant_cible')->constrained('participants')->onDelete('cascade');
            $table->integer('priorite');
            $table->enum('type', ['envoye', 'recu'])->default('envoye');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('souhaits');
    }
};