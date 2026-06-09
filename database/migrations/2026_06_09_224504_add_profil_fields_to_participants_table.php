<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->string('sous_secteur')->nullable()->after('secteur_activite');
            $table->text('description_activites')->nullable()->after('sous_secteur');
            $table->text('principaux_produits')->nullable()->after('description_activites');
            $table->integer('annee_creation')->nullable()->after('principaux_produits');
            $table->integer('nombre_salaries')->nullable()->after('annee_creation');
            $table->decimal('chiffre_affaires', 5, 2)->nullable()->after('nombre_salaries');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn([
                'sous_secteur',
                'description_activites',
                'principaux_produits',
                'annee_creation',
                'nombre_salaries',
                'chiffre_affaires',
            ]);
        });
    }
};