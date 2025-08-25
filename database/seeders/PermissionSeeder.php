<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        // Tableau de bord
        Permission::firstOrCreate(
            [
                'menu_id' => 1,
                'action_id' => 1,
                'profile_id' => 1,
            ],
            [
                'menu_id' => 1,
                'action_id' => 1,
                'profile_id' => 1,
            ]
        );
        // Documents
        Permission::firstOrCreate(
            [
                'menu_id' => 2,
                'action_id' => 1,
                'profile_id' => 1,
            ],
            [
                'menu_id' => 2,
                'action_id' => 1,
                'profile_id' => 1,
            ]
        );
        // Pièces à fournir
        Permission::firstOrCreate(
            [
                'menu_id' => 3,
                'action_id' => 1,
                'profile_id' => 1,
            ],
            [
                'menu_id' => 3,
                'action_id' => 1,
                'profile_id' => 1,
            ]
        );
        // Paramètres
        Permission::firstOrCreate(
            [
                'menu_id' => 4,
                'action_id' => 1,
                'profile_id' => 1,
            ],
            [
                'menu_id' => 4,
                'action_id' => 1,
                'profile_id' => 1,
            ]
        );
        // Gestion des Profils
        Permission::firstOrCreate(
            [
                'menu_id' => 5,
                'action_id' => 1,
                'profile_id' => 1,
            ],
            [
                'menu_id' => 5,
                'action_id' => 1,
                'profile_id' => 1,
            ]
        );
        // Gestion des Utilisateurs
        Permission::firstOrCreate(
            [
                'menu_id' => 6,
                'action_id' => 1,
                'profile_id' => 1,
            ],
            [
                'menu_id' => 6,
                'action_id' => 1,
                'profile_id' => 1,
            ]
        );
        // Rendez-vous
        Permission::firstOrCreate(
            [
                'menu_id' => 7,
                'action_id' => 1,
                'profile_id' => 1,
            ],
            [
                'menu_id' => 7,
                'action_id' => 1,
                'profile_id' => 1,
            ]
        );
        // Piste d'audit
        Permission::firstOrCreate(
            [
                'menu_id' => 8,
                'action_id' => 1,
                'profile_id' => 1,
            ],
            [
                'menu_id' => 8,
                'action_id' => 1,
                'profile_id' => 1,
            ]
        );
    }
}
