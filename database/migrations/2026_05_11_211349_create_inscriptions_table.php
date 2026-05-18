<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_participant')->constrained('participants')->onDelete('cascade');
            $table->foreignId('id_evenement')->constrained('evenements')->onDelete('cascade');
            $table->date('date_inscription');
            $table->decimal('montant_paye', 10, 2);
            $table->enum('statut_paiement', ['en_attente', 'paye', 'annule'])->default('en_attente');
            $table->enum('statut_presence', ['present', 'absent', 'excuse'])->default('absent');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};