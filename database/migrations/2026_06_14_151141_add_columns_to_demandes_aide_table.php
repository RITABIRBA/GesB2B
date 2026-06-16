<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demandes_aide', function (Blueprint $table) {
            if (!Schema::hasColumn('demandes_aide', 'id_participant')) {
                $table->foreignId('id_participant')->after('id')->constrained('participants')->onDelete('cascade');
            }
            if (!Schema::hasColumn('demandes_aide', 'id_evenement')) {
                $table->foreignId('id_evenement')->nullable()->after('id_participant')->constrained('evenements')->onDelete('set null');
            }
            if (!Schema::hasColumn('demandes_aide', 'id_cdd')) {
                $table->unsignedBigInteger('id_cdd')->nullable()->after('id_evenement');
            }
            if (!Schema::hasColumn('demandes_aide', 'destinataire_type')) {
                $table->enum('destinataire_type', ['cdd', 'admin_superviseur'])->default('admin_superviseur')->after('id_cdd');
            }
            if (!Schema::hasColumn('demandes_aide', 'sujet')) {
                $table->enum('sujet', ['inscription', 'rendez_vous', 'autre'])->default('autre')->after('destinataire_type');
            }
            if (!Schema::hasColumn('demandes_aide', 'message')) {
                $table->text('message')->after('sujet');
            }
            if (!Schema::hasColumn('demandes_aide', 'statut')) {
                $table->enum('statut', ['en_attente', 'traite'])->default('en_attente')->after('message');
            }
            if (!Schema::hasColumn('demandes_aide', 'traite_le')) {
                $table->timestamp('traite_le')->nullable()->after('statut');
            }
        });
    }

    public function down(): void
    {
        Schema::table('demandes_aide', function (Blueprint $table) {
            $columns = ['id_participant', 'id_evenement', 'id_cdd', 'destinataire_type', 'sujet', 'message', 'statut', 'traite_le'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('demandes_aide', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};