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
            ["en" => 'See'],
            [
                "fr" => 'Voir',
                "en" => 'See',
            ]
        );
        Action::firstOrCreate(
            ["en" => 'Add'],
            [
                "fr" => 'Ajouter',
                "en" => 'Add',
            ]
        );
        Action::firstOrCreate(
            ["en" => 'Update'],
            [
                "fr" => 'Modifier',
                "en" => 'Update',
            ]
        );
        Action::firstOrCreate(
            ["en" => 'Enable/Disable'],
            [
                "fr" => 'Activer/Désactiver',
                "en" => 'Enable/Disable',
            ]
        );
        Action::firstOrCreate(
            ["en" => 'Send'],
            [
                "fr" => 'Envoyer',
                "en" => 'Send',
            ]
        );
        Action::firstOrCreate(
            ["en" => 'Approve'],
            [
                "fr" => 'Approuver',
                "en" => 'Approve',
            ]
        );
        Action::firstOrCreate(
            ["en" => 'Delete'],
            [
                "fr" => 'Supprimer',
                "en" => 'Delete',
            ]
        );
        Action::firstOrCreate(
            ["en" => 'Export'],
            [
                "fr" => 'Exporter',
                "en" => 'Export',
            ]
        );
    }
}
