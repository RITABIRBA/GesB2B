<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les rôles
        $admin       = Role::create(['name' => 'admin']);
        $superviseur = Role::create(['name' => 'superviseur']);
        $cdd         = Role::create(['name' => 'cdd']);
        $entreprise  = Role::create(['name' => 'entreprise']);
        $participant = Role::create(['name' => 'participant']);
        $traducteur  = Role::create(['name' => 'traducteur']);

        // Créer les permissions
        Permission::create(['name' => 'gerer evenements']);
        Permission::create(['name' => 'gerer utilisateurs']);
        Permission::create(['name' => 'valider entreprises']);
        Permission::create(['name' => 'gerer participants']);
        Permission::create(['name' => 'gerer stands']);
        Permission::create(['name' => 'gerer rendez_vous']);
        Permission::create(['name' => 'gerer badges']);
        Permission::create(['name' => 'envoyer notifications']);
        Permission::create(['name' => 'voir statistiques']);
        Permission::create(['name' => 'emettre souhaits']);
        Permission::create(['name' => 'voir planning']);
        Permission::create(['name' => 'telecharger badge']);

        // Assigner les permissions aux rôles
        $admin->givePermissionTo(Permission::all());

        $superviseur->givePermissionTo([
            'voir statistiques',
            'voir planning',
        ]);

        $cdd->givePermissionTo([
            'valider entreprises',
            'gerer participants',
            'voir planning',
        ]);

        $entreprise->givePermissionTo([
            'gerer participants',
            'voir planning',
        ]);

        $participant->givePermissionTo([
            'emettre souhaits',
            'voir planning',
            'telecharger badge',
        ]);

        $traducteur->givePermissionTo([
            'voir planning',
        ]);
    }
}