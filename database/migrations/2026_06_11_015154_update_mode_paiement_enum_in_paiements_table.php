<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Passe la colonne en VARCHAR pour accepter toutes les valeurs
        // utilisées dans le code (cheque, ligdicash_orange_money, etc.)
        DB::statement("ALTER TABLE paiements MODIFY mode_paiement VARCHAR(50) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE paiements MODIFY mode_paiement ENUM('orange_money','moov_money') NOT NULL");
    }
};