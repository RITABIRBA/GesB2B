<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE participants MODIFY statut_historique ENUM(
            'actif',
            'inactif',
            'en_attente'
        ) DEFAULT 'en_attente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE participants MODIFY statut_historique ENUM(
            'actif',
            'inactif'
        ) DEFAULT 'actif'");
    }
};