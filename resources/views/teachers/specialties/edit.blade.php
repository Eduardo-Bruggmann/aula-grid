@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Editar especialidade do professor</h1>
        <p class="text-slate-400">
            Professor: {{ $teacher->name }} | Matrícula: {{ $teacher->registration }}
        </p>
    </div>

    <form action="{{ route('teachers.specialties.update', [$teacher, $teacherSpecialty]) }}"
          method="POST"
          class="max-w-2xl">
        @csrf
        @method('PUT')

        @include('teachers.specialties._form')

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded">
                Atualizar
            </button>

            <a href="{{ route('teachers.specialties.index', $teacher) }}"
               class="px-4 py-2 rounded bg-slate-800">
                Cancelar
            </a>
        </div>
    </form>
@endsection