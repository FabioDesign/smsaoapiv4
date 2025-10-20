<?php

namespace Database\Seeders;

use App\Models\Town;
use Illuminate\Database\Seeder;

class TownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Town::firstOrCreate(
            ["label" => 'Tchouk'],
            ["country_id" => 1]
        );
        Town::firstOrCreate(
            ["label" => 'Luanda'],
            ["country_id" => 1]
        );
        Town::firstOrCreate(
            ["label" => 'Tchoupy'],
            ["country_id" => 1]
        );
        Town::firstOrCreate(
            ["label" => 'Kilamba'],
            ["country_id" => 1]
        );
        Town::firstOrCreate(
            ["label" => 'Lobito'],
            ["country_id" => 1]
        );
        Town::firstOrCreate(
            ["label" => 'Cabinda'],
            ["country_id" => 1]
        );
        Town::firstOrCreate(
            ["label" => 'Ondjiva'],
            ["country_id" => 1]
        );
        Town::firstOrCreate(
            ["label" => 'Huambo'],
            ["country_id" => 1]
        );
        Town::firstOrCreate(
            ["label" => 'Lubango'],
            ["country_id" => 1]
        );
        Town::firstOrCreate(
            ["label" => 'Lucapa'],
            ["country_id" => 1]
        );
        Town::firstOrCreate(
            ["label" => 'Porto Amboim'],
            ["country_id" => 1]
        );
        Town::firstOrCreate(
            ["label" => 'Moxico'],
            ["country_id" => 1]
        );
        Town::firstOrCreate(
            ["label" => 'Landa'],
            ["country_id" => 1]
        );
        Town::firstOrCreate(
            ["label" => 'Catumbela'],
            ["country_id" => 1]
        );
        Town::firstOrCreate(
            ["label" => 'Benguela'],
            ["country_id" => 1]
        );
    }
}
