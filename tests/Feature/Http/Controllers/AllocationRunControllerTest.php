<?php

namespace Tests\Feature\Http\Controllers;

use App\Domain\Allocation\Enums\AllocationConflictCode;
use App\Models\Allocation;
use App\Models\AllocationConflict;
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

class AllocationRunControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_the_allocation_runs_index(): void
    {
        $allocationRun = AllocationRun::query()->create([
            'status' => 'completed',
            'score' => 100,
            'total_allocations' => 1,
            'total_conflicts' => 0,
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        $response = $this->get(
            route('allocation-runs.index')
        );

        $response
            ->assertOk()
            ->assertViewIs('allocation-runs.index')
            ->assertViewHas('allocationRuns')
            ->assertSee("#{$allocationRun->id}")
            ->assertSee('1 de 1 períodos');
    }

    public function test_it_displays_an_allocation_run_result(): void
    {
        $scenario = $this->createCompleteScenario();

        $response = $this->get(
            route(
                'allocation-runs.show',
                $scenario['allocationRun']
            )
        );

        $response
            ->assertOk()
            ->assertViewIs('allocation-runs.show')
            ->assertViewHas('allocationRun')
            ->assertSee('Professor Teste')
            ->assertSee('Automação 01')
            ->assertSee('P1')
            ->assertSee('1 de 1 períodos preenchidos');
    }

    public function test_it_generates_an_allocation_and_redirects_to_the_result(): void
    {
        $this->createGenerationScenario();

        $response = $this->post(
            route('allocation-runs.store')
        );

        $allocationRun = AllocationRun::query()
            ->latest('id')
            ->firstOrFail();

        $response
            ->assertRedirect(
                route(
                    'allocation-runs.show',
                    $allocationRun
                )
            )
            ->assertSessionHas('success');

        $this->assertDatabaseCount(
            'allocation_runs',
            1
        );

        $this->assertDatabaseCount(
            'allocations',
            1
        );
    }

    public function test_it_displays_conflicts_in_the_result(): void
    {
        $scenario = $this->createCompleteScenario(
            withConflict: true
        );

        $response = $this->get(
            route(
                'allocation-runs.show',
                $scenario['allocationRun']
            )
        );

        $response
            ->assertOk()
            ->assertViewIs('allocation-runs.show')
            ->assertViewHas('allocationRun')
            ->assertDontSee(
                AllocationConflictCode::NO_VALID_CANDIDATE->value
            )
            ->assertSee(
                'Nenhum professor válido foi encontrado.'
            );
    }

    public function test_failed_run_without_useful_result_has_no_view_link(): void
    {
        $allocationRun = AllocationRun::query()->create([
            'status' => 'failed',
            'score' => 0,
            'total_allocations' => 0,
            'total_conflicts' => 0,
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        $response = $this->get(route('allocation-runs.index'));

        $response
            ->assertOk()
            ->assertSee('Falhou')
            ->assertSee('0 de 0 períodos')
            ->assertSee('Sem resultado')
            ->assertDontSee(
                route('allocation-runs.show', $allocationRun),
                false
            );
    }

    public function test_generation_forms_expose_the_confirmation_message(): void
    {
        $confirmationMessage = 'Deseja gerar uma nova alocação? Uma nova execução será registrada.';

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-allocation-form', false)
            ->assertSee($confirmationMessage);

        $allocationRun = AllocationRun::query()->create([
            'status' => 'completed',
            'score' => 100,
            'total_allocations' => 0,
            'total_conflicts' => 0,
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        foreach ([
            route('allocation-runs.index'),
            route('allocation-runs.show', $allocationRun),
        ] as $url) {
            $response = $this->get($url);

            $response
                ->assertOk()
                ->assertSee('data-allocation-form', false)
                ->assertSee($confirmationMessage);
        }
    }

    private function createGenerationScenario(): array
    {
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
            'is_available' => true,
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

    private function createCompleteScenario(
        bool $withConflict = false
    ): array {
        $scenario = $this->createGenerationScenario();

        $allocationRun = AllocationRun::query()->create([
            'status' => $withConflict
                ? 'completed_with_conflicts'
                : 'completed',
            'score' => $withConflict ? 0 : 100,
            'total_allocations' => $withConflict ? 0 : 1,
            'total_conflicts' => $withConflict ? 1 : 0,
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        if (! $withConflict) {
            Allocation::query()->create([
                'allocation_run_id' => $allocationRun->id,
                'school_class_id' => $scenario['schoolClass']->id,
                'teacher_id' => $scenario['teacher']->id,
                'period_id' => $scenario['period']->id,
                'status' => 'generated',
                'score' => 100,
            ]);
        }

        if ($withConflict) {
            AllocationConflict::query()->create([
                'allocation_run_id' => $allocationRun->id,
                'school_class_id' => $scenario['schoolClass']->id,
                'period_id' => $scenario['period']->id,
                'reason_code' => AllocationConflictCode::NO_VALID_CANDIDATE->value,
                'reason_description' => 'Nenhum professor válido foi encontrado.',
            ]);
        }

        return [
            ...$scenario,
            'allocationRun' => $allocationRun,
        ];
    }
}
