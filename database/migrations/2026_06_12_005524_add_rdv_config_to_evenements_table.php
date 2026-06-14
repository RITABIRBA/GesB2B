<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            // Durée d'un RDV en minutes (ex: 20 min)
            $table->integer('duree_rdv')->default(20)->after('max_souhaits');
            // Durée de pause entre 2 RDV en minutes (ex: 5 min)
            $table->integer('duree_pause')->default(5)->after('duree_rdv');
        });
    }

    public function down(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            $table->dropColumn(['duree_rdv', 'duree_pause']);
        });
    }
};