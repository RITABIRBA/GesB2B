<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeEvenement;

class TypeEvenementSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Forum B2B',
            'Salon Commercial',
            'Exposition',
            'Rencontre d\'affaires',
            'Conférence',
            'Atelier',
            'Forum Africallia',
            'SIAO',
        ];

        foreach ($types as $type) {
            TypeEvenement::create(['nom' => $type]);
        }
    }
}