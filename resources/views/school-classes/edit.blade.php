@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Editar turma</h1>

    <form action="{{ route('school-classes.update', $schoolClass) }}" method="POST" class="max-w-2xl">
        @csrf
        @method('PUT')

        @include('school-classes._form')

        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded">
                Atualizar
            </button>

            <a href="{{ route('school-classes.index') }}" class="px-4 py-2 rounded bg-slate-800">
                Cancelar
            </a>
        </div>
    </form>
@endsection
