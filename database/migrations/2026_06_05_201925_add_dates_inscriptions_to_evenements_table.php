<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            $table->date('date_ouverture_inscriptions')->nullable()->after('date_fin');
            $table->date('date_cloture_inscriptions')->nullable()->after('date_ouverture_inscriptions');
        });
    }

    public function down(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            $table->dropColumn([
                'date_ouverture_inscriptions',
                'date_cloture_inscriptions',
            ]);
        });
    }
};