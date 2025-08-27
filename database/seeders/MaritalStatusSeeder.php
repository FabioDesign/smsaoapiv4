<?php

namespace Database\Seeders;

use App\Models\MaritalStatus;
use Illuminate\Database\Seeder;

class MaritalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        MaritalStatus::firstOrCreate(
            ["label_en" => 'Bachelor'],
            [
                "label_en" => 'Bachelor',
                "label_fr" => 'Célibataire',
            ]
        );
        MaritalStatus::firstOrCreate(
            ["label_en" => 'Married'],
            [
                "label_en" => 'Married',
                "label_fr" => 'Marié(e)',
            ]
        );
        MaritalStatus::firstOrCreate(
            ["label_en" => 'Divorced'],
            [
                "label_en" => 'Divorced',
                "label_fr" => 'Divorcé(e)',
            ]
        );
        MaritalStatus::firstOrCreate(
            ["label_en" => 'Widower'],
            [
                "label_en" => 'Widower',
                "label_fr" => 'Veuf/Veuve',
            ]
        );
    }
}
