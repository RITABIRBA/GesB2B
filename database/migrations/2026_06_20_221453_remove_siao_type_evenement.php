<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\TypeEvenement;
use App\Models\Evenement;

return new class extends Migration
{
    public function up(): void
    {
        $siao = TypeEvenement::where('nom', 'SIAO')->first();

        if ($siao) {
            // Détache les événements liés avant suppression (évite erreur FK)
            Evenement::where('id_type_evenement', $siao->id)
                ->update(['id_type_evenement' => null]);

            $siao->delete();
        }
    }

    public function down(): void
    {
        // Pas de retour arrière nécessaire
    }
};