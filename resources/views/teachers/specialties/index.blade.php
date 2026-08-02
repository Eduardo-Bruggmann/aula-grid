@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Especialidades de {{ $teacher->name }}</h1>
            <p class="text-slate-400">
                Matrícula: {{ $teacher->registration }} |
                Unidade: {{ $teacher->schoolUnit->name }}
            </p>
        </div>

        <a href="{{ route('teachers.specialties.create', $teacher) }}"
           class="inline-flex w-full items-center justify-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-500 sm:w-auto">
            Vincular especialidade
        </a>
    </div>

    <div class="overflow-hidden rounded border border-slate-800 bg-slate-900">
        <div class="overflow-x-auto">
        <table class="min-w-[42rem] w-full">
            <thead class="bg-slate-800">
                <tr>
                    <th class="text-left px-4 py-3">Especialidade</th>
                    <th class="text-left px-4 py-3">Aderência</th>
                    <th class="text-right px-4 py-3">Ações</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($teacher->teacherSpecialties as $teacherSpecialty)
                    <tr class="border-t border-slate-800">
                        <td class="px-4 py-3">
                            {{ $teacherSpecialty->specialty->name }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $teacherSpecialty->adherence_score }}%
                        </td>

                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('teachers.specialties.edit', [$teacher, $teacherSpecialty]) }}"
                               class="text-yellow-400">
                                Editar
                            </a>

                            <form action="{{ route('teachers.specialties.destroy', [$teacher, $teacherSpecialty]) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="text-red-400"
                                    onclick="return confirm('Remover esta especialidade do professor?')"
                                >
                                    Remover
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-slate-400">
                            Nenhuma especialidade vinculada a este professor.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
        <a href="{{ route('teachers.show', $teacher) }}"
           class="px-4 py-2 rounded bg-slate-800">
            Voltar ao professor
        </a>

        <a href="{{ route('teachers.index') }}"
           class="px-4 py-2 rounded bg-slate-800">
            Voltar à lista
        </a>
    </div>
@endsection
