<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Type de partenariat (max 3 choix + autre)
            $table->json('types_partenariat')->nullable()->after('type_partenaire');
            $table->string('type_partenariat_autre')->nullable()->after('types_partenariat');

            // Profil de partenaire recherché (max 3 choix)
            $table->json('profils_partenaire')->nullable()->after('type_partenariat_autre');

            // Secteur recherché (max 3 choix + autre)
            $table->json('secteurs_recherche')->nullable()->after('profils_partenaire');
            $table->string('secteur_recherche_autre')->nullable()->after('secteurs_recherche');

            // Objectif de participation (200 caractères)
            $table->string('objectif_participation', 200)->nullable()->after('secteur_recherche_autre');

            // Chef de délégation
            $table->unsignedBigInteger('id_chef_delegation')->nullable()->after('objectif_participation');
            $table->foreign('id_chef_delegation')
                  ->references('id')
                  ->on('participants')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign(['id_chef_delegation']);
            $table->dropColumn([
                'types_partenariat',
                'type_partenariat_autre',
                'profils_partenaire',
                'secteurs_recherche',
                'secteur_recherche_autre',
                'objectif_participation',
                'id_chef_delegation',
            ]);
        });
    }
};