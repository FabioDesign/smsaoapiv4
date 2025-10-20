<?php

namespace Database\Seeders;

use App\Models\SmsType;
use Illuminate\Database\Seeder;

class SmsTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        SmsType::firstOrCreate(
            ["label" => 'Draft']
        );
        SmsType::firstOrCreate(
            ["label" => 'Standard']
        );
        SmsType::firstOrCreate(
            ["label" => 'Scheduled']
        );
        SmsType::firstOrCreate(
            ["label" => 'Publipostage']
        );
    }
}
