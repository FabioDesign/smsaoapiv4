<?php

namespace Database\Seeders;

use App\Models\Network;
use Illuminate\Database\Seeder;

class NetworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Network::firstOrCreate(
            ["label" => 'Movicel'],
            ["country_id" => 1]
        );
        Network::firstOrCreate(
            ["label" => 'Unitel'],
            ["country_id" => 1]
        );
        Network::firstOrCreate(
            ["label" => 'Africel'],
            ["country_id" => 1]
        );
    }
}
