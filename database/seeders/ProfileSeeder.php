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
            ["libelle" => 'Administrateur'],
            [
                "libelle" => 'Administrateur',
                "status" => 1,
            ]
        );
    }
}
