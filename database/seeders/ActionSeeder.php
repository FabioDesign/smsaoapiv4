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
            ["fr" => 'Voir']
        );
        Action::firstOrCreate(
            ["en" => 'Add'],
            ["fr" => 'Ajouter']
        );
        Action::firstOrCreate(
            ["en" => 'Update'],
            ["fr" => 'Modifier']
        );
        Action::firstOrCreate(
            ["en" => 'Enable/Disable'],
            ["fr" => 'Activer/Désactiver']
        );
        Action::firstOrCreate(
            ["en" => 'Send'],
            ["fr" => 'Envoyer']
        );
        Action::firstOrCreate(
            ["en" => 'Approve'],
            ["fr" => 'Approuver']
        );
        Action::firstOrCreate(
            ["en" => 'Delete'],
            ["fr" => 'Supprimer']
        );
        Action::firstOrCreate(
            ["en" => 'Export'],
            ["fr" => 'Exporter']
        );
    }
}
