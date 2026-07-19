<?php

namespace App\Domain\Allocation\DTOs;

use App\Models\Teacher;

final readonly class ScoredCandidate
{
    /**
     * @param array<string, int|float> $criteria
     */
    public function __construct(
        public Teacher $teacher,
        public float $score,
        public array $criteria = [],
    ) {}
}
