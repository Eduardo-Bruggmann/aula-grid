<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolUnitRequest;
use App\Http\Requests\UpdateSchoolUnitRequest;
use App\Models\SchoolUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SchoolUnitController extends Controller
{
    public function index(): View
    {
        $schoolUnits = SchoolUnit::query()
            ->orderBy('name')
            ->paginate(10);

        return view('school-units.index', compact('schoolUnits'));
    }

    public function create(): View
    {
        return view('school-units.create');
    }

    public function store(StoreSchoolUnitRequest $request): RedirectResponse
    {
        SchoolUnit::create($request->validated());

        return redirect()
            ->route('school-units.index')
            ->with('success', 'Unidade criada com sucesso.');
    }

    public function show(SchoolUnit $schoolUnit): View
    {
        return view('school-units.show', compact('schoolUnit'));
    }

    public function edit(SchoolUnit $schoolUnit): View
    {
        return view('school-units.edit', compact('schoolUnit'));
    }

    public function update(UpdateSchoolUnitRequest $request, SchoolUnit $schoolUnit): RedirectResponse
    {
        $schoolUnit->update($request->validated());

        return redirect()
            ->route('school-units.index')
            ->with('success', 'Unidade atualizada com sucesso.');
    }

    public function destroy(SchoolUnit $schoolUnit): RedirectResponse
    {
        $schoolUnit->delete();

        return redirect()
            ->route('school-units.index')
            ->with('success', 'Unidade removida com sucesso.');
    }
}
