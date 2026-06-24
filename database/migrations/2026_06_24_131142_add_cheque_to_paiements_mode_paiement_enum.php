<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    DB::statement("ALTER TABLE paiements MODIFY COLUMN mode_paiement ENUM('especes','virement','mobile_money','carte','orange_money','moov_money','ligdicash_orange_money','ligdicash_moov_money','cheque') NOT NULL DEFAULT 'mobile_money'");
}

public function down(): void
{
    DB::statement("ALTER TABLE paiements MODIFY COLUMN mode_paiement ENUM('especes','virement','mobile_money','carte','orange_money','moov_money','ligdicash_orange_money','ligdicash_moov_money') NOT NULL DEFAULT 'mobile_money'");
}