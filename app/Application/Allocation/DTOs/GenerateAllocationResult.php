<?php

namespace App\Application\Allocation\DTOs;

use App\Models\AllocationRun;

final readonly class GenerateAllocationResult
{
    public function __construct(
        public AllocationRun $allocationRun,
        public int $allocationsCreated,
        public int $conflictsCreated,
        public int $requestedAllocations,
        public float $coveragePercentage,
    ) {}

    public function wasFullyAllocated(): bool
    {
        return $this->conflictsCreated === 0;
    }
}
