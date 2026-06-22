<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('types_stands', function (Blueprint $table) {
            if (!Schema::hasColumn('types_stands', 'quantite')) {
                $table->integer('quantite')->default(1)->after('standing');
            }
        });
    }

    public function down(): void
    {
        Schema::table('types_stands', function (Blueprint $table) {
            if (Schema::hasColumn('types_stands', 'quantite')) {
                $table->dropColumn('quantite');
            }
        });
    }
};