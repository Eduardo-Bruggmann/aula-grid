@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Vincular especialidade</h1>
        <p class="text-slate-400">
            Professor: {{ $teacher->name }} | Matrícula: {{ $teacher->registration }}
        </p>
    </div>

    @if ($specialties->isEmpty())
        <div class="bg-yellow-900/40 border border-yellow-700 text-yellow-200 rounded px-4 py-3 mb-6">
            Todas as especialidades já foram vinculadas a este professor.
        </div>

        <a href="{{ route('teachers.specialties.index', $teacher) }}"
           class="px-4 py-2 rounded bg-slate-800">
            Voltar
        </a>
    @else
        <form action="{{ route('teachers.specialties.store', $teacher) }}"
              method="POST"
              class="max-w-2xl">
            @csrf

            @include('teachers.specialties._form')

            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded">
                    Salvar
                </button>

                <a href="{{ route('teachers.specialties.index', $teacher) }}"
                   class="px-4 py-2 rounded bg-slate-800">
                    Cancelar
                </a>
            </div>
        </form>
    @endif
@endsection
