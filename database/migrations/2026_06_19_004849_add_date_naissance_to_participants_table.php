<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Date de naissance
            $table->date('date_naissance')
                  ->nullable()
                  ->after('email');

            // Filière et université (si étudiant)
            $table->string('filiere')
                  ->nullable()
                  ->after('date_naissance');

            $table->string('universite')
                  ->nullable()
                  ->after('filiere');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn([
                'date_naissance',
                'filiere',
                'universite',
            ]);
        });
    }
};