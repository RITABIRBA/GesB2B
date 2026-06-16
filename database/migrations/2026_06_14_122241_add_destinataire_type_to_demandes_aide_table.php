<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demandes_aide', function (Blueprint $table) {
            if (!Schema::hasColumn('demandes_aide', 'destinataire_type')) {
                $table->enum('destinataire_type', ['cdd', 'admin_superviseur'])->default('admin_superviseur')->after('id_cdd');
            }
        });
    }

    public function down(): void
    {
        Schema::table('demandes_aide', function (Blueprint $table) {
            $table->dropColumn('destinataire_type');
        });
    }
};