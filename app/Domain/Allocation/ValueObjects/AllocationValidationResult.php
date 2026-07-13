<?php

namespace App\Domain\Allocation\ValueObjects;

use App\Domain\Allocation\Enums\AllocationConflictCode;

final readonly class AllocationValidationResult
{
    private function __construct(
        private array $violations
    ) {}

    public static function valid(): self
    {
        return new self([]);
    }

    public static function invalid(array $violations): self
    {
        return new self(array_values(array_unique(
            $violations,
            SORT_REGULAR
        )));
    }

    public function isValid(): bool
    {
        return $this->violations === [];
    }

    public function isInvalid(): bool
    {
        return !$this->isValid();
    }

    public function violations(): array
    {
        return $this->violations;
    }

    public function has(AllocationConflictCode $code): bool
    {
        return in_array($code, $this->violations, true);
    }

    public function messages(): array
    {
        return array_map(
            static fn(AllocationConflictCode $code): string => $code->message(),
            $this->violations
        );
    }
}
