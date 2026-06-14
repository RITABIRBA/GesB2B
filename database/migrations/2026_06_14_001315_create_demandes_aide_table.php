<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes_aide', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_participant')->constrained('participants')->onDelete('cascade');
            $table->unsignedBigInteger('id_cdd')->nullable();
            $table->enum('sujet', ['inscription', 'rendez_vous', 'autre'])->default('autre');
            $table->text('message');
            $table->enum('statut', ['en_attente', 'traite'])->default('en_attente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes_aide');
    }
};