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
            ["en" => 'Super Admin'],
            [
                "fr" => 'Super Admin',
                "description_en" => 'Super system administrator.',
                "description_fr" => 'Super administrateur du système.',
                "status" => 1,
                "created_user" => 1,
            ]
        );
        Profile::firstOrCreate(
            ["en" => 'Administrator'],
            [
                "fr" => 'Administrateur',
                "description_en" => 'System Manager.',
                "description_fr" => 'Gestionnaire du système.',
                "status" => 1,
                "created_user" => 1,
            ]
        );
        Profile::firstOrCreate(
            ["en" => 'User'],
            [
                "fr" => 'Utilisateur',
                "description_en" => 'System user.',
                "description_fr" => 'Utilisateur du système.',
                "status" => 1,
                "created_user" => 1,
            ]
        );
    }
}
