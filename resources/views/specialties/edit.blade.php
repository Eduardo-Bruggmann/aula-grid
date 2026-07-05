@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Editar unidade SENAI</h1>

    <form action="{{ route('specialties.update', $specialty) }}" method="POST" class="max-w-xl">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block mb-2">Nome</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $specialty->name) }}"
                class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2"
            >

            @error('name')
                <p class="text-red-400 mt-2">{{ $message }}</p>
            @enderror

            <label for="description" class="block mb-2 mt-4">Descrição</label>
            <input
                type="text"
                id="description"
                name="description"
                value="{{ old('description', $specialty->description) }}"
                class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2"
            >

            @error('description')
                <p class="text-red-400 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded">
                Atualizar
            </button>

            <a href="{{ route('specialties.index') }}" class="px-4 py-2 rounded bg-slate-800">
                Cancelar
            </a>
        </div>
    </form>
@endsection