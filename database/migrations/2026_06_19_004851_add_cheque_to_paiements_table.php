<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            // Numéro de chèque
            $table->string('numero_cheque')
                  ->nullable()
                  ->after('mode_paiement');

            // Type de paiement : individuel ou entreprise
            $table->enum('type_paiement', [
                'individuel',
                'entreprise',
            ])->default('individuel')->after('numero_cheque');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn([
                'numero_cheque',
                'type_paiement',
            ]);
        });
    }
};