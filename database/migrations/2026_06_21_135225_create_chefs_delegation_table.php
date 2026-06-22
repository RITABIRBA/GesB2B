<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chefs_delegation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nom');
            $table->string('email')->nullable();
            $table->string('pays'); // ou "Autre"
            $table->string('zone_autre')->nullable(); // si pays = "Autre"
            // ✅ Si l'admin lui-même est CDD (cumul de rôle)
            $table->boolean('est_admin')->default(false);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chefs_delegation');
    }
};