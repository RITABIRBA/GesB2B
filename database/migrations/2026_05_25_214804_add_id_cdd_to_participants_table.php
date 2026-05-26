<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cdd')->nullable()->after('id');
            $table->foreign('id_cdd')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign(['id_cdd']);
            $table->dropColumn('id_cdd');
        });
    }
};