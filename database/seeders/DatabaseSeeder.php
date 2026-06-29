<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SchoolUnitSeeder::class,
            SpecialtySeeder::class,
            SubjectSeeder::class,
            PeriodSeeder::class,
            TeacherSeeder::class,
            TeacherSpecialtySeeder::class,
            SchoolClassSeeder::class,
            TeacherAvailabilitySeeder::class,
        ]);
    }
}
