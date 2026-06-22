<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            if (!Schema::hasColumn('stands', 'id_type_stand')) {
                $table->foreignId('id_type_stand')
                      ->nullable()
                      ->after('id')
                      ->constrained('types_stands')
                      ->nullOnDelete();
            }
            if (!Schema::hasColumn('stands', 'standing')) {
                $table->string('standing')
                      ->nullable()
                      ->after('id_type_stand');
            }
            if (!Schema::hasColumn('stands', 'superficie')) {
                $table->string('superficie')
                      ->nullable()
                      ->after('standing');
            }
            if (!Schema::hasColumn('stands', 'composants')) {
                $table->json('composants')
                      ->nullable()
                      ->after('superficie');
            }
            if (!Schema::hasColumn('stands', 'id_participant')) {
                $table->foreignId('id_participant')
                      ->nullable()
                      ->after('composants')
                      ->constrained('participants')
                      ->nullOnDelete();
            }
            if (!Schema::hasColumn('stands', 'motif_gratuite')) {
                $table->text('motif_gratuite')
                      ->nullable()
                      ->after('id_participant');
            }
            if (!Schema::hasColumn('stands', 'est_gratuit')) {
                $table->boolean('est_gratuit')
                      ->default(false)
                      ->after('motif_gratuite');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            if (Schema::hasColumn('stands', 'id_type_stand')) {
                $table->dropConstrainedForeignId('id_type_stand');
            }
            if (Schema::hasColumn('stands', 'id_participant')) {
                $table->dropConstrainedForeignId('id_participant');
            }
            foreach (['standing', 'superficie', 'composants',
                      'motif_gratuite', 'est_gratuit'] as $col) {
                if (Schema::hasColumn('stands', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};