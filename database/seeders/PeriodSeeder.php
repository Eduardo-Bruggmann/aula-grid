<?php

namespace Database\Seeders;

use App\Models\Period;
use Illuminate\Database\Seeder;

class PeriodSeeder extends Seeder
{
    public function run(): void
    {
        $periods = [
            ['code' => 'P1',  'weekday' => 1, 'shift' => 'morning',   'description' => 'Segunda-feira Manhã', 'sort_order' => 1],
            ['code' => 'P2',  'weekday' => 1, 'shift' => 'afternoon', 'description' => 'Segunda-feira Tarde', 'sort_order' => 2],
            ['code' => 'P3',  'weekday' => 1, 'shift' => 'night',     'description' => 'Segunda-feira Noite', 'sort_order' => 3],

            ['code' => 'P4',  'weekday' => 2, 'shift' => 'morning',   'description' => 'Terça-feira Manhã', 'sort_order' => 4],
            ['code' => 'P5',  'weekday' => 2, 'shift' => 'afternoon', 'description' => 'Terça-feira Tarde', 'sort_order' => 5],
            ['code' => 'P6',  'weekday' => 2, 'shift' => 'night',     'description' => 'Terça-feira Noite', 'sort_order' => 6],

            ['code' => 'P7',  'weekday' => 3, 'shift' => 'morning',   'description' => 'Quarta-feira Manhã', 'sort_order' => 7],
            ['code' => 'P8',  'weekday' => 3, 'shift' => 'afternoon', 'description' => 'Quarta-feira Tarde', 'sort_order' => 8],
            ['code' => 'P9',  'weekday' => 3, 'shift' => 'night',     'description' => 'Quarta-feira Noite', 'sort_order' => 9],

            ['code' => 'P10', 'weekday' => 4, 'shift' => 'morning',   'description' => 'Quinta-feira Manhã', 'sort_order' => 10],
            ['code' => 'P11', 'weekday' => 4, 'shift' => 'afternoon', 'description' => 'Quinta-feira Tarde', 'sort_order' => 11],
            ['code' => 'P12', 'weekday' => 4, 'shift' => 'night',     'description' => 'Quinta-feira Noite', 'sort_order' => 12],

            ['code' => 'P13', 'weekday' => 5, 'shift' => 'morning',   'description' => 'Sexta-feira Manhã', 'sort_order' => 13],
            ['code' => 'P14', 'weekday' => 5, 'shift' => 'afternoon', 'description' => 'Sexta-feira Tarde', 'sort_order' => 14],
            ['code' => 'P15', 'weekday' => 5, 'shift' => 'night',     'description' => 'Sexta-feira Noite', 'sort_order' => 15],
        ];

        foreach ($periods as $period) {
            Period::firstOrCreate(
                ['code' => $period['code']],
                $period
            );
        }
    }
}
