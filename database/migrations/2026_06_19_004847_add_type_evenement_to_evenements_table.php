<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            if (!Schema::hasColumn('evenements', 'type_evenement')) {
                $table->enum('type_evenement', ['avec_b2b', 'sans_b2b'])
                      ->default('avec_b2b')
                      ->after('nom');
            }
            if (!Schema::hasColumn('evenements', 'date_limite_rdv')) {
                $table->date('date_limite_rdv')
                      ->nullable()
                      ->after('date_fin');
            }
            if (!Schema::hasColumn('evenements', 'date_cloture_inscriptions')) {
                $table->date('date_cloture_inscriptions')
                      ->nullable()
                      ->after('date_limite_rdv');
            }
        });
    }

    public function down(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            if (Schema::hasColumn('evenements', 'type_evenement')) {
                $table->dropColumn('type_evenement');
            }
            if (Schema::hasColumn('evenements', 'date_limite_rdv')) {
                $table->dropColumn('date_limite_rdv');
            }
            if (Schema::hasColumn('evenements', 'date_cloture_inscriptions')) {
                $table->dropColumn('date_cloture_inscriptions');
            }
        });
    }
};