<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\SchoolUnit;
use App\Models\Specialty;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherSpecialty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpecialtyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_specialty_linked_to_subject_cannot_be_deleted(): void
    {
        $specialty = Specialty::query()->create([
            'name' => 'Automação',
            'description' => 'Automação industrial.',
        ]);

        Subject::query()->create([
            'name' => 'CLP Industrial',
            'specialty_id' => $specialty->id,
        ]);

        $response = $this->delete(route('specialties.destroy', $specialty));

        $response
            ->assertRedirect(route('specialties.index'))
            ->assertSessionHas('error', 'Não é possível remover esta especialidade porque ela está vinculada a unidades curriculares ou professores.');

        $this->followingRedirects()
            ->delete(route('specialties.destroy', $specialty))
            ->assertOk()
            ->assertSee('Não é possível remover esta especialidade porque ela está vinculada a unidades curriculares ou professores.');

        $this->assertDatabaseHas('specialties', [
            'id' => $specialty->id,
        ]);
    }

    public function test_specialty_linked_to_teacher_cannot_be_deleted(): void
    {
        $schoolUnit = SchoolUnit::query()->create([
            'name' => 'SENAI Teste',
        ]);

        $teacher = Teacher::query()->create([
            'registration' => 'TEST-001',
            'name' => 'Professor Teste',
            'email' => 'professor@example.com',
            'school_unit_id' => $schoolUnit->id,
            'max_weekly_periods' => 20,
            'max_daily_periods' => 2,
            'is_active' => true,
        ]);

        $specialty = Specialty::query()->create([
            'name' => 'Metrologia',
            'description' => 'Metrologia dimensional.',
        ]);

        TeacherSpecialty::query()->create([
            'teacher_id' => $teacher->id,
            'specialty_id' => $specialty->id,
            'adherence_score' => 100,
        ]);

        $response = $this->delete(route('specialties.destroy', $specialty));

        $response
            ->assertRedirect(route('specialties.index'))
            ->assertSessionHas('error', 'Não é possível remover esta especialidade porque ela está vinculada a unidades curriculares ou professores.');

        $this->assertDatabaseHas('specialties', [
            'id' => $specialty->id,
        ]);
    }

    public function test_specialty_without_links_can_be_deleted(): void
    {
        $specialty = Specialty::query()->create([
            'name' => 'Mecânica',
            'description' => 'Mecânica industrial.',
        ]);

        $response = $this->delete(route('specialties.destroy', $specialty));

        $response
            ->assertRedirect(route('specialties.index'))
            ->assertSessionHas('success', 'Especialidade removida com sucesso.');

        $this->assertDatabaseMissing('specialties', [
            'id' => $specialty->id,
        ]);
    }
}
