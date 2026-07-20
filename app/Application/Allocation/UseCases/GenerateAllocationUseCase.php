<?php

namespace App\Application\Allocation\UseCases;

use App\Application\Allocation\DTOs\GenerateAllocationResult;
use App\Domain\Allocation\Enums\AllocationConflictCode;
use App\Domain\Allocation\Services\AllocationEngine;
use App\Models\AllocationConflict;
use App\Models\AllocationRun;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class GenerateAllocationUseCase
{
    public function __construct(
        private AllocationEngine $allocationEngine,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(): GenerateAllocationResult
    {
        return DB::transaction(function (): GenerateAllocationResult {
            $allocationRun = AllocationRun::query()->create([
                'status' => 'running',
                'score' => 0,
                'total_allocations' => 0,
                'total_conflicts' => 0,
                'started_at' => now(),
                'finished_at' => null,
            ]);

            try {
                $engineResult = $this->allocationEngine->execute(
                    $allocationRun
                );

                foreach ($engineResult->unresolved as $unresolved) {
                    AllocationConflict::query()->create([
                        'allocation_run_id' => $allocationRun->id,
                        'school_class_id' => $unresolved->schoolClass->id,
                        'period_id' => $unresolved->period->id,
                        'reason_code' => AllocationConflictCode::NO_VALID_CANDIDATE->value,
                        'reason_description' => $unresolved->reason,
                        'status' => 'open',
                    ]);
                }

                $status = $engineResult->hasConflicts()
                    ? 'completed_with_conflicts'
                    : 'completed';

                $coverage = $engineResult->coveragePercentage();

                $allocationRun->update([
                    'status' => $status,
                    'score' => $coverage,
                    'total_allocations' => $engineResult->allocationsCreated,
                    'total_conflicts' => $engineResult->conflictsCount(),
                    'finished_at' => now(),
                ]);

                return new GenerateAllocationResult(
                    allocationRun: $allocationRun->fresh(),
                    allocationsCreated: $engineResult->allocationsCreated,
                    conflictsCreated: $engineResult->conflictsCount(),
                    requestedAllocations: $engineResult->requestedAllocations,
                    coveragePercentage: $coverage,
                );
            } catch (Throwable $exception) {
                /*
                 * Essa atualização não sobreviverá ao rollback, mas deixa
                 * explícita a intenção caso futuramente a estratégia de
                 * persistência de falhas seja separada.
                 */
                $allocationRun->update([
                    'status' => 'failed',
                    'finished_at' => now(),
                ]);

                throw $exception;
            }
        }, attempts: 3);
    }
}
