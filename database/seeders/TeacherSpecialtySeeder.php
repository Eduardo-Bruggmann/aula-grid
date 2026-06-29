<?php

namespace Database\Seeders;

use App\Models\Specialty;
use App\Models\Teacher;
use App\Models\TeacherSpecialty;
use Illuminate\Database\Seeder;

class TeacherSpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'P001' => [
                'CLP Industrial' => 100,
                'Automação Industrial' => 80,
            ],
            'P002' => [
                'Metrologia' => 100,
                'Mecânica' => 70,
            ],
            'P003' => [
                'Automação Industrial' => 100,
                'CLP Industrial' => 85,
            ],
            'P004' => [
                'Elétrica' => 100,
                'Automação Industrial' => 75,
            ],
            'P005' => [
                'Mecânica' => 100,
                'Metrologia' => 80,
            ],
        ];

        foreach ($data as $registration => $specialties) {
            $teacher = Teacher::where('registration', $registration)->firstOrFail();

            foreach ($specialties as $specialtyName => $adherenceScore) {
                $specialty = Specialty::where('name', $specialtyName)->firstOrFail();

                TeacherSpecialty::firstOrCreate(
                    [
                        'teacher_id' => $teacher->id,
                        'specialty_id' => $specialty->id,
                    ],
                    [
                        'adherence_score' => $adherenceScore,
                    ]
                );
            }
        }
    }
}
