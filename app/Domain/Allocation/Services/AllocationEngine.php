<?php

namespace App\Domain\Allocation\Services;

use App\Domain\Allocation\DTOs\AllocationEngineResult;
use App\Domain\Allocation\DTOs\UnresolvedAllocation;
use App\Models\Allocation;
use App\Models\AllocationRun;
use App\Models\Period;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final readonly class AllocationEngine
{
    public function __construct(
        private CandidateFinder $candidateFinder,
        private AllocationScorer $allocationScorer,
    ) {}

    public function execute(
        AllocationRun $allocationRun
    ): AllocationEngineResult {
        $allocationsCreated = 0;
        $requestedAllocations = 0;
        $unresolved = [];

        $periods = $this->periods();
        $schoolClasses = $this->schoolClassesOrderedByDifficulty();

        foreach ($schoolClasses as $schoolClass) {
            $requiredPeriods = (int) $schoolClass->required_periods;

            $requestedAllocations += $requiredPeriods;

            $classAllocations = $this->allocateSchoolClass(
                allocationRun: $allocationRun,
                schoolClass: $schoolClass,
                periods: $periods,
                requiredPeriods: $requiredPeriods,
                unresolved: $unresolved,
            );

            $allocationsCreated += $classAllocations;
        }

        return new AllocationEngineResult(
            allocationsCreated: $allocationsCreated,
            requestedAllocations: $requestedAllocations,
            unresolved: $unresolved,
        );
    }

    /**
     * @param Collection<int, Period> $periods
     * @param array<int, UnresolvedAllocation> $unresolved
     */
    private function allocateSchoolClass(
        AllocationRun $allocationRun,
        SchoolClass $schoolClass,
        Collection $periods,
        int $requiredPeriods,
        array &$unresolved,
    ): int {
        $allocationsCreated = 0;

        foreach ($periods as $period) {
            if ($allocationsCreated >= $requiredPeriods) {
                break;
            }

            $candidates = $this->candidateFinder->find(
                $schoolClass,
                $period,
                $allocationRun,
            );

            if ($candidates->isEmpty()) {
                continue;
            }

            $rankedCandidates = $this->allocationScorer->rank(
                $candidates,
                $schoolClass,
                $period,
                $allocationRun,
            );

            $bestCandidate = $rankedCandidates->first();

            if ($bestCandidate === null) {
                continue;
            }

            Allocation::query()->create([
                'allocation_run_id' => $allocationRun->id,
                'school_class_id' => $schoolClass->id,
                'teacher_id' => $bestCandidate->teacher->id,
                'period_id' => $period->id,
                'status' => 'generated',
                'score' => $bestCandidate->score,
            ]);

            $allocationsCreated++;
        }

        $missingPeriods = $requiredPeriods - $allocationsCreated;

        if ($missingPeriods > 0)
            $this->registerMissingPeriods(
                allocationRun: $allocationRun,
                schoolClass: $schoolClass,
                periods: $periods,
                missingPeriods: $missingPeriods,
                unresolved: $unresolved,
            );


        return $allocationsCreated;
    }

    /**
     * @param Collection<int, Period> $periods
     * @param array<int, UnresolvedAllocation> $unresolved
     */
    private function registerMissingPeriods(
        AllocationRun $allocationRun,
        SchoolClass $schoolClass,
        Collection $periods,
        int $missingPeriods,
        array &$unresolved,
    ): void {
        $usedPeriodIds = Allocation::query()
            ->where('allocation_run_id', $allocationRun->id)
            ->where('school_class_id', $schoolClass->id)
            ->pluck('period_id');

        $availablePeriods = $periods
            ->reject(
                fn(Period $period): bool =>
                $usedPeriodIds->contains($period->id)
            )
            ->take($missingPeriods);

        foreach ($availablePeriods as $period) {
            $unresolved[] = new UnresolvedAllocation(
                schoolClass: $schoolClass,
                period: $period,
                reason: 'Nenhum professor válido foi encontrado para o período.',
            );
        }

        $remainingConflicts =
            $missingPeriods - $availablePeriods->count();

        $fallbackPeriod = $periods->first();

        while (
            $remainingConflicts > 0
            && $fallbackPeriod !== null
        ) {
            $unresolved[] = new UnresolvedAllocation(
                schoolClass: $schoolClass,
                period: $fallbackPeriod,
                reason: 'Não foi possível completar a carga exigida pela turma.',
            );

            $remainingConflicts--;
        }
    }

    /**
     * @return Collection<int, Period>
     */
    private function periods(): Collection
    {
        return Period::query()
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * @return Collection<int, SchoolClass>
     */
    private function schoolClassesOrderedByDifficulty(): Collection
    {
        return SchoolClass::query()
            ->where('is_active', true)
            ->with([
                'subject.specialty.teachers' => fn($query) =>
                $query->where('teachers.is_active', true),
            ])
            ->get()
            ->sort(function (
                SchoolClass $first,
                SchoolClass $second
            ): int {
                $requiredComparison =
                    $second->required_periods
                    <=> $first->required_periods;

                if ($requiredComparison !== 0)
                    return $requiredComparison;


                $firstCandidates =
                    $first->subject?->specialty?->teachers->count() ?? 0;

                $secondCandidates =
                    $second->subject?->specialty?->teachers->count() ?? 0;

                $candidateComparison =
                    $firstCandidates <=> $secondCandidates;

                if ($candidateComparison !== 0)
                    return $candidateComparison;


                return $first->id <=> $second->id;
            })
            ->values();
    }
}
