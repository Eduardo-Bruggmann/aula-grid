<?php

namespace App\Domain\Allocation\Services;

use App\Models\AllocationRun;
use App\Models\Period;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final readonly class CandidateFinder
{
    public function __construct(
        private AllocationValidator $validator,
    ) {}

    /**
     * @return Collection<int, Teacher>
     */
    public function find(
        SchoolClass $schoolClass,
        Period $period,
        AllocationRun $allocationRun,
    ): Collection {
        $schoolClass->loadMissing('subject');

        $specialtyId = $schoolClass->subject?->specialty_id;

        if (
            !$schoolClass->is_active
            || $specialtyId === null
        ) {
            return collect();
        }

        $teachers = Teacher::query()
            ->where('is_active', true)
            ->whereHas(
                'specialties',
                fn(Builder $query): Builder =>
                $query->whereKey($specialtyId)
            )
            ->whereHas(
                'availabilities',
                fn(Builder $query): Builder => $query
                    ->where('period_id', $period->id)
                    ->where('is_available', true)
            )
            ->with([
                'specialties' => fn($query) =>
                $query->whereKey($specialtyId),
            ])
            ->get();

        return $teachers
            ->filter(function (Teacher $teacher) use (
                $schoolClass,
                $period,
                $allocationRun
            ): bool {
                return $this->validator
                    ->validate(
                        $teacher,
                        $schoolClass,
                        $period,
                        $allocationRun
                    )
                    ->isValid();
            })
            ->values();
    }
}
