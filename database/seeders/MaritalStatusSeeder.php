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
            ["en" => 'Bachelor'],
            [
                "en" => 'Bachelor',
                "fr" => 'Célibataire',
            ]
        );
        MaritalStatus::firstOrCreate(
            ["en" => 'Married'],
            [
                "en" => 'Married',
                "fr" => 'Marié(e)',
            ]
        );
        MaritalStatus::firstOrCreate(
            ["en" => 'Divorced'],
            [
                "en" => 'Divorced',
                "fr" => 'Divorcé(e)',
            ]
        );
        MaritalStatus::firstOrCreate(
            ["en" => 'Widower'],
            [
                "en" => 'Widower',
                "fr" => 'Veuf/Veuve',
            ]
        );
    }
}
