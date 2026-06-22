<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chefs_delegation', function (Blueprint $table) {
            if (!Schema::hasColumn('chefs_delegation', 'nom')) {
                $table->string('nom')->nullable()->after('id_user');
            }
            if (!Schema::hasColumn('chefs_delegation', 'email')) {
                $table->string('email')->nullable()->after('nom');
            }
            if (!Schema::hasColumn('chefs_delegation', 'est_admin')) {
                $table->boolean('est_admin')->default(false)->after('zone_personnalisee');
            }
            if (!Schema::hasColumn('chefs_delegation', 'actif')) {
                $table->boolean('actif')->default(true)->after('est_admin');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chefs_delegation', function (Blueprint $table) {
            $table->dropColumn(['nom', 'email', 'est_admin', 'actif']);
        });
    }
};