<?php

namespace App\Domain\Allocation\DTOs;

final readonly class AllocationEngineResult
{
    /**
     * @param array<int, UnresolvedAllocation> $unresolved
     */
    public function __construct(
        public int $allocationsCreated,
        public int $requestedAllocations,
        public array $unresolved,
    ) {}

    public function conflictsCount(): int
    {
        return count($this->unresolved);
    }

    public function coveragePercentage(): float
    {
        if ($this->requestedAllocations === 0)
            return 100;


        return round(
            ($this->allocationsCreated / $this->requestedAllocations) * 100,
            2
        );
    }

    public function hasConflicts(): bool
    {
        return $this->unresolved !== [];
    }
}
