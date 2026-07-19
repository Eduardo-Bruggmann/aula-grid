<?php

namespace Tests\Feature\Domain\Allocation;

use App\Domain\Allocation\Services\AllocationEngine;
use App\Domain\Allocation\Services\AllocationScorer;
use App\Domain\Allocation\Services\AllocationValidator;
use App\Domain\Allocation\Services\CandidateFinder;
use App\Models\Allocation;
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

class AllocationEngineTest extends TestCase
{
    use RefreshDatabase;

    private AllocationEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();

        $validator = new AllocationValidator();

        $candidateFinder = new CandidateFinder($validator);

        $this->engine = new AllocationEngine(
            $candidateFinder,
            new AllocationScorer(),
        );
    }

    public function test_it_allocates_a_teacher_to_a_class(): void
    {
        $scenario = $this->createScenario(
            requiredPeriods: 1
        );

        $result = $this->engine->execute(
            $scenario['allocationRun']
        );

        $this->assertSame(1, $result->allocationsCreated);
        $this->assertSame(1, $result->requestedAllocations);
        $this->assertSame(100.0, $result->coveragePercentage());
        $this->assertFalse($result->hasConflicts());

        $this->assertDatabaseHas('allocations', [
            'allocation_run_id' => $scenario['allocationRun']->id,
            'school_class_id' => $scenario['schoolClass']->id,
            'teacher_id' => $scenario['teacher']->id,
            'period_id' => $scenario['period']->id,
        ]);
    }

    public function test_it_selects_the_highest_ranked_candidate(): void
    {
        $scenario = $this->createScenario(
            requiredPeriods: 1,
            adherenceScore: 40,
        );

        $betterTeacher = $this->createTeacher(
            schoolUnit: $scenario['schoolUnit'],
            specialty: $scenario['specialty'],
            registration: 'BETTER-001',
            adherenceScore: 100,
        );

        $this->createAvailability(
            teacher: $betterTeacher,
            period: $scenario['period'],
        );

        $result = $this->engine->execute(
            $scenario['allocationRun']
        );

        $this->assertSame(1, $result->allocationsCreated);

        $allocation = Allocation::query()->firstOrFail();

        $this->assertTrue(
            $allocation->teacher->is($betterTeacher)
        );
    }

    public function test_it_respects_the_teacher_weekly_limit(): void
    {
        $scenario = $this->createScenario(
            requiredPeriods: 2,
            maximumWeeklyPeriods: 1,
        );

        $secondPeriod = $this->createPeriod(
            code: 'P2',
            weekday: 2,
            shift: 'morning',
            sortOrder: 2,
        );

        $this->createAvailability(
            teacher: $scenario['teacher'],
            period: $secondPeriod,
        );

        $result = $this->engine->execute(
            $scenario['allocationRun']
        );

        $this->assertSame(1, $result->allocationsCreated);
        $this->assertSame(2, $result->requestedAllocations);
        $this->assertSame(50.0, $result->coveragePercentage());
        $this->assertTrue($result->hasConflicts());

        $this->assertDatabaseCount('allocations', 1);
    }

    public function test_it_does_not_allocate_one_teacher_to_two_classes_in_the_same_period(): void
    {
        $scenario = $this->createScenario(
            requiredPeriods: 1
        );

        $secondClass = SchoolClass::query()->create([
            'name' => 'Automação 02',
            'school_unit_id' => $scenario['schoolUnit']->id,
            'subject_id' => $scenario['subject']->id,
            'required_periods' => 1,
            'is_active' => true,
        ]);

        $result = $this->engine->execute(
            $scenario['allocationRun']
        );

        $this->assertSame(1, $result->allocationsCreated);
        $this->assertSame(2, $result->requestedAllocations);
        $this->assertSame(1, $result->conflictsCount());

        $this->assertDatabaseCount('allocations', 1);

        $teacherAllocations = Allocation::query()
            ->where('teacher_id', $scenario['teacher']->id)
            ->where('period_id', $scenario['period']->id)
            ->count();

        $this->assertSame(1, $teacherAllocations);

        $this->assertTrue(
            collect([
                $scenario['schoolClass']->id,
                $secondClass->id,
            ])->contains(
                Allocation::query()->firstOrFail()->school_class_id
            )
        );
    }

    public function test_it_returns_conflicts_when_no_candidate_exists(): void
    {
        $scenario = $this->createScenario(
            requiredPeriods: 1,
            teacherAvailable: false,
        );

        $result = $this->engine->execute(
            $scenario['allocationRun']
        );

        $this->assertSame(0, $result->allocationsCreated);
        $this->assertSame(1, $result->requestedAllocations);
        $this->assertSame(1, $result->conflictsCount());
        $this->assertSame(0.0, $result->coveragePercentage());

        $this->assertDatabaseCount('allocations', 0);

        $this->assertSame(
            $scenario['schoolClass']->id,
            $result->unresolved[0]->schoolClass->id
        );
    }

    private function createScenario(
        int $requiredPeriods,
        int $adherenceScore = 100,
        int $maximumWeeklyPeriods = 7,
        bool $teacherAvailable = true,
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
            'required_periods' => $requiredPeriods,
            'is_active' => true,
        ]);

        $period = $this->createPeriod(
            code: 'P1',
            weekday: 1,
            shift: 'morning',
            sortOrder: 1,
        );

        $teacher = $this->createTeacher(
            schoolUnit: $schoolUnit,
            specialty: $specialty,
            registration: 'TEST-001',
            adherenceScore: $adherenceScore,
            maximumWeeklyPeriods: $maximumWeeklyPeriods,
        );

        TeacherAvailability::query()->create([
            'teacher_id' => $teacher->id,
            'period_id' => $period->id,
            'is_available' => $teacherAvailable,
        ]);

        $allocationRun = AllocationRun::query()->create([
            'status' => 'running',
            'score' => 0,
            'total_allocations' => 0,
            'total_conflicts' => 0,
            'started_at' => now(),
        ]);

        return compact(
            'schoolUnit',
            'specialty',
            'subject',
            'schoolClass',
            'period',
            'teacher',
            'allocationRun',
        );
    }

    private function createTeacher(
        SchoolUnit $schoolUnit,
        Specialty $specialty,
        string $registration,
        int $adherenceScore,
        int $maximumWeeklyPeriods = 7,
    ): Teacher {
        $teacher = Teacher::query()->create([
            'registration' => $registration,
            'name' => "Professor {$registration}",
            'email' => strtolower($registration) . '@example.com',
            'school_unit_id' => $schoolUnit->id,
            'max_weekly_periods' => $maximumWeeklyPeriods,
            'max_daily_periods' => 2,
            'is_active' => true,
        ]);

        TeacherSpecialty::query()->create([
            'teacher_id' => $teacher->id,
            'specialty_id' => $specialty->id,
            'adherence_score' => $adherenceScore,
        ]);

        return $teacher;
    }

    private function createAvailability(
        Teacher $teacher,
        Period $period,
    ): void {
        TeacherAvailability::query()->create([
            'teacher_id' => $teacher->id,
            'period_id' => $period->id,
            'is_available' => true,
        ]);
    }

    private function createPeriod(
        string $code,
        int $weekday,
        string $shift,
        int $sortOrder,
    ): Period {
        return Period::query()->create([
            'code' => $code,
            'weekday' => $weekday,
            'shift' => $shift,
            'description' => "{$code} - período de teste",
            'sort_order' => $sortOrder,
        ]);
    }
}
