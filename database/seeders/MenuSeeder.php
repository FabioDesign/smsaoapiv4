<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Menu::firstOrCreate(
            ["code" => 'dashboard'],
            [
                "code" => 'dashboard',
                "label_fr" => 'Tableau de bord',
                "label_en" => 'Dashboard',
                "status" => 1,
                "icone" => 'dashboard-icon',
                "target" => 'dashboard',
                "menu_id" => 0,
                "position" => 1,
            ]
        );
    }
}
