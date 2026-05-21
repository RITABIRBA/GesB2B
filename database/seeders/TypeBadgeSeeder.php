<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeBadge;

class TypeBadgeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'libelle'     => 'Participant',
                'description' => 'Badge standard pour les participants',
            ],
            [
                'libelle'     => 'VIP',
                'description' => 'Badge VIP pour les invités de marque',
            ],
            [
                'libelle'     => 'Exposant',
                'description' => 'Badge pour les entreprises exposantes',
            ],
            [
                'libelle'     => 'Organisateur',
                'description' => 'Badge pour l\'équipe organisatrice',
            ],
            [
                'libelle'     => 'Traducteur',
                'description' => 'Badge pour les traducteurs',
            ],
        ];

        foreach ($types as $type) {
            TypeBadge::firstOrCreate(
                ['libelle'     => $type['libelle']],
                ['description' => $type['description']]
            );
        }

        $this->command->info('Types de badges créés avec succès !');
    }
}