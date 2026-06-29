<?php

namespace Database\Factories;

use App\Models\SchoolUnit;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolClassFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Eletromecânica',
                'Mecânica',
                'Automação',
                'Elétrica',
                'Manutenção',
            ]) . ' ' . fake()->numberBetween(1, 99),

            'subject_id' => Subject::query()->inRandomOrder()->value('id'),
            'school_unit_id' => SchoolUnit::query()->inRandomOrder()->value('id'),
            'required_periods' => 7,
            'is_active' => true,
        ];
    }
}
