<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\SchoolUnit;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        $schoolUnit = SchoolUnit::where('name', 'SENAI Londrina')->firstOrFail();

        $classes = [
            [
                'name' => 'Eletromecânica 01',
                'subject' => 'CLP Industrial',
                'required_periods' => 7,
            ],
            [
                'name' => 'Mecânica 01',
                'subject' => 'Metrologia',
                'required_periods' => 7,
            ],
            [
                'name' => 'Automação 01',
                'subject' => 'Automação Industrial',
                'required_periods' => 7,
            ],
            [
                'name' => 'Elétrica 01',
                'subject' => 'Comandos Elétricos',
                'required_periods' => 7,
            ],
            [
                'name' => 'Manutenção 01',
                'subject' => 'Manutenção Mecânica',
                'required_periods' => 7,
            ],
        ];

        foreach ($classes as $class) {
            $subject = Subject::where('name', $class['subject'])->firstOrFail();

            SchoolClass::firstOrCreate(
                [
                    'name' => $class['name'],
                    'school_unit_id' => $schoolUnit->id,
                ],
                [
                    'subject_id' => $subject->id,
                    'required_periods' => $class['required_periods'],
                    'is_active' => true,
                ]
            );
        }
    }
}
