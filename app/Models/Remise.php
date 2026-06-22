<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remise extends Model
{
    protected $table = 'remises';

    protected $fillable = [
        'id_evenement', 'libelle', 'type', 'seuil_min',
        'age_min', 'age_max', 'genre', 'pourcentage', 'actif',
    ];

    protected $casts = [
        'actif'      => 'boolean',
        'pourcentage' => 'decimal:2',
    ];

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'id_evenement');
    }

    /**
     * Calcule le meilleur pourcentage de remise applicable à un
     * participant donné, pour son événement, en tenant compte
     * du nombre de participants déjà inscrits par son entreprise.
     */
    public static function calculerMeilleureRemise(Participant $participant): float
    {
        if (!$participant->id_evenement) return 0;

        $remises = self::where('id_evenement', $participant->id_evenement)
            ->where('actif', true)
            ->get();

        $meilleurPourcentage = 0;

        foreach ($remises as $remise) {
            $applicable = false;

            if ($remise->type === 'nb_participants' && $participant->id_entreprise) {
                $nbParticipants = Participant::where('id_entreprise', $participant->id_entreprise)
                    ->where('id_evenement', $participant->id_evenement)
                    ->count();
                $applicable = $nbParticipants >= ($remise->seuil_min ?? 0);
            }

            if ($remise->type === 'age' && $participant->age !== null) {
                $applicable = $participant->age >= ($remise->age_min ?? 0)
                    && $participant->age <= ($remise->age_max ?? 999);
            }

            if ($remise->type === 'genre' && $participant->genre) {
                $applicable = strtolower($participant->genre) === strtolower($remise->genre ?? '');
            }

            if ($applicable && (float) $remise->pourcentage > $meilleurPourcentage) {
                $meilleurPourcentage = (float) $remise->pourcentage;
            }
        }

        return $meilleurPourcentage;
    }
}