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
            ["label_en" => 'Passport'],
            [
                "label_en" => 'Passport',
                "label_fr" => 'Passeport',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["label_en" => 'National Biometric Identity Card'],
            [
                "label_en" => 'National Biometric Identity Card',
                "label_fr" => 'Carte nationale d’identité Biométrique',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["label_en" => 'Extract from birth certificate'],
            [
                "label_en" => 'Extract from birth certificate',
                "label_fr" => 'Extrait d’acte de naissance',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["label_en" => 'Supplementary judgment in lieu of birth certificate'],
            [
                "label_en" => 'Supplementary judgment in lieu of birth certificate',
                "label_fr" => 'Jugement supplétif tenant lieu d’acte de naissance',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["label_en" => 'Guinean nationality certificate'],
            [
                "label_en" => 'Guinean nationality certificate',
                "label_fr" => 'Certificat de nationalité guinéenne',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["label_en" => 'Residence permit'],
            [
                "label_en" => 'Residence permit',
                "label_fr" => 'Titre de séjour',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["label_en" => 'Handwritten request addressed to the Minister of Foreign Affairs'],
            [
                "label_en" => 'Handwritten request addressed to the Minister of Foreign Affairs',
                "label_fr" => 'Demande manuscrite adressée au ministre des Affaires Etrangères',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["label_en" => 'SC Hierarchical route'],
            [
                "label_en" => 'SC Hierarchical route',
                "label_fr" => 'SC Voie hiérarchique',
                "user_id" => 1,
                "status" => 1,
            ]
        );
        Requestdoc::firstOrCreate(
            ["label_en" => 'Old consular identity card'],
            [
                "label_en" => 'Old consular identity card',
                "label_fr" => 'Ancienne carte d’identité consulaire',
                "user_id" => 1,
                "status" => 1,
            ]
        );
    }
}
