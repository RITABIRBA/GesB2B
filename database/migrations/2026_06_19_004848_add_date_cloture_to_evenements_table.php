<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            // date_cloture_inscriptions existe déjà
            // On ajoute uniquement date_limite_rdv si elle n'existe pas
            if (!Schema::hasColumn('evenements', 'date_limite_rdv')) {
                $table->date('date_limite_rdv')
                      ->nullable()
                      ->after('date_cloture_inscriptions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            if (Schema::hasColumn('evenements', 'date_limite_rdv')) {
                $table->dropColumn('date_limite_rdv');
            }
        });
    }
};