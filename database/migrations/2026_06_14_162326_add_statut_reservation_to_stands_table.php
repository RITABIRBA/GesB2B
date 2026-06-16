<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            if (!Schema::hasColumn('stands', 'statut_reservation')) {
                $table->enum('statut_reservation', ['en_attente', 'valide', 'rejete'])
                    ->nullable()
                    ->after('id_entreprise');
            }
            if (!Schema::hasColumn('stands', 'statut_paiement_stand')) {
                $table->string('statut_paiement_stand')->nullable();
            }
            if (!Schema::hasColumn('stands', 'prix')) {
                $table->decimal('prix', 10, 2)->nullable()->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            foreach (['statut_reservation', 'statut_paiement_stand', 'prix'] as $col) {
                if (Schema::hasColumn('stands', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};