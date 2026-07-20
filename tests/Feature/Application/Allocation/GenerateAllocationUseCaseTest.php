<?php

namespace Tests\Feature\Application\Allocation;

use App\Application\Allocation\UseCases\GenerateAllocationUseCase;
use App\Domain\Allocation\Services\AllocationEngine;
use App\Domain\Allocation\Services\AllocationScorer;
use App\Domain\Allocation\Services\AllocationValidator;
use App\Domain\Allocation\Services\CandidateFinder;
use App\Models\AllocationRun;
use App\Models\Period;
use App\Models\SchoolClass;
use App\Models\SchoolUnit;
use App\Models\Specialty;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherAvailability;
use App\Models\TeacherSpecialty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateAllocationUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private GenerateAllocationUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $validator = new AllocationValidator();

        $candidateFinder = new CandidateFinder(
            $validator
        );

        $engine = new AllocationEngine(
            $candidateFinder,
            new AllocationScorer(),
        );

        $this->useCase = new GenerateAllocationUseCase(
            $engine
        );
    }

    public function test_it_generates_a_complete_allocation_run(): void
    {
        $scenario = $this->createScenario(
            teacherAvailable: true
        );

        $result = $this->useCase->execute();

        $this->assertSame(1, $result->allocationsCreated);
        $this->assertSame(0, $result->conflictsCreated);
        $this->assertSame(1, $result->requestedAllocations);
        $this->assertSame(100.0, $result->coveragePercentage);
        $this->assertTrue($result->wasFullyAllocated());

        $this->assertDatabaseHas('allocation_runs', [
            'id' => $result->allocationRun->id,
            'status' => 'completed',
            'total_allocations' => 1,
            'total_conflicts' => 0,
        ]);

        $this->assertDatabaseHas('allocations', [
            'allocation_run_id' => $result->allocationRun->id,
            'school_class_id' => $scenario['schoolClass']->id,
            'teacher_id' => $scenario['teacher']->id,
            'period_id' => $scenario['period']->id,
        ]);

        $this->assertDatabaseCount('allocation_conflicts', 0);
    }

    public function test_it_persists_conflicts_when_allocation_is_incomplete(): void
    {
        $scenario = $this->createScenario(
            teacherAvailable: false
        );

        $result = $this->useCase->execute();

        $this->assertSame(0, $result->allocationsCreated);
        $this->assertSame(1, $result->conflictsCreated);
        $this->assertSame(0.0, $result->coveragePercentage);
        $this->assertFalse($result->wasFullyAllocated());

        $this->assertDatabaseHas('allocation_runs', [
            'id' => $result->allocationRun->id,
            'status' => 'completed_with_conflicts',
            'total_allocations' => 0,
            'total_conflicts' => 1,
        ]);

        $this->assertDatabaseHas('allocation_conflicts', [
            'allocation_run_id' => $result->allocationRun->id,
            'school_class_id' => $scenario['schoolClass']->id,
            'period_id' => $scenario['period']->id,
            'reason_code' => 'NO_VALID_CANDIDATE',
        ]);

        $this->assertDatabaseCount('allocations', 0);
    }

    public function test_each_execution_creates_a_separate_run(): void
    {
        $this->createScenario(
            teacherAvailable: true
        );

        $firstResult = $this->useCase->execute();
        $secondResult = $this->useCase->execute();

        $this->assertNotSame(
            $firstResult->allocationRun->id,
            $secondResult->allocationRun->id
        );

        $this->assertDatabaseCount('allocation_runs', 2);
        $this->assertDatabaseCount('allocations', 2);
    }

    private function createScenario(
        bool $teacherAvailable,
    ): array {
        $schoolUnit = SchoolUnit::query()->create([
            'name' => 'SENAI Teste',
        ]);

        $specialty = Specialty::query()->create([
            'name' => 'Automação',
            'description' => 'Automação industrial.',
        ]);

        $subject = Subject::query()->create([
            'name' => 'CLP',
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
            'max_weekly_periods' => 7,
            'max_daily_periods' => 2,
            'is_active' => true,
        ]);

        TeacherSpecialty::query()->create([
            'teacher_id' => $teacher->id,
            'specialty_id' => $specialty->id,
            'adherence_score' => 100,
        ]);

        TeacherAvailability::query()->create([
            'teacher_id' => $teacher->id,
            'period_id' => $period->id,
            'is_available' => $teacherAvailable,
        ]);

        return compact(
            'schoolUnit',
            'specialty',
            'subject',
            'schoolClass',
            'period',
            'teacher',
        );
    }
}
