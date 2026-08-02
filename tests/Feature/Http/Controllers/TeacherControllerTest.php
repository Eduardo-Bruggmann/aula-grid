<?php

namespace Tests\Feature\Http\Controllers;

use App\Domain\Allocation\Enums\AllocationConflictCode;
use App\Models\Allocation;
use App\Models\AllocationConflict;
use App\Models\AllocationRun;
use App\Models\ConflictSuggestion;
use App\Models\Period;
use App\Models\SchoolClass;
use App\Models\SchoolUnit;
use App\Models\Specialty;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_with_allocation_history_cannot_be_deleted(): void
    {
        $scenario = $this->createTeacherScenario();

        $allocationRun = AllocationRun::query()->create([
            'status' => 'completed',
            'score' => 100,
            'total_allocations' => 1,
            'total_conflicts' => 0,
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        Allocation::query()->create([
            'allocation_run_id' => $allocationRun->id,
            'school_class_id' => $scenario['schoolClass']->id,
            'teacher_id' => $scenario['teacher']->id,
            'period_id' => $scenario['period']->id,
            'status' => 'generated',
            'score' => 100,
        ]);

        $response = $this->delete(route('teachers.destroy', $scenario['teacher']));

        $response
            ->assertRedirect(route('teachers.index'))
            ->assertSessionHas('error', 'Não é possível remover este professor porque ele possui histórico de alocações. Inative o professor para removê-lo de novas alocações.');

        $this->assertDatabaseHas('teachers', [
            'id' => $scenario['teacher']->id,
            'is_active' => true,
        ]);
    }

    public function test_teacher_with_conflict_suggestion_history_cannot_be_deleted(): void
    {
        $scenario = $this->createTeacherScenario();

        $allocationRun = AllocationRun::query()->create([
            'status' => 'completed_with_conflicts',
            'score' => 0,
            'total_allocations' => 0,
            'total_conflicts' => 1,
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        $conflict = AllocationConflict::query()->create([
            'allocation_run_id' => $allocationRun->id,
            'school_class_id' => $scenario['schoolClass']->id,
            'period_id' => $scenario['period']->id,
            'reason_code' => AllocationConflictCode::NO_VALID_CANDIDATE,
            'reason_description' => AllocationConflictCode::NO_VALID_CANDIDATE->message(),
            'status' => 'open',
        ]);

        ConflictSuggestion::query()->create([
            'allocation_conflict_id' => $conflict->id,
            'teacher_id' => $scenario['teacher']->id,
            'suggestion_score' => 80,
            'reason' => 'Sugestão histórica.',
        ]);

        $response = $this->delete(route('teachers.destroy', $scenario['teacher']));

        $response
            ->assertRedirect(route('teachers.index'))
            ->assertSessionHas('error', 'Não é possível remover este professor porque ele possui histórico de alocações. Inative o professor para removê-lo de novas alocações.');

        $this->assertDatabaseHas('teachers', [
            'id' => $scenario['teacher']->id,
            'is_active' => true,
        ]);
    }

    public function test_teacher_can_be_deactivated(): void
    {
        $scenario = $this->createTeacherScenario();

        $response = $this->patch(route('teachers.deactivate', $scenario['teacher']));

        $response
            ->assertRedirect(route('teachers.index'))
            ->assertSessionHas('success', 'Professor inativado com sucesso. Ele não será considerado em novas alocações.');

        $this->assertDatabaseHas('teachers', [
            'id' => $scenario['teacher']->id,
            'is_active' => false,
        ]);
    }

    public function test_teacher_without_history_can_be_deleted(): void
    {
        $scenario = $this->createTeacherScenario();

        $response = $this->delete(route('teachers.destroy', $scenario['teacher']));

        $response
            ->assertRedirect(route('teachers.index'))
            ->assertSessionHas('success', 'Professor removido com sucesso.');

        $this->assertDatabaseMissing('teachers', [
            'id' => $scenario['teacher']->id,
        ]);
    }

    private function createTeacherScenario(): array
    {
        $schoolUnit = SchoolUnit::query()->create([
            'name' => 'SENAI Teste',
        ]);

        $specialty = Specialty::query()->create([
            'name' => 'Automação',
            'description' => 'Automação industrial.',
        ]);

        $subject = Subject::query()->create([
            'name' => 'CLP Industrial',
            'specialty_id' => $specialty->id,
        ]);

        $schoolClass = SchoolClass::query()->create([
            'name' => 'Automação 01',
            'school_unit_id' => $schoolUnit->id,
            'subject_id' => $subject->id,
            'required_periods' => 1,
            'is_active' => true,
        ]);

        $period = Period::query()->create([
            'code' => 'P1',
            'weekday' => 1,
            'shift' => 'morning',
            'description' => 'Segunda-feira de manhã',
            'sort_order' => 1,
        ]);

        $teacher = Teacher::query()->create([
            'registration' => 'TEST-001',
            'name' => 'Professor Teste',
            'email' => 'professor@example.com',
            'school_unit_id' => $schoolUnit->id,
            'max_weekly_periods' => 15,
            'max_daily_periods' => 2,
            'is_active' => true,
        ]);

        return compact('schoolUnit', 'specialty', 'subject', 'schoolClass', 'period', 'teacher');
    }
}
