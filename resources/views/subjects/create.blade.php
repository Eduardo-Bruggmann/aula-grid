@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Nova UC</h1>

    <form action="{{ route('subjects.store') }}" method="POST" class="max-w-xl">
        @csrf

        <div class="mb-4">
            <label for="name" class="block mb-2">Nome</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2"
            >

            @error('name')
                <p class="text-red-400 mt-2">{{ $message }}</p>
            @enderror

            <label for="specialty_id" class="block mb-2 mt-4">Especialidade</label>
            <select
                id="specialty_id"
                name="specialty_id"
                class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2 text-slate-100 shadow-sm"
            >
                <option value="" disabled @selected(old('specialty_id') === null)>
                    Selecione uma especialidade
                </option>
                @foreach ($specialties as $specialty)
                    <option
                        value="{{ $specialty->id }}"
                        class="bg-slate-900 text-slate-100"
                        @selected(old('specialty_id') == $specialty->id)
                    >
                        {{ $specialty->name }}
                    </option>
                @endforeach
            </select>

            @error('specialty_id')
                <p class="text-red-400 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded">
                Salvar
            </button>

            <a href="{{ route('subjects.index') }}" class="px-4 py-2 rounded bg-slate-800">
                Cancelar
            </a>
        </div>
    </form>
@endsection