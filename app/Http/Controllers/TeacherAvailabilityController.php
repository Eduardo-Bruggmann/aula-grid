<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTeacherAvailabilityRequest;
use App\Models\Period;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherAvailabilityController extends Controller
{
    public function edit(Teacher $teacher): View
    {
        $periods = Period::query()
            ->orderBy('sort_order')
            ->get();

        $availablePeriodIds = $teacher->availabilities()
            ->where('is_available', true)
            ->pluck('period_id')
            ->toArray();

        return view('teachers.availability.edit', compact(
            'teacher',
            'periods',
            'availablePeriodIds'
        ));
    }

    public function update(
        UpdateTeacherAvailabilityRequest $request,
        Teacher $teacher
    ): RedirectResponse {
        $validated = $request->validated();

        $availablePeriodIds = array_map(
            'intval',
            $validated['periods'] ?? []
        );

        DB::transaction(function () use ($teacher, $availablePeriodIds): void {
            $periods = Period::query()->get();

            foreach ($periods as $period) {
                $teacher->availabilities()->updateOrCreate(
                    [
                        'period_id' => $period->id,
                    ],
                    [
                        'is_available' => in_array(
                            $period->id,
                            $availablePeriodIds,
                            true
                        ),
                    ]
                );
            }
        });

        return redirect()
            ->route('teachers.availability.edit', $teacher)
            ->with('success', 'Disponibilidade atualizada com sucesso.');
    }
}
