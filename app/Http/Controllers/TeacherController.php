<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\SchoolUnit;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(): View
    {
        $teachers = Teacher::query()
            ->with('schoolUnit')
            ->orderBy('name')
            ->paginate(10);

        return view('teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        $schoolUnits = SchoolUnit::query()
            ->orderBy('name')
            ->get();

        return view('teachers.create', compact('schoolUnits'));
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        Teacher::create($request->validated());

        return redirect()
            ->route('teachers.index')
            ->with('success', 'Professor criado com sucesso.');
    }

    public function show(Teacher $teacher): View
    {
        $teacher->load(['schoolUnit', 'specialties']);

        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher): View
    {
        $schoolUnits = SchoolUnit::query()
            ->orderBy('name')
            ->get();

        return view('teachers.edit', compact('teacher', 'schoolUnits'));
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        $teacher->update($request->validated());

        return redirect()
            ->route('teachers.index')
            ->with('success', 'Professor atualizado com sucesso.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->delete();

        return redirect()
            ->route('teachers.index')
            ->with('success', 'Professor removido com sucesso.');
    }
}
