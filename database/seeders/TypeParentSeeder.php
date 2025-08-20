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
            ["label_en" => 'Father'],
            [
                "label_en" => 'Father',
                "label_fr" => 'Père',
            ]
        );
        TypeParent::firstOrCreate(
            ["label_en" => 'Mother'],
            [
                "label_en" => 'Mother',
                "label_fr" => 'Mère',
            ]
        );
    }
}
