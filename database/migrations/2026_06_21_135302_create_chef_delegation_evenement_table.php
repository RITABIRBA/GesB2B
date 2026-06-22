<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chef_delegation_evenement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_chef_delegation')->constrained('chefs_delegation')->cascadeOnDelete();
            $table->foreignId('id_evenement')->constrained('evenements')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['id_chef_delegation', 'id_evenement']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chef_delegation_evenement');
    }
};