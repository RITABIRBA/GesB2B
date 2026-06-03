<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->string('genre')->nullable()->after('prenom');
            $table->string('fonction')->nullable()->after('genre');
            $table->boolean('participation_rdv')->default(true)->after('fonction');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['genre', 'fonction', 'participation_rdv']);
        });
    }
};