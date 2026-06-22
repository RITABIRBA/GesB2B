<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // ✅ Nouvelle référence propre vers chefs_delegation
            // (l'ancienne id_chef_delegation pointait vers participants,
            // ce qui était incohérent)
            $table->foreignId('chef_delegation_id')->nullable()
                ->after('id_chef_delegation')
                ->constrained('chefs_delegation')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign(['chef_delegation_id']);
            $table->dropColumn('chef_delegation_id');
        });
    }
};