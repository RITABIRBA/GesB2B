<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE evenements MODIFY COLUMN type_paiement ENUM('gratuit', 'payant', 'par_entreprise') NOT NULL DEFAULT 'gratuit'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE evenements MODIFY COLUMN type_paiement ENUM('gratuit', 'par_entreprise') NOT NULL DEFAULT 'gratuit'");
    }
};