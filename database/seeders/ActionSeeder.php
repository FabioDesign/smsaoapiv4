<?php

namespace Database\Seeders;

use App\Models\Action;
use Illuminate\Database\Seeder;

class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Action::firstOrCreate(
            ["code" => 'read'],
            [
                "code" => 'read',
                "label_fr" => 'Lire',
                "label_en" => 'Read',
                "icone" => 'dashboard-icon',
                "status" => 1,
                "position" => 1,
            ]
        );
    }
}
