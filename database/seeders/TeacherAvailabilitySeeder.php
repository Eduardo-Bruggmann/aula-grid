<?php

namespace Database\Seeders;

use App\Models\Period;
use App\Models\Teacher;
use App\Models\TeacherAvailability;
use Illuminate\Database\Seeder;

class TeacherAvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        $availabilityByTeacher = [
            'P001' => ['P1', 'P2', 'P4', 'P5', 'P7', 'P10', 'P14'],
            'P002' => ['P1', 'P3', 'P4', 'P6', 'P8', 'P11', 'P13'],
            'P003' => ['P2', 'P3', 'P5', 'P7', 'P9', 'P12', 'P15'],
            'P004' => ['P1', 'P2', 'P6', 'P8', 'P10', 'P11', 'P14'],
            'P005' => ['P3', 'P4', 'P5', 'P9', 'P13', 'P14', 'P15'],
        ];

        $periods = Period::all();

        foreach ($availabilityByTeacher as $registration => $availablePeriodCodes) {
            $teacher = Teacher::where('registration', $registration)->firstOrFail();

            foreach ($periods as $period) {
                TeacherAvailability::firstOrCreate(
                    [
                        'teacher_id' => $teacher->id,
                        'period_id' => $period->id,
                    ],
                    [
                        'is_available' => in_array($period->code, $availablePeriodCodes, true),
                    ]
                );
            }
        }
    }
}
