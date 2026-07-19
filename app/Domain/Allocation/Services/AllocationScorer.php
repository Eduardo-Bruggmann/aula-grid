<?php

namespace App\Domain\Allocation\Services;

use App\Domain\Allocation\DTOs\ScoredCandidate;
use App\Models\Allocation;
use App\Models\AllocationRun;
use App\Models\Period;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class AllocationScorer
{
    private const ADHERENCE_WEIGHT = 0.50;
    private const WEEKLY_CAPACITY_WEIGHT = 0.30;
    private const DAILY_CAPACITY_WEIGHT = 0.15;
    private const SAME_UNIT_WEIGHT = 0.05;

    public function score(
        Teacher $teacher,
        SchoolClass $schoolClass,
        Period $period,
        AllocationRun $allocationRun,
    ): ScoredCandidate {
        $adherenceScore = $this->adherenceScore(
            $teacher,
            $schoolClass
        );

        $weeklyCapacityScore = $this->weeklyCapacityScore(
            $teacher,
            $allocationRun
        );

        $dailyCapacityScore = $this->dailyCapacityScore(
            $teacher,
            $period,
            $allocationRun
        );

        $sameUnitScore = $this->sameUnitScore(
            $teacher,
            $schoolClass
        );

        $score =
            ($adherenceScore * self::ADHERENCE_WEIGHT)
            + ($weeklyCapacityScore * self::WEEKLY_CAPACITY_WEIGHT)
            + ($dailyCapacityScore * self::DAILY_CAPACITY_WEIGHT)
            + ($sameUnitScore * self::SAME_UNIT_WEIGHT);

        return new ScoredCandidate(
            teacher: $teacher,
            score: round($score, 2),
            criteria: [
                'adherence' => $adherenceScore,
                'weekly_capacity' => $weeklyCapacityScore,
                'daily_capacity' => $dailyCapacityScore,
                'same_unit' => $sameUnitScore,
            ],
        );
    }

    /**
     * @param Collection<int, Teacher> $teachers
     * @return Collection<int, ScoredCandidate>
     */
    public function rank(
        Collection $teachers,
        SchoolClass $schoolClass,
        Period $period,
        AllocationRun $allocationRun,
    ): Collection {
        return $teachers
            ->map(
                fn(Teacher $teacher): ScoredCandidate =>
                $this->score(
                    $teacher,
                    $schoolClass,
                    $period,
                    $allocationRun
                )
            )
            ->sort(function (
                ScoredCandidate $first,
                ScoredCandidate $second
            ): int {
                $scoreComparison = $second->score <=> $first->score;

                if ($scoreComparison !== 0) {
                    return $scoreComparison;
                }

                return $first->teacher->id
                    <=> $second->teacher->id;
            })
            ->values();
    }

    private function adherenceScore(
        Teacher $teacher,
        SchoolClass $schoolClass,
    ): float {
        $schoolClass->loadMissing('subject');

        $specialtyId = $schoolClass->subject?->specialty_id;

        if ($specialtyId === null) {
            return 0;
        }

        $specialty = $teacher->specialties()
            ->whereKey($specialtyId)
            ->first();

        if ($specialty === null) {
            return 0;
        }

        return $this->normalizeScore(
            $specialty->pivot->adherence_score ?? 100
        );
    }

    private function weeklyCapacityScore(
        Teacher $teacher,
        AllocationRun $allocationRun,
    ): float {
        $maximum = (int) $teacher->max_weekly_periods;

        if ($maximum <= 0) {
            return 0;
        }

        $allocated = Allocation::query()
            ->where('allocation_run_id', $allocationRun->id)
            ->where('teacher_id', $teacher->id)
            ->count();

        return $this->remainingCapacityScore(
            allocated: $allocated,
            maximum: $maximum,
        );
    }

    private function dailyCapacityScore(
        Teacher $teacher,
        Period $period,
        AllocationRun $allocationRun,
    ): float {
        $maximum = (int) $teacher->max_daily_periods;

        if ($maximum <= 0) {
            return 0;
        }

        $allocated = Allocation::query()
            ->where('allocation_run_id', $allocationRun->id)
            ->where('teacher_id', $teacher->id)
            ->whereHas(
                'period',
                fn(Builder $query): Builder =>
                $query->where('weekday', $period->weekday)
            )
            ->count();

        return $this->remainingCapacityScore(
            allocated: $allocated,
            maximum: $maximum,
        );
    }

    private function sameUnitScore(
        Teacher $teacher,
        SchoolClass $schoolClass,
    ): float {
        return $teacher->school_unit_id === $schoolClass->school_unit_id
            ? 100
            : 0;
    }

    private function remainingCapacityScore(
        int $allocated,
        int $maximum,
    ): float {
        if ($allocated >= $maximum) {
            return 0;
        }

        $remaining = $maximum - $allocated;

        return round(
            ($remaining / $maximum) * 100,
            2
        );
    }

    private function normalizeScore(
        int|float|string|null $score,
    ): float {
        $score = (float) $score;

        return max(0, min(100, $score));
    }
}
