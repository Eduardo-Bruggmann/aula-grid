@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Novo professor</h1>

    <form action="{{ route('teachers.store') }}" method="POST" class="max-w-2xl">
        @csrf

        @include('teachers._form')

        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded">
                Salvar
            </button>

            <a href="{{ route('teachers.index') }}" class="px-4 py-2 rounded bg-slate-800">
                Cancelar
            </a>
        </div>
    </form>
@endsection
