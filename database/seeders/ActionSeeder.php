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
            ["label_en" => 'See'],
            [
                "label_fr" => 'Voir',
                "label_en" => 'See',
            ]
        );
        Action::firstOrCreate(
            ["label_en" => 'Add'],
            [
                "label_fr" => 'Ajouter',
                "label_en" => 'Add',
            ]
        );
        Action::firstOrCreate(
            ["label_en" => 'Update'],
            [
                "label_fr" => 'Modifier',
                "label_en" => 'Update',
            ]
        );
        Action::firstOrCreate(
            ["label_en" => 'Enable/Disable'],
            [
                "label_fr" => 'Activer/Désactiver',
                "label_en" => 'Enable/Disable',
            ]
        );
        Action::firstOrCreate(
            ["label_en" => 'Send'],
            [
                "label_fr" => 'Envoyer',
                "label_en" => 'Send',
            ]
        );
        Action::firstOrCreate(
            ["label_en" => 'Approve'],
            [
                "label_fr" => 'Approuver',
                "label_en" => 'Approve',
            ]
        );
        Action::firstOrCreate(
            ["label_en" => 'Delete'],
            [
                "label_fr" => 'Supprimer',
                "label_en" => 'Delete',
            ]
        );
        Action::firstOrCreate(
            ["label_en" => 'Export'],
            [
                "label_fr" => 'Exporter',
                "label_en" => 'Export',
            ]
        );
    }
}
