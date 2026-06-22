<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('souhaits', function (Blueprint $table) {
            if (!Schema::hasColumn('souhaits', 'id_evenement')) {
                $table->unsignedBigInteger('id_evenement')->nullable()->after('id_participant_cible');
            }
            if (!Schema::hasColumn('souhaits', 'statut')) {
                $table->string('statut')->default('en_attente')->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('souhaits', function (Blueprint $table) {
            $table->dropColumn(['id_evenement', 'statut']);
        });
    }
};