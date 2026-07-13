<?php

namespace App\Domain\Allocation\Services;

use App\Domain\Allocation\Enums\AllocationConflictCode;
use App\Domain\Allocation\ValueObjects\AllocationValidationResult;
use App\Models\Allocation;
use App\Models\AllocationRun;
use App\Models\Period;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Builder;

final class AllocationValidator
{
    public function validate(
        Teacher $teacher,
        SchoolClass $schoolClass,
        Period $period,
        ?AllocationRun $allocationRun = null
    ): AllocationValidationResult {
        $violations = [];

        if (!$teacher->is_active) {
            $violations[] = AllocationConflictCode::INACTIVE_TEACHER;
        }

        if (!$schoolClass->is_active) {
            $violations[] = AllocationConflictCode::INACTIVE_SCHOOL_CLASS;
        }

        if (!$this->hasCompatibleSpecialty($teacher, $schoolClass)) {
            $violations[] = AllocationConflictCode::INCOMPATIBLE_SPECIALTY;
        }

        if (!$this->isAvailable($teacher, $period)) {
            $violations[] = AllocationConflictCode::TEACHER_UNAVAILABLE;
        }

        if ($allocationRun !== null) {
            $this->validateRunConstraints(
                $teacher,
                $schoolClass,
                $period,
                $allocationRun,
                $violations
            );
        }

        if ($violations === []) {
            return AllocationValidationResult::valid();
        }

        return AllocationValidationResult::invalid($violations);
    }

    private function hasCompatibleSpecialty(
        Teacher $teacher,
        SchoolClass $schoolClass
    ): bool {
        $schoolClass->loadMissing('subject');

        $specialtyId = $schoolClass->subject?->specialty_id;

        if ($specialtyId === null) {
            return false;
        }

        return $teacher->specialties()
            ->whereKey($specialtyId)
            ->exists();
    }

    private function isAvailable(
        Teacher $teacher,
        Period $period
    ): bool {
        return $teacher->availabilities()
            ->where('period_id', '=', $period->id)
            ->where('is_available', '=', true)
            ->exists();
    }

    private function validateRunConstraints(
        Teacher $teacher,
        SchoolClass $schoolClass,
        Period $period,
        AllocationRun $allocationRun,
        array &$violations
    ): void {
        if ($this->teacherAlreadyAllocated(
            $teacher,
            $period,
            $allocationRun
        )) {
            $violations[] = AllocationConflictCode::TEACHER_ALREADY_ALLOCATED;
        }

        if ($this->schoolClassAlreadyAllocated(
            $schoolClass,
            $period,
            $allocationRun
        )) {
            $violations[] = AllocationConflictCode::SCHOOL_CLASS_ALREADY_ALLOCATED;
        }

        if ($this->weeklyLimitReached(
            $teacher,
            $allocationRun
        )) {
            $violations[] = AllocationConflictCode::TEACHER_WEEKLY_LIMIT_EXCEEDED;
        }

        if ($this->dailyLimitReached(
            $teacher,
            $period,
            $allocationRun
        )) {
            $violations[] = AllocationConflictCode::TEACHER_DAILY_LIMIT_EXCEEDED;
        }
    }

    private function teacherAlreadyAllocated(
        Teacher $teacher,
        Period $period,
        AllocationRun $allocationRun
    ): bool {
        return Allocation::query()
            ->where('allocation_run_id', '=', $allocationRun->id)
            ->where('teacher_id', '=', $teacher->id)
            ->where('period_id', '=', $period->id)
            ->exists();
    }

    private function schoolClassAlreadyAllocated(
        SchoolClass $schoolClass,
        Period $period,
        AllocationRun $allocationRun
    ): bool {
        return Allocation::query()
            ->where('allocation_run_id', '=', $allocationRun->id)
            ->where('school_class_id', '=', $schoolClass->id)
            ->where('period_id', '=', $period->id)
            ->exists();
    }

    private function weeklyLimitReached(
        Teacher $teacher,
        AllocationRun $allocationRun
    ): bool {
        $allocatedPeriods = Allocation::query()
            ->where('allocation_run_id', '=', $allocationRun->id)
            ->where('teacher_id', '=', $teacher->id)
            ->count();

        return $allocatedPeriods >= $teacher->max_weekly_periods;
    }

    private function dailyLimitReached(
        Teacher $teacher,
        Period $period,
        AllocationRun $allocationRun
    ): bool {
        $allocatedPeriodsOnDay = Allocation::query()
            ->where('allocation_run_id', '=', $allocationRun->id)
            ->where('teacher_id', '=', $teacher->id)
            ->whereHas(
                'period',
                fn(Builder $query): Builder =>
                $query->where('weekday', '=', $period->weekday)
            )
            ->count();

        return $allocatedPeriodsOnDay >= $teacher->max_daily_periods;
    }
}
