<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            $table->integer('min_souhaits')->default(5)->after('nombre_tables');
            $table->integer('max_souhaits')->default(20)->after('min_souhaits');
        });
    }

    public function down(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            $table->dropColumn(['min_souhaits', 'max_souhaits']);
        });
    }
};