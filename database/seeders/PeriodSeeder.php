<?php

namespace Database\Seeders;

use App\Models\Period;
use Illuminate\Database\Seeder;

class PeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Period::firstOrCreate(
            ["en" => 'Days'],
            ["fr" => 'Jours']
        );
        Period::firstOrCreate(
            ["en" => 'Weeks'],
            ["fr" => 'Semaines']
        );
        Period::firstOrCreate(
            ["en" => 'Months'],
            ["fr" => 'Mois']
        );
        Period::firstOrCreate(
            ["en" => 'Years'],
            ["fr" => 'Années']
        );
    }
}
