@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Editar professor</h1>

    <form action="{{ route('teachers.update', $teacher) }}" method="POST" class="max-w-2xl">
        @csrf
        @method('PUT')

        @include('teachers._form')

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded">
                Atualizar
            </button>

            <a href="{{ route('teachers.index') }}" class="px-4 py-2 rounded bg-slate-800">
                Cancelar
            </a>
        </div>
    </form>
@endsection