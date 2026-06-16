<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE rendez_vous MODIFY statut ENUM('a_planifier','planifie','confirme','annule','termine') NOT NULL DEFAULT 'a_planifier'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE rendez_vous MODIFY statut ENUM('planifie','confirme','annule','termine') NOT NULL DEFAULT 'planifie'");
    }
};