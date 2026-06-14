<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('souhaits', function (Blueprint $table) {
            $table->foreignId('id_evenement')
                ->nullable()
                ->after('id_participant_cible')
                ->constrained('evenements')
                ->onDelete('cascade');

            $table->enum('statut', [
                'en_attente',
                'compatible',
                'incompatible',
                'accepte',
                'rejete',
                'annule'
            ])->default('en_attente')->after('type');

            $table->text('note')->nullable()->after('statut');
        });
    }

    public function down(): void
    {
        Schema::table('souhaits', function (Blueprint $table) {
            $table->dropForeign(['id_evenement']);
            $table->dropColumn(['id_evenement', 'statut', 'note']);
        });
    }
};