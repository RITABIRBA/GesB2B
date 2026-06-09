<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->string('secteur_recherche')->nullable()->after('statut_presence');
            $table->string('type_partenaire')->nullable()->after('secteur_recherche');
            $table->string('zone_geographique')->nullable()->after('type_partenaire');
        });
    }

    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropColumn(['secteur_recherche', 'type_partenaire', 'zone_geographique']);
        });
    }
};