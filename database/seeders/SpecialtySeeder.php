<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            [
                'name' => 'CLP Industrial',
                'description' => 'Controladores lógicos programáveis e automação industrial.',
            ],
            [
                'name' => 'Metrologia',
                'description' => 'Medição, instrumentos e controle dimensional.',
            ],
            [
                'name' => 'Automação Industrial',
                'description' => 'Sistemas automatizados, sensores, atuadores e controle.',
            ],
            [
                'name' => 'Mecânica',
                'description' => 'Processos mecânicos, manutenção e fabricação.',
            ],
            [
                'name' => 'Elétrica',
                'description' => 'Instalações elétricas, comandos elétricos e circuitos.',
            ],
        ];

        foreach ($specialties as $specialty) {
            Specialty::firstOrCreate(
                ['name' => $specialty['name']],
                ['description' => $specialty['description']]
            );
        }
    }
}
