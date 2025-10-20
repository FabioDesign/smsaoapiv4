<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        AccountType::firstOrCreate(
            ["en" => 'Personal account'],
            ["pt" => 'Conta pessoal']
        );
        AccountType::firstOrCreate(
            ["en" => 'Community'],
            ["pt" => 'Comunidade']
        );
        AccountType::firstOrCreate(
            ["en" => 'Educational institution'],
            ["pt" => 'Instituição de ensino']
        );
        AccountType::firstOrCreate(
            ["en" => 'Organisation'],
            ["pt" => 'Organização']
        );
        AccountType::firstOrCreate(
            ["en" => 'Company'],
            ["pt" => 'Empresa']
        );
    }
}
