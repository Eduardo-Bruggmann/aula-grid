<?php

namespace Tests\Feature\Domain\Allocation;

use App\Domain\Allocation\Enums\AllocationConflictCode;
use App\Domain\Allocation\Services\AllocationValidator;
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

class AllocationValidatorTest extends TestCase
{
    use RefreshDatabase;

    private AllocationValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new AllocationValidator();
    }

    public function test_it_accepts_a_valid_allocation(): void
    {
        $scenario = $this->createScenario();

        $result = $this->validator->validate(
            $scenario['teacher'],
            $scenario['schoolClass'],
            $scenario['period'],
            $scenario['allocationRun']
        );

        $this->assertTrue($result->isValid());
        $this->assertSame([], $result->violations());
    }

    public function test_it_rejects_an_inactive_teacher(): void
    {
        $scenario = $this->createScenario();

        $scenario['teacher']->update([
            'is_active' => false,
        ]);

        $result = $this->validator->validate(
            $scenario['teacher']->fresh(),
            $scenario['schoolClass'],
            $scenario['period'],
            $scenario['allocationRun']
        );

        $this->assertTrue($result->isInvalid());

        $this->assertTrue(
            $result->has(AllocationConflictCode::INACTIVE_TEACHER)
        );
    }

    public function test_it_rejects_an_incompatible_specialty(): void
    {
        $scenario = $this->createScenario();

        TeacherSpecialty::query()
            ->where('teacher_id', '=', $scenario['teacher']->id)
            ->delete();

        $result = $this->validator->validate(
            $scenario['teacher'],
            $scenario['schoolClass'],
            $scenario['period'],
            $scenario['allocationRun']
        );

        $this->assertTrue($result->isInvalid());

        $this->assertTrue(
            $result->has(AllocationConflictCode::INCOMPATIBLE_SPECIALTY)
        );
    }

    public function test_it_rejects_an_unavailable_teacher(): void
    {
        $scenario = $this->createScenario();

        TeacherAvailability::query()
            ->where('teacher_id', '=', $scenario['teacher']->id)
            ->where('period_id', '=', $scenario['period']->id)
            ->update([
                'is_available' => false,
            ]);

        $result = $this->validator->validate(
            $scenario['teacher'],
            $scenario['schoolClass'],
            $scenario['period'],
            $scenario['allocationRun']
        );

        $this->assertTrue($result->isInvalid());

        $this->assertTrue(
            $result->has(AllocationConflictCode::TEACHER_UNAVAILABLE)
        );
    }

    public function test_it_rejects_a_teacher_already_allocated_in_the_period(): void
    {
        $scenario = $this->createScenario();

        $otherClass = SchoolClass::create([
            'name' => 'Automação 02',
            'subject_id' => $scenario['subject']->id,
            'school_unit_id' => $scenario['schoolUnit']->id,
            'required_periods' => 7,
            'is_active' => true,
        ]);

        Allocation::create([
            'allocation_run_id' => $scenario['allocationRun']->id,
            'school_class_id' => $otherClass->id,
            'teacher_id' => $scenario['teacher']->id,
            'period_id' => $scenario['period']->id,
            'status' => 'generated',
            'score' => 100,
        ]);

        $result = $this->validator->validate(
            $scenario['teacher'],
            $scenario['schoolClass'],
            $scenario['period'],
            $scenario['allocationRun']
        );

        $this->assertTrue($result->isInvalid());

        $this->assertTrue(
            $result->has(
                AllocationConflictCode::TEACHER_ALREADY_ALLOCATED
            )
        );
    }

    public function test_it_rejects_a_teacher_at_the_weekly_limit(): void
    {
        $scenario = $this->createScenario();

        $scenario['teacher']->update([
            'max_weekly_periods' => 1,
        ]);

        $allocatedPeriod = $this->createPeriod(
            code: 'P2',
            weekday: 2,
            shift: 'morning',
            sortOrder: 2
        );

        Allocation::create([
            'allocation_run_id' => $scenario['allocationRun']->id,
            'school_class_id' => $scenario['schoolClass']->id,
            'teacher_id' => $scenario['teacher']->id,
            'period_id' => $allocatedPeriod->id,
            'status' => 'generated',
            'score' => 100,
        ]);

        $result = $this->validator->validate(
            $scenario['teacher']->fresh(),
            $scenario['schoolClass'],
            $scenario['period'],
            $scenario['allocationRun']
        );

        $this->assertTrue($result->isInvalid());

        $this->assertTrue(
            $result->has(
                AllocationConflictCode::TEACHER_WEEKLY_LIMIT_EXCEEDED
            )
        );
    }

    public function test_it_rejects_a_teacher_at_the_daily_limit(): void
    {
        $scenario = $this->createScenario();

        $morning = $this->createPeriod(
            code: 'P2',
            weekday: 1,
            shift: 'afternoon',
            sortOrder: 2
        );

        $afternoon = $this->createPeriod(
            code: 'P3',
            weekday: 1,
            shift: 'night',
            sortOrder: 3
        );

        Allocation::create([
            'allocation_run_id' => $scenario['allocationRun']->id,
            'school_class_id' => $scenario['schoolClass']->id,
            'teacher_id' => $scenario['teacher']->id,
            'period_id' => $morning->id,
            'status' => 'generated',
            'score' => 100,
        ]);

        $secondClass = SchoolClass::create([
            'name' => 'Automação 02',
            'subject_id' => $scenario['subject']->id,
            'school_unit_id' => $scenario['schoolUnit']->id,
            'required_periods' => 7,
            'is_active' => true,
        ]);

        Allocation::create([
            'allocation_run_id' => $scenario['allocationRun']->id,
            'school_class_id' => $secondClass->id,
            'teacher_id' => $scenario['teacher']->id,
            'period_id' => $afternoon->id,
            'status' => 'generated',
            'score' => 100,
        ]);

        $result = $this->validator->validate(
            $scenario['teacher'],
            $scenario['schoolClass'],
            $scenario['period'],
            $scenario['allocationRun']
        );

        $this->assertTrue($result->isInvalid());

        $this->assertTrue(
            $result->has(
                AllocationConflictCode::TEACHER_DAILY_LIMIT_EXCEEDED
            )
        );
    }

    private function createScenario(): array
    {
        $schoolUnit = SchoolUnit::create([
            'name' => 'SENAI Teste',
        ]);

        $specialty = Specialty::create([
            'name' => 'Automação Industrial',
            'description' => 'Especialidade usada nos testes.',
        ]);

        $subject = Subject::create([
            'name' => 'CLP Industrial',
            'specialty_id' => $specialty->id,
        ]);

        $teacher = Teacher::create([
            'registration' => 'TEST-001',
            'name' => 'Professor Teste',
            'email' => 'professor.teste@example.com',
            'school_unit_id' => $schoolUnit->id,
            'max_weekly_periods' => 7,
            'max_daily_periods' => 2,
            'is_active' => true,
        ]);

        TeacherSpecialty::create([
            'teacher_id' => $teacher->id,
            'specialty_id' => $specialty->id,
            'adherence_score' => 100,
        ]);

        $schoolClass = SchoolClass::create([
            'name' => 'Automação 01',
            'subject_id' => $subject->id,
            'school_unit_id' => $schoolUnit->id,
            'required_periods' => 7,
            'is_active' => true,
        ]);

        $period = $this->createPeriod(
            code: 'P1',
            weekday: 1,
            shift: 'morning',
            sortOrder: 1
        );

        TeacherAvailability::create([
            'teacher_id' => $teacher->id,
            'period_id' => $period->id,
            'is_available' => true,
        ]);

        $allocationRun = AllocationRun::create([
            'status' => 'running',
            'score' => 0,
            'total_allocations' => 0,
            'total_conflicts' => 0,
            'started_at' => now(),
        ]);

        return [
            'schoolUnit' => $schoolUnit,
            'specialty' => $specialty,
            'subject' => $subject,
            'teacher' => $teacher,
            'schoolClass' => $schoolClass,
            'period' => $period,
            'allocationRun' => $allocationRun,
        ];
    }

    private function createPeriod(
        string $code,
        int $weekday,
        string $shift,
        int $sortOrder
    ): Period {
        return Period::create([
            'code' => $code,
            'weekday' => $weekday,
            'shift' => $shift,
            'description' => "{$code} - período de teste",
            'sort_order' => $sortOrder,
        ]);
    }
}
