<?php

namespace Database\Seeders;

use App\Models\TypeParent;
use Illuminate\Database\Seeder;

class TypeParentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        TypeParent::firstOrCreate(
            ["en" => 'Father'],
            [
                "en" => 'Father',
                "fr" => 'Père',
            ]
        );
        TypeParent::firstOrCreate(
            ["en" => 'Mother'],
            [
                "en" => 'Mother',
                "fr" => 'Mère',
            ]
        );
    }
}
