<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Specialty;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::query()
            ->with('specialty')
            ->orderBy('name')
            ->paginate(10);

        return view('subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        $specialties = Specialty::query()
            ->orderBy('name')
            ->get();

        return view('subjects.create', compact('specialties'));
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Subject::create($request->validated());

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Unidade Curricular criada com sucesso.');
    }

    public function show(Subject $subject): View
    {
        return view('subjects.show', compact('subject'));
    }

    public function edit(Subject $subject): View
    {
        $specialties = Specialty::query()
            ->orderBy('name')
            ->get();

        return view('subjects.edit', compact('subject', 'specialties'));
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Unidade Curricular atualizada com sucesso.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Unidade Curricular removida com sucesso.');
    }
}
