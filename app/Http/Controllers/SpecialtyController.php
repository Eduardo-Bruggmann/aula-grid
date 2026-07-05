<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpecialtyRequest;
use App\Http\Requests\UpdateSpecialtyRequest;
use App\Models\Specialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SpecialtyController extends Controller
{
    public function index(): View
    {
        $specialties = Specialty::query()
            ->orderBy('name')
            ->paginate(10);

        return view('specialties.index', compact('specialties'));
    }

    public function create(): View
    {
        return view('specialties.create');
    }

    public function store(StoreSpecialtyRequest $request): RedirectResponse
    {
        Specialty::create($request->validated());

        return redirect()
            ->route('specialties.index')
            ->with('success', 'Especialidade criada com sucesso.');
    }

    public function show(Specialty $specialty): View
    {
        return view('specialties.show', compact('specialty'));
    }

    public function edit(Specialty $specialty): View
    {
        return view('specialties.edit', compact('specialty'));
    }

    public function update(UpdateSpecialtyRequest $request, Specialty $specialty): RedirectResponse
    {
        $specialty->update($request->validated());

        return redirect()
            ->route('specialties.index')
            ->with('success', 'Especialidade atualizada com sucesso.');
    }

    public function destroy(Specialty $specialty): RedirectResponse
    {
        $specialty->delete();

        return redirect()
            ->route('specialties.index')
            ->with('success', 'Especialidade removida com sucesso.');
    }
}
