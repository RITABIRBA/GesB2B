<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('types_stands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_evenement')
                  ->constrained('evenements')
                  ->onDelete('cascade');

            // Ex: Standard, Premium, VIP
            $table->string('standing');

            // Ex: 9m², 18m², 36m²
            $table->string('superficie')->nullable();

            // Gratuit ou payant
            $table->boolean('est_gratuit')->default(false);
            $table->decimal('montant', 10, 2)->default(0);

            // Composants du standing
            // ex: {"table": 1, "chaises": 2, "prise": 1}
            $table->json('composants')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('types_stands');
    }
};