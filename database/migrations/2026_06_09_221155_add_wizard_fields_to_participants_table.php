<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->string('type_partenaire')->nullable()->after('secteur_activite');
            $table->string('zone_geographique')->nullable()->after('type_partenaire');
            $table->json('disponibilites')->nullable()->after('zone_geographique');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['type_partenaire', 'zone_geographique', 'disponibilites']);
        });
    }
};