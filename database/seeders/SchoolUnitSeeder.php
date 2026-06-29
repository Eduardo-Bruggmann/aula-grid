<?php

namespace Database\Seeders;

use App\Models\SchoolUnit;
use Illuminate\Database\Seeder;

class SchoolUnitSeeder extends Seeder
{
    public function run(): void
    {
        SchoolUnit::firstOrCreate([
            'name' => 'SENAI Londrina',
        ]);

        SchoolUnit::firstOrCreate([
            'name' => 'SENAI Curitiba',
        ]);
    }
}
