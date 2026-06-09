<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            $table->decimal('prix_stand_standard', 10, 2)->nullable()->default(0)->after('montant_inscription');
            $table->decimal('prix_stand_premium', 10, 2)->nullable()->default(0)->after('prix_stand_standard');
            $table->decimal('prix_stand_vip', 10, 2)->nullable()->default(0)->after('prix_stand_premium');
        });
    }

    public function down(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            $table->dropColumn([
                'prix_stand_standard',
                'prix_stand_premium',
                'prix_stand_vip',
            ]);
        });
    }
};