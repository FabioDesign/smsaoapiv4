<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Profile::firstOrCreate(
            ["label_en" => 'Administrator'],
            [
                "label_en" => 'Administrator',
                "label_fr" => 'Administrateur',
                "description_en" => 'System Manager.',
                "description_fr" => 'Gestionnaire du système.',
                "status" => 1,
            ]
        );
    }
}
