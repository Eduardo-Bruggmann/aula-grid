<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherSpecialtyRequest;
use App\Http\Requests\UpdateTeacherSpecialtyRequest;
use App\Models\Specialty;
use App\Models\Teacher;
use App\Models\TeacherSpecialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeacherSpecialtyController extends Controller
{
    public function index(Teacher $teacher): View
    {
        $teacher->load([
            'schoolUnit',
            'teacherSpecialties.specialty',
        ]);

        return view('teachers.specialties.index', compact('teacher'));
    }

    public function create(Teacher $teacher): View
    {
        $usedSpecialtyIds = $teacher->teacherSpecialties()
            ->pluck('specialty_id')
            ->toArray();

        $specialties = Specialty::query()
            ->whereNotIn('id', $usedSpecialtyIds)
            ->orderBy('name')
            ->get();

        $teacherSpecialty = new TeacherSpecialty();

        return view('teachers.specialties.create', compact(
            'teacher',
            'specialties',
            'teacherSpecialty'
        ));
    }

    public function store(StoreTeacherSpecialtyRequest $request, Teacher $teacher): RedirectResponse
    {
        $teacher->teacherSpecialties()->create($request->validated());

        return redirect()
            ->route('teachers.specialties.index', $teacher)
            ->with('success', 'Especialidade vinculada ao professor com sucesso.');
    }

    public function edit(Teacher $teacher, TeacherSpecialty $teacherSpecialty): View
    {
        $this->ensureSpecialtyBelongsToTeacher($teacher, $teacherSpecialty);

        $specialties = Specialty::query()
            ->orderBy('name')
            ->get();

        return view('teachers.specialties.edit', compact(
            'teacher',
            'teacherSpecialty',
            'specialties'
        ));
    }

    public function update(
        UpdateTeacherSpecialtyRequest $request,
        Teacher $teacher,
        TeacherSpecialty $teacherSpecialty
    ): RedirectResponse {
        $this->ensureSpecialtyBelongsToTeacher($teacher, $teacherSpecialty);

        $teacherSpecialty->update($request->validated());

        return redirect()
            ->route('teachers.specialties.index', $teacher)
            ->with('success', 'Especialidade do professor atualizada com sucesso.');
    }

    public function destroy(Teacher $teacher, TeacherSpecialty $teacherSpecialty): RedirectResponse
    {
        $this->ensureSpecialtyBelongsToTeacher($teacher, $teacherSpecialty);

        $teacherSpecialty->delete();

        return redirect()
            ->route('teachers.specialties.index', $teacher)
            ->with('success', 'Especialidade removida do professor com sucesso.');
    }

    private function ensureSpecialtyBelongsToTeacher(
        Teacher $teacher,
        TeacherSpecialty $teacherSpecialty
    ): void {
        abort_if($teacherSpecialty->teacher_id !== $teacher->id, 404);
    }
}
