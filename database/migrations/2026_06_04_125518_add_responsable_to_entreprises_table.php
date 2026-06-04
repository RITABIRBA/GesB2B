<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entreprises', function (Blueprint $table) {
            $table->string('nom_responsable')->nullable()->after('nom');
            $table->string('prenom_responsable')->nullable()->after('nom_responsable');
            $table->string('fonction_responsable')->nullable()->after('prenom_responsable');
            $table->string('email_responsable')->nullable()->after('fonction_responsable');
        });
    }

    public function down(): void
    {
        Schema::table('entreprises', function (Blueprint $table) {
            $table->dropColumn([
                'nom_responsable',
                'prenom_responsable',
                'fonction_responsable',
                'email_responsable',
            ]);
        });
    }
};