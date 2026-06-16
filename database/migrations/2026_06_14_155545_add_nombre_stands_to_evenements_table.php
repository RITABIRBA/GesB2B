<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            if (!Schema::hasColumn('evenements', 'nombre_stands')) {
                $table->integer('nombre_stands')->nullable()->default(0)->after('nombre_tables');
            }
            if (!Schema::hasColumn('evenements', 'prix_stand_standard')) {
                $table->decimal('prix_stand_standard', 10, 2)->nullable()->default(0);
            }
            if (!Schema::hasColumn('evenements', 'prix_stand_premium')) {
                $table->decimal('prix_stand_premium', 10, 2)->nullable()->default(0);
            }
            if (!Schema::hasColumn('evenements', 'prix_stand_vip')) {
                $table->decimal('prix_stand_vip', 10, 2)->nullable()->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            foreach (['nombre_stands', 'prix_stand_standard', 'prix_stand_premium', 'prix_stand_vip'] as $col) {
                if (Schema::hasColumn('evenements', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};