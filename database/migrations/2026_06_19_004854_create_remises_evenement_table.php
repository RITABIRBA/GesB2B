<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('remises_evenement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_evenement')
                  ->constrained('evenements')
                  ->onDelete('cascade');

            // Critère de remise
            $table->enum('critere', [
                'femme',
                'jeune',
                'sponsor',
                'partenaire',
                'entreprise',
            ]);

            // Age max si critère = jeune
            $table->integer('age_max')->nullable();

            // Seuil participants si critère = entreprise
            $table->integer('seuil_participants')->nullable();

            // Pourcentage de remise (0-100)
            $table->decimal('pourcentage_remise', 5, 2)->default(0);

            // Stand gratuit pour ce critère
            $table->boolean('stand_gratuit')->default(false);

            // Remises cumulables ou non
            $table->boolean('cumulable')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remises_evenement');
    }
};