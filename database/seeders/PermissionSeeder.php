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
    }
}
