<?php

namespace Database\Seeders;

use App\Models\Requestdoc;
use Illuminate\Database\Seeder;

class RequestdocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Requestdoc::firstOrCreate(
            ["en" => 'Passport'],
            [
                "en" => 'Passport',
                "fr" => 'Passeport',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["en" => 'National Biometric Identity Card'],
            [
                "en" => 'National Biometric Identity Card',
                "fr" => 'Carte nationale d’identité Biométrique',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["en" => 'Extract from birth certificate'],
            [
                "en" => 'Extract from birth certificate',
                "fr" => 'Extrait d’acte de naissance',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["en" => 'Supplementary judgment in lieu of birth certificate'],
            [
                "en" => 'Supplementary judgment in lieu of birth certificate',
                "fr" => 'Jugement supplétif tenant lieu d’acte de naissance',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["en" => 'Guinean nationality certificate'],
            [
                "en" => 'Guinean nationality certificate',
                "fr" => 'Certificat de nationalité guinéenne',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["en" => 'Residence permit'],
            [
                "en" => 'Residence permit',
                "fr" => 'Titre de séjour',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["en" => 'Handwritten request addressed to the Minister of Foreign Affairs'],
            [
                "en" => 'Handwritten request addressed to the Minister of Foreign Affairs',
                "fr" => 'Demande manuscrite adressée au ministre des Affaires Etrangères',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["en" => 'SC Hierarchical route'],
            [
                "en" => 'SC Hierarchical route',
                "fr" => 'SC Voie hiérarchique',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["en" => 'Old consular identity card'],
            [
                "en" => 'Old consular identity card',
                "fr" => 'Ancienne carte d’identité consulaire',
                "user_id" => 1,
                "status" => 1,
            ]
        );
    }
}
