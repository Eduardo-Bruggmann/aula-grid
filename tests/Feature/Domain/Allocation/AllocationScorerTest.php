<?php

namespace Tests\Feature\Domain\Allocation;

use App\Domain\Allocation\Services\AllocationScorer;
use App\Models\Allocation;
use App\Models\AllocationRun;
use App\Models\Period;
use App\Models\SchoolClass;
use App\Models\SchoolUnit;
use App\Models\Specialty;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherSpecialty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AllocationScorerTest extends TestCase
{
    use RefreshDatabase;

    private AllocationScorer $scorer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scorer = new AllocationScorer();
    }

    public function test_it_gives_a_higher_score_to_a_teacher_with_more_capacity(): void
    {
        $scenario = $this->createScenario();

        $availableTeacher = $this->createTeacher(
            $scenario['schoolUnit'],
            $scenario['specialty'],
            'FREE-001'
        );

        $loadedTeacher = $this->createTeacher(
            $scenario['schoolUnit'],
            $scenario['specialty'],
            'LOADED-001'
        );

        $allocatedPeriods = [
            $this->createPeriod('P2', 2, 'morning', 2),
            $this->createPeriod('P3', 3, 'morning', 3),
            $this->createPeriod('P4', 4, 'morning', 4),
        ];

        foreach ($allocatedPeriods as $index => $period) {
            $class = SchoolClass::create([
                'name' => "Turma Extra {$index}",
                'school_unit_id' => $scenario['schoolUnit']->id,
                'subject_id' => $scenario['subject']->id,
                'required_periods' => 1,
                'is_active' => true,
            ]);

            Allocation::create([
                'allocation_run_id' => $scenario['allocationRun']->id,
                'school_class_id' => $class->id,
                'teacher_id' => $loadedTeacher->id,
                'period_id' => $period->id,
                'status' => 'generated',
                'score' => 100,
            ]);
        }

        $freeScore = $this->scorer->score(
            $availableTeacher,
            $scenario['schoolClass'],
            $scenario['period'],
            $scenario['allocationRun'],
        );

        $loadedScore = $this->scorer->score(
            $loadedTeacher,
            $scenario['schoolClass'],
            $scenario['period'],
            $scenario['allocationRun'],
        );

        $this->assertGreaterThan(
            $loadedScore->score,
            $freeScore->score
        );
    }

    public function test_it_gives_a_bonus_to_a_teacher_from_the_same_unit(): void
    {
        $scenario = $this->createScenario();

        $sameUnitTeacher = $this->createTeacher(
            $scenario['schoolUnit'],
            $scenario['specialty'],
            'SAME-001'
        );

        $otherUnit = SchoolUnit::create([
            'name' => 'SENAI Outra Unidade',
        ]);

        $otherUnitTeacher = $this->createTeacher(
            $otherUnit,
            $scenario['specialty'],
            'OTHER-001'
        );

        $sameUnitScore = $this->scorer->score(
            $sameUnitTeacher,
            $scenario['schoolClass'],
            $scenario['period'],
            $scenario['allocationRun'],
        );

        $otherUnitScore = $this->scorer->score(
            $otherUnitTeacher,
            $scenario['schoolClass'],
            $scenario['period'],
            $scenario['allocationRun'],
        );

        $this->assertGreaterThan(
            $otherUnitScore->score,
            $sameUnitScore->score
        );
    }

    public function test_it_ranks_candidates_from_highest_to_lowest_score(): void
    {
        $scenario = $this->createScenario();

        $highAdherence = $this->createTeacher(
            $scenario['schoolUnit'],
            $scenario['specialty'],
            'HIGH-001',
            100
        );

        $lowAdherence = $this->createTeacher(
            $scenario['schoolUnit'],
            $scenario['specialty'],
            'LOW-001',
            40
        );

        $ranked = $this->scorer->rank(
            collect([$lowAdherence, $highAdherence]),
            $scenario['schoolClass'],
            $scenario['period'],
            $scenario['allocationRun'],
        );

        $this->assertTrue(
            $ranked->first()->teacher->is($highAdherence)
        );

        $this->assertTrue(
            $ranked->last()->teacher->is($lowAdherence)
        );
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

        $period = $this->createPeriod(
            'P1',
            1,
            'morning',
            1
        );

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
        string $registration,
        int $adherenceScore = 100,
    ): Teacher {
        $teacher = Teacher::create([
            'registration' => $registration,
            'name' => "Professor {$registration}",
            'email' => strtolower($registration) . '@example.com',
            'school_unit_id' => $schoolUnit->id,
            'max_weekly_periods' => 7,
            'max_daily_periods' => 2,
            'is_active' => true,
        ]);

        TeacherSpecialty::create([
            'teacher_id' => $teacher->id,
            'specialty_id' => $specialty->id,
            'adherence_score' => $adherenceScore,
        ]);

        return $teacher;
    }

    private function createPeriod(
        string $code,
        int $weekday,
        string $shift,
        int $sortOrder,
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
