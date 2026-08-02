<?php

namespace App\Http\Controllers;

use App\Application\Allocation\UseCases\GenerateAllocationUseCase;
use App\Models\AllocationRun;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AllocationRunController extends Controller
{
    public function index(): View
    {
        $allocationRuns = AllocationRun::query()
            ->latest('created_at')
            ->paginate(10);

        return view('allocation-runs.index', [
            'allocationRuns' => $allocationRuns,
        ]);
    }

    public function store(
        GenerateAllocationUseCase $generateAllocationUseCase
    ): RedirectResponse {
        $result = $generateAllocationUseCase->execute();

        $message = $result->wasFullyAllocated()
            ? 'Alocação gerada com sucesso.'
            : 'Alocação gerada com conflitos pendentes.';

        return redirect()
            ->route(
                'allocation-runs.show',
                $result->allocationRun
            )
            ->with('success', $message);
    }

    public function show(
        AllocationRun $allocationRun
    ): View {
        $allocationRun->load([
            'allocations' => fn ($query) => $query
                ->with([
                    'teacher',
                    'schoolClass.subject',
                    'schoolClass.schoolUnit',
                    'period',
                ])
                ->orderBy('period_id')
                ->orderBy('school_class_id'),

            'conflicts' => fn ($query) => $query
                ->with([
                    'schoolClass.subject',
                    'schoolClass.schoolUnit',
                    'period',
                ])
                ->orderBy('period_id')
                ->orderBy('school_class_id'),
        ]);

        return view('allocation-runs.show', [
            'allocationRun' => $allocationRun,
        ]);
    }
}
