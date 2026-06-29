<?php

namespace Database\Factories;

use App\Models\SchoolUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    public function definition(): array
    {
        return [
            'registration' => 'P' . fake()->unique()->numberBetween(100, 999),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'school_unit_id' => SchoolUnit::query()->inRandomOrder()->value('id')
                ?? SchoolUnit::factory(),
            'max_weekly_periods' => 7,
            'max_daily_periods' => 2,
            'is_active' => true,
        ];
    }
}
