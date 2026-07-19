<?php

namespace Tests\Feature\Domain\Allocation;

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

class CandidateFinderTest extends TestCase
{
    use RefreshDatabase;

    private CandidateFinder $candidateFinder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->candidateFinder = new CandidateFinder(
            new AllocationValidator()
        );
    }

    public function test_it_returns_only_valid_candidates(): void
    {
        $scenario = $this->createScenario();

        $validTeacher = $this->createTeacher(
            schoolUnit: $scenario['schoolUnit'],
            specialty: $scenario['specialty'],
            period: $scenario['period'],
            registration: 'VALID-001',
        );

        $inactiveTeacher = $this->createTeacher(
            schoolUnit: $scenario['schoolUnit'],
            specialty: $scenario['specialty'],
            period: $scenario['period'],
            registration: 'INACTIVE-001',
            isActive: false,
        );

        $unavailableTeacher = $this->createTeacher(
            schoolUnit: $scenario['schoolUnit'],
            specialty: $scenario['specialty'],
            period: $scenario['period'],
            registration: 'UNAVAILABLE-001',
            isAvailable: false,
        );

        $differentSpecialty = Specialty::create([
            'name' => 'Mecânica',
            'description' => 'Especialidade incompatível.',
        ]);

        $incompatibleTeacher = $this->createTeacher(
            schoolUnit: $scenario['schoolUnit'],
            specialty: $differentSpecialty,
            period: $scenario['period'],
            registration: 'INCOMPATIBLE-001',
        );

        $candidates = $this->candidateFinder->find(
            $scenario['schoolClass'],
            $scenario['period'],
            $scenario['allocationRun'],
        );

        $this->assertCount(1, $candidates);

        $this->assertTrue(
            $candidates->contains(
                fn(Teacher $teacher): bool =>
                $teacher->is($validTeacher)
            )
        );

        $this->assertFalse(
            $candidates->contains(
                fn(Teacher $teacher): bool =>
                $teacher->is($inactiveTeacher)
            )
        );

        $this->assertFalse(
            $candidates->contains(
                fn(Teacher $teacher): bool =>
                $teacher->is($unavailableTeacher)
            )
        );

        $this->assertFalse(
            $candidates->contains(
                fn(Teacher $teacher): bool =>
                $teacher->is($incompatibleTeacher)
            )
        );
    }

    public function test_it_excludes_a_teacher_already_allocated_in_the_period(): void
    {
        $scenario = $this->createScenario();

        $teacher = $this->createTeacher(
            schoolUnit: $scenario['schoolUnit'],
            specialty: $scenario['specialty'],
            period: $scenario['period'],
            registration: 'ALLOCATED-001',
        );

        $otherClass = SchoolClass::create([
            'name' => 'Outra Turma',
            'school_unit_id' => $scenario['schoolUnit']->id,
            'subject_id' => $scenario['subject']->id,
            'required_periods' => 5,
            'is_active' => true,
        ]);

        Allocation::create([
            'allocation_run_id' => $scenario['allocationRun']->id,
            'school_class_id' => $otherClass->id,
            'teacher_id' => $teacher->id,
            'period_id' => $scenario['period']->id,
            'status' => 'generated',
            'score' => 100,
        ]);

        $candidates = $this->candidateFinder->find(
            $scenario['schoolClass'],
            $scenario['period'],
            $scenario['allocationRun'],
        );

        $this->assertTrue($candidates->isEmpty());
    }

    public function test_it_returns_an_empty_collection_for_an_inactive_class(): void
    {
        $scenario = $this->createScenario();

        $scenario['schoolClass']->update([
            'is_active' => false,
        ]);

        $candidates = $this->candidateFinder->find(
            $scenario['schoolClass']->fresh(),
            $scenario['period'],
            $scenario['allocationRun'],
        );

        $this->assertTrue($candidates->isEmpty());
    }

    private function createScenario(): array
    {
        $schoolUnit = SchoolUnit::create([
            'name' => 'SENAI Teste',
        ]);

        $specialty = Specialty::create([
            'name' => 'Automação',
            'description' => 'Automação industrial.',
        ]);

        $subject = Subject::create([
            'name' => 'CLP',
            'specialty_id' => $specialty->id,
        ]);

        $schoolClass = SchoolClass::create([
            'name' => 'Automação 01',
            'school_unit_id' => $schoolUnit->id,
            'subject_id' => $subject->id,
            'required_periods' => 5,
            'is_active' => true,
        ]);

        $period = Period::create([
            'code' => 'P1',
            'weekday' => 1,
            'shift' => 'morning',
            'description' => 'Segunda-feira de manhã',
            'sort_order' => 1,
        ]);

        $allocationRun = AllocationRun::create([
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
            'allocationRun',
        );
    }

    private function createTeacher(
        SchoolUnit $schoolUnit,
        Specialty $specialty,
        Period $period,
        string $registration,
        bool $isActive = true,
        bool $isAvailable = true,
    ): Teacher {
        $teacher = Teacher::create([
            'registration' => $registration,
            'name' => "Professor {$registration}",
            'email' => strtolower($registration) . '@example.com',
            'school_unit_id' => $schoolUnit->id,
            'max_weekly_periods' => 7,
            'max_daily_periods' => 2,
            'is_active' => $isActive,
        ]);

        TeacherSpecialty::create([
            'teacher_id' => $teacher->id,
            'specialty_id' => $specialty->id,
            'adherence_score' => 100,
        ]);

        TeacherAvailability::create([
            'teacher_id' => $teacher->id,
            'period_id' => $period->id,
            'is_available' => $isAvailable,
        ]);

        return $teacher;
    }
}
