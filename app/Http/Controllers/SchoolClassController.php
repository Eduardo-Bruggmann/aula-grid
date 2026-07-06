<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Models\Subject;
use App\Models\SchoolUnit;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(): View
    {
        $schoolClasses = SchoolClass::query()
            ->with(['subject.specialty', 'schoolUnit'])
            ->orderBy('name')
            ->paginate(10);

        return view('school-classes.index', compact('schoolClasses'));
    }

    public function create(): View
    {
        $subjects = Subject::query()->orderBy('name')->get();
        $schoolUnits = SchoolUnit::query()->orderBy('name')->get();

        return view('school-classes.create', compact('subjects', 'schoolUnits'));
    }

    public function store(StoreSchoolClassRequest $request): RedirectResponse
    {
        SchoolClass::create($request->validated());

        return redirect()
            ->route('school-classes.index')
            ->with('success', 'Turma criada com sucesso.');
    }

    public function show(SchoolClass $schoolClass): View
    {
        return view('school-classes.show', compact('schoolClass'));
    }

    public function edit(SchoolClass $schoolClass): View
    {
        $subjects = Subject::query()->orderBy('name')->get();
        $schoolUnits = SchoolUnit::query()->orderBy('name')->get();

        return view('school-classes.edit', compact('schoolClass', 'subjects', 'schoolUnits'));
    }

    public function update(UpdateSchoolClassRequest $request, SchoolClass $schoolClass): RedirectResponse
    {
        $schoolClass->update($request->validated());

        return redirect()
            ->route('school-classes.index')
            ->with('success', 'Turma atualizada com sucesso.');
    }

    public function destroy(SchoolClass $schoolClass): RedirectResponse
    {
        $schoolClass->delete();

        return redirect()
            ->route('school-classes.index')
            ->with('success', 'Turma removida com sucesso.');
    }
}
