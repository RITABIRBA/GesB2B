<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->unsignedBigInteger('absent_participant_id')->nullable()->after('statut');
            $table->foreign('absent_participant_id')->references('id')->on('participants')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->dropForeign(['absent_participant_id']);
            $table->dropColumn('absent_participant_id');
        });
    }
};