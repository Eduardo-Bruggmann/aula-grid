<?php

namespace Database\Seeders;

use App\Models\Specialty;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            'CLP Industrial' => 'CLP Industrial',
            'Metrologia' => 'Metrologia',
            'Automação Industrial' => 'Automação Industrial',
            'Manutenção Mecânica' => 'Mecânica',
            'Comandos Elétricos' => 'Elétrica',
        ];

        foreach ($subjects as $subjectName => $specialtyName) {
            $specialty = Specialty::where('name', $specialtyName)->firstOrFail();

            Subject::firstOrCreate(
                ['name' => $subjectName],
                ['specialty_id' => $specialty->id]
            );
        }
    }
}
