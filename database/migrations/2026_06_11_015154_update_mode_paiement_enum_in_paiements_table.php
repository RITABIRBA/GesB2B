<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE paiements MODIFY mode_paiement ENUM(
            'especes',
            'virement',
            'mobile_money',
            'carte',
            'orange_money',
            'moov_money',
            'ligdicash_orange_money',
            'ligdicash_moov_money'
        ) NOT NULL DEFAULT 'mobile_money'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE paiements MODIFY mode_paiement ENUM(
            'especes',
            'virement',
            'mobile_money',
            'carte',
            'orange_money',
            'moov_money'
        ) NOT NULL DEFAULT 'mobile_money'");
    }
};