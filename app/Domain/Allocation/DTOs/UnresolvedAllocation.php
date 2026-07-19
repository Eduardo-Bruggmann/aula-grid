<?php

namespace App\Domain\Allocation\DTOs;

use App\Models\Period;
use App\Models\SchoolClass;

final readonly class UnresolvedAllocation
{
    public function __construct(
        public SchoolClass $schoolClass,
        public Period $period,
        public string $reason,
    ) {}
}
