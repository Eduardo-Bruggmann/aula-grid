<?php

namespace Database\Seeders;

use App\Models\SchoolUnit;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $schoolUnit = SchoolUnit::where('name', 'SENAI Londrina')->firstOrFail();

        $teachers = [
            [
                'registration' => 'P001',
                'name' => 'João Silva',
                'email' => 'joao.silva@senai.local',
            ],
            [
                'registration' => 'P002',
                'name' => 'Carlos Souza',
                'email' => 'carlos.souza@senai.local',
            ],
            [
                'registration' => 'P003',
                'name' => 'Maria Oliveira',
                'email' => 'maria.oliveira@senai.local',
            ],
            [
                'registration' => 'P004',
                'name' => 'Ana Pereira',
                'email' => 'ana.pereira@senai.local',
            ],
            [
                'registration' => 'P005',
                'name' => 'Roberto Lima',
                'email' => 'roberto.lima@senai.local',
            ],
        ];

        foreach ($teachers as $teacher) {
            Teacher::firstOrCreate(
                ['registration' => $teacher['registration']],
                [
                    'name' => $teacher['name'],
                    'email' => $teacher['email'],
                    'school_unit_id' => $schoolUnit->id,
                    'max_weekly_periods' => 7,
                    'max_daily_periods' => 2,
                    'is_active' => true,
                ]
            );
        }
    }
}
